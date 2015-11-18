<?php
// --------------------------------------------------------------------
// workorder_selectipt.php -- 
//
// Created: 12/29/14 DLB
// --------------------------------------------------------------------

require_once "libs/all.php";
session_start();
log_page();
CheckLogin();
CheckAdmin();
$timer = new timer();
$loc = 'workorders_selectipt.php';
$error_msg = "";
$success_msg = "";

if( $_SERVER["REQUEST_METHOD"] == "POST")
{
	if(!empty($_POST["RequestingIPTGroup"]) ) { $IPTGroup = $_POST["RequestingIPTGroup"];}

        JumpToPage("workorders_listipts.php?IPTGroup=" . $IPTGroup);

  if(empty($_POST["RequestingIPTGroup"]))
    {
        DieWithMsg($loc, "Bad Page Invoke. No IPTGroup given.");
    }
}
GenerateHtml:
include "forms/header.php";
include "forms/navform.php";
include "forms/workorders_menubar.php";
include "forms/workorders_selectipt_form.php";
include "forms/footer.php";
?>

