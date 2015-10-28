<?php
// --------------------------------------------------------------------
// attendance_fullreport.php -- Generals the "full" attendance report.
//
// Created: 12/30/14 DLB
// --------------------------------------------------------------------

require_once "config.php";
require_once "libs/all.php";
session_start();
log_page();
CheckLogin();
$timer = new Timer();
$loc = 'attendance_fullreport.php';
$sall = CalculateAllScores();
$nusers = $sall["NUsers"];
$browser_width = 1200;
$nc = $sall[0]["NEvents"];
$browser_width = 800;
$tc = $nc - 6;
if($tc > 0) $browser_width += $tc*39;

include "forms/header.php";
include "forms/navform.php";
include "forms/attendance_menubar.php";
echo '<div class="content_area">';
echo '<h2>Full Attendance Report</h2>';

if($nusers == 0) 
{
    echo '<p>No Users.</p>'; 
}
else 
{
    //echo '<div style="width: 2000px; overflow-x: scroll;">';
    ShowReport($sall);
    //echo '</div>';
}

echo '</div>';
include "forms/footer.php";

// --------------------------------------------------------------------
// Prints the header for the big report.
function PrintHeader($sall)
{
    // First, get headers for columns.  Use first user's data for this.
    $nc = $sall[0]["NEvents"];
    echo "<tr>\n";
    echo "<th align=left width=170><u>Name</u></th>";
    echo "<th align=right width=60><u>Score</u></th>";
    echo "<th align=right width=60><u>T-Hours</u></th>";
    echo "<th align=right width=60><u>Off-Hrs</u></th>";
    echo "<th width=10></th>";
    for($i = 0; $i < $nc; $i++)
    {
        echo "<th align=right width=40><u>" . $sall[0][$i]["Name"] . "</u></th>\n";
    }
    echo "</tr>";
}

// --------------------------------------------------------------------
// Shows the report for all attendance to date.
function ShowReport($sall)
{
    $tlastday = strtotime($sall["LastDay"]);
    $slastday = date("l, F j, Y", $tlastday);
    $c = $sall["Counts"][0] + $sall["Counts"][1] + $sall["Counts"][2];

    echo '<p>As of <b>' . $slastday . '</b>:<br>';
    $nusers = $sall["NUsers"];
    // Put out header...
    echo "<br>\n";
    echo '<table class="admin_userlist">';
    PrintHeader($sall);
    for($iu = 0; $iu < $nusers; $iu++)
    {
        echo '<tr>';
        $sc = $sall[$iu];
        
        echo '<th align=left width=170>';
        echo '<a href="attendance_user.php?UserID=' . $sc["UserID"] . '">' . $sc["Name"] . '</a></th>';
        echo '<th align=right width=60>' . sprintf("%4.1f", $sc["Score"]) . '</th>';
        echo '<th align=right width=60>' . sprintf("%5.1f", $sc["TotalHours"]) . '</th>';
        echo '<th align=right width=60>' . sprintf("%5.1f", $sc["OutHours"]) . '</th>';
        echo "<th width=10></th>";
        $nc = $sc["NEvents"];
        for($i = 0; $i < $nc; $i++)
        {
            $ss = "";
            $scc = $sc[$i];
            $c = '';
            if($scc["Present"]) $c = 'p';
            if(isset($scc["Correction"])) $c = 'c';
            $ss .= $c;
            if($scc["Hours"] > 0.001) 
            {
                if($scc["Hours"] > 10.0) $ss .= sprintf("%2.0f", $scc["Hours"]);
                else                     $ss .= sprintf("%3.1f", $scc["Hours"]);
            }
            echo "<th align=right width=40>" . $ss . "</th>\n";
        }
        
        echo '</tr>' . "\n";
    }
    
    
    echo '</table>';
}

?>