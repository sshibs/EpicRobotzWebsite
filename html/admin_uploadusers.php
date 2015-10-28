<?php
// --------------------------------------------------------------------
// admin_uploadusers.php -- Adds users by loading a csv file.
//
// Created: 12/17/14 DLB
// Updated: 12/30/14 DLB -- Hacked from Epic Scouts
// --------------------------------------------------------------------

require_once "libs/all.php";
session_start();
log_page();
CheckLogin();
CheckAdmin();
$timer = new timer();
$loc = 'admin_uploadusers.php';
$ins_file = "docs/uploadusers.txt";
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
        ProcessBulkUsers($tempfile, $error_msg);
    }
}

include "forms/header.php";
include "forms/navform.php";
include "forms/admin_menubar.php";
include "forms/admin_uploadusersform.php";
include "forms/footer.php";

// --------------------------------------------------------------------
function ProcessBulkUsers($filename, &$error_msg)
{
    global $config;
    $loc = "adduserbulk.php->ProcessBulkUsers";
    
    $file = fopen($filename, "r");
    if($file === false)
    {
        $error_msg = "Unable to open file.";
        return 0;
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
       !in_array("LastName",  $header) ||
       !in_array("FirstName", $header) ||
       !in_array("Password",  $header))
    {
        $error_msg = "Input file does not required columns.";
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
        
        // Make sure we have required data
        if(!isset($fields["UserName"])  ||
           !isset($fields["LastName"])  ||
           !isset($fields["FirstName"]) ||
           !isset($fields["Password"]))
        {
            log_msg($loc, 'User not added. Fields missing, line ' . $ln);
            $n_fail++;
            continue;
        }
        
        // Make sure none of the required fields are empty.
        if(empty($fields["UserName"]) ||
           empty($fields["LastName"]) ||
           empty($fields["FirstName"]) ||
           empty($fields["Password"]))
        {
            log_msg($loc, 'User not added. Some requried fields are empty. Line ' . $ln);
            $n_fail++;
            continue;
        }           
        
        if(!isset($fields["NickName"])) $fields["NickName"] = "";
        if(!isset($fields["Title"]))    $fields["Title"]    = "";
        if(!isset($fields["BadgeID"]))  $fields["BadgeID"]  = "";
        if(!isset($fields["Email"]))    $fields["Email"]    = "";
        if(!isset($fields["Active"]))   $fields["Active"]   = 0;
        if(!isset($fields["Tags"]))     $fields["Tags"]     = "";
        if(!isset($fields["Picture"]))  $fields["Picture"]  = "";
        
        $error_msg = CreateNewUser($fields);
        if($error_msg === true)
        {
            $n_okay++;
            // Now, try to upload thier pic... if any.
            if(!empty($fields["Picture"])) AddPictureToUser($fields);
            
        }
        else
        {
            log_msg($loc, array('User not added. Line ' . $ln, $error_msg));
            $n_fail++;
        }
        
        $telp = (microtime(true) - $tstart);
        if($telp > 240.0) {$btimeout = true; break; }
        
    }
    $error_msg = $n_okay . ' users added. ' . $n_fail . ' failures. ' . $ln . ' lines processed.';
    if($btimeout) {$error_msg .= ' ** TimeOut Occured, Process aborted. **'; }
    log_msg($loc, $error_msg);
}


function AddPictureToUser($fields)
{
    $loc = "admin_uploadusers.php->AddPIctureToUser";
    $userid   = GetUserIDFromName($fields["UserName"]);
    $userinfo = GetUserInfo($userid);
    if($userinfo === false) DieWithMsg($loc, 'User with ID=' . $userid . ' not found, but should be there.');
    
    $source = $fields["Picture"];
    
    // Copy the file into our website.
    $target = GetTempDir() . "temppic.jpg";
    $result = @copy($source, $target);
    if($result == false)
    {
       log_msg($loc, array('Picture not added. Unable to copy file.',
              'External File=' . $source,
              'Internal Target=' . $target));
       return;
    }
    $id = StoreUserPic($target, $userid);
}

?>
