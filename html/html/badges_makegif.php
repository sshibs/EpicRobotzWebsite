<?php
// --------------------------------------------------------------------
// badges_makegif.php -- Make gif images for reader.
//
// Created: 1/2/15 DLB
// --------------------------------------------------------------------

require_once "libs/all.php";
session_start();
log_page();
CheckLogin();
CheckEditor();
$loc = 'badges_makegif.php';

include "forms/header.php";
include "forms/navform.php";
include "forms/badges_menubar.php";

echo '<div class="content_area">' . "\n";
echo '<h2 class="page_title">Make GIF Images for Reader</h2>';

echo '<p>This action will cause GIF images for the reader ';
echo 'to be made and placed in the upload/gifs directory. ';
echo 'Each image will be named after the badge number so ';
echo 'that the reader can display the image on a scan. ';
echo 'Currently, this operation does not automatically ';
echo 'update the reader with new images. You must use ';
echo 'FileZilla for that.  Also, be prepared to wait ';
echo 'about a minute for all the image processing to be completed.';
echo '</p>';

echo '<div style="font-size: 20pt; margin-left: 100px; margin-top: 20px;">';
echo '<a href="badges_showall.php?action=makegif">GO</a>';
echo '</div>';

include "forms/footer.php";

?>