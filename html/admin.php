<?php
// --------------------------------------------------------------------
// admin.php -- The main admin page.  Come here on "admin" in nav menu.
//
// Created: 12/29/14 DLB
// --------------------------------------------------------------------

require_once "config.php";
require_once "libs/all.php";
session_start();
log_page();
CheckLogin();
CheckAdmin();

include "forms/header.php";
include "forms/navform.php";
include "forms/admin_menubar.php";
echo '<div class="content_area">';
echo '<h2>Administration for This Website</h2>';
echo '<p>Use the links above for various admin tasks.</p>';
echo '</div>';
include "forms/footer.php";

?>