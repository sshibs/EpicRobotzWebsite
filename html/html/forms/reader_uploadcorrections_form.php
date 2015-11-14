<?php
// --------------------------------------------------------------------
// reader_uploadcorrections_form.php -- HTML fragment to show a form for
//                              uploading correction files from reader.
//
// Created:  1/15/15 DLB
// --------------------------------------------------------------------
?>

<div class="content_area">

<h2 class="page_title">Upload Corrections File</h2>

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
<form action="reader_uploadcorrections.php" method="post" enctype="multipart/form-data">

    <div class="inputform_paramblock">
    <div class="inputform_label">Corrections File</div>
    <input type ="file" name="CorrectionFiles[]" multiple id="adduserbulk_fileselect"></input>
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