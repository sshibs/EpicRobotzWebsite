<?php
// --------------------------------------------------------------------
// attendance_user.php -- Attendance report for a given user.
//
// Created:  1/22/15 DLB
// --------------------------------------------------------------------

require_once "config.php";
require_once "libs/all.php";
session_start();
log_page();
CheckLogin();
$timer = new Timer();
$loc = 'attendance_user.php';
$userid = 0;
$data = null;
$name = "";

if( $_SERVER["REQUEST_METHOD"] == "GET")
{
    if(empty($_GET["UserID"]))
    {
        DieWithMsg($loc, "Bad Page Invoke. No UserID given.");
    }
    $userid = $_GET["UserID"];
    $data = GetUserInfo($userid);
    if($data === false) DieWithMsg($loc, 'User with ID=' . $userid . ' not found.');
    $name = $data['FirstName'] . ' ' . $data["LastName"];
    goto GenerateHtml;
}
else
{
    DieWithMsg($loc, "Bad Page Invoke. Must be by GET.");
}

GenerateHtml:
include "forms/header.php";
include "forms/navform.php";
include "forms/attendance_menubar.php";
echo '<div class="content_area">';
echo '<h2>Detailed Attendance Report for ' . $name . '</h2>';

if(empty($data["BadgeID"]))
{
    echo '<p>' . $name . ' does not have a badge.  Nothing to report. <\p>';
}
else GenerateUserReport($data);

include "forms/footer.php";

// --------------------------------------------------------------------
// Generate report for a user.
function GenerateUserReport($data)
{
    $badgeid = $data["BadgeID"];
    $score = CalculateScoreForOne($badgeid);
    $c = $score["Counts"];
    $tm = $c[0] + $c[1] + $c[2];
    $ne = $score["NEvents"];
    
    RptLine('As of <b>' . $score["LastDay"] . '</b>:');
    RptLine(' ');
    RptLine('Total possible meetings:', $tm);
    RptLine('Meeting types: Manditory=<b>' . $c[0] . '</b>, Regular=<b>' . $c[1] . '</b>, Optional=<b>' . $c[2] . '</b>');
    RptLine(' ');
    RptLine('Meetings attended:', $score["Present"]);
    RptLine('Attendance score: <b>' . sprintf("%4.1f", $score["Score"]) . '</b>%');
    RptLine('Total hours:', sprintf("%5.1f", $score["TotalHours"]));
    RptLine('Hours on days of meetings:', sprintf("%5.1f", $score["InHours"]));
    RptLine('Hours outside of meeting days:', sprintf("%5.1f", $score["OutHours"]));
    RptLine(' ');
    
    for($i = 0; $i < $ne; $i++)
    {
        $d = $score[$i];
        $sout = $d["Name"] . ': ';
        if($d["Present"]) $sout .= "Present";
        else $sout .= "<b>Absent</b>";
        if($d["Hours"] > 0.001) $sout .= ', Hours: <b>' . sprintf("%4.1f", $d["Hours"]) . '</b>';
        if(isset($d["Correction"])) $sout .= ', **' . $d["Correction"];
        RptLine($sout);
    }
    
    RptLine(' ');
    RptLine('Raw Scans:');
    ShowRawScans($data["BadgeID"]);
    ShowCorrections($data["BadgeID"]);
}

function RptLine($s, $t="")
{
    echo '<div style="font-size: 12pt; margin-top: 5px; min-height: 10px;">';
    echo $s;
    if(!empty($t)) echo ' <b>' . $t . '</b>';
    echo '</div>' . "\n";
}

function ShowRawScans($badgeid)
{
    $loc = "attendance_user.php->ShowRawScans()";
    $sql = 'SELECT * from RawScans WHERE BadgeID="' . $badgeid . '"';
    $result = SqlQuery($loc, $sql);
    while($row = $result->fetch_assoc())
    {
        echo '<div style="display: block; height: 20px; ">';
        echo '<br>' . "\n";
        echo '<div style="float: left; width: 180px;">';
        echo $row["ScanTime"];
        echo '</div>' . "\n";
        
        $dir = "";
        if($row["Direction"] == 0) $dir = "Scan In";
        if($row["Direction"] == 1) $dir = "Scan Out";
        if($row["Direction"] == 2) $dir = "?";
        
        echo '<div style="float: left; width: 100px;">';
        echo $dir;
        echo '</div>' . "\n";
        
        echo '<div style="float: left; width: 80px;">';
        echo $row["Flags"];
        echo '</div>' . "\n";
        
        echo '<div style="clear: both;"></div>' . "\n";
        echo '</div>';
    }
}

function ShowCorrections($badgeid)
{
    $loc = "attendance_user.php->ShowRawScans()";
    $corrections = GetSqlTable("Corrections");
    RptLine('');
    RptLine('Corrections applied:');
    foreach($corrections as $c)
    {
        if($c["BadgeID"] == '*' || strtolower($badgeid) == strtolower($c["BadgeID"]))
        {   
            echo '<div style="display: block; hieght: 20px; ">';
            echo '<div style="float: left; width: 100px;">';
            echo $c["Action"];
            echo '</div>';
            echo '<div style="float: left; width: 150px;">';
            echo $c["ScanTime"];
            echo '</div>';
            echo '<div style="float: left; width: 350px; margin-left: 10px; ">';
            echo $c["Reason"];
            echo '</div>';
            echo '</div>';
            echo '<div style="clear: both;"></div>' . "\n";
        }
    }
}

?>