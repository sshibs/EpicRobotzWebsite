<?php
// --------------------------------------------------------------------
// Given the user id, returns a associative array, with the following
// keys: UserID, UserName, LastName, FirstName, NickName, Email, 
// Tags, Active. False returned if user not found.
function GetWorkOrderInfo($workorderid)
{
    $loc = "userlib.php->GetUserInfo";
    $sql = 'SELECT * FROM WorkOrders WHERE WorkOrderID=' . SqlClean($workorderid);
    $result = SqlQuery($loc, $sql);
    if($result->num_rows != 1) { return false; }
    $row = $result->fetch_assoc();
    return $row;
}
// --------------------------------------------------------------------
// Given the user id, returns a associative array, with the following
// keys: UserID, UserName, LastName, FirstName, NickName, Email, 
// Tags, Active. False returned if user not found.
function GetWorkOrderPrereqInfo($userid)
{
    $loc = "userlib.php->GetUserInfo";
    $sql = 'SELECT * FROM UserView WHERE UserID=' . SqlClean($userid);
    $result = SqlQuery($loc, $sql);
    if($result->num_rows != 1) { return false; }
    $row = $result->fetch_assoc();
    return $row;
}

?>
