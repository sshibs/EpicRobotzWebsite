<?php
// --------------------------------------------------------------------
// navform.php -- Form for nav area that fits to left of most pages.
//                Include after header.php if used on a given page.
// 
// Created: 12/29/14 DLB
// --------------------------------------------------------------------

require_once "libs/all.php";


echo '<div id="nav_area">';
               echo '<div class="nav_button"><a href="attendance.php">Attendance</a></div>' . "\n";
	       echo '<div class="nav_button"><a href="WorkOrder_AddWorkOrder.php">Work Orders</a></div>' . "\n";
if(IsEditor()) echo '<div class="nav_button"><a href="members.php"   >Members   </a></div>' . "\n";
if(IsEditor()) echo '<div class="nav_button"><a href="badges.php"    >Badges    </a></div>' . "\n";
if(IsEditor()) echo '<div class="nav_button"><a href="reader.php"    >Reader    </a></div>' . "\n";
               echo '<div class="nav_button"><a href="account.php"   >Account   </a></div>' . "\n";
if(IsAdmin())  echo '<div class="nav_button"><a href="admin.php"     >Admin     </a></div>' . "\n";

if(IsAdmin() && isset($config['DevBypass']))
{
    echo '<div style="font-size: 8pt;"><a href="t.php">test</a></div>' . "\n";
}

echo '</div>' . "\n";

?>
