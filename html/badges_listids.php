<?php
// --------------------------------------------------------------------
// badges_listids.php -- The main badges page. 
//
// Created: 12/31/14 DLB
// --------------------------------------------------------------------

require_once "libs/all.php";
session_start();
log_page();
CheckLogin();
CheckEditor();
$loc = 'badges_listids.php';
$timer = new Timer();

include "forms/header.php";
include "forms/navform.php";
include "forms/badges_menubar.php";
echo '<div class="content_area">';
echo '<h2>List of Known Badge IDs</h2>';

$sql = 'SELECT * FROM UserView ORDER BY BadgeID';
$result = SqlQuery($loc, $sql);

if ($result->num_rows > 0) {
    // output data of each row
    echo "<br>\n";
    echo '<table class="members_userlist">' . "\n<tr>\n";
    echo "<th align=left width=80><u>Badge ID</u></th>";
    echo "<th align=left width=200><u>Name</u></th>";
    echo "<th align=left width=100><u>Has Badge?</u></th>";
    echo "<th align=left width=100><u>Has Pic?</u></th>";
    
    while($row = $result->fetch_assoc()) {
        if($row["Active"] == false) continue;
        $tags = ArrayFromSlashStr($row["Tags"]);
        if(!in_array("member", $tags)) continue;
        $badgeid = $row["BadgeID"];
        if(empty($badgeid)) continue;
        
        $haspic = "No";
        $havebadge = false;
        $badgeid = $row["BadgeID"];
        $havebadge = BadgeExists($badgeid);
        if(isset($row["PicID"]) && intval($row["PicID"]) > 0) $haspic = "Yes";
        
        echo "\n<tr>";
        if($havebadge) {echo '<th align=left> <a href="badges_showbadge.php?UserID=' . $row["UserID"] . '">' . $row["BadgeID"] . '</a></th>'; }
        else           {echo '<th align=left>' . $badgeid . '</th>' . "\n"; }
        if($havebadge) $havebadge = 'Yes';
        else           $havebadge = 'No';
        
        echo '<th align=left>'  . $row["FirstName"] . ' ' . $row["LastName"] . '</th>';
        echo '<th align=left>'  . $havebadge            . '</th>';
        echo '<th align=left>'  . $haspic               . '</th>';
        echo "</tr>\n";
    }
    echo "</table>\n";
} else {
    echo "No Badges Exist!!  (How can that be?)";
}

echo '</div>';
include "forms/footer.php";

?>