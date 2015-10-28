<?php
// --------------------------------------------------------------------
// admin_uploadusersform.php -- HTML fragment to show a form for
//                              uploading csv files of users for
//                              making user accounts.
//
// Created: 12/17/14 DLB
// Updated: 12/30/14 DLB -- Hacked from Epic Scouts
// --------------------------------------------------------------------
?>

<div class="content_area">

<h2 class="page_title">Upload User Accounts</h2>

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
<form action="admin_uploadusers.php" method="post" enctype="multipart/form-data">

    <div class="inputform_paramblock">
    <div class="inputform_label">CSV File to Upload</div>
    <input type ="file" name ="CsvFile" id="adduserbulk_fileselect"></input>
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