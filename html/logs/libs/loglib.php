<?php
// --------------------------------------------------------------------
// loglib:  Library fucntions for logging.
//
// Created: 12/03/14 DLB
// --------------------------------------------------------------------

// The routines here provide a way that the website can keep a running
// log of things that happen (errors, logins, page loads) so that we
// can keep a history of what happened for debugging purposes.

// The logs are kept in text files, one new file per day.  The
// files are named log_yymmdd.txt, where the 'yymmdd' is the date.
// The files run midnight to midnight.  The location of the logs
// is specified in "config.php".
//
// The format of the lines in the log are a follows:
//
//  #mm/dd/yyyy dayofweek  serverinfo
//  -hh:mm:ss c nn filename> Message
//  +> Message
//
// where:
// the '#' in frist column means date/sever info for the rest of the file
// the '-' in first column indicates the starting of a separate log event
// the '+' in first column indicates a continuation of a log event,
// the 'mm/dd/yyy dayofweek' gives the date and week day of the data in the file,
// The 'hh:mm:ss' is the time that the log was written to.
// The 'c' is a one letter code that indicats the type of log message, as follows:
//
//  s  -- general status message, can be anything the programmer deems important to log
//  e  -- An un accounted for error.  Error info usually follows.
//  p  -- Tracking of a page load.  
//  
// The 'nn' is the UserID number, or 0 if not known, or not logged in.
// The 'filename' is the name of script that is running, or some indication
// of the place in the code where the logging event occured.

require_once "config.php";
require_once "libs/misclib.php";

// --------------------------------------------------------------------
// This fuction formats the final output.  The $message argument can either
// be a simple string, or an array of strings.  Note: all errors that
// might occur in this fuction are ignored.  The caller can be sure
// to get control back.
function rawlog($message, $type='s', $loc="")
{
    global $config;
    // First determine the name of the current log file. The name is 
    // based on time.
    
    $t = UnixTimeNow();
    $fbase = "log_" . date("ymd", $t) . ".txt";
    $filename = $config["LogDir"] . $fbase;
    $newfile = !file_exists($filename);
    $logfile = fopen($filename, "a+");
    if($newfile)
    {
        // New file. Write info at top.
        $line = "#" . date("m/d/Y l") . '   ' . $config["ServerName"] . "\n";
        fwrite($logfile, $line);
    }
    
    // Make sure message is an array of lines, and that embedded new-lines are 
    // converted into array elements.
    $msglines = log_fixmsg($message);
    
    // Now, format the first line...
    $firstline = $msglines[0];
    array_shift($msglines);

    if (empty($type)) $type = 's';
    $line = '*' . date("H:i:s") . ' ' . substr($type, 0, 1);
    $line .= sprintf("%3d", GetUserID());
    if(empty($loc)) { $page = basename($_SERVER["SCRIPT_NAME"]); }
    else { $page = $loc; }
    if(!empty($page)) { $line .= ' ' . $page; }
    $line .= '> ' . $firstline . "\n";
    fwrite($logfile, $line);
    
    foreach($msglines as $line)
    {
        fwrite($logfile, '+> ' . $line . "\n"); 
    }
    fclose($logfile);
}

// --------------------------------------------------------------------
// Processes the message so that all separate lines are converted into
// array elements.  That way, new-lines can be buried in the message
// and not break the format of the log file.
function log_fixmsg($message)
{
    $out = array();
    if(!is_array($message)) 
    {
        $out[] = $message;
        return $out;
    }
    foreach($message as $m)
    {
        $lines = explode("\n", $m);
        foreach($lines as $x) { $out[] = $x; }
    }
    return $out;
}

// --------------------------------------------------------------------
// Logs a standard message with a user-specfied location. $loc can
// be empty, in which case the name of the top-level script is used.
// $message can be a single string or an array of strings.
function log_msg($loc, $message)
{
    rawlog($message, 's', $loc);
}

// --------------------------------------------------------------------
// Log a page load...
function log_page()
{
    rawlog(" ", 'p');
}

// --------------------------------------------------------------------
// Logs a error message with a user-specfied location. $loc can
// be empty, in which case the name of the top-level script is used.
// $message can be a single string or an array of strings.
function log_error($loc, $message)
{
    rawlog($message, 'e', $loc);
}

// --------------------------------------------------------------------
// Returns the entire contents of a log file as an array of lines.  If
// the file is not found, then the array is empty.  $date is a simple
// string that is converted to an actual date.  If that fails, false
// is returned.
function GetLogFileContents($date)
{
    global $config;
    date_default_timezone_set($config["TimeZone"]);
    $ts = strtotime($date);
    if($ts === false) return false;
    
    $fbase = "log_" . date("ymd", $ts) . ".txt";
    $filename = $config["LogDir"] . $fbase;
    if(!file_exists($filename)) { return array(); }
    $lines = array();
    $data = file_get_contents($filename);
    return $data;
    //if($data === false) { return array(); }
    //return $data;
}

// --------------------------------------------------------------------
// Given log data as one concatated string, returns an array of 
// lines, filtered according to the inputs.
function FilterLogData($data, $b_pages, $b_errors, $b_general, $b_oneid, $uid)
{
    $rawlines = explode("\n", $data);
    $output_lines = array();
    $keep = true;
    foreach($rawlines as $line)
    {
        $col1 = substr($line, 0, 1);
        if($col1 == '*')
        {
            $keep = true;
            $code = substr($line, 10, 1);
            if($b_oneid)
            {
                $id = intval(substr($line, 11));
                if($id != $uid) $keep = false;
            }
            if (!$b_pages && $code == 'p') $keep = false;
            if (!$b_errors && $code == 'e') $keep = false;
            if (!$b_general && $code == 's') $keep = false;
        }
        if($keep) { array_push($output_lines, $line); }
    }
    return $output_lines;
}

// --------------------------------------------------------------------
// Given an array of log lines, uses first char to determine which lines
// belong to the same event and then returns an array of events. 
// Each "event" is an array of one or more lines.

function GetLogEvents($lines)
{
    $output_events = array();
    $current_event = array();
    foreach($lines as $line)
    {
        if(blank($line)) continue;
        $col1 = substr($line, 0, 1);
        if($col1 != '+')
        {
            if(count($current_event) > 0) $output_events[] = $current_event;
            $current_event = array($line);
            continue;
        }
        $current_event[] = $line;
    }
    if(count($current_event) > 0) $output_events[] = $current_event;
    return $output_events;
}

// --------------------------------------------------------------------
// Given an array of events, where each event is an array of one or 
// more log lines, returns an array of lines.

function GetLogLinesFromEvents($events)
{
    $lines = array();
    foreach($events as $e)
    {
        foreach($e as $l) $lines[] = $l;
    }
    return $lines;
}

// --------------------------------------------------------------------
// Given log lines from a log file, reverses the order of events so that
// the newest events are shown first. Does this while keeping multiple
// lines from same event intact.  Returns an array of lines.
function ReverseLogLines($lines)
{
    $events = GetLogEvents($lines);
    $events = array_reverse($events);
    $output = GetLogLinesFromEvents($events);
    return $output;
}

?>
