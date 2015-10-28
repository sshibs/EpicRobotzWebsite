<?php
// --------------------------------------------------------------
// members_list.php -- Lists users from the members page.
//
// Created: 12/29/14 DLB
// --------------------------------------------------------------------

require_once 'libs/all.php';
session_start();
log_page();
CheckLogin();
CheckEditor();
$loc = 'members_list.php';
$timer = new timer();
//$browser_width = 900;

include 'forms/header.php';
include 'forms/navform.php';
include 'forms/members_menubar.php';

echo '<div class="content_area">' . "\n";
echo '<h2 class="page_title">List of Team Members</h2>';

$sql = 'SELECT * FROM UserView ORDER BY LastName, FirstName';
$result = SqlQuery($loc, $sql);

if ($result->num_rows > 0) {
    // output data of each row
    echo "<br>\n";
    echo '<table class="members_userlist">' . "\n<tr>\n";
    echo "<th align=left width=150><u>Last Name</u></th>";
    echo "<th align=left width=100><u>First Name</u></th>";
    echo "<th align=left width=120><u>Title</u></th>";
    echo "<th align=left width=80><u>Badge ID</u></th>";
    echo "<th align=left width=80><u>Has Pic?</u></th>";
    
    while($row = $result->fetch_assoc()) {
        if($row["Active"] == false) continue;
        $tags = ArrayFromSlashStr($row["Tags"]);
        if(!in_array("member", $tags)) continue;
        $haspic = "NO";
        if(isset($row["PicID"]) && intval($row["PicID"]) > 0) $haspic = "";
        echo "\n<tr>";
        echo '<th align=left> <a href="members_edituser.php?UserID=' . $row["UserID"] . '">' . $row["LastName"] . '</a></th>';
        echo '<th align=left>'  . $row["FirstName"]     . '</th>';
        echo '<th align=left>'  . $row["Title"]         . '</th>';
        echo '<th align=left>'  . $row["BadgeID"]       . '</th>';
        echo '<th align=left>'  . $haspic               . '</th>';
        echo "</tr>\n";
    }
    echo "</table>\n";
} else {
    echo "No Members Exist!!  (How can that be?)";
}

echo '</div>' . "\n";
include 'forms/footer.php';

?>