<?php
// --------------------------------------------------------------------
// badges_print.php -- Make badge image suitable for printing.
//
// Created: 1/15/15 DLB
// --------------------------------------------------------------------

require_once "libs/all.php";
session_start();
log_page();
CheckLogin();
CheckEditor();
$loc = 'badges_print.php';
$ins_file = "docs/printing_onebadge.txt";
if(file_exists($ins_file)) { $instructions = file_get_contents($ins_file); }


if( $_SERVER["REQUEST_METHOD"] != "GET")
{
    DieWithMsg($loc, 'Bad Page Invoke. Only GET request allowed.');
}
if(empty($_GET["BadgeID"]))
{
    DieWithMsg($loc, "Bad Page Invoke. No BadgeID given.");
}

$badgeid = $_GET["BadgeID"];
$havebadge = BadgeExists($badgeid);
if(!$havebadge)
{
    DieWithMsg($loc, "Badge does not exist.");
}

$badge_front_url = GetBadgeUrl($badgeid, 'front');
$badge_back_url  = GetBadgeUrl($badgeid, 'back');    
$urls = MakePrintImageForOneBadge($badgeid);
    
include "forms/header.php";
include "forms/navform.php";
include "forms/badges_menubar.php";

echo '<div class="content_area">' . "\n";
echo '<h2 class="page_title">JPG Image for Printing Badge</h2>';

echo '<img class="badges_showbadge_badge" src="' . $badge_front_url . '" style="width: 100px; heigth: auto; margin-left: 10px;">';
echo '<img class="badges_showbadge_badge" src="' . $badge_back_url . '" style="width: 100px; height: auto; margin-left: 10px;">';

echo '<div style="float: left; width: 300px; ">';
echo '<div style="font-size: 16pt; margin-left: 100px; margin-top: 20px;">';
echo '<a href="' . $urls[0] . '" download>Download Front</a>';
echo '</div>';

echo '<div style="font-size: 16pt; margin-left: 100px; margin-top: 20px;">';
echo '<a href="' . $urls[1] . '" download>Download Back</a>';
echo '</div>';
echo '</div>';

echo '<div style="clear: both; margin-top: 20px"> </div>';
echo '<div><pre>';
echo $instructions;
echo '</pre></div>';

echo '</div>';


include "forms/footer.php";

?>