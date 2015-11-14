<?php
//------------------------------------
//workorder_addnew.php -- page to upload work orders
//
// Created: 11/3/2015 NG
//------------------------------------

require_once "libs/all.php";
session_start();
log_page();
CheckLogin();
$timer = new timer();
$loc = 'workorder_addworkorder.php';
$error_msg = "";
$success_msg = "";

if( $_SERVER["REQUEST_METHOD"] == "POST")
{
        // Check for illegal input...
        if(!IsSqlTextOkay($_POST))
        {
            $error_msg = "Illegal characters in input... Do not use quotes and control chars.";
            goto GenerateHtml;
        }

        //Check for required inputs:
        $sEmpty = array();
        if(empty($_POST["WorkOrderName"]))  $sEmpty[] = "Work Order Name";
        if(empty($_POST["RequestingIPTGroup"]))  $sEmpty[] = "Requesting IPT Group";
        if(empty($_POST["ReceivingIPTGroup"]))  $sEmpty[] = "Recieving IPT Group";
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

		//$data = ExtractValuesFromParamList($param_list);
		global $config;
		$WorkOrderName = "";
		$DateNeeded = "";
		$Priority = "";
		$DayEstimate = "";
		$Revision = "";
		$Requestor = "";
		$Project = "";
		$RequestingIPTGroup = "";
		$ReceivingIPTGroup = "";
		$Quantity = "";
		$Description = "";
		$UnitPrice = "";
		$FilePath = "";
		$Prereq = "";

		if(!empty($_POST["WorkOrderName"]) ) { $WorkOrderName = SQLClean($_POST["WorkOrderName"]);}
		if(!empty($_POST["DueYear"]) && !empty($_POST["DueMonth"]) && !empty($_POST["DueDay"])) { $DateNeeded = SQLClean($_POST["DueYear"]) . "-" . SQLClean($_POST["DueMonth"]) . "-" . SQLClean($_POST["DueDay"]);}
		if(!empty($_POST["Priority"]) ) { $Priority = SQLClean($_POST["Priority"]);}
		if(!empty($_POST["DayEstimate"]) ) { $DayEstimate = SQLClean($_POST["DayEstimate"]);}
		if(!empty($_POST["Revision"]) ) { $Revision = SQLClean($_POST["Revision"]);}
		if(!empty($_POST["Requestor"]) ) { $Requestor = SQLClean($_POST["Requestor"]);}
		if(!empty($_POST["Project"]) ) { $Project = SQLClean($_POST["Project"]);}
		if(!empty($_POST["RequestingIPTGroup"]) ) { $RequestingIPTGroup = SQLClean($_POST["RequestingIPTGroup"]);}
		if(!empty($_POST["ReceivingIPTGroup"]) ) { $ReceivingIPTGroup = SQLClean($_POST["ReceivingIPTGroup"]);}
		if(!empty($_POST["Quantity"]) ) { $Quantity = SQLClean($_POST["Quantity"]);}
		if(!empty($_POST["Description"]) ) { $Description = SQLClean($_POST["Description"]);}
		if(!empty($_POST["Prereq"]) ) { $Prereq = SQLClean($_POST["Prereq"]);}
		if(!empty($_POST["UnitPrice"]) ) { $UnitPrice = SQLClean($_POST["UnitPrice"]);}
		if(!empty($_POST["FilePath"]) ) { $FilePath = SQLClean($_POST["FilePath"]);}
		$Requestor = GetUserName();
		// Check for duplicate name
		$sql =  'SELECT WorkOrderName FROM WorkOrders WHERE WorkOrderName ="' . $WorkOrderName . '"';
		$result = SqlQuery($loc, $sql);
		if($result->num_rows > 0)
		{
			$error_msg = 'Unable to add new Work Order. Duplicate Work Order Name. (' . $WorkOrderName . ')';
			log_msg($loc, $msg);
			goto GenerateHtml;
		}

		// Build the sql to add workorder
		$sql = 'INSERT INTO WorkOrders (WorkOrderName, DateNeeded, Priority, DayEstimate, Revision, Requestor, ' 
			.  'Project, RequestingIPTGroup, ReceivingIPTGroup,RequestingIPTLeadApproval, AssignedIPTLeadApproval, ProjectOfficeApproval, DateRequested) ';
			$sql .= ' VALUES(';
		$sql .= '  "' . $WorkOrderName  . '"';
		$sql .= ', "' . $DateNeeded    . '"';
		$sql .= ', "' . $Priority  . '"';
		$sql .= ', "' . $DayEstimate . '"';
		$sql .= ', "' . $Revision  . '"';
		$sql .= ', "' . $Requestor     . '"';
		$sql .= ', "' . $Project   . '"';
		$sql .= ', "' . $RequestingIPTGroup     . '"';
		$sql .= ', "' . $ReceivingIPTGroup      . '"';
		$sql .= ', true';
		$sql .= ', false';
		$sql .= ', false';
		$sql .= ', CURDATE()';
		$sql .= ')';

		$result = SqlQuery($loc, $sql);
		$sql = 'SELECT WorkOrderID, WorkOrderName FROM WorkOrders WHERE WorkOrderName = "' . $WorkOrderName . '";';
		$result =SqlQuery($loc, $sql);
		$rowCount = $result->num_rows;
		if ($rowCount > 0) {
    			$row = $result->fetch_assoc();
			$WorkOrderID = $row["WorkOrderID"];
			$sql = 'INSERT INTO WorkOrderTasks  (WorkOrderID, Quantity, Description, UnitPrice )';
			$sql .= ' VALUES(';
			$sql .= ' "' . $WorkOrderID  . '"';
			$sql .= ', "' . $Quantity  . '"';
			$sql .= ', "' . $Description     . '"';
			$sql .= ', "' . $UnitPrice   . '"';
			$sql .= ')';
			$result = SqlQuery($loc, $sql);
			$sql = 'SELECT WorkOrderID from WorkOrders WHERE WorkOrderName  = "' . $Prereq . '";';
			$result =SqlQuery($loc, $sql);
			$rowCount = $result->num_rows;
			if ($rowCount > 0) {
    				$row = $result->fetch_assoc();
				$PrevWorkOrderID = $row["WorkOrderID"];
				$sql = 'INSERT INTO Prerequisites  (WorkOrderID, PrevWorkOrderID )';
				$sql .= ' VALUES(';
				$sql .= ' "' . $WorkOrderID  . '"';
				$sql .= ', "' . $PrevWorkOrderID  . '"';
				$sql .= ')';
				$result =SqlQuery($loc, $sql);
			}
		}
			//$sql .= ', "' . $FilePath      . '"';
		log_msg($loc,
       array("New Work Order added!  Work Order Name =" . $WorkOrderName  . " id = " . $WorkOrderID));

        $success_msg = 'Work Order "' . $_POST["WorkOrderName"] . '"' . $Priority . 'ID ' . $WorkOrderID . ' successfully added.';
        //foreach($param_list as &$param_spec) { unset($param_spec["Value"]); }

       // $error_msg = true;

	   // Render the page based on state variables that were set above...
// These are: $error_msg, $success_msg, $param_list.
}
GenerateHtml:
include "forms/header.php";
include "forms/navform.php";
include "forms/workorders_menubar.php";
include "forms/workorder_create_form.php";
include "forms/footer.php";
?>
