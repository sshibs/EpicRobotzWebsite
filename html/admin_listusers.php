<?php
// --------------------------------------------------------------
// admin_listusers.php -- Lists users from admin page.
//
// Created: 12/29/14 DLB
// --------------------------------------------------------------------

require_once 'libs/all.php';
session_start();
log_page();
CheckLogin();
CheckAdmin();
$loc = 'admin_listusers.php';
$timer = new timer();
$browser_width = 900;

include 'forms/header.php';
include 'forms/navform.php';
include 'forms/admin_menubar.php';

echo '<div class="content_area">' . "\n";
echo '<h2 class="page_title">User Accounts</h2>';

$sql = 'SELECT UserID, UserName, LastName, FirstName, NickName, Title, BadgeID, Email, Tags, Active FROM Users ORDER BY LastName, FirstName';
$result = SqlQuery($loc, $sql);

if ($result->num_rows > 0) {
    // output data of each row
    echo "<br>\n";
    echo '<table class="admin_userlist">' . "\n<tr>\n";
    echo "<th align=right width=20><u>ID</u></th>";
    echo "<th width=10> </th>";
    echo "<th align=left width=100><u>Username</u></th>";
    echo "<th align=left width=140><u>Last Name</u></th>";
    echo "<th align=left width=140><u>First Name</u></th>";
    echo "<th align=left width=150><u>Title</u></th>";
    echo "<th align=left width=60><u>BadgeID</u></th>";
    echo "<th align=left width=100><u>Active</u></th>";
    echo "<th align=left width=100><u>Tags</u></th></tr>\n";
    
    while($row = $result->fetch_assoc()) {
        echo "\n<tr>";
        echo '<th align=right>' . $row["UserID"]        . "</th>";
        echo '<th> </th>';
        echo '<th align=left> <a href="admin_edituser.php?UserID=' . $row["UserID"] . '">' . $row["UserName"] . '</a></th>';
        echo '<th align=left>'  . $row["LastName"]      . '</th>';
        echo '<th align=left>'  . $row["FirstName"]     . '</th>';
        echo '<th align=left>'  . $row["Title"]         . '</th>';
        echo '<th align=left>'  . $row["BadgeID"]       . '</th>';
        echo '<th align=left>'  . TFstr($row["Active"]) . '</th>';
        echo '<th align=left>'  . $row["Tags"]          . '</th>';
        echo "</tr>\n";
    }
    echo "</table>\n";
} else {
    echo "No Users Exist!!  (How can that be?)";
}

echo '</div>' . "\n";
include 'forms/footer.php';

?>
