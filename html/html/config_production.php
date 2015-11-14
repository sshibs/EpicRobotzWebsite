<?php
// --------------------------------------------------------------------
// config.php: Configuration File for Entire Epic Admin website/application
//
// This file is conttains all the config settings that are used through-out
// the website.  There is ususally a different version of this file for
// each host: on your local computer, and on the real website.
//
// Created: 12/29/14 DLB
// --------------------------------------------------------------------

// This version is for the actual admin.epicscouts.org website!  
// Be Careful NOT TO OVERWITE!!!

$config = array(
    "db" => array(
            "host" => "localhost",
            "dbname" => "EpicAdmin",
            "username" => "webpage",
            "password" => "loveepic",  //matthew1016
        ),
    "BaseUrl" => "http://admin.epicscouts.org/",
    "Salt" => "41566a17c50a", 
    "UploadDir" => "/var/www/html/uploads/",
    "UploadUrl" => "http://admin.epicscouts.org/uploads/",
    "LogDir" => "/var/www/logs/",
    "TimeZone" => "America/Los_Angeles",
    "ServerName" => "Admin.EpicScouts.Org"
);
 
 
//     Error reporting.
ini_set("error_reporting", "true");
error_reporting(E_ALL|E_STRCT);
 
?>
