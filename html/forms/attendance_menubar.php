<?php
// --------------------------------------------------------------------
// attendance_menubar.php -- HTML fragment to show the attendance menu bar.
//
// Created: 12/30/14 DLB
// --------------------------------------------------------------------

require_once "libs/all.php";

echo '<style>' . "\n";
echo '  .content_area {min-height: 275px; } ' . "\n";
echo '</style>' . "\n";

echo '<div class="menubar_area">' . "\n";

echo '<div class="menu_button">' . "\n";
echo '<a href="attendance_fullreport.php">Full Rpt</a>' . "\n";
echo '</div>' . "\n";

echo '<div class="menu_button">' . "\n";
echo '<a href="attendance.php?mode=user">By User</a>' . "\n";
echo '</div>' . "\n";

echo '<div class="menu_button">' . "\n";
echo '<a href="attendance.php?mode=schedule">By Schedule</a>' . "\n";
echo '</div>' . "\n";

echo '<div class="menu_button">' . "\n";
echo '<a href="attendance.php?mode=download">Download</a>' . "\n";
echo '</div>' . "\n";

if(IsEditor()) 
{
echo '<div class="menu_button">' . "\n";
echo '<a href="attendance_setup.php">Settings</a>' . "\n";
echo '</div>' . "\n";

echo '<div class="menu_button">' . "\n";
echo '<a href="attendance_uploadevents.php">Events</a>' . "\n";
echo '</div>' . "\n";
}

echo '</div>'

?>