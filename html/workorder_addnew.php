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
        if(empty($_POST["RecievingIPTGroup"]))  $sEmpty[] = "Recieving IPT Group";
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
		
		$data = ExtractValuesFromParamList($param_list);
		global $config;
		$WorkOrderName = "";
		$NeedByDate = "";
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
		$JobName = "";
		$FilePath = "";
		
		if(!empty($_POST["WorkOrderName"]) ) { $WorkOrderName = SQLClean($params["WorkOrderName"]);}
		if(!empty($_POST["DueYear"]) && empty($_POST["DueMonth"]) && empty($_POST["DueDay"])) { $NeedByDate = SQLClean($params["DueYear"] . "-" . SQLClean($params["DueMonth"]) . "-" . SQLClean($params["DueDay"];}
		if(!empty($_POST["Priority"]) ) { $Priority = SQLClean($params["Priority"]);}
		if(!empty($_POST["DayEstimate"]) ) { $DayEstimate = SQLClean($params["DayEstimate"]);}
		if(!empty($_POST["Revision"]) ) { $Revision = SQLClean($params["Revision"]);}
		if(!empty($_POST["Requestor"]) ) { $Requestor = SQLClean($params["Requestor"]);}
		if(!empty($_POST["Project"]) ) { $Project = SQLClean($params["Project"]);}
		if(!empty($_POST["RequestingIPTGroup"]) ) { $RequestingIPTGroup = SQLClean($params["RequestingIPTGroup"]);}
		if(!empty($_POST["ReceivingIPTGroup"]) ) { $ReceivingIPTGroup = SQLClean($params["ReceivingIPTGroup"]);}
		if(!empty($_POST["Quantity"]) ) { $Quantity = SQLClean($params["Quantity"]);}
		if(!empty($_POST["Description"]) ) { $Description = SQLClean($params["Description"]);}
		if(!empty($_POST["UnitPrice"]) ) { $UnitPrice = SQLClean($params["UnitPrice"]);}
		if(!empty($_POST["JobName"]) ) { $JobName = SQLClean($params["JobName"]);}
		if(!empty($_POST["FilePath"]) ) { $FilePath = SQLClean($params["FilePath"]);}
		
		// Check for duplicate name
		sql =  'SELECT WorkOrderName FROM WorkOrders WHERE WorkOrderName ="' . $WorkOrderName . '"';
		$result - SqlQuery($loc, $sql);
		if($result->num_rows > 0)
		{
			$error_msg = 'Unable to add new Work Order. Duplicate Work Order Name. (' . $WorkOrderName . ')';
			log_msg($loc, $msg);
			goto GenerateHtml;
		}
		
		// Build the sql to add workorder
		$sql = 'INSERT INTO WorkOrders (WorkOrderName, NeedByDate, Priority, DayEstimate, Revision, Requestor, ' 
			.  'Project, RequestingIPTGroup, RecievingIPTGroup, Quantity, Description, UnitPrice, JobName, FilePath) ';
			$sql .= ' VALUES(';
		$sql .= '  "' . $WorkOrderName  . '"';
		$sql .= ', "' . $NeedByDate    . '"';
		$sql .= ', "' . $Priority  . '"';
		$sql .= ', "' . $DayEstimate . '"';
		$sql .= ', "' . $Revision  . '"';
		$sql .= ', "' . $Requestor     . '"';
		$sql .= ', "' . $Project   . '"';
		$sql .= ', "' . $RequestingIPTGroup     . '"';
		$sql .= ', "' . $ReceivingIPTGroup      . '"';
		$sql .= ', "' . $RQuantity  . '"';
		$sql .= ', "' . $Description     . '"';
		$sql .= ', "' . $UnitPrice   . '"';
		$sql .= ', "' . $JobName     . '"';
		$sql .= ', "' . $FilePath      . '"';
		$sql .= ')';
		
		$result = SqlQuery($loc, $sql);
		log_msg($loc, 
       array("New Work Order added!  Job name =" . $JobName ,
       "DueDate= " . $NeedByDate ));
	   
	   
        $success_msg = 'User "' . $_POST["UserName"] . '" successfully added.';
        //foreach($param_list as &$param_spec) { unset($param_spec["Value"]); }

        $error_msg = true;

	   // Render the page based on state variables that were set above...
// These are: $error_msg, $success_msg, $param_list.

GenerateHtml:
include "forms/header.php";
include "forms/navform.php";
include "forms/members_menubar.php";
include "forms/workorder_create_form.php";
include "forms/footer.php";
?>
