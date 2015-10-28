<?php
// --------------------------------------------------------------------
// attendance.php -- The main attendance page. 
//
// Created: 12/30/14 DLB
// --------------------------------------------------------------------

require_once "config.php";
require_once "libs/all.php";
session_start();
log_page();
CheckLogin();
$timer = new Timer();
$loc = 'attendance.php';

include "forms/header.php";
include "forms/navform.php";
include "forms/attendance_menubar.php";
echo '<div class="content_area">';
echo '<h2>Attendance Tracking Reports</h2>';
echo '<p>Use links above to see various reports. </p>';
echo '<p>Currently, these reports are not real-time, but instead require that the data be ';
echo 'manually uploaded from the reader(s).  Therefore the last day in which the reports are ';
echo 'valid needs to be set by the "settings" link, above.';
echo '</p>';
include "forms/footer.php";

?>
