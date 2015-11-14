<?php
// --------------------------------------------------------------
// login.php -- Implements the login page.
//
// Created: 12/29/14 DLB
// --------------------------------------------------------------------

require_once "libs/all.php";
session_start();
log_page();

$LoginOkay = false;
$ShowError = false;
if( $_SERVER["REQUEST_METHOD"] == "POST") 
{
    // Here we have already shown the form, and now
    // we are processing input from the form...
    $name = $_POST["name"];
    $pw   = $_POST["password"];
    
    // Here we do a trick, and allow a developer 
    // to log in by leaving both fields empty. The
    // bypass must be enabled in the config file.
    $bypass = false;
    if(!empty($config["DevBypass"]) && empty($name) && empty($ps))
    {
        $name = $config["DevBypass"];
        $pw = "junk";
        log_msg("login.php", "Developer bypass attempted for username=" . $name);
        $bypass = true;
    }

    // Continue normal processing...
    if (empty($name) || empty($pw)) 
    {
        $LoginOkay = false;
    }
    else 
    {
        $LoginOkay = StartLogin($name, $pw, $bypass);
        if($LoginOkay) JumpToPage("welcome.php");
    }
    if(!$LoginOkay) 
    {
        log_msg("login.php", array("Login Attempt Failed. UserName=" . $name, "IP Address=" . $_SERVER["REMOTE_ADDR"]));
        $ShowError = true;
    }
}

// Generate HTML:
include "forms/header.php";
include "forms/loginform.php";
include "forms/footer.php";
?>
