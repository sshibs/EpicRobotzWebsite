<?php
// --------------------------------------------------------------------
// admin_adduser.php -- page to allow adding of a new user
//
// Created: 12/29/14 DLB
// --------------------------------------------------------------------

require_once "libs/all.php";
session_start();
log_page();
CheckLogin();
CheckAdmin();
$timer = new timer();
$loc = 'admin_adduser.php';
$error_msg = "";
$success_msg = "";

$param_list = array(
array("FieldName" => "UserName",  "FieldType" => "Text"),
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

if( $_SERVER["REQUEST_METHOD"] == "POST")
{
    PopulateParamList($param_list, $_POST);

    // Check for illegal input...
    if(!IsSqlTextOkay($_POST))
    {
        $error_msg = "Illegal characters in input... Do not use quotes and control chars.";
        goto GenerateHtml;
    }

    // Check for required inputs:
    $sEmpty = array();
    if(empty($_POST["UserName"]))  $sEmpty[] = "User Name";
    if(empty($_POST["Password"]))  $sEmpty[] = "Password";
    if(empty($_POST["Password2"])) $sEmpty[] = "Password Again";
    if(empty($_POST["LastName"]))  $sEmpty[] = "Last Name";
    if(empty($_POST["FirstName"])) $sEmpty[] = "First Name";
    if(count($sEmpty) > 0)
    {
        $error_msg = "Required information missing: ";
        $c = 0;
        foreach($sEmpty as $s) 
        {
            if($c > 0) $error_msg .= ', ';
            $error_msg .= $s;
            $c++;
        }
        $error_msg .= '.';
        goto GenerateHtml;
    }
    
    // Check for password errors...
    if(!empty($_POST["Password"]) || !empty($_POST["Password2"])) 
    {
        if($_POST["Password"] != $_POST["Password2"])
        {
            $error_msg = "Error: new passwords do not match.";
            goto GenerateHtml;
        }
    }
    if(empty($_POST["Password"]) || empty($_POST["Password2"])) 
    {
        $error_msg = "Error: Password cannot be blank.";
        goto GenerateHtml;
    }

    $data = ExtractValuesFromParamList($param_list);
    $okay = CreateNewUser($data);
    if($okay === true)
    {
        $success_msg = 'User "' . $_POST["UserName"] . '" successfully added.';
        foreach($param_list as &$param_spec) { unset($param_spec["Value"]); }
    }
    else
    {
        $error_msg = $okay;
    }
}

// Render the page based on state variables that were set above...
// These are: $error_msg, $success_msg, $param_list.

GenerateHtml:
include "forms/header.php";
include "forms/navform.php";
include "forms/admin_menubar.php";
include "forms/admin_adduser_form.php";
include "forms/footer.php";
?>