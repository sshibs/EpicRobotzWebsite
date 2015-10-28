<?php
// --------------------------------------------------------------------
// badges_showbadge_form.php -- HTML fragment to show a badge.
//
// Created: 12/31/14 DLB
// --------------------------------------------------------------------

echo '<div class="content_area">';
echo '<h2>Badge For ' . $firstname . ' ' . $lastname . '</h2>';

if(!empty($success_msg))
{
    echo '<div class="inputform_msg" id="inputform_success_msg" >' . $success_msg . "</div>";
}
if(!empty($error_msg))
{
    echo '<div class="inputform_msg" id="inputform_error_msg" >' . $error_msg . "</div>";
}

if($havebadge) 
{
    echo '<div class="badges_showbadge_picarea">';
    echo '<a href="' . $badge_front_url . '" download>';
    echo '<img class="badges_showbadge_badge" src="' . $badge_front_url . '">';
    echo '</a>';
    echo '<div class="badges_badgelabel">Front</div></div>';             

    echo '<div class="badges_showbadge_picarea">';
    echo '<a href="' . $badge_back_url . '" download>';
    echo '<img class="badges_showbadge_badge" src="' . $badge_back_url . '">';
    echo '</a>';
    echo '<div class="badges_badgelabel">Back</div></div>';             
}

echo '<div class="badges_showbadge_memberInfo">';

echo '<div class="badges_param_div">';
echo '<div class="badges_paramlabel">User ID:</div>';
echo '<div class="badges_paramvalue">' . $userid . '</div>';
echo '</div>';

echo '<div class="badges_param_div">';
echo '<div class="badges_paramlabel">Badge ID:</div>';
echo '<div class="badges_paramvalue">' . $badgeid . '</div>';
echo '</div>';

echo '<div class="badges_param_div">';
echo '<div class="badges_paramlabel">User Name:</div>';
echo '<div class="badges_paramvalue">' . $username . '</div>';
echo '</div>';

echo '<div class="badges_param_div">';
echo '<div class="badges_paramlabel">Last Name:</div>';
echo '<div class="badges_paramvalue">' . $lastname . '</div>';
echo '</div>';

echo '<div class="badges_param_div">';
echo '<div class="badges_paramlabel">First Name:</div>';
echo '<div class="badges_paramvalue">' . $firstname . '</div>';
echo '</div>';

echo '<div class="badges_param_div">';
echo '<div class="badges_paramlabel">Nick Name:</div>';
echo '<div class="badges_paramvalue">' . $nickname . '</div>';
echo '</div>';

echo '<div class="badges_param_div">';
echo '<div class="badges_paramlabel">Title:</div>';
echo '<div class="badges_paramvalue">' . $title . '</div>';
echo '</div>';

if(IsEditor()) 
{
    echo '<div class="badges_params_link">';
    echo '<a href="members_edituser.php?UserID=' . $userid . '">Edit Member</a>';
    echo '</div>';
}

echo '<div class="badges_params_link">';
echo '<a href="badges_print.php?BadgeID=' . $badgeid . '">Print Badge</a>';
echo '</div>';

echo '<div class="badges_params_link">';
echo '<a href="badges_printsticker.php?BadgeID=' . $badgeid . '">Print Sticker</a>';
echo '</div>';

echo '</div>';

echo '<div style="clear:both;"></div>';
echo '<p>Click on badge to download 300dpi image for printing.</p>';

echo '</div' . "\n";
?>