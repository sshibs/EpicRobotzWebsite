<?php
// --------------------------------------------------------------------
// preflib:  library fucntions that deal with storing preferences.
//
// Created: 12/06/14 DLB
// --------------------------------------------------------------------

require_once "config.php";
require_once "libs/databaselib.php";
require_once "libs/loglib.php";

// These functions work with the database table "Prefs" to implement 
// a system that stores and recalls prefereces for a given user.
//
// Preferences are simple key/value pairs stored for a user. For
// example, "BrowserWidth=600".   Any number of preferences can
// be stored for a user, and the keys do not need to be the same
// set from one user to another (but that noramlly would be a 
// good idea).  Keys and values are always strings. Values can
// can be up to 256 chars long. Keys can be up to 32 chars long.
// (Actually, see CreatePrefs.sql for the latest size limitations.)
//
// All prefereces for a given user are retrived from the database
// at the same time.  However, they can be writen to the database
// in smaller goups.  For example, on Retrive, say you get A,B,C,D,E.
// You can then change the value for B, and store just B.  The other
// preferences are not changed. If then you store M by it self, that does
// not remove/delete the others, and now you would have A,B,C,D,E,M.
//
// You can delete all prefereces for a user.
//
// NOTE: the functions provided in this file are "low level".  They
// are used by userlib.php to get and set preferences for the user
// that is currently logged in.  See comments in userlib.php for
// more infomation.
//

// --------------------------------------------------------------------
// Get all preferences in an associative array for a given user.
function GetPrefsForUser($userid)
{
    $loc = "preflib.php->GetPrefsForUser";
    $sql = "SELECT PrefName, PrefValue From Prefs WHERE UserID=" . intval($userid);
    $result = SqlQuery($loc, $sql);
    $output = array();
    while($row = $result->fetch_assoc()) 
    {
        $key = $row["PrefName"];
        $value = $row["PrefValue"];
        $output[$key] = $value;
    }
    log_msg('preflib.php->GetPrefsForUser', 
      count($output) . ' preferences retrieved successfully for user ' . intval($userid));
    return $output;
}

// --------------------------------------------------------------------
// Save preferences for a given user.  Input is an indexed array.
function SavePrefsForUser($userid, $prefs)
{
    $loc = "preflib.php->SavePrefsForUser";
    
    // First, start with current set of preferences so that we 
    // don't duplicate any new ones.
    $current_prefs = GetPrefsForUser($userid);
    
    // Separate the new prefs into those that already exist,
    // and those that are truely new.
    $new_prefs = array();
    $changed_prefs = array();
    foreach($prefs as $key => $value)
    {
        if(array_key_exists($key, $current_prefs)) 
        {
            // The key is alreay in the database. If the value is the
            // same, then we don't need to re-save it.
            if($value != $current_prefs[$key])
            {
                $changed_prefs[$key] = $value;
            }
        }
        else
        {
            // The key is new.  
            $new_prefs[$key] = $value;
        }
    }
    
    // Now, update the database table for each pref that is
    // already in the table.
    foreach($changed_prefs as $key => $value)
    {
        $sql = 'UPDATE Prefs SET PrefValue = "' . $value . '" WHERE UserID=' . intval($userid) . ' AND PrefName="' . SqlClean($key) . '"';
        $result = SqlQuery($loc, $sql);
    }
    
    // Finally, insert the new prefereces into the table.
    foreach($new_prefs as $key => $value)
    {
        $sql = 'INSERT INTO Prefs (UserID, PrefName, PrefValue) VALUES ('. intval($userid) . ', "' . SqlClean($key) . '", "' . SqlClean($value) . '")';
        $result = SqlQuery($loc, $sql);
    }
    
    log_msg($loc, count($prefs) . ' preferences updated/saved successfully for user ' . intval($userid));
}

// --------------------------------------------------------------------
// Delete all prefereces for a given user.
function DeleteAllPrefsForUser($userid)
{
    $loc = "preflib.php->DeleteAllPrefsForUser";
    $sql = 'DELETE FROM Prefs WHERE UserID=' . intval($userid);
    $result = SqlQuery($loc, $sql);
    log_msg($loc, 'All preferences deleted successfully for user ' . intval($userid));
}

?>