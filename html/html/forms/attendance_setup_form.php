<?php
// --------------------------------------------------------------------
// attendance_setup_form.php -- HTML fragment to show the user setup form.
//
// Created:  1/20/15 DLB
// --------------------------------------------------------------------

echo '<div class="content_area">';
echo '<h2 class="page_title">Attendance Settings</h2>' . "\n";

if(!empty($success_msg))
{
    echo '<div class="inputform_msg" id="inputform_success_msg" >' . $success_msg . "</div>";
}
if(!empty($error_msg))
{
    echo '<div class="inputform_msg" id="inputform_error_msg" >' . $error_msg . "</div>";
}

echo '<div class="inputform_area">' . "\n";
echo '<form action="attendance_setup.php" method="post">' . "\n";

RenderParams($param_list);

echo '<input class="inputform_submit_button" type="submit" value="Submit">' . "\n";
echo '</form></div>' . "\n";

echo '</div' . "\n";
?>