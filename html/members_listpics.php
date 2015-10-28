<?php
// --------------------------------------------------------------
// members_listpics.php -- Lists users with pictures.
//
// Created: 12/30/14 DLB
// --------------------------------------------------------------------

require_once 'libs/all.php';
session_start();
log_page();
CheckLogin();
CheckEditor();
$loc = 'members_listpics.php';
$timer = new timer();

include 'forms/header.php';
include 'forms/navform.php';
include 'forms/members_menubar.php';

echo '<div class="content_area">' . "\n";
echo '<h2 class="page_title">Team Members by Picture</h2>';

$sql = 'SELECT * FROM UserView ORDER BY FirstName, LastName';
$result = SqlQuery($loc, $sql);

if ($result->num_rows > 0) 
{
    while($row = $result->fetch_assoc()) 
    {
        if($row["Active"] == false) continue;
        $tags = ArrayFromSlashStr($row["Tags"]);
        if(!in_array("member", $tags)) continue;
        $picid = 0;
        if(isset($row["PicID"]) && intval($row["PicID"]) > 0) $picid = intval($row["PicID"]);
        $username = $row["UserName"];
        $lastname = $row["LastName"];
        $firstname = $row["FirstName"];
        $userid = intval($row["UserID"]);
        if($picid > 0) $url = PicUrl($picid, "thumb");
        else           $url = "img/nopic.jpg";
        echo '<div class="members_picframe">' . "\n";
        echo '<div class="members_pic">';
        echo '<a href="members_edituser.php?UserID='. $userid .'"><img src="' . $url . '"></a></div>' . "\n";
        echo '<div class="members_piclabel">' . $firstname . '</div>';
        echo '</div>' . "\n";
    }
} 
else
{
    echo "No Members Exist!!  (How can that be?)";
}
echo '</div>' . "\n";
include 'forms/footer.php';

?>