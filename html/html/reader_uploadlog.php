<?php
// --------------------------------------------------------------------
// reader_uploadlog.php -- Uploads and processes log files.
//
// Created:  1/15/15 DLB
// --------------------------------------------------------------------

require_once "libs/all.php";
session_start();
log_page();
CheckLogin();
CheckAdmin();
$timer = new timer();
$loc = 'reader_uploadlog.php';
$ins_file = "docs/uploadlog.txt";
$error_msg = "";
$success_msg = "";
$instructions = "";

if(file_exists($ins_file)) { $instructions = file_get_contents($ins_file); }

if( $_SERVER["REQUEST_METHOD"] == "POST")
{
    if(!isset($_FILES["LogFiles"])) { DieWithMsg($loc, '$_FILES not set.'); }
    $files = $_FILES["LogFiles"];
    $filenames  = $files["name"];
    $filetypes  = $files["type"];
    $tempnames  = $files["tmp_name"];
    $fileerrors = $files["error"];
    $filesizes  = $files["size"];
    $nfiles = count($tempnames);
    if($nfiles <= 0) 
    {
        $error_msg = "No input file(s) given.";
        goto GenerateHTML;
    }
    $nbadfiles = 0;
    $nlines = 0;
    $nadded = 0;
    $nupdated = 0;
    $nignored = 0;
    for($i = 0; $i < $nfiles; $i++)
    {
        $result = ProcessLogFile($tempnames[$i]);
        if($result === false) {$nbadfiles++; continue; }
        $nlines   += $result[0];
        $nadded     += $result[1];
        $nupdated += $result[2];
        $nignored += $result[3];
    }
    
    $msg  = 'Files Processed: '   . strval($nfiles);
    $msg .= ', Bad Files: '       . strval($nbadfiles);
    $msg .= ', Lines Processed: ' . strval($nlines);
    $msg .= ', New Records: '     . strval($nadded);
    $msg .= ', Updated Records: ' . strval($nupdated);
    $success_msg = $msg;
    goto GenerateHTML;
}


GenerateHTML:
include "forms/header.php";
include "forms/navform.php";
include "forms/reader_menubar.php";
include "forms/reader_uploadlog_form.php";
include "forms/footer.php";

?>