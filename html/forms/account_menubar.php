<?php
// --------------------------------------------------------------------
// account_menubar.php -- HTML fragment to show the account menu bar.
//
// Created:  1/16/15 DLB
// --------------------------------------------------------------------

require_once "libs/all.php";

echo '<style>' . "\n";
echo '  .content_area {min-height: 275px; } ' . "\n";
echo '</style>' . "\n";

echo '<div class="menubar_area">' . "\n";

echo '<div class="menu_button">' . "\n";
echo '<a href="account.php">Summary</a>' . "\n";
echo '</div>' . "\n";

echo '<div class="menu_button">' . "\n";
echo '<a href="account_showscans.php">Scans</a>' . "\n";
echo '</div>' . "\n";

echo '<div class="menu_button">' . "\n";
echo '<a href="account_setup.php">Settings</a>' . "\n";
echo '</div>' . "\n";

echo '<div class="menu_button">' . "\n";
//echo '<a href="account_makeclaim.php">Make Claim</a>' . "\n";
echo 'Make Claim';
echo '</div>' . "\n";

echo '</div>'

?>