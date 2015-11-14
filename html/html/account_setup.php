<?php
// --------------------------------------------------------------------
// account_setup.php -- The main account setup page. 
//
// Created: 12/30/14 DLB
// Updated:  1/16/15 DLB -- Reorganized...
// --------------------------------------------------------------------

require_once "libs/all.php";
session_start();
log_page();
CheckLogin();

$timer = new timer();
$loc = 'account_setup.php';
$error_msg = "";
$success_msg = "";
$userid = GetUserID();
$username = GetUserName();
$picid = 0;
$havebadge = false;

$param_list = array(
array("FieldName" => "Password",  "FieldType" => "Password"),
array("FieldName" => "Password2", "FieldType" => "Password", "Caption" => "Password Again"),
array("FieldName" => "NickName",  "FieldType" => "Text", "Caption" => "Your Nick Name"),
array("FieldName" => "Email",     "FieldType" => "Text", "Caption" => "Your Email"));

if( $_SERVER["REQUEST_METHOD"] == "GET")
{
    $data = GetUserInfo($userid);
    if($data === false) DieWithMsg($loc, 'User with ID=' . $userid . ' not found.');
    
    PopulateParamList($param_list, $data);
    goto GenerateHtml;
}

if( $_SERVER["REQUEST_METHOD"] == "POST")
{
    $data = GetUserInfo($userid);
    if($data === false) DieWithMsg($loc, 'User with ID=' . $userid . ' not found.');

    PopulateParamList($param_list, $_POST);
   
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
        $success_msg = "Data Updated!";
        $data = GetUserInfo($userid);
        PopulateParamList($param_list, $data);
    }
    else 
    {
        $error_msg = $okay;
    }
}

GenerateHtml:
$picid = GetPicIDForUserID($userid);
if($picid > 0) 
{
    $picurl = PicUrl($picid, "thumb");
}
$badgeid = $data["BadgeID"];
$havebadge = BadgeExists($badgeid);
if($havebadge)
{
    $badge_front_url = GetBadgeUrl($badgeid, 'front');
    $badge_back_url  = GetBadgeUrl($badgeid, 'back');
}

include "forms/header.php";
include "forms/navform.php";
include "forms/account_menubar.php";
include "forms/account_setup_form.php";
include "forms/footer.php";

?>