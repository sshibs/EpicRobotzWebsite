<?php
// --------------------------------------------------------------------
// account_showscans.php -- Dumps the users scans.
//
// Created:  1/16/15 DLB
// --------------------------------------------------------------------

require_once "libs/all.php";
session_start();
log_page();
CheckLogin();

$timer = new timer();
$loc = 'account_showscans.php';
$error_msg = "";
$success_msg = "";
$userid = GetUserID();
$username = GetUserName();
$picid = 0;
$badgeid = "";

$userinfo = GetUserInfo($userid);
if(isset($userinfo["BadgeID"]))
{
    if(!empty($userinfo["BadgeID"])) 
    {
        $badgeid = $userinfo["BadgeID"];
    }
}

include "forms/header.php";
include "forms/navform.php";
include "forms/account_menubar.php";
echo '<div class="content_area">';
echo '<h2>A List of Your Badge Scans</h2>';

if(empty($badgeid))
{
    echo '<p>You do not seem to have a badge... Without a badge, no data is available.</p>';
}
else
{
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
    echo '<br>';
    ShowCorrections($badgeid);
    
}

echo '</div>';
include "forms/footer.php";

function RptLine($s, $t="")
{
    echo '<div style="font-size: 12pt; margin-top: 5px; min-height: 10px;">';
    echo $s;
    if(!empty($t)) echo ' <b>' . $t . '</b>';
    echo '</div>' . "\n";
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