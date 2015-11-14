<?php
// --------------------------------------------------------------------
// account.php -- Main account page.
//
// Created:  1/16/15 DLB
// --------------------------------------------------------------------

require_once "libs/all.php";
session_start();
log_page();
CheckLogin();

$timer = new timer();
$loc = 'account.php';
$error_msg = "";
$success_msg = "";
$userid = GetUserID();
$username = GetUserName();
$userinfo = GetUserInfo($userid);
$picid = 0;
$havebadge = false;
$badgeid = "";
if(isset($userinfo["BadgeID"])) $badgeid = $userinfo["BadgeID"];

include "forms/header.php";
include "forms/navform.php";
include "forms/account_menubar.php";
echo '<div class="content_area">';
echo '<h2>Your Attendace Record</h2>';
if(empty($badgeid)) 
{
    echo '<p>You do not seem to have a badge.  Please talk to your IT administrator to get one.</p>';
}
else
{
    ReportSummary($badgeid);
}    

echo '<p style="font-size: 12pt;">Manage your account with the links above.</p>';

echo '</div>';
include "forms/footer.php";

function RptLine($s, $t="")
{
    echo '<div style="font-size: 12pt; margin-top: 5px; min-height: 10px;">';
    echo $s;
    if(!empty($t)) echo ' <b>' . $t . '</b>';
    echo '</div>' . "\n";
}

function ReportSummary($badgeid)
{
    $score = CalculateScoreForOne($badgeid);
    $c = $score["Counts"];
    $tm = $c[0] + $c[1] + $c[2];
    $ne = $score["NEvents"];
    $tlastday = strtotime($score["LastDay"]);
    $slastday = date("l, F j, Y", $tlastday);

    RptLine('As of <b>' . $slastday . '</b>');
    RptLine('Your Attendance Score is <b>' . sprintf("%4.1f", $score["Score"]) . '</b> percent.');
    RptLine(' ');
    RptLine('Total possible meetings:', $tm);
    RptLine('Meeting types: Manditory=<b>' . $c[0] . '</b>, Regular=<b>' . $c[1] . '</b>, Optional=<b>' . $c[2] . '</b>');
    RptLine(' ');
    RptLine('Meetings attended:', $score["Present"]);
    RptLine('Total hours:', sprintf("%5.1f", $score["TotalHours"]));
    RptLine('Hours on days of meetings:', sprintf("%5.1f", $score["InHours"]));
    RptLine('Hours outside of meeting days:', sprintf("%5.1f", $score["OutHours"]));
    RptLine(' ');
    
    RptLine('Your Record:');
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
    
}