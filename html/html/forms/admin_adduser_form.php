<?php
// --------------------------------------------------------------------
// admin_adduser_form.php -- HTML fragment to show the add_user form.
//
// Created: 12/29/14 DLB
// --------------------------------------------------------------------

echo '<div class="content_area">';
echo '<h2 class="page_title">Add New User</h2>' . "\n";

if(!empty($success_msg))
{
    echo '<div class="inputform_msg" id="inputform_success_msg" >' . $success_msg . "</div>";
}
if(!empty($error_msg))
{
    echo '<div class="inputform_msg" id="inputform_error_msg" >' . $error_msg . "</div>";
}

echo '<div class="inputform_area">' . "\n";
echo '<form action="admin_adduser.php" method="post">' . "\n";

RenderParams($param_list);

echo '<input class="inputform_submit_button" type="submit" value="Add User">' . "\n";
echo '</form></div>' . "\n";

echo 'Badge IDs must be in the form of "A000", where A is an alpha character and 0 are digits.';
echo '</div' . "\n";
?>
