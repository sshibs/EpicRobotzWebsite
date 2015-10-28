<?php
// --------------------------------------------------------------------
// admin_masquerade.php -- page to allow masquerading.
//
// Created: 12/05/14 DLB
// Updated: 12/29/14 DLB -- Hacked from Epic Scouts...
// --------------------------------------------------------------------

require_once "libs/all.php";
session_start();
log_page();
CheckLogin();
CheckAdmin();
$timer = new timer();
$loc = 'admin_masquerade.php';
$error_msg = "";

if( $_SERVER["REQUEST_METHOD"] == "POST") 
{
    if(empty($_POST["UserName"])) goto GenerateHtml;
    $username = $_POST["UserName"];
    $userid = GetUserIDFromName($username);
    if($userid == false)
    {
        $error_msg = "User does not exist.";
        goto GenerateHtml;
    }
    
    $currentuser = GetUserName();
    log_msg($loc, 'User ' . $currentuser . ' is attemping to masquerade as ' . $username);
    session_unset();
    session_destroy();
    session_start();
    $okay = StartLogin($username, "", true);
    if($okay === false)
    {
        log_msg($loc, "Login failure for masquerade.  Starting ALL over.");
        session_unset();
        session_destroy();
        JumpToPage("login.php");
    }
    SetMasquerader($currentuser);
    JumpToPage("welcome.php");
}
   
GenerateHtml:   
include "forms/header.php";
include "forms/navform.php";
include "forms/admin_menubar.php";
include "forms/admin_masqueradeform.php";
include "forms/footer.php";

?>