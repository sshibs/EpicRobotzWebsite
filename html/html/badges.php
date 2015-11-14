<?php
// --------------------------------------------------------------------
// badges.php -- The main badges page. 
//
// Created: 12/30/14 DLB
// --------------------------------------------------------------------

require_once "libs/all.php";
session_start();
log_page();
CheckLogin();
CheckEditor();
$loc = 'badges.php';
$timer = new Timer();

JumpToPage("badges_showall.php");

include "forms/header.php";
include "forms/navform.php";
include "forms/badges_menubar.php";
echo '<div class="content_area">';
echo '<h2>Badges</h2>';
echo '<p>Use links above to work with the badges.</p>';
echo '</div>';
include "forms/footer.php";

?>