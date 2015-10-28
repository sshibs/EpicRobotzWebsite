<?php
// --------------------------------------------------------------------
// logout.php -- Impements the logout page.
//
// Created: 12/29/14 DLB
// --------------------------------------------------------------------

require "libs/all.php";

session_start();
log_page();

if(IsMasquerading())
{
    $olduser = GetMasquerader();
    log_msg("logout.php", "Masquerade session is over.");
    session_unset();
    session_destroy();
    session_start();
    if(!empty($olduser))
    {
        log_msg("logout.php", "Attempting to re-login as " . $olduser);
        $okay = StartLogin($olduser, "", true);
        if($okay) JumpToPage("welcome.php");
    }
}
else
{
    log_msg("logout.php", "User " . UserLastFirstName() . " is Logging Out.");
}

session_unset();
session_destroy();

include "forms/header.php";
include "forms/logoutmsg.php";
include "forms/footer.php";

?>