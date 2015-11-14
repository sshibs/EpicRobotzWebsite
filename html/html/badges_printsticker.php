<?php
// --------------------------------------------------------------------
// badges_printsticker.php -- Make sticker image suitable for printing.
//
// Created: 1/15/15 DLB
// --------------------------------------------------------------------

require_once "libs/all.php";
session_start();
log_page();
CheckLogin();
CheckEditor();
$loc = 'badges_printsticker.php';
$ins_file = "docs/printing_stickers.txt";
$instructions = "";

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

$userinfo = GetUserInfoFromBadgeID($badgeid);
if($userinfo === false)
{
    DieWithMsg($loc, "No user associated with BadgeID = " . $badgeid);
}
$picid     = $userinfo["PicID"];
$firstname = $userinfo["FirstName"];
$lastname  = $userinfo["LastName"];
$title     = $userinfo["Title"];
$url = CreateLabelFile($badgeid, $picid, $firstname, $lastname, $title);

include "forms/header.php";
include "forms/navform.php";
include "forms/badges_menubar.php";

echo '<div class="content_area">' . "\n";
echo '<h2 class="page_title">JPG Image for Printing Sticker</h2>';

echo '<img class="badges_showbadge_badge" src="' . $url . '" style="width: 100px; heigth: auto; margin-left: 10px;">';

echo '<div style="float: left; width: 300px; ">';
echo '<div style="font-size: 16pt; margin-left: 100px; margin-top: 20px;">';
echo '<a href="' . $url . '" download>Download</a>';
echo '</div>';
echo '</div>';


echo '<div style="clear: both; margin-top: 20px"> </div>';
echo '<div><pre>';
echo $instructions;
echo '</pre></div>';

echo '</div>';

include "forms/footer.php";