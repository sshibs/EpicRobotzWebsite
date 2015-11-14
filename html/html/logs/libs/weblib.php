<?php
// --------------------------------------------------------------------
// weblib:  library fucntions to handle website-wide functions.
//
// Created: 12/13/14 DLB
// --------------------------------------------------------------------

require_once "config.php";
require_once "libs/loglib.php";

// --------------------------------------------------------------------
// Puts out a complete page, with the given error message, and then
// exits.  The error message can be either a simple string, or an
// array of strings.  This routine also writes the error message
// to the log file.
function DieWithMsg($loc, $e)
{
    log_error($loc, $e);
    
    include "forms/header.php";
    echo '<h2 class="page_title">Major Error Alert</h2>' . "\n";
    echo '<div class="diemsg">';
    echo '<p>You have arrived at this page because some unexpected error happened that probably was';
    echo ' not your fault.  It might be due to bad programming, or something that changed deep inside the ';
    echo 'system.  This error has been logged for futher review.  The info about the error is below. ';
    echo 'You should try again from the main menu.  Sorry. </p>';
    echo '</div>';
    echo "\n";
    if(is_array($e))
    {
        foreach($e as $t) { echo $t . "<br>\n"; }
    }
    else { echo $e . "<br>\n"; }
    echo "<br><br>";
    echo '<a href="welcome.php">Back to Start</a>';
    echo "<br><br>";
    include "forms/footer.php";
    exit;
}

// --------------------------------------------------------------------
// Dies due to SQL querry...
function DieWithBadSql($loc, $sql)
{
    DieWithMsg($loc, array("SQL Querry Failure!", "SQL=" . $sql));
}

// --------------------------------------------------------------------
// Puts out a complete page, with only the given error message, and 
// not the long explanation, and then exits.  The error message can
// be either a simple string, or an array of strings.  This routine
// also writes the error message to the log file.
function DieNice($loc, $e)
{
    log_error($loc, $e);
    
    include "forms/header.php";
    echo '<h2 class="page_title">Error...</h2>' . "\n";
    echo '<div class="diemsg">' . "\n";
    if(is_array($e))
    {
        foreach($e as $t) { echo $t . "<br>\n"; }
    }
    else { echo $e . "<br>\n"; }
    echo "</div>";
    echo "<br><br>";
    echo '<a href="welcome.php">Back to Start</a>';
    echo "<br><br>";
    include "forms/footer.php";
    exit;
}

// --------------------------------------------------------------------
// Jumps to another page by re-direction.  The $pagefile is the base
// name of the new page to load, relative to the top directory of the
// website.  $args is either null or an associtive array of arguments
// to pass to the page.  This function should be called
// before ANY output is writen to the response.
//
// NOTE: the arguments, if used, must be super simple, and not contain
// any weird characters or spaces.  This is because we do not rely
// on the pecl_http extension.
//
// This function does not return to the caller.
function JumpToPage($pagefile, $args = null)
{
    global $config;
    $path = $config["BaseUrl"] . $pagefile;
    if(isset($args))
    {
        $first = true;
        foreach($args as $key => $value)
        {
            if($first) { $path = $path . '?'; $first = false; }
            else {$path = $path. '&'; }
            $path = $path . $key . "=" . $value;
        }
    }
 
    header("Location: " . $path);
    exit;
}



?>