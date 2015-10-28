<?php
// --------------------------------------------------------------------
// member_uploadpic_form.php -- HTML fragment for uploading a pic.
//
// Created: 12/30/14 DLB
// --------------------------------------------------------------------

echo '<div class="content_area">';
echo '<h2 class="page_title">Upload Member Picture</h2>' . "\n";

if(!empty($success_msg))
{
    echo '<div class="inputform_msg" id="inputform_success_msg" >' . $success_msg . "</div>";
}
if(!empty($error_msg))
{
    echo '<div class="inputform_msg" id="inputform_error_msg" >' . $error_msg . "</div>";
}

echo '<div class="members_paramlabel">UserID:</div>';
echo '<div class="members_paramvalue">' . $userid . '</div>';
echo '<div class="members_paramlabel">UserName:</div>';
echo '<div class="members_paramvalue">' . $username . '</div>';
echo '<div class="members_upload_link">';

echo '<div class="members_param_div">';
echo '<div class="members_paramlabel">Last Name:</div>';
echo '<div class="members_paramvalue">' . $lastname . '</div>';
echo '</div>';
echo '<div class="members_param_div">';
echo '<div class="members_paramlabel">First Name:</div>';
echo '<div class="members_paramvalue">' . $firstname . '</div>';
echo '</div>';

echo '<div class="inputform_area">' . "\n";
echo '<form action="members_uploadpic.php" method="post" enctype="multipart/form-data">' . "\n";

echo '<div class="inputform_paramblock">' . "\n";
echo '<div class="inputform_label">File to Upload</div>' . "\n";
echo '<input type ="file" name ="PicFile" id="member_photo_fileselect"></input>' . "\n";
echo '</div>' . "\n";
    
if(isset($param_list)) { RenderParams($param_list); }

echo '<input class="inputform_submit_button" type="submit" value="Upload" name="submit" />' . "\n";
echo '</form> </div>' . "\n";

echo '</div>' . "\n";

?>