<?php
// --------------------------------------------------------------------
// admin_edituser_form.php -- HTML fragment to show the edit_user form.
//
// Created: 12/29/14 DLB
// --------------------------------------------------------------------

echo '<div class="content_area">';
echo '<h2 class="page_title">Edit User</h2>' . "\n";

if(!empty($success_msg))
{
    echo '<div class="inputform_msg" id="inputform_success_msg" >' . $success_msg . "</div>";
}
if(!empty($error_msg))
{
    echo '<div class="inputform_msg" id="inputform_error_msg" >' . $error_msg . "</div>";
}

echo '<span style="font-size: 12pt; color: #999999;">UserID:</span>';
echo '<span style="font-size: 14pt; color: black; font-weight: bold;">' . $userid . '</span>';
echo '<span style="font-size: 12pt; color: #999999; margin-left: 20px;">UserName:</span>';
echo '<span style="font-size: 14pt; color: black; font-weight: bold;">' . $username . '</span>';

echo '<div class="inputform_area">' . "\n";
echo '<form action="admin_edituser.php" method="post">' . "\n";

RenderParams($param_list);

echo '<input class="inputform_submit_button" type="submit" value="Submit">' . "\n";
echo '</form></div>' . "\n";

echo 'Leave password blank to keep current one.'; 
echo '<br>';
echo 'Badge IDs must be in the form of "A000", where A is an alpha character and 0 are digits.';

echo '</div' . "\n";
?>