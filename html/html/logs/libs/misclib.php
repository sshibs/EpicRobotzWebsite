<?php
// --------------------------------------------------------------------
// misclib:  miscellanous library fucntions
//
// Note: these functions should not be dependent on other script files.
//
// Created: 11/22/14 DLB
// --------------------------------------------------------------------

require_once("config.php");

// --------------------------------------------------------------------
// Returns an array of strings from a line of text where elements
// delimited by slashes. Returned strings are all lowercase.
function ArrayFromSlashStr($s)
{
    if(empty($s)) { return array(); }
    $tags = str_getcsv($s, '/');
    $tagArray = array();
    foreach($tags as $t)
    {
        array_push($tagArray, strtolower(trim($t)));
    }
    return $tagArray;
}

// --------------------------------------------------------------------
// Returns a string of either true or false.  To be true, the input
// must be a boolean true.  False for all else.
function TFstr($thing)
{
    if($thing) { return "true"; }
    return "false";
}

// --------------------------------------------------------------------
// Formats a unix time stamp into a sequence of characters suitable
// for MySQL.  Note: the outter quotes are not included.
function DateTimeForSQL($unixtime)
{
    return date("Y-m-d H:i:s", $unixtime);
}

// --------------------------------------------------------------------
// Return s unix time stamp given the string from a MySQL datetime
// field.
function DateTimeFromSQL($sqltime)
{
    return strtotime($sqltime);
}

// --------------------------------------------------------------------
// Returns the current time with the correct time zone applied
// as a unix time stamp.
function UnixTimeNow()
{
    global $config;
    date_default_timezone_set($config["TimeZone"]);
    return time();
}

// --------------------------------------------------------------------
// Given two indexed arrays, returns an associtive array where the
// keys come from $keys, and values from $values.  Arrays can be
// different lenghts.  The output array will be the shorter of the two.
// Also, processing stops when the first null element is found in 
// either array.
function JoinKeyValues($keys, $values)
{
    $out = array();
    $n = count($keys);
    $n2 = count($values);
    if($n <= 0 || $n2 <= 0) {return $out; }
    if($n > $n2) $n = $n2;
    for($i = 0; $i < $n; $i++)
    {
        if(is_null($keys[$i]))   {return $out; }
        if(is_null($values[$i])) {return $out; }
        $out[$keys[$i]] = $values[$i];
    }
    return $out;
}

// --------------------------------------------------------------------
// Prints the content of a variable to the browser window in a "nice"
// format -- only for debugging.
function dumpit($v)
{
    echo "<pre>";
    print_r($v);
    echo "</pre><br>";
}

// --------------------------------------------------------------------
// Returns a path to a place where temp files can be stored.
// This is usually under the Upload directory, but doesn't have to be.
// Note: temp folder is for files that are immediately processed and
// moved or deleted. Do not put files here that are for viewing, or
// long term storage.
function GetTempDir()
{
    global $config;
    $pt = $config["UploadDir"] . 'tmp/';
    if(!file_exists($pt))
    {
        $result = @mkdir($pt, 0764);
        if($result === false)
        {
            DieWithMsg($loc, "Unable to create folder: " . $pt);
        }
    }
    return $pt;
}

// --------------------------------------------------------------------
// Checks to see if all the values in an associtive array are empty.
function AreValuesEmpty($a)
{
    foreach($a as $k => $v)
    {
        if(!empty($v)) return false;
    }
    return true;
}

// --------------------------------------------------------------------
// Returns true if input is "blank".  Blank is similar to empty, but
// actual zero values are not blank.  Neither are bools.  That is, 0,
// 0.0, "0", and false all return non-blank (false).  True is returned
// for "", null, and not-set.
function blank($a)
{
    if(is_bool($a)) return false;
    if(is_numeric($a)) return false;
    if(!empty($a)) return false;
    return true;
}

// --------------------------------------------------------------------
// Performs word wrapping, returning lines that are less than the 
// given number of characters.  Will break words in the middle if
// neceeary.  
function WordWrapWithForcedBreak($s, $maxlen)
{
    return $s;
}
    /*
    NOT FINISHED!
    $words = explode(' ', $s);
    $lines = array();
    $c = 0;
    $current_line = "";
    foreach($words as $w)
    {
        $n = strlen($w);
        if(empty($current_line)) $space = 0;
        else $space = 1;
        if($n + $c + $space <= $maxlen)
        {
            // It will fit completely on current line.
            if($space == 1) $curent_line .= ' ';
            $current_line .= $w;
            continue;
        }
        // Won't fit.  Should we put it partly on current line?
        $y = $maxlen - ($c + $space);
        if($n > $maxlen && $y > 3)
        {
            // Its longer than a full line anyway, so start it now.
            $w1 = substr($w, 0, $y - 1);
            $w2 = substr($w, $y-1);
            
            ????
        }
    }
    */
// --------------------------------------------------------------------
// Returns true if the first char in $a is alpha (a-z, A-Z).
function is_alpha($a)
{
    $alpha = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    if(strstr($alpha, $a) === false) return false;
    return true;
}

// --------------------------------------------------------------------
// Returns true if the first char in $a is a digit (0123456789).
function is_digit($a)
{
    $digits = '0123456789';
    if(strstr($digits, $a) === false) return false;
    return true; 
}
    
    
// --------------------------------------------------------------------
// Simple class to implement a code timer. Starts timing on construction.
// after construction, you can access the elapsed time with various 
// function calls.
class Timer
{
    private $start = 0;
    
    function __construct()
    {
        $this->start = microtime(true);
    }
    
    // Returns the elapsed seconds.
    function Secs()
    {
        return microtime(true) - $this->start;
    }
    
    // Returns the elapsed miliseconds.
    function Ms()
    {
        return (microtime(true) - $this->start) * 1000.0;
    }
    
    // Returns a string in the form " xxx.x secs" representing the elapsed time.
    function SecStr()
    {
        $s = microtime(true) - $this->start;
        return sprintf(" %3.1f secs", $s);
    }
    
    // Returns a string in the form " xxxx.x ms" representing the elapsed time.
    function MsStr()
    {
        $t = (microtime(true) - $this->start) * 1000.0; 
        return sprintf(" %4.1f ms", $t);
    }
    
    // Returns a string in the form " xxx.xx uuuu" representing the elapsed time.
    // where, xxx.x is numeric, and uuuu is either 'secs' or 'ms' depending on
    // the maginitude of the elapsed time.
    function Str()
    {
        if($this->Secs() > 1.0) return $this->SecStr();
        else return $this->MsStr();
    }
    
}

?>