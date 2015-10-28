<?php
// --------------------------------------------------------------------
// badges_makeall.php -- Make all badges.
//
// Created: 12/31/14 DLB
// --------------------------------------------------------------------

require_once "libs/all.php";
session_start();
log_page();
CheckLogin();
CheckEditor();
$loc = 'badges_makeall.php';

include "forms/header.php";
include "forms/navform.php";
include "forms/badges_menubar.php";

echo '<div class="content_area">' . "\n";
echo '<h2 class="page_title">Make All Badges</h2>';

echo '<p>This action will cause all the current badges to be destoyed ';
echo 'and each badge to be remade with current information.  This ';
echo 'action is not dangerous, but it can be time consuming. ';
echo 'About 3 badges can be made per second, so be perpared to ';
echo 'wait up to a minute. </p>';

echo '<div style="font-size: 20pt; margin-left: 100px; margin-top: 20px;">';
echo '<a href="badges_showall.php?action=makeall">GO</a>';
echo '</div>';

include "forms/footer.php";

?>