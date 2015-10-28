<?php
// --------------------------------------------------------------------
// reader_uploadcorrections.php -- Uploads the corrections.
//
// Created:  2/09/15 DLB
// --------------------------------------------------------------------

require_once "libs/all.php";
session_start();
log_page();
CheckLogin();
CheckAdmin();
$timer = new timer();
$loc = 'reader_uploadcorrections.php';
$ins_file = "docs/corrections.txt";
$error_msg = "";
$success_msg = "";
$instructions = "";

if(file_exists($ins_file)) { $instructions = file_get_contents($ins_file); }

if( $_SERVER["REQUEST_METHOD"] == "POST")
{
    if(!isset($_FILES["CorrectionFiles"])) { DieWithMsg($loc, '$_FILES not set.'); }
    $files = $_FILES["CorrectionFiles"];
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
    
    DeleteAllCorrections();
    $nbadfiles = 0;
    $nlines = 0;
    $nadded = 0;
    $nupdated = 0;
    $nignored = 0;
    for($i = 0; $i < $nfiles; $i++)
    {
        $result = ProcessCorrectionFile($tempnames[$i]);
        if($result === false) {$nbadfiles++; continue; }
        $nlines   += $result[0];
        $nadded   += $result[1];
        $nignored += $result[2];
    }
    
    $msg  = 'Files Processed: '   . strval($nfiles);
    $msg .= ', Bad Files: '       . strval($nbadfiles);
    $msg .= ', Lines Processed: ' . strval($nlines);
    $msg .= ', Corrections: '     . strval($nadded);
    $msg .= ', Ingored Lines: '   . strval($nignored);
    $success_msg = $msg;
    goto GenerateHTML;
}


GenerateHTML:
include "forms/header.php";
include "forms/navform.php";
include "forms/reader_menubar.php";
include "forms/reader_uploadcorrections_form.php";
include "forms/footer.php";

?>