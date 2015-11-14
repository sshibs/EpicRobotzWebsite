<?php
// --------------------------------------------------------------
// badges_showall.php -- Shows all Badges
//
// Created: 12/31/14 DLB
// --------------------------------------------------------------------

require_once 'libs/all.php';
session_start();
log_page();
CheckLogin();
CheckEditor();
$loc = 'badges_showall.php';
$timer = new timer();
$error_msg = "";
$success_msg = "";

if( $_SERVER["REQUEST_METHOD"] == "GET")
{
    $action = "";
    if(isset($_GET["action"])) $action = $_GET["action"];
    if($action == 'makeall') 
    {
        $success_msg = MakeAllBadges();
    }
    if($action == 'makegif') 
    {
        $success_msg = MakeGifImages();
    }
    
}
else
{
    DieWithMsg($loc, "Page should not be invoked by POST.");
}

include "forms/header.php";
include "forms/navform.php";
include "forms/badges_menubar.php";

echo '<div class="content_area">' . "\n";
echo '<h2 class="page_title">All Badges</h2>';

if(!empty($success_msg))
{
    echo '<div class="inputform_msg" id="inputform_success_msg" >' . $success_msg . "</div>";
}
if(!empty($error_msg))
{
    echo '<div class="inputform_msg" id="inputform_error_msg" >' . $error_msg . "</div>";
}

$sql = 'SELECT * FROM UserView ORDER BY BadgeID';
$result = SqlQuery($loc, $sql);

if ($result->num_rows > 0) 
{
    while($row = $result->fetch_assoc()) 
    {
        if($row["Active"] == false) continue;
        $tags = ArrayFromSlashStr($row["Tags"]);
        if(!in_array("member", $tags)) continue;
        $badgeid = $row["BadgeID"];
        $userid  = $row["UserID"];
        $havebadge = BadgeExists($badgeid);
        if(!$havebadge) continue;
        $badge_front_url = GetBadgeUrl($badgeid, 'front');
        echo '<div class="badges_badgeframe">' . "\n";
        echo '<div class="badges_pic">';
        echo '<a href="badges_showbadge.php?UserID='. $userid .'"><img src="' . $badge_front_url . '"></a></div>' . "\n";
        echo '<div class="badges_badgelabel">' . $badgeid . '</div>';
        echo '</div>' . "\n";
    }
} 
else
{
    echo "No Badges Exist!!";
}
echo '</div>' . "\n";
include 'forms/footer.php';

// --------------------------------------------------------------------
// Makes badges for all users that are members, and have badge IDs. 
function MakeAllBadges()
{
    $loc = 'badges_showall.php->MakeAllBadges';
    $sql = 'SELECT * FROM UserView ORDER BY BadgeID';
    $result = SqlQuery($loc, $sql);
    $nempty = 0;
    $nmade = 0;
    $nfail = 0;
    while($row = $result->fetch_assoc()) 
    {
        if($row["Active"] == false) continue;
        $tags = ArrayFromSlashStr($row["Tags"]);
        if(!in_array("member", $tags)) continue;
        $badgeid = $row["BadgeID"];
        if(empty($badgeid)) {$nempty++; continue; }
        $r = MakeBadge($row);
        if($r === true) $nmade++;
        else            $nfail++;
    }
    $status = 'Badges Made: ' . $nmade . ', Members without BadgeIDs: ' . $nempty;
    if($nfail > 0) $status .= ', Failures: ' . $nfail . '. (See sys log!)';
    log_msg($loc, array('All badges remade!', $status));
    return $status;
}

// --------------------------------------------------------------------
// Makes gif images for all users that are members, and have badge IDs.
// The gif images are named with the badge id, and put in the 
// directory uploads/gifs. 
function MakeGifImages()
{
    $loc = 'badges_showall.php->MakeAllBadges';
    $sql = 'SELECT * FROM UserView ORDER BY BadgeID';
    $result = SqlQuery($loc, $sql);
    $nempty = 0;
    $nmade = 0;
    $nfail = 0;
    while($row = $result->fetch_assoc()) 
    {
        if($row["Active"] == false) continue;
        $tags = ArrayFromSlashStr($row["Tags"]);
        if(!in_array("member", $tags)) continue;
        $badgeid = $row["BadgeID"];
        if(empty($badgeid)) {$nempty++; continue; }
        $r = MakeGif($row);
        if($r === true) $nmade++;
        else            $nfail++;
    }
    $status = 'Images Made: ' . $nmade . ', Members without BadgeIDs: ' . $nempty;
    if($nfail > 0) $status .= ', Failures: ' . $nfail . '. (See sys log!)';
    log_msg($loc, array('All gif images made!', $status));
    return $status;
}

?>
