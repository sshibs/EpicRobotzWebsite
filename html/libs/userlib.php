<?php
// --------------------------------------------------------------------
// userlib:  library fucntions that deal with users and permissions.
//
// Created: 11/22/14 DLB
// Updated: 11/29/14 DLB -- Updated for Epic Admin Website
// --------------------------------------------------------------------

require_once "config.php";
require_once "libs/databaselib.php";
require_once "libs/loglib.php";

// The fuctions in this file are mostly concerned with database
// interaction for the currently logged in user.  Note, that, upon
// a successful login, information about the user is stored in the
// $_SESSION super variable.   This includes preference info. If
// the user changes preference info, it is updated in the $_SESSION
// variable as well as written to the database.

// --------------------------------------------------------------------
// Starts a proper login.  If credentials are not found, false returned. 
// Otherwize true returned, and login session is started.  Set $bypass
// true to ingore password checking.
function StartLogin($name, $pw, $bypass)
{
    global $config;
    $loc = "userlib.php->StartLogin";
    $_SESSION["LoggedIn"] = false;
    
    log_msg($loc, "checking=" . $name . ', bypass=' . TFstr($bypass));
    
    $sql = 'SELECT UserID, UserName, PasswordHash, LastName, FirstName, Tags, Active FROM Users ';
    $sql .= 'WHERE UserName="' . SqlClean($name) . '"';
    $result = SqlQuery($loc, $sql);
    if($result->num_rows < 1) 
    { 
        log_msg($loc, 'Login failure for username: "' . $name. '". User not found.');
        return false; 
    }
    $row = $result->fetch_assoc();
    if(empty($row["Active"])) 
    {
        log_msg($loc, 'Login failure for username "' . $name . '". User not active.');
        return false;
    }

    $pwHash = crypt($pw, $config["Salt"]);
    if($row["PasswordHash"] != $pwHash) 
    {
        if(!$bypass)
        {
            log_msg($loc, 'Login failure for username "' . $name . '". Password mismatch. ');
            return false;
        }
        log_msg($loc, 'User "' . $name . '" used bypass feature to avoid password match.');
    }

    $_SESSION["LoggedIn"] = true;
    $_SESSION["Login_Time"] = time();
    $_SESSION["Login_UserID"] = $row["UserID"];
    $_SESSION["Login_UserName"] = $name;
    $_SESSION["Login_LastName"] = $row["LastName"];
    $_SESSION["Login_FirstName"] = $row["FirstName"];
    $_SESSION["Login_Tags"] = ArrayFromSlashStr($row["Tags"]);
    $_SESSION["Login_IsAdmin"]  = CheckForTag("admin");
    $_SESSION["Login_IsMember"] = CheckForTag("member");
    $_SESSION["Login_IsEditor"] = CheckForTag("editor");
    
    // Get all the current preferences.
    $_SESSION["Prefs"] = GetPrefsForUser(GetUserID());
    
    $lines = array();
    array_push($lines, ">>>>>>>>>>> " . $row["LastName"]. ', ' . $row["FirstName"] );
    array_push($lines, "New Login!  UserName=" . $row["UserName"] . ',   UserID=' . $row["UserID"]);
    array_push($lines, "IP Address= " . $_SERVER["REMOTE_ADDR"]  . "    Tags=" . $row["Tags"]);
    array_push($lines, "Browser=" . $_SERVER["HTTP_USER_AGENT"]);
    log_msg($loc, $lines);
    return true;
}

// --------------------------------------------------------------------
// Returns true if the client is logged in. 
function IsLoggedIn()
{
    if(empty($_SESSION["LoggedIn"])) { return false; }
    if($_SESSION["LoggedIn"]) { return true; }
    return false;
}

// --------------------------------------------------------------------
// Returns true if the client is logged in as an Admin.
function IsAdmin()
{
    if(!IsLoggedIn()) { return false; }
    if(!isset($_SESSION["Login_IsAdmin"])) { return false; }
    if($_SESSION["Login_IsAdmin"] === true) { return true; }
    return false;
}

// --------------------------------------------------------------------
// Returns true if the client is logged in as a Member.
function IsMember()
{
    if(!IsLoggedIn()) { return false; }
    if(!isset($_SESSION["Login_IsMember"])) { return false; }
    if($_SESSION["Login_IsMember"] === true) { return true; }
    return false;
}

// --------------------------------------------------------------------
// Returns true if the client is logged in as an Editor.
function IsEditor()
{
    if(!IsLoggedIn()) { return false; }
    if(!isset($_SESSION["Login_IsEditor"])) { return false; }
    if($_SESSION["Login_IsEditor"] === true) { return true; }
    return false;
}

// --------------------------------------------------------------------
// Returns true if one of the tags is found in the user's tag list.
function CheckForTag($tag)
{
    if(!isset($_SESSION["Login_Tags"])) { return false; }
    foreach($_SESSION["Login_Tags"] as $t)
    {
        if(strtolower($t) == strtolower($tag)) { return true; }
    }
    return false;
}

// --------------------------------------------------------------------
// Returns true if the client is currently logged in.  Will automatically logout after
// 2 days.  If not Logged in, the page will show a login link, but the fuction will not
// return to the caller.
function CheckLogin()
{
    if(!IsLoggedIn())
    {
        log_msg("userlib.php->CheckLogin", array("User is not logged in!  Privilege  violation!",
        "IP Address=" . $_SERVER["REMOTE_ADDR"]));
        include "forms/header.php";
        echo 'You are NOT LOGGED IN.';
        echo '<br>';
        echo '<a href="login.php">Login</a>';
        include "forms/footer.php";
        exit;
    }
    return true;
}

// --------------------------------------------------------------------
// Returns true if the client has admin privileges.  Otherwise, it will
// not return and instead will given an error message to the user with a
// link back to the home page.
function CheckAdmin()
{
    if(IsAdmin()) return true;
    log_msg("userlib.php->CheckAdmin", "User is not Admin!  Privilege  violation!");
    include "forms/header.php";
    echo '<p class="ErrorMsg"> You do not have privilege to access this page.<p>';
    echo '<br>';
    echo '<a href="welcome.php">Back to Start</a>';
    include "forms/footer.php";
    exit;
}

// --------------------------------------------------------------------
// Returns true if the client has editor privileges.  Otherwise, it will
// not return and instead will give an error message to the user with a
// link back to the home page.
function CheckEditor()
{
    if(IsEditor()) return true;
    log_msg("userlib.php->CheckEditor", "User is not Editor!  Privilege  violation!");
    include "forms/header.php";
    echo '<p class="ErrorMsg"> You do not have privilege to access this page.<p>';
    echo '<br>';
    echo '<a href="welcome.php">Back to Start</a>';
    include "forms/footer.php";
    exit;
}

// --------------------------------------------------------------------
// Set Masquerader name.  Special feature that must manage session
// in a careful way.
function SetMasquerader($username)
{
    $_SESSION["Masquerade_User"] = $username;
}

// --------------------------------------------------------------------
// Returns true if masquerading.  False otherwise.
function IsMasquerading()
{
    if(isset($_SESSION["Masquerade_User"])) return true;
    return false;
}

// --------------------------------------------------------------------
// Gets the name of a masquerader, if any. Empty otherwise.
function GetMasquerader()
{
    if(isset($_SESSION["Masquerade_User"])) 
    {
        return $_SESSION["Masquerade_User"];
    }
    return "";
}

// --------------------------------------------------------------------
// Returns the name of the user that is currently logged in, in a nice format.
function UserFormattedName()
{
    if(IsLoggedIn()){ return $_SESSION["Login_FirstName"] . " " . $_SESSION["Login_LastName"]; }
    return "";
}

// --------------------------------------------------------------------
// Returns the name of the user that is currently logged in, in 
// "LastName, FirstName" format.  If no user logged in, empty
// string returned.
function UserLastFirstName()
{
    if(IsLoggedIn()){ return $_SESSION["Login_LastName"] . ", " . $_SESSION["Login_FirstName"]; }
    return "";
}

// --------------------------------------------------------------------
// Returns the user ID, or zero if not logged in.
function GetUserID()
{
    if(!IsLoggedIn()) return 0;
    return intval($_SESSION["Login_UserID"]);
}

// --------------------------------------------------------------------
// Returns the user name of the currently logged in user, or zero if
// no logged in.
function GetUserName()
{
    if(!IsLoggedIn()) return 0;
    return $_SESSION["Login_UserName"];
}

// --------------------------------------------------------------------
// Gets a user ID from a username.  If username not found, false
// returned.
function GetUserIDFromName($username)
{
    $loc = "userlib.php->GetUserIDFromName";
    $sql = 'SELECT UserID From Users WHERE UserName = "' . SqlClean($username) . '"';
    $result = SqlQuery($loc, $sql);
    if($result->num_rows <= 0) return false;
    $row = $result->fetch_assoc();
    $id = $row["UserID"];
    return $id;
}

// --------------------------------------------------------------------
// Gets a user ID from a badge ID.  If not found, false is returned.
function GetUserIDFromBadgeID($badgeid)
{
    $loc = "userlib.php->GetUserIDFromBadgeID";
    $sql = 'SELECT UserID From UserView WHERE BadgeID = "' . SqlClean($badgeid) . '"';
    $result = SqlQuery($loc, $sql);
    if($result->num_rows <= 0) return false;
    $row = $result->fetch_assoc();
    $id = $row["UserID"];
    return $id;
}

// --------------------------------------------------------------------
// Changes the password of the current user.
function ChangePassword($pw)
{
    global $config;
    $loc = "userlib.php-ChangePassword";
    if(!IsLoggedIn()) { return false; }
    if(empty($pw)) { return false; }
    
    $pwhash = crypt($pw, $config["Salt"]);
    $sql = "UPDATE Users SET PasswordHash=\"" . $pwhash . "\" WHERE UserID=" . GetUserID();
    $result = SqlQuery($loc, $sql);
    log_msg($loc, "Password Changed.");
    return true;
}

// --------------------------------------------------------------------
// Gets a single preference for the current user.  If the preference
// does not exist, the given default is returned.
function GetPref($PrefName, $default="")
{
    if(!IsLoggedIn()) { return $default; }
    if(!isset($_SESSION["Prefs"])) {
        DieWithMsg("userlib.php->GetPref", '$_SESSION ["Prefs"] Not set!'); 
    }
    if(!isset($_SESSION["Prefs"][$PrefName])) { return $default; }
    return $_SESSION["Prefs"][$PrefName];
}

// --------------------------------------------------------------------
// Saves one preference for the current user.  The preference is saved
// to the database as well as to the current session.
function SavePref($PrefName, $PrefValue)
{
    if(!IsLoggedIn()) 
    {
        DieWithMsg("userlib.php->SavePref", "Call to SavePref while not logged in.");
    }
    if(!isset($_SESSION["Prefs"])) 
    {
        DieWithMsg("userlib.php->SavePref", '$_SESSION["Prefs"] Not set!'); 
    }
    $_SESSION["Prefs"][$PrefName] = $PrefValue;
    SavePrefsForUser(GetUserID(), $_SESSION["Prefs"]);
}

// --------------------------------------------------------------------
// Creates a new user.  Returns true if successful.  Otherwise
// returns an error message that is suitable for display.
// The input is an associtive array with the following
// required keys: LastName, FirstName, UserName, Password.  Optional
// keys are NickName, Title, BadgeID, Email, Tags, Active.  If failure
// results from bad inputs, or database problems -- DieWithMsg is called.
// Non-serious failurs return an explianation string.  On success, true
// is returned.
function CreateNewUser($params)
{
    global $config;
    $loc = "userlib.php->CreateNewUser";
    
    // Check inputs
    if(!isset($params["LastName"]) ||
       !isset($params["FirstName"]) ||
       !isset($params["UserName"]) ||
       !isset($params["Password"]))
    { DieWithMsg($loc, "Required input keys not found."); }
    if(empty($params["LastName"])) {return "Last name cannot be empty."; }
    if(empty($params["FirstName"])) {return "First name cannot be empty."; }
    if(empty($params["UserName"])) {return "Username cannot be empty."; }
    if(empty($params["Password"])) {return "Password cannot be empty."; }
    
    $username  = SqlClean($params["UserName"]);
    $lastname  = SqlClean($params["LastName"]);
    $firstname = SqlClean($params["FirstName"]);

    $nickname = "";
    $title = "";
    $badgeid = "";
    $email = "";
    $tags = "";
    $active = false;
    if(isset($params["NickName"])) { $nickname = SQLClean($params["NickName"]); }
    if(isset($params["Title"]))    { $title    = SQLClean($params["Title"]); }
    if(isset($params["BadgeID"]))  { $badgeid  = SQLClean($params["BadgeID"]); }
    if(isset($params["Email"]))    { $email    = SQLClean($params["Email"]); }
    if(isset($params["Tags"]))     { $tags     = SQLClean($params["Tags"]); }
    if(isset($params["Active"]))   { $active   = $params["Active"]; }
    
    // Check for duplicate username.
    $sql = 'SELECT UserID FROM Users WHERE UserName="' . $username . '"';
    $result = SqlQuery($loc, $sql);
    if($result->num_rows > 0)
    {
        $msg = 'Unable to add new user. Duplicate username. (' . $username . ')';
        log_msg($loc, $msg);
        return $msg;
    }
    
    // Check for duplicate first/last name
    $sql = 'SELECT UserID FROM Users WHERE LastName="' . 
           $lastname  . '" AND FirstName="' .
           $firstname . '"';
    $result = SqlQuery($loc, $sql);
    if($result->num_rows > 0)
    {
        $msg = 'Unable to add new user. Duplicate first/last name. (' .
               $lastname . ', ' . $firstname . ')';
        log_msg($loc, $msg);
        return $msg;
    }
    
    // Check for invalid BadgeID.
    if(!VerifyBadgeFormat($badgeid))
    {
        $msg = 'Bad Badge Format.  Must be in form of "A000".';
        log_msg($loc, $msg);
        return $msg;
    }
    
    if(!blank($badgeid))
    {
        // Check for duplicate BadgeID
        $sql = 'SELECT UserID FROM Users WHERE BadgeID="' . $badgeid . '"';
        $result = SqlQuery($loc, $sql);
        if($result->num_rows > 0)
        {
            $msg = 'Unable to add new user. Duplicate BadgeID. (' . $badgeid . ').';
            log_msg($loc, $msg);
            return $msg;
        }
    }
    
    // Build the sql to add user.
    $pwhash = crypt($params["Password"], $config["Salt"]);
    $sql = 'INSERT INTO Users (UserName, PasswordHash, LastName, FirstName, NickName, ' .
           'Title, BadgeID, Email, Tags, Active) ';
    $sql .= ' VALUES(';
    $sql .= '  "' . $username  . '"';
    $sql .= ', "' . $pwhash    . '"';
    $sql .= ', "' . $lastname  . '"';
    $sql .= ', "' . $firstname . '"';
    $sql .= ', "' . $nickname  . '"';
    $sql .= ', "' . $title     . '"';
    $sql .= ', "' . $badgeid   . '"';
    $sql .= ', "' . $email     . '"';
    $sql .= ', "' . $tags      . '"';
    $sql .= ', '  . TFstr($active);
    $sql .= ')';

    $result = SqlQuery($loc, $sql);
    log_msg($loc, 
       array("New User added!  Username=" . $username ,
       "Full name= " . $lastname . ', ' . $firstname, 
       "tags=" . $tags . ", Active=" . TFstr($active)));
    return true;
}

// --------------------------------------------------------------------
// Given the user id, returns a associative array, with the following
// keys: UserID, UserName, LastName, FirstName, NickName, Email, 
// Tags, Active. False returned if user not found.
function GetUserInfo($userid)
{
    $loc = "userlib.php->GetUserInfo";
    $sql = 'SELECT * FROM UserView WHERE UserID=' . SqlClean($userid);
    $result = SqlQuery($loc, $sql);
    if($result->num_rows != 1) { return false; }
    $row = $result->fetch_assoc();
    return $row;
}

// --------------------------------------------------------------------
// Given the badge id, returns a associative array, with the following
// keys: UserID, UserName, LastName, FirstName, NickName, Email, 
// Tags, Active. False returned if user not found.
function GetUserInfoFromBadgeID($badgeid)
{
    $loc = "userlib.php->GetUserInfo";
    $sql = 'SELECT * FROM UserView WHERE BadgeID="' . SqlClean($badgeid) . '"';
    $result = SqlQuery($loc, $sql);
    if($result->num_rows != 1) { return false; }
    $row = $result->fetch_assoc();
    return $row;
}

// --------------------------------------------------------------------
// Updates info about a user.  The input is a param_list for the 
// fields that need updating. Possible fields names are:
// UserName, Password, LastName, FirstName, NickName, Title, BadgeID, 
// Email, Tags, Active.  Note that the UserName of an account cannot
// be changed. If it is included in the param_list, it is used to find
// the account to change.  If, however, $userid is proivded, then that
// is used the find the account. True is returned on success, otherwise
// an error message suitable for display is returned.  Bad SQL errors
// will die. 

function UpdateUser($param_list, $userid=0)
{
    global $config;
    $loc = "userlib.php->UpdateUser";
    $pwchanged = false;
    
    $fields = array(array("LastName",     "str"),
                    array("FirstName",    "str"),
                    array("PasswordHash", "str"),
                    array("NickName",     "str"),
                    array("Title",        "str"),
                    array("BadgeID",      "str"),
                    array("Email",        "str"),
                    array("Tags",         "str"),
                    array("Active",       "bool"));
    
    if($userid != 0)
    {
        $sql = "SELECT * FROM Users WHERE UserID=" . intval($userid);
        $result = SqlQuery($loc, $sql);
        if($result->num_rows <= 0) 
        {
            $error_msg = "Unable to update user. UserID=" . intval($userid) . " not found.";
            log_msg($loc, $error_msg);
            return $error_msg;
        }
    }
    else
    {
        if(!IsFieldInParamList("UserName", $param_list))
        {
            $error_msg = 'Unable to update user. No UserName or UserID Given.';
            log_msg($loc, $error_msg);
            return $error_msg;
        }
        $username = GetValueFromParamList($param_list, "UserName");
        $sql = 'SELECT * FROM Users WHERE UserName="' . SqlClean($username) . '"';
        $result = SqlQuery($loc, $sql);
        if($result->num_rows <= 0) 
        {
            $error_msg = 'Unable to update user. UserName="' . SqlClean($username) . '" not found.';
            log_msg($loc, $error_msg);
            return $error_msg;
        }
        $row = $result->fetch_assoc();
        $userid = intval($row["UserID"]);
    }
    
    // If the BadgeID is being changed we need to make sure its not a duplicate.
    if(IsFieldInParamList("BadgeID", $param_list))
    {
        $badgeid = GetValueFromParamList($param_list, "BadgeID");
        if(!blank($badgeid))
        {
            if(!VerifyBadgeFormat($badgeid))
            {
                $error_msg = 'Unable to update user. Bad Format for BadgeID. Must be in form of "A000".';
                log_msg($loc, $error_msg);
                return $error_msg;
            }
            $sql = 'SELECT UserID FROM Users WHERE BadgeID="' . $badgeid . '"';
            $result = SqlQuery($loc, $sql);
            while ($row = $result->fetch_assoc())
            {
                if($row["UserID"] != $userid) 
                {
                    $error_msg = 'Unable to update user. BadgeID ' . $badgeid . ' already in use.';
                    log_msg($loc, $error_msg);
                    return $error_msg;
                }
            }
        }
    }
        
    // At this point, move all values into a seperate array, but treat password special.
    $data = array();
    $c = 0;
    foreach($param_list as $param_spec)
    {
        if(!isset($param_spec["FieldName"])) continue;
        if(!isset($param_spec["Value"])) continue;
        if($param_spec["FieldName"] == "Password") 
        {
            $pw = $param_spec["Value"];
            if(empty($pw)) continue;
            $v = crypt($pw, $config["Salt"]);
            $pwchanged = true;
            $fn = "PasswordHash";
            $data[$fn] = $v;
            $c++;
            continue;  
        }
        $fn = $param_spec["FieldName"];
        $v  = $param_spec["Value"];
        $data[$fn] = $v;
        $c++;
    }
    
    if($c <= 0) 
    {
        $error_msg = "Unable to update user. UserID=" . intval($userid) . ". Nothing to update.";
        log_msg($loc, $error_msg);
        return $error_msg;
    }
    
    // At this point, we have a userid that we can count on, and the data.
    $sql  = 'UPDATE Users SET ';
    $sql  .= GenerateSqlSet($data, $fields);
    $sql  .= " WHERE UserID=" . intval($userid);
    SqlQuery($loc, $sql);
    
    $msg = 'Info for User ' . $userid . ' updated by ' . GetUserName() . '. ';
    if($pwchanged) $msg .= '(Including a password change.)';
    log_msg($loc, $msg);
    return true;
}

// --------------------------------------------------------------------
// Verify BadgeID format.  Badge IDs must be either blank (no ID) or
// exactly four characters consisting of a capital letter followed by
// 3 digits. True returned if okay, false otherwise.
function VerifyBadgeFormat($BadgeID)
{
    if(blank($BadgeID)) return true;
    if(strlen($BadgeID) != 4) return false;
    if(!is_alpha(substr($BadgeID, 0, 1))) return false;
    if(!is_digit(substr($BadgeID, 1, 1))) return false;
    if(!is_digit(substr($BadgeID, 2, 1))) return false;
    if(!is_digit(substr($BadgeID, 3, 1))) return false;
    return true;
}

?>