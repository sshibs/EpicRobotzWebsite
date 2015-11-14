<?php
// --------------------------------------------------------------------
// account_setup_form.php -- HTML fragment to show the user setup form.
//
// Created: 12/30/14 DLB
// --------------------------------------------------------------------

echo '<div class="content_area">';
echo '<h2 class="page_title">User Account Setup</h2>' . "\n";

echo '<div class="members_toparea">';
if(!empty($picurl))
{
    echo '<div class="members_pic_div">';
    echo '<div class="members_pic"><img src="' . $picurl . '"></div>';
    echo '</div>';
}

if($havebadge)
{
    echo '<div class="members_showbadge_picarea">';
    echo '<img class="members_showbadge_badge" src="' . $badge_front_url . '">';
    echo '</div>';             

    echo '<div class="members_showbadge_picarea">';
    echo '<img class="members_showbadge_badge" src="' . $badge_back_url . '">';
    echo '</div>';   
}

echo '</div>';

echo '<div style="clear: both;"></div>';


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

echo '<div class="inputform_area">' . "\n";
echo '<form action="account_setup.php" method="post">' . "\n";

RenderParams($param_list);

echo '<input class="inputform_submit_button" type="submit" value="Submit">' . "\n";
echo '</form></div>' . "\n";

echo 'Leave password blank to keep current one.'; 

echo '</div' . "\n";
?>