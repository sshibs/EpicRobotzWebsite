<?php
// --------------------------------------------------------------------
// members_bulkpics.php -- Uploads pictures in bulk.
//
// Created: 1/2/15 DLB -- Hacked from Admin and Epic Scouts
// --------------------------------------------------------------------

require_once "libs/all.php";
session_start();
log_page();
CheckLogin();
CheckEditor();
$timer = new timer();
$loc = 'members_bulkpics.php';
$ins_file = "docs/importpics.txt";
$error_msg = "";
$success_msg = "";
$instructions = "";

if(file_exists($ins_file)) { $instructions = file_get_contents($ins_file); }

if( $_SERVER["REQUEST_METHOD"] == "POST")
{
    if(!isset($_FILES["CsvFile"])) { DieWithMsg($loc, '$_FILES not set.'); }
    $fileinfo = $_FILES["CsvFile"];
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
        ProcessBulkPics($tempfile, $error_msg);
    }
}

include "forms/header.php";
include "forms/navform.php";
include "forms/members_menubar.php";
include "forms/members_bulkpics_form.php";
include "forms/footer.php";

// --------------------------------------------------------------------
function ProcessBulkPics($filename, &$error_msg)
{
    global $config;
    $loc = "members_bulkpics.php->ProcessBulkPics";
    
    $file = fopen($filename, "r");
    if($file === false)
    {
        $error_msg = "Unable to open file.";
        return;
    }
    
    $n_okay = 0;
    $n_fail = 0;
    $ln = 1;
    // The first line is the column headers. 
    $header = fgetcsv($file); $ln++;
    if($header === false) return $n;
    // Now, do some sanity checks to make sure we have
    // an appropriate file.
    if(!in_array("UserName",  $header) ||
       !in_array("Picture",   $header))
    {
        $error_msg = "Input file does not required columns.";
        return;
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
        
        // Organize the data into an associtive array
        $fields = JoinKeyValues($header, $data);
        
        // Skip lines that don't have required data
        if(!isset($fields["UserName"])  ||
           !isset($fields["Picture"]))
        {
           continue;
        }
        
        $username = $fields["UserName"];
        $picfile  = $fields["Picture"];
        if(empty($username) || empty($picfile)) continue;
        
        $result = AddPictureToUser($username, $picfile);
        if($result === true) $n_okay++;
        else $n_fail++;
        
        $telp = (microtime(true) - $tstart);
        if($telp > 240.0) {$btimeout = true; break; }
    }
    $error_msg = $n_okay . ' pictures imported. ' . $n_fail . ' failures. ' . $ln . ' lines processed.';
    if($btimeout) {$error_msg .= ' ** TimeOut Occured, Process aborted. **'; }
    log_msg($loc, $error_msg);
}

function AddPictureToUser($username, $source)
{
    $loc = "members_bulkpics.php->AddPIctureToUser";
    $userid   = GetUserIDFromName($username);
    $userinfo = GetUserInfo($userid);
    if($userinfo === false) DieWithMsg($loc, 'User with ID=' . $userid . ' not found, but should be there.');
    
    // Copy the file into our website.
    $target = GetTempDir() . "temppic.jpg";
    $result = @copy($source, $target);
    if($result == false)
    {
       log_msg($loc, array('Picture not added. Unable to copy file.',
              'External File=' . $source,
              'Internal Target=' . $target));
       return false;
    }
    $id = StoreUserPic($target, $userid);
    return true;
}

?>