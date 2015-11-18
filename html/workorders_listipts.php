<?php
//--------------------------
// workorders_listipts.php -- php file to list workorders by IPT
//
// Created: 11/14/15 NG
//--------------------------

require_once "libs/all.php";
session_start();
log_page();
CheckLogin();
CheckEditor();
$loc = 'workorders_showworkorder.php';
$timer = new Timer();
$action = "";
$error_msg = "";
$success_msg = "";

if( $_SERVER["REQUEST_METHOD"] == "GET")
{
    if(empty($_GET["IPTGroup"]))
    {
        DieWithMsg($loc, "Bad Page Invoke. No IPTGroup given.");
    }

    $ReceivingIPTGroup = $_GET["IPTGroup"];
}
include "forms/header.php";
include "forms/navform.php";
include "forms/workorders_menubar.php";
echo '<div class="content_area">';
echo '<h2>List of Known Work Order IDs</h2>';

echo '<br>';
echo '<h2>Completed Work Orders</h2>';
$sql = 'SELECT * FROM WorkOrders WHERE ReceivingIPTGroup= "' . $ReceivingIPTGroup . '" AND Completed = 1 ORDER BY DateNeeded';
//$sql = 'SELECT * FROM WorkOrders';
$result = SqlQuery($loc, $sql);

if ($result->num_rows > 0) {
    // output data of each row
    echo "<br>\n";
    echo '<table class="members_userlist">' . "\n<tr>\n";
    echo "<th align=left width=80><u>WorkOrder ID</u></th>";
    echo "<th align=left width=200><u>Name</u></th>";
    echo "<th align=left width=200><u>Due Date</u></th>";
    echo "<th align=left width=200><u>Requesting Approval</u></th>";
    echo "<th align=left width=200><u>Receiving  Approval</u></th>";
    echo "<th align=left width=200><u>Office Approval</u></th>";
//    echo "<th align=left width=200><u>Completed?</u></th>";


    while($row = $result->fetch_assoc()) {

        $WorkOrderID = $row["WorkOrderID"];
        $WorkOrderName = $row["WorkOrderName"];
        echo "\n<tr>";
        echo '<th align=left> <a href="workorders_showworkorder.php?WorkOrderID=' . $row["WorkOrderID"] . '">' . $row["WorkOrderID"] . '</a></th>';
        echo '<th align=left>'  . $row["WorkOrderName"] . '</th>';
        echo '<th align=left>'  . $row["DateNeeded"] . '</th>';
        echo '<th align=left>'  . $row["RequestingIPTLeadApproval"] . '</th>';
        echo '<th align=left>'  . $row["AssignedIPTLeadApproval"] . '</th>';
        echo '<th align=left>'  . $row["ProjectOfficeApproval"] . '</th>';
        //echo '<th align=left>'  . $row["Completed"] . '</th>';
        }

    while($row = $result->fetch_assoc()) {

        $WorkOrderID = $row["WorkOrderID"];
        $WorkOrderName = $row["WorkOrderName"];
        echo "\n<tr>";
        echo '<th align=left> <a href="workorders_showworkorder.php?WorkOrderID=' . $row["WorkOrderID"] . '">' . $row["WorkOrderID"] . '</a></th>';
        echo '<th align=left>'  . $row["WorkOrderName"] . '</th>';
        echo '<th align=left>'  . $row["DateNeeded"] . '</th>';
        echo '<th align=left>'  . $row["RequestingIPTLeadApproval"] . '</th>';
        echo '<th align=left>'  . $row["AssignedIPTLeadApproval"] . '</th>';
        echo '<th align=left>'  . $row["ProjectOfficeApproval"] . '</th>';
        //echo '<th align=left>'  . $row["Completed"] . '</th>';
        }
    echo "</table>\n";
    echo '<h2>Uncompleted Work Orders</h2>';
}
$sql = 'SELECT * FROM WorkOrders WHERE ReceivingIPTGroup= "' . $ReceivingIPTGroup . '" AND Completed = 0 ORDER BY DateNeeded';

$result = SqlQuery($loc, $sql);

if ($result->num_rows > 0) {
    // output data of each row
    echo "<br>\n";
    echo '<table class="members_userlist">' . "\n<tr>\n";
    echo "<th align=left width=80><u>WorkOrder ID</u></th>";
    echo "<th align=left width=200><u>Name</u></th>";
    echo "<th align=left width=200><u>Due Date</u></th>";
    echo "<th align=left width=200><u>Requesting Approval</u></th>";
    echo "<th align=left width=200><u>Receiving  Approval</u></th>";
    echo "<th align=left width=200><u>Office Approval</u></th>";
//    echo "<th align=left width=200><u>Completed?</u></th>";


    while($row = $result->fetch_assoc()) {

        $WorkOrderID = $row["WorkOrderID"];
        $WorkOrderName = $row["WorkOrderName"];
        echo "\n<tr>";
        echo '<th align=left> <a href="workorders_showworkorder.php?WorkOrderID=' . $row["WorkOrderID"] . '">' . $row["WorkOrderID"] . '</a></th>';
        echo '<th align=left>'  . $row["WorkOrderName"] . '</th>';
        echo '<th align=left>'  . $row["DateNeeded"] . '</th>';
        echo '<th align=left>'  . $row["RequestingIPTLeadApproval"] . '</th>';
       echo '<th align=left>'  . $row["AssignedIPTLeadApproval"] . '</th>';
        echo '<th align=left>'  . $row["ProjectOfficeApproval"] . '</th>';
        //echo '<th align=left>'  . $row["Completed"] . '</th>';
        }
    echo "</table>\n";
}
else {
    echo "No WorkOrders Exist!!  (How can that be?)";
}

echo '</div>';
include "forms/footer.php";
 ?>
