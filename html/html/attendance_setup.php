<?php
// --------------------------------------------------------------------
// attendance_setup.php -- The attendance setup page. 
//
// Created:  1/20/15 DLB
// Updated:  1/16/15 DLB -- Reorganized...
// --------------------------------------------------------------------

require_once "libs/all.php";
session_start();
log_page();
CheckLogin();
CheckEditor();

$timer = new timer();
$loc = 'attendance_setup.php';
$error_msg = "";
$success_msg = "";

$param_list = array(
array("FieldName" => "LastDay",   "FieldType" => "Text", "Caption" => "Last Date On Record"));

if( $_SERVER["REQUEST_METHOD"] == "GET")
{
    $data = GetPrefsForUser(0);
    PopulateParamList($param_list, $data);
    goto GenerateHtml;
}

if( $_SERVER["REQUEST_METHOD"] == "POST")
{
    $data = GetPrefsForUser(0);

    PopulateParamList($param_list, $_POST);
   
    // Check for illegal input...
    if(!IsSqlTextOkay($_POST))
    {
        $error_msg = "Illegal characters in input... Do not use quotes and control chars.";
        goto GenerateHtml;
    }

    $update = false;
    // Check for changes.
    foreach($param_list as $param_spec)
    {
        $k = $param_spec["FieldName"];
        if(isset($param_spec["Value"])) 
        {
            $curval = null;
            if(isset($data[$k])) $curval = $data[$k];
            if($curval != $param_spec["Value"]) { $update = true; break; }
        }
    }

    if($update === false) 
    {
        $success_msg = "No changes given.";
        goto GenerateHtml;
    }
    
    // Looks like we are okay to update database!
    $newdata = array();
    foreach($param_list as $param_spec)
    {
        $n = $param_spec["FieldName"];
        $v = $param_spec["Value"];
        if($n == "LastDay")
        {
            $r = strtotime($v);
            if($r === false) 
            {
                $error_msg = "Undecodeable time value, try again."; 
                goto GenerateHtml; 
            }
            $v = date("Y-m-d", $r);
            
        }
        $newdata[$n] = $v;
    }
    SavePrefsForUser(0, $newdata);
    $data = GetPrefsForUser(0);
    PopulateParamList($param_list, $data);
    $success_msg = "Data Updated!";
    goto GenerateHtml;
}

GenerateHtml:
include "forms/header.php";
include "forms/navform.php";
include "forms/attendance_menubar.php";
include "forms/attendance_setup_form.php";
include "forms/footer.php";

?>