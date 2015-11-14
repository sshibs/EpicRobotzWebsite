<?php
// --------------------------------------------------------------------
// members_uploadpic.php -- Uploading pictures for a user. 
//
// Created: 12/7/14 DLB
// Updated: 12/30/14 DLB -- Hacked from Epic Scouts.
// --------------------------------------------------------------------

require_once "libs/all.php";
session_start();
log_page();
CheckLogin();
CheckEditor();
$timer = new timer();
$loc = 'members_uploadpic.php';
$error_msg = "";
$success_msg = "";
$userid = 0;
$username = "";
$lastname = "";
$firstname = "";
$tempfile = "";

$param_list = array(
array("FieldName" => "UserID",    "FieldType" => "Hidden"),
array("FieldName" => "UserName",  "FieldType" => "Hidden"));

if( $_SERVER["REQUEST_METHOD"] == "GET") 
{
    if(empty($_GET["UserID"]))
    {
        DieWithMsg($loc, "Bad Page Invoke. No UserID given.");
    }
    $userid = $_GET["UserID"];
    $data = GetUserInfo($userid);
    if($data === false) DieWithMsg($loc, 'User with ID=' . $userid . ' not found.');
    
    PopulateParamList($param_list, $data);
    $username = $data['UserName'];
    $lastname = $data['LastName'];
    $firstname = $data['FirstName'];
    goto GenerateHtml;
}

if( $_SERVER["REQUEST_METHOD"] == "POST")
{
    if(!isset($_POST["UserID"])) DieWithMsg($loc,"Bad Post, no UserID.");
    $userid = $_POST["UserID"];
    $data = GetUserInfo($userid);
    if($data === false) DieWithMsg($loc, 'User with ID=' . $userid . ' not found.');
    $username = $data['UserName']; 
    $lastname = $data['LastName'];
    $firstname = $data['FirstName'];

    PopulateParamList($param_list, $data);
    
    $result = CheckFileInput($error_msg, $tempfile);
    if($result === true)
    {
        $id = StoreUserPic($tempfile, $userid);
        if($id === false)
        {
            $error_msg = "Unable to save picture on server.";
        }
        else
        {
            $success_msg ="Picture successfully saved on server. Id=" . $id;
        }
    }
}

GenerateHtml:
include "forms/header.php";
include "forms/navform.php";
include "forms/members_menubar.php";
include "forms/members_uploadpic_form.php";
include "forms/footer.php";

// --------------------------------------------------------------------
// Helper function to check the uploaded file and to get the name
// of the file for processing.
function CheckFileInput(&$error_msg, &$tempfile)
{
    $loc = 'members_uploadpic.php->CheckFileInput';
    $filesize = 0;
    $width = 0;
    $height = 0;
    $filetempname = "";
    if(!array_key_exists("PicFile", $_FILES))
    {
        $error_msg = "No file specified. (PicFile not found.)";
        return false;
    }
    $fileinfo = $_FILES["PicFile"];
    $filesize = $fileinfo["size"];
    $filetype = $fileinfo["type"];
    $fileerr  = $fileinfo["error"];
    $tempfile = $fileinfo["tmp_name"];
    if($fileerr != 0)
    {
        if($fileerr == 4) 
        {
            $error_msg = "No file chosen.";
            return false;
        }
        $en = $fileinfo["error"];
        $error_msg = 'Transfer error (' . $en . ').';
        log_error($loc, array($error_msg, '$_FILES["PicFile"]=' . print_r($fileinfo, true)));
        return false;
    }
    if($filesize < 100)
    {
        $error_msg = 'File size too small. Filesize=' . $filesize;
        log_error($loc, array($error_msg, '$_FILES["PicFile"]=' . print_r($fileinfo, true)));
        return false;
    }
    if($filesize > 10000000)
    {
        $error_msg = 'File too big. Max 10MB.  Filesize=' . $filesize;
        log_error($loc, array($error_msg, '$_FILES["PicFile"]=' . print_r($fileinfo, true)));
        return false;
    }
    if(empty($tempfile))
    {
        $error_msg = 'No temp file name found in $_FILES.';
        log_error($loc, array($error_msg, '$_FILES["PicFile"]=' . print_r($fileinfo, true)));
        return false;
    }
    if(!file_exists($tempfile))
    {
        $error_msg = "Temp file doesn't exist on server.";
        log_error($loc, array($error_msg, '$_FILES["PicFile"]=' . print_r($fileinfo, true)));
        return false;
    }
    if(trim(strtolower($filetype)) != 'image/jpeg')
    {
        $error_msg = 'Pic is not marked as a jpeg.';
        log_error($loc, array($error_msg, '$_FILES["PicFile"]=' . print_r($fileinfo, true)));
        return false;
    }
    
    $imginfo = @getimagesize($tempfile);
    if($imginfo === false)
    {
        $error_msg = 'Pic file appears unreadable.  Getimagesize() failed reading ' . $tempfile;
        log_error($loc, array($error_msg, '$_FILES["PicFile"]=' . print_r($fileinfo, true)));
        return false;
    }
    return true;
}
?>