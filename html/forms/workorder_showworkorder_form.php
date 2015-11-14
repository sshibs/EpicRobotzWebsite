<?php
//----------------------------------
//workorder_showworkorder_form.php -- HTML fragment to show a workorder
//
// Created: 11/10/15 NG
//----------------------------------

echo '<div class="content_area">';
echo '<h2>Work Order Display For ' . $WorkOrderName . '</h2>';

echo '<div class="badges_param_div">';
echo '<div class="badges_paramvalue">Work Order ID:</div>';
echo '<div class="badges_paramvalue">' . $WorkOrderID . '</div>';
echo '</div>';

echo '<div class="badges_param_div">';
echo '<div class="badges_paramvalue">Work Order Name:</div>';
echo '<div class="badges_paramvalue">' . $WorkOrderName . '</div>';
echo '</div>';

echo '<div class="badges_param_div">';
echo '<div class="badges_paramvalue">Date Requested:</div>';
echo '<div class="badges_paramvalue">' . $DateRequested . '</div>';
echo '</div>';

echo '<div class="badges_param_div">';
echo '<div class="badges_paramvalue">Date Needed:</div>';
echo '<div class="badges_paramvalue">' . $DateNeeded . '</div>';
echo '</div>';

echo '<div class="badges_param_div">';
echo '<div class="badges_paramvalue">Priority:</div>';
echo '<div class="badges_paramvalue">' . $Priority . '</div>';
echo '</div>';

echo '<div class="badges_param_div">';
echo '<div class="badges_paramvalue">Day Estimate:</div>';
echo '<div class="badges_paramvalue">' . $DayEstimate . '</div>';
echo '</div>';

echo '<div class="badges_param_div">';
echo '<div class="badges_paramvalue">Revision:</div>';
echo '<div class="badges_paramvalue">' . $Revision . '</div>';
echo '</div>';

echo '<div class="badges_param_div">';
echo '<div class="badges_paramvalue">Requestor:</div>';
echo '<div class="badges_paramvalue">' . $Requestor . '</div>';
echo '</div>';

echo '<div class="badges_param_div">';
echo '<div class="badges_paramvalue">Requesting IPT Lead Approval:</div>';
echo '<div class="badges_paramvalue">' . $RequestingIPTLeadApproval . '</div>';
echo '</div>';

echo '<div class="badges_param_div">';
echo '<div class="badges_paramvalue">Assigned IPT Lead Approval:</div>';
echo '<div class="badges_paramvalue">' . $AssignedIPTLeadApproval . '</div>';
echo '</div>';

echo '<div class="badges_param_div">';
echo '<div class="badges_paramvalue">Project:</div>';
echo '<div class="badges_paramvalue">' . $Project . '</div>';
echo '</div>';

echo '<div class="badges_param_div">';
echo '<div class="badges_paramvalue">Requesting IPT Group:</div>';
echo '<div class="badges_paramvalue">' . $RequestingIPTGroup . '</div>';
echo '</div>';

echo '<div class="badges_param_div">';
echo '<div class="badges_paramvalue">Recieving IPT Group:</div>';
echo '<div class="badges_paramvalue">' . $ReceivingIPTGroup . '</div>';
echo '</div>';

echo '<div class="badges_param_div">';
echo '<div class="badges_paramvalue">Project Office Approval:</div>';
echo '<div class="badges_paramvalue">' . $ProjectOfficeApproval . '</div>';
echo '</div>';

echo '<div class="badges_param_div">';
echo '<div class="badges_paramvalue">Reviewed By:</div>';
echo '<div class="badges_paramvalue">' . $ReviewedBy . '</div>';
echo '</div>';

echo '<div class="badges_param_div">';
echo '<div class="badges_paramvalue">Assigned To:</div>';
echo '<div class="badges_paramvalue">' . $AssignedTo . '</div>';
echo '</div>';
/*
echo '<div class="badges_param_div">';
echo '<div class="badges_paramvalue">Completed:</div>';
echo '<div class="badges_paramvalue">' . $Completed . '</div>';
echo '</div>';
*/
echo '<div class="badges_param_div">';
echo '<div class="badges_paramvalue">Completed On:</div>';
echo '<div class="badges_paramvalue">' . $CompletedOn . '</div>';
echo '</div>';
/*
echo '<div class="badges_paramlabel">Quantity:</div>';
echo '<div class="badges_paramvalue">' . $quantity . '</div>';

echo '<div class="badges_paramlabel">Description:</div>';
echo '<div class="badges_paramvalue">' . $description . '</div>';

echo '<div class="badges_paramlabel">Unit Price:</div>';
echo '<div class="badges_paramvalue">' . $unitprice . '</div>';

echo '<div class="badges_paramlabel">Prerequisite ID:</div>';
echo '<div class="badges_params_link">';
echo '<a href="workorders_showworkorder.php?WorkOrderID=' . $prerequisiteid . '">' . $prerequisitename . '</a>';
*/

echo '</div' . "\n";
?>
