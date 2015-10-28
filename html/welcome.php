<?php
// --------------------------------------------------------------------
// welcome.php -- The main welcome page.  Come here directly after
//                a login.
//
// Created: 12/29/14 DLB
// --------------------------------------------------------------------

require_once "config.php";
require_once "libs/all.php";
session_start();
log_page();
CheckLogin();

include "forms/header.php";
include "forms/navform.php";
include "forms/welcomeform.php";
include "forms/footer.php";

?>