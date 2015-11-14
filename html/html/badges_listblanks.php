<?php
// --------------------------------------------------------------------
// badges_listblanks.php -- Lists memebers without badge ids.
//
// Created: 12/31/14 DLB
// --------------------------------------------------------------------

require_once "libs/all.php";
session_start();
log_page();
CheckLogin();
CheckEditor();
$loc = 'badges_listblanks.php';
$timer = new Timer();

include "forms/header.php";
include "forms/navform.php";
include "forms/badges_menubar.php";
echo '<div class="content_area">';
echo '<h2>List of Members without Badge IDs.</h2>';

$sql = 'SELECT * FROM UserView ORDER BY LastName, FirstName';
$result = SqlQuery($loc, $sql);

// First, filter rows that have BadgeIDs.
$data = array();
while($row = $result->fetch_assoc()) 
{
    if($row["Active"] == false) continue;
    $tags = ArrayFromSlashStr($row["Tags"]);
    if(!in_array("member", $tags)) continue;
    $badgeid = $row["BadgeID"];
    if(!empty($badgeid)) continue;
    $data[] = $row;
}

if(count($data) <= 0)
{
    echo '<p>All members have badge IDs!</p>';
}
else 
{
    echo "<br>\n";
    echo '<table class="members_userlist">' . "\n<tr>\n";
    echo "<th align=left width=100><u>UserName</u></th>";
    echo "<th align=left width=150><u>Last Name</u></th>";
    echo "<th align=left width=100><u>First Name</u></th>";
    echo "</tr>";
    
    foreach($data as $row)
    {
        echo "\n<tr>";
        echo '<th align=left> <a href="members_edituser.php?UserID=' . $row["UserID"] . '">' . $row["UserName"] . '</a></th>';
        echo '<th align=left>'  . $row["LastName"]  . '</th>';
        echo '<th align=left>'  . $row["FirstName"] . '</th>';
        echo "</tr>\n";
    }
    echo "</table>\n";
}

echo '</div>';
include "forms/footer.php";

?>