<?php
// --------------------------------------------------------------
// index.php -- Default entry page into Epic Admin website.
//
// Created: 12/29/14 DLB
// --------------------------------------------------------------------

require_once "libs/all.php";
session_start();
log_page();

if(IsLoggedIn()) { JumpToPage("welcome.php");  }
else             { JumpToPage("login.php");     }

DieWithMsg("index.php", "Unreachable code reached!");

?>