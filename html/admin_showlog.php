<?php
// --------------------------------------------------------------------
// admin_showlog.php -- page to show logs 
//
// Created: 12/05/14 DLB
// Updated: 12/29/14 DLB -- Hacked from Epic Scouts...
// --------------------------------------------------------------------

require_once "libs/all.php";
session_start();
log_page();
CheckLogin();
CheckAdmin();
$timer = new timer();
$loc = 'admin_showlog.php';
$error_msg = "";

if(isset($_SESSION["ShowLogParams"]))
{
    $date      = $_SESSION["ShowLogParams"]["Date"];
    $b_pages   = $_SESSION["ShowLogParams"]["Pages"];
    $b_errors  = $_SESSION["ShowLogParams"]["Errors"];
    $b_general = $_SESSION["ShowLogParams"]["General"];
    $b_oneuser = $_SESSION["ShowLogParams"]["OneUser"];
    $uid       = $_SESSION["ShowLogParams"]["UID"];
}
else{
    $date = date("m/d/y");
    $b_pages = false;
    $b_errors = true;
    $b_general = true;
    $b_oneuser = false;
    $uid = 0;
}

if( $_SERVER["REQUEST_METHOD"] == "POST") 
{
    $date      = $_POST["Date"];
    if(empty($date)) $date = date("m/d/y");
    
    $b_pages   = isset($_POST["Pages"]);
    $b_errors  = isset($_POST["Errors"]);
    $b_general = isset($_POST["General"]);
    $b_oneuser = isset($_POST["OneUser"]);
    $uid = intval($_POST["UID"]);
}

// Store settings away, so not lost if user leaves the page...
$t = array("Date" => $date, "Pages" => $b_pages, "Errors" => $b_errors,
     "General" => $b_general, "OneUser" => $b_oneuser, "UID" => $uid);
$_SESSION["ShowLogParams"] = $t;

unset($output_lines);
$data = GetLogFileContents($date);
if($data === false) { $error_msg = "Invalid date."; }
else if(empty($data)) { $error_msg = "Log file not found."; }
else
{
    $output_lines = FilterLogData($data, $b_pages, $b_errors, $b_general, $b_oneuser, $uid);
}

$output_lines = ReverseLogLines($output_lines);

include "forms/header.php";
include "forms/navform.php";
include "forms/admin_menubar.php";
echo '<div class="content_area">';
include "forms/admin_logform.php";
include "forms/admin_logdata.php";
echo '</div>';
include "forms/footer.php";

?>