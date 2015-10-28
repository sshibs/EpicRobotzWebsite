<?php
// --------------------------------------------------------------------
// attendance_uploadevents.php -- Uploads the event table.
//
// Created:  1/16/15 DLB
// --------------------------------------------------------------------

require_once "libs/all.php";
session_start();
log_page();
CheckLogin();
CheckAdmin();
$timer = new timer();
$loc = 'attendance_uploadevents.php';
$ins_file = "docs/eventtable.txt";
$error_msg = "";
$success_msg = "";
$instructions = "";

if(file_exists($ins_file)) { $instructions = file_get_contents($ins_file); }

if( $_SERVER["REQUEST_METHOD"] == "POST")
{
    if(!isset($_FILES["EventFile"])) { DieWithMsg($loc, '$_FILES not set.'); }
    $fileinfo = $_FILES["EventFile"];
    $filesize = $fileinfo["size"];
    $filetype = $fileinfo["type"];
    $fileerr  = $fileinfo["error"];
    $tempfile = $fileinfo["tmp_name"];
    if(empty($tempfile) || $fileerr == 4)
    {
        $error_msg = "No input file given.";
    }
    else if($filesize <= 0)
    {
        $error_msg = "Input file was empty.";
    }
    else 
    {
        RemoveAllEvents();
        $result = ProcessEventFile($tempfile);
        if($result[0] === true) $success_msg = $result[1];
        else                    $error_msg = $result[1];
    }
}

include "forms/header.php";
include "forms/navform.php";
include "forms/attendance_menubar.php";
include "forms/attendance_uploadevent_form.php";
include "forms/footer.php";


// --------------------------------------------------------------------
function ProcessEventFile($filename)
{
    global $config;
    $loc = "attendance_uploadevents.php->ProcessEventFile";
    
    $file = fopen($filename, "r");
    if($file === false)
    {
        return array(false, "Unable to open file.");
    }
    
    $n_okay = 0;
    $n_fail = 0;
    $ln = 1;
    // The first line is the column headers. 
    $header = fgetcsv($file); $ln++;
    if($header === false) return $n;
    foreach($header as &$h)
    {
        $h = trim($h);
    }
    
    // Now, do some sanity checks to make sure we have
    // an appropriate file.
    if(!in_array("Name",  $header) ||
       !in_array("StartTime",  $header) ||
       !in_array("EndTime", $header) ||
       !in_array("Type",  $header) ||
       !in_array("Purpose", $header))
    {
        return array(false, "Input file does not have required columns.");
    }
    
    $tstart = microtime(true);  // Time the entire operation...  Don't go over 4 minutes.
    $btimeout = false;
    
    while(true)
    {
        $result = set_time_limit(60);   
        if($result == false) {log_error($loc, "Unable to set/reset time limit to 20 seconds."); }
    
        $data = fgetcsv($file); $ln++;
        if($data === false) break;
        
        // Don't process blank lines.
        if(count($data) <= 0) continue;  
        if(is_null($data[0])) continue;

        foreach($data as &$d) { $d = trim($d); }
        
        // Organize the data into an associtive array
        $fields = JoinKeyValues($header, $data);
        
        // Make sure we have required data
        if(!isset($fields["Name"])  ||
           !isset($fields["StartTime"])  ||
           !isset($fields["EndTime"]) ||
           !isset($fields["Type"]) ||
           !isset($fields["Purpose"]))
        {
            log_msg($loc, 'Event not added. Fields missing, line ' . $ln);
            $n_fail++;
            continue;
        }
        
        // Make sure none of the required fields are empty.
        if(empty($fields["Name"]) ||
           empty($fields["StartTime"]) ||
           empty($fields["EndTime"]) ||
           empty($fields["Type"]) ||
           empty($fields["Purpose"]))
        {
            log_msg($loc, 'Event not added. Some requried fields are empty. Line ' . $ln);
            $n_fail++;
            continue;
        }           
        
        StoreEvent($fields);
        $n_okay++;
        
        $telp = (microtime(true) - $tstart);
        if($telp > 240.0) {$btimeout = true; break; }
        
    }
    
    $msg = $n_okay . ' events added. ' . $n_fail . ' failures. ' . $ln . ' lines processed.';
    if($btimeout) {$msg .= ' ** TimeOut Occured, Process aborted. **'; }
    log_msg($loc, $msg);
    return array(true, $msg);
}

?>