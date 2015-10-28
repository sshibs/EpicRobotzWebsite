<?php
// --------------------------------------------------------------------
// admin_edituser.php -- page to allow editing of user info
//
// Created: 11/27/14 DLB
// Updated: 12/29/14 DLB -- Adapted for Epic Admin.
// --------------------------------------------------------------------

// Note: unlike the familar GET/POST pattern for other pages -- where
// there are no arguments passed in the GET, this page
// is expected to be first invoked with a GET and a UserID=x argument.

require_once "libs/all.php";
session_start();
log_page();
CheckLogin();
CheckAdmin();
$timer = new timer();
$loc = 'admin_edituser.php';
$error_msg = "";
$success_msg = "";
$userid = 0;
$username = "";

$param_list = array(
array("FieldName" => "UserID",    "FieldType" => "Hidden"),
array("FieldName" => "UserName",  "FieldType" => "Hidden"),
array("FieldName" => "Password",  "FieldType" => "Password"),
array("FieldName" => "Password2", "FieldType" => "Password", "Caption" => "Password Again"),
array("FieldName" => "LastName",  "FieldType" => "Text", "Caption" => "Last Name"),
array("FieldName" => "FirstName", "FieldType" => "Text", "Caption" => "First Name"),
array("FieldName" => "NickName",  "FieldType" => "Text", "Caption" => "Nick Name"),
array("FieldName" => "Title",     "FieldType" => "Text"),
array("FieldName" => "BadgeID",   "FieldType" => "Text", "Caption" => "Badge ID"),
array("FieldName" => "Email",     "FieldType" => "Text"),
array("FieldName" => "Tags",      "FieldType" => "Text"),
array("FieldName" => "Active",    "FieldType" => "Boolean"));


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
    goto GenerateHtml;
}

if( $_SERVER["REQUEST_METHOD"] == "POST")
{
    // Find the user we are dealing with...
    if(!isset($_POST["UserID"])) DieWithMsg($loc,"Bad Post, no UserID.");
    $userid = $_POST["UserID"];
    $data = GetUserInfo($userid);
    if($data === false) DieWithMsg($loc, 'User with ID=' . $userid . ' not found.');
    $username = $data['UserName']; 

    PopulateParamList($param_list, $_POST);

    if(GetValueFromParamList($param_list, "UserName") != $username)
    {
        DieWithMsg($loc, "Logic error... UserName mismatch.");
    }
    if(GetValueFromParamList($param_list, "UserID") != $userid)
    {
        DieWithMsg($loc, "Logic error... UserID mismatch.");
    }
    
    // Check for illegal input...
    if(!IsSqlTextOkay($_POST))
    {
        $error_msg = "Illegal characters in input... Do not use quotes and control chars.";
        goto GenerateHtml;
    }

    $update = false;
    if(!empty($_POST["Password"]) || !empty($_POST["Password2"])) 
    {
        if($_POST["Password"] != $_POST["Password2"])
        {
            $error_msg = "Error: new passwords do not match.";
            goto GenerateHtml;
        }
        $update = true;
    }
    
    // Check for changes.
    foreach($data as $key => $value)
    {
        if(!IsFieldInParamList($key, $param_list)) continue;
        if($value != GetValueFromParamList($param_list, $key))
        {
            $update = true;
            break;
        }
    }
    
    if($update === false) 
    {
        $success_msg = "No changes given.";
        goto GenerateHtml;
    }
    
    // Looks like we are okay to update database!
    $okay = UpdateUser($param_list, $userid);
    if($okay === true)
    {
        $success_msg = "User Data Updated!";
        $data = GetUserInfo($userid);
        PopulateParamList($param_list, $data);
    }
    else 
    {
        $error_msg = $okay;
    }
}

// Render the page based on state variables that were set above...
// These are: $error_msg, $success_msg, $username, $userid, $param_list.

GenerateHtml:
$userinfo = $username . ', ' . $userid;
include "forms/header.php";
include "forms/navform.php";
include "forms/admin_menubar.php";
include "forms/admin_edituser_form.php";
include "forms/footer.php";
?>