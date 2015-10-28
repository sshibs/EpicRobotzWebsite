<?php
// --------------------------------------------------------------------
// reader_uploadlog_form.php -- HTML fragment to show a form for
//                              uploading log files from reader.
//
// Created:  1/15/15 DLB
// --------------------------------------------------------------------
?>

<div class="content_area">

<h2 class="page_title">Upload Reader Log Files</h2>

<?php
if(!empty($success_msg))
{
    echo '<div class="inputform_msg" id="inputform_success_msg" >' . $success_msg . "</div>";
}
if(!empty($error_msg))
{
    echo '<div class="inputform_msg" id="inputform_error_msg" >' . $error_msg . "</div>";
}
?>

<div class="inputform_area">
<form action="reader_uploadlog.php" method="post" enctype="multipart/form-data">

    <div class="inputform_paramblock">
    <div class="inputform_label">Log File(s) From Reader to Upload</div>
    <input type ="file" name="LogFiles[]" multiple id="adduserbulk_fileselect"></input>
    </div>
    
    <input class="inputform_submit_button" type="submit" value="Process" name="submit" />
</form>
</div>

<?php if(!empty($instructions)) 
{ 
    echo '<div class="instructions"><pre>';
    echo $instructions;
    echo '</pre></div>';
}
?>

</div>