<?php
// --------------------------------------------------------------------
// Workorder Test page - Outline borrowed  from: admin_adduser.php
//
// Created: 10/24/15
// --------------------------------------------------------------------

require_once "libs/all.php";
session_start();
log_page();
CheckLogin();
CheckAdmin();
$timer = new timer();
$loc = 'Workorder_Test.php';
$error_msg = "";
$success_msg = "";

$param_list = array(
array("FieldName" => "DateRequested",  "FieldType" => "Text"),
array("FieldName" => "DueDate",  "FieldType" => "Text"),
array("FieldName" => "Priority", "FieldType" => "Text"),
array("FieldName" => "Job",  "FieldType" => "Text"),
array("FieldName" => "Revision", "FieldType" => "Text"),
array("FieldName" => "Predecessor",    "FieldType" => "Text"),
array("FieldName" => "From",  "FieldType" => "Text"),
array("FieldName" => "RequestingGroup",     "FieldType" => "Text"),
array("FieldName" => "Requestor",   "FieldType" => "Text",
array("FieldName" => "RequestingIPTLead pproval",     "FieldType" => "Boolean"),
array("FieldName" => "Project",      "FieldType" => "Text"),
array("FieldName" => "To",    "FieldType" => "Text"),
array("FieldName" => "Quantity",    "FieldType" => "Text"),
array("FieldName" => "Description",    "FieldType" => "Text"),
array("FieldName" => "UnitPrice",    "FieldType" => "Text"),
array("FieldName" => "LineTotal",    "FieldType" => "Text"),
array("FieldName" => "SubTotal",    "FieldType" => "Text"),
array("FieldName" => "SalesTax",    "FieldType" => "Text"),
array("FieldName" => "Total",    "FieldType" => "Text"),
array("FieldName" => "ProjectOfficeApproval",    "FieldType" => "Boolean"),
array("FieldName" => "ReviewedBy",    "FieldType" => "Text"),
array("FieldName" => "WorkOrderNumber",    "FieldType" => "Text"),
array("FieldName" => "AssignedIPTLeadApproval",    "FieldType" => "Boolean"),
array("FieldName" => "AssignedTo",    "FieldType" => "Text"),
array("FieldName" => "CompletedOn",    "FieldType" => "Text")));


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
/*    $sEmpty = array();
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
*/
    $data = ExtractValuesFromParamList($param_list);
  /*  $okay = CreateNewUser($data);
    if($okay === true)
    {
        $success_msg = 'User "' . $_POST["UserName"] . '" successfully added.';
        foreach($param_list as &$param_spec) { unset($param_spec["Value"]); }
    }
    else
    {
        $error_msg = $okay;
    }
*/
}

// Render the page based on state variables that were set above...
// These are: $error_msg, $success_msg, $param_list.

GenerateHtml:
include "forms/header.php";
include "forms/navform.php";
include "forms/admin_menubar.php";
include "forms/Workorder_Test_Form.php";
include "forms/footer.php";
?>
