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

// This version is for Dal's development machine on Windows.

$config = array(
    "db" => array(
            "host" => "localhost",
            "dbname" => "EpicAdmin",
            "username" => "Dalbert",
            "password" => "lovelove",
        ),
    "BaseUrl" => "http://localhost//EpicAdmin/",
    "Salt" => "41566a17c50a",
    "UploadDir" => "C:\\EpicAdmin\\uploads\\",
    "UploadUrl" => "http://localhost//EpicAdmin/uploads/",
    "LogDir" => "C:\\EpicAdmin\\logs\\",
    "TimeZone" => "America/Los_Angeles",
    "ServerName" => "Dals Development Computer",
    "DevBypass" => "dal"
);

//     Error reporting.
ini_set("error_reporting", "true");
error_reporting(E_ALL|E_STRCT);
 
?>