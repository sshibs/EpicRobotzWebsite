<?php
// --------------------------------------------------------------------
// members.php -- The main members page. 
//
// Created: 12/30/14 DLB
// --------------------------------------------------------------------

require_once "config.php";
require_once "libs/all.php";
session_start();
log_page();
CheckLogin();
CheckEditor();
$loc = 'members.php';

JumpToPage("members_listpics.php");

include "forms/header.php";
include "forms/navform.php";
include "forms/members_menubar.php";
echo '<div class="content_area">';
echo '<h2>Management of Team Members</h2>';
echo '<p>Use the links above to accomplish various management tasks.</p>';
echo '</div>';
include "forms/footer.php";

?>