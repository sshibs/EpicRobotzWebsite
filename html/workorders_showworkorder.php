<?php
// --------------------------------------------------------------------
// workorder_showworkorder.php -- Shows one work order.
//
// Created: 11/10/15 SS
// --------------------------------------------------------------------

require_once "libs/all.php";
session_start();
log_page();
CheckLogin();
CheckEditor();
$loc = 'workorder_showworkorder.php';
$timer = new Timer();
$action = "";
$error_msg = "";
$success_msg = "";

if( $_SERVER["REQUEST_METHOD"] == "GET")
{
    if(empty($_GET["WorkOrderID"]))
    {
        DieWithMsg($loc, "Bad Page Invoke. No WorkOrderID given.");
    }

    $WorkOrderID = $_GET["WorkOrderID"];
    $data = GetWorkOrderInfo($WorkOrderID);
    if($data === false) DieWithMsg($loc, 'Work Order with ID=' . $WorkOrderID . ' not found.');
    $WorkOrderID  = $data["WorkOrderID"];
    $WorkOrderName = $data["WorkOrderName"];
    $Description = $data["Description"];
    $DateRequested  = $data["DateRequested"];
    $DateNeeded  = $data["DateNeeded"];
    $DayEstimate = $data["DayEstimate"];
    $Revision = $data["Revision"];
    $Requestor  = $data["Requestor"];
    $RequestingIPTLeadApproval  = $data["RequestingIPTLeadApproval"];
    $AssignedIPTLeadApproval = $data["AssignedIPTLeadApproval"];
    $Project  = $data["Project"];
    $Priority  = $data["Priority"];
    $RequestingIPTGroup = $data["RequestingIPTGroup"];
    $ReceivingIPTGroup  = $data["ReceivingIPTGroup"];
    $ProjectOfficeApproval  = $data["ProjectOfficeApproval"];
    $ReviewedBy = $data["ReviewedBy"];
    $AssignedTo = $data["AssignedTo"];
    $Completed = $data["Completed"];
    $CompletedOn  = $data["CompletedOn"];
}

GenerateHtml:
include "forms/header.php";
include "forms/navform.php";
include "forms/workorders_menubar.php";
include "forms/workorders_showworkorder_form.php";
include "forms/footer.php";

?>
