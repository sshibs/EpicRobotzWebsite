<?php
// --------------------------------------------------------------------
// badges_showbadge.php -- Shows one person's badge.
//
// Created: 12/31/14 DLB
// --------------------------------------------------------------------

require_once "libs/all.php";
session_start();
log_page();
CheckLogin();
CheckEditor();
$loc = 'badges_showbadge.php';
$timer = new Timer();
$action = "";
$error_msg = "";
$success_msg = "";

if( $_SERVER["REQUEST_METHOD"] == "GET")
{
    if(empty($_GET["UserID"]))
    {
        DieWithMsg($loc, "Bad Page Invoke. No UserID given.");
    }
    
    if(isset($_GET["action"])) $action = $_GET["action"];
    
    $userid = $_GET["UserID"];
    $data = GetUserInfo($userid);
    if($data === false) DieWithMsg($loc, 'User with ID=' . $userid . ' not found.');
    $username  = $data["UserName"];
    $firstname = $data["FirstName"];
    $lastname  = $data["LastName"];
    $nickname  = $data["NickName"];
    $title     = $data["Title"];
    $picid = GetPicIDForUserID($userid);
    if($picid > 0) $picurl = PicUrl($picid, "thumb");
    else           $picurl = "";
    $badgeid = $data["BadgeID"];
    
    if($action == "make")
    {
        $result = MakeBadge($data);
        if($result === true) $success_msg = "Badge Successfully Made!";
        else $error_msg = $result;
    }

    $havebadge = BadgeExists($badgeid);
    $badge_front_url = GetBadgeUrl($badgeid, 'front');
    $badge_back_url  = GetBadgeUrl($badgeid, 'back');
    goto GenerateHtml;
}
else
{
    DieWithMsg($loc, "Page should not be invoked by POST.");
}

GenerateHtml:
include "forms/header.php";
include "forms/navform.php";
include "forms/badges_menubar.php";
include "forms/badges_showbadge_form.php";
include "forms/footer.php";

?>