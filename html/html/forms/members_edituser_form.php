<?php
// --------------------------------------------------------------------
// member_edituser_form.php -- HTML fragment to show the edit_user form.
//
// Created: 12/30/14 DLB
// --------------------------------------------------------------------

echo '<div class="content_area">';
echo '<h2 class="page_title">Edit Member Data</h2>' . "\n";

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

echo '<div class="members_link_area">';

echo '<div class="members_link_line">';
echo '<a href="badges_showbadge.php?UserID=' . $userid . '&action=make">Make Badge</a>';
echo '</div>';

echo '<div class="members_link_line">';
echo '<a href="members_uploadpic.php?UserID=' . $userid . '">Upload Picture</a>';
echo '</div>';

if($havebadge)
{
    echo '<div class="members_link_line">';
    echo '<a href="badges_print.php?BadgeID=' . $badgeid . '">Print Badge</a>';
    echo '</div>';

    echo '<div class="members_link_line">';
    echo '<a href="badges_printsticker.php?BadgeID=' . $badgeid . '">Print Sticker</a>';
    echo '</div>';
}

echo '</div>';
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
echo '<form action="members_edituser.php" method="post">' . "\n";

RenderParams($param_list);

echo '<input class="inputform_submit_button" type="submit" value="Submit">' . "\n";
echo '</form></div>' . "\n";

echo 'Leave password blank to keep current one.'; 
echo '<br>';
echo 'Badge IDs must be in the form of "A000", where A is an alpha character and 0 are digits.';

echo '</div>' . "\n";
?>