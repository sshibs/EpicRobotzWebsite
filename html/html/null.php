<?php
// --------------------------------------------------------------------
// null.php -- Not Implemented Feature
//
// Created: 12/30/14 DLB
// --------------------------------------------------------------------

require_once "config.php";
require_once "libs/all.php";
session_start();
log_page();
CheckLogin();
CheckAdmin();
$loc = 'null.php';

include "forms/header.php";
include "forms/navform.php";
echo '<div class="content_area">';
echo '<h2>Feature Not Implemented</h2>';
echo '<p>Sorry, this feature is not implemented yet.</p>';
echo '</div>';
include "forms/footer.php";

?>