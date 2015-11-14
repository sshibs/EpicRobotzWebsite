<?php
// --------------------------------------------------------------------
// reader.php -- The main reader page. 
//
// Created: 12/30/14 DLB
// --------------------------------------------------------------------

require_once "config.php";
require_once "libs/all.php";
session_start();
log_page();
CheckLogin();
$loc = 'reader.php';

include "forms/header.php";
include "forms/navform.php";
include "forms/reader_menubar.php";
echo '<div class="content_area">';
echo '<h2>Reader</h2>';
echo '<p>Manage the reader with the links above.</p>';
echo '</div>';
include "forms/footer.php";

?>