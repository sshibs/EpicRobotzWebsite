<?php
// --------------------------------------------------------------------
// piclib.php:  library fucntions that deal with database and pic uploads.
//
// Created: 12/12/14 DLB
// Updated: 12/30/14 DLB -- Hacked from Epic Scouts
// --------------------------------------------------------------------

// These functions deal with storing and recalling picture files.  When
// a user first uploades a pic, the server places the picture 
// file in a temporary place.  Then lots of checks are made to be sure
// it is a valid picture.  If the checks pass, a database record is
// created in the table "Picture" and the picture is given a "PicID".
// The picture is then copied to a sub directory named "orig", with its
// new file name, that has the PicID embedded in it.  Then, the picture
// is resized into variout widths, and those new pics are
// stored in sub directories named "wxxx", where xxx is the width
// size in pixels.  Once the resized pictures are stored, the database
// record is upldated to say that the picture is valid and ready to use.
//
// The above is all done in the function StorePicture().  The Picture
// table only keeps track of valid pictures, not what they are used
// for.  Other tables such as "UserPic" store much more information 
// about what is in the picture by using the PicID as the reference.
//
// The PicID can be used with the various functions below to retrieve
// the picture in various sizes.  The actual size is specified by what
// is called a Size Enumeration, which is one of the following strings:
// "Orig", "Big", "Medium", "Small", "Tiny", and "Thumb". These coorespond
// to various widths, as defined in PicSizeTable().
//
// If you want to store information for a User Pic, that can be done
// with StoreUserPic().  StoreUserPic will frist call StorePicture to 
// store the basic picture, and then it will put the additional information
// in the UserPic table.
//
// Some important points:
// 1. Only JPEGs are allowed.  No provision has been made for other types.
// 2. In the future, pic might be only downsized, not upsized.  They will
//    exist in all the sub directories however.  Therefore, it is possible
//    that a pic with a smaller width could be put in a sub directory for
//    bigger pics.  
// 3. Becuase of #2 above, you should always specify the display size
//    the your html.
//
// Public API 
// ----------
// PicPathName()  -- Returns the path name to the picture.
// PicURL()       -- Returns the URL for a picture.
// StorePicture() -- Does all the work to store a basic picture on the server.
// StoreUserPic() -- Stores a picture for a given user.
// BestPicSize()  -- Suggests the correct Size Enumeration.
//

require_once "config.php";
require_once "libs/databaselib.php";
require_once "libs/loglib.php";

// --------------------------------------------------------------------
// Returns a list of sub directories for different pic sizes.
// The first entry is "orig". The following enties can be changed,
// but they need to be in order from largest to smallest.
function PicFolderList()
{
    return array("orig", "w1920", "w1024", "w640", "w320", "w80");
}

// --------------------------------------------------------------------
// Given a desired pic width in pixels, returns a size enumeration
// that best fits the picture.
function BestPicSize($width)
{
    $folders = PicFolderList();
    $n = count($folders);
    if($n <= 1) return $folders[0];
    for($i = $n-1; $i >= 1; $i--)
    {
        $f = $folders[$i];
        $w = intval(substr($f, 1));
        if($width <= $w) return $f;
    }
    return $folders[0];
}

// --------------------------------------------------------------------
// Given a Size Enumeration, returns the sub directory name for that
// size.  For example, "Big" returns "w1920".  This function must
// be kept synced with PicFolderList(), above.
function PicFolderFromSize($size)
{
    if(in_array($size, PicFolderList(), true)) {return $size; }
    if(strtolower($size) == "orig")     return "orig";
    if(strtolower($size) == "huge")     return "w1920";
    if(strtolower($size) == "big")      return "w1920";
    if(strtolower($size) == "medium")   return "w1024";
    if(strtolower($size) == "small")    return "w640";
    if(strtolower($size) == "standard") return "w640";
    if(strtolower($size) == "tiny")     return "w320";
    if(strtolower($size) == "thumb")    return "w80";
    return "w640";  // our default
}

// --------------------------------------------------------------------
// Returns the full path name including extension for a pic, given its
// PicID and Size Enumeration.
function PicPathName($id, $size)
{
    global $config;
    $f = MakePicFileBaseName($id, $size);
    return $config["UploadDir"] . "pics/". $f;
}

// --------------------------------------------------------------------
// Returns the url for the given picture id and a size enumeration.
function PicUrl($id, $size)
{
    global $config;
    $f = MakePicFileBaseName($id, $size);
    return $config["UploadUrl"] . "pics/" . $f;
}

// --------------------------------------------------------------------
// Returns the base file name for a picture, given its ID and Size 
// Enumeration.  Help function -- not intended for public use.
function MakePicFileBaseName($id, $size)
{
    $f = PicFolderFromSize($size) . '/pic_' . sprintf("%06u", $id) . ".jpg";
    return $f;
}

// --------------------------------------------------------------------
// Check to make sure all the sub directories for the various picture
// sizes exist.  If they don't, make them.  If fail, the die-with-msg.
function CheckPicDirs()
{
    global $config;
    $loc = "piclib.php->CheckPicDirs";
    $pt = $config["UploadDir"];
    if(!file_exists($pt))
    {
        $result = @mkdir($pt, 0764);
        if($result === false)
        {
            DieWithMsg($loc, "Unable to Create Folder: " . $pt);
        }
    }
    
    $pt = $config["UploadDir"] . 'pics/';
    if(!file_exists($pt))
    {
        $result = @mkdir($pt, 0764);
        if($result === false)
        {
            DieWithMsg($loc, "Unable to Create Folder: " . $pt);
        }
    }
    
    $folders = PicFolderList();
    foreach($folders as $f)
    {
        $p = $config["UploadDir"] . 'pics/' . $f;
        if(!file_exists($p))
        {
            $result = mkdir($p, 0764);
            if($result == false)
            {
                DieWithMsg($loc, "Unable to Create Folder: " . $p);
            }
        }
    }
}

// --------------------------------------------------------------------
// Creates an entry in the RawPic database, copies the picture to 
// various splaces, resizes it, and returns the new ID for the picture.
// On error, false is returned and error is logged.  Picture file must be
// jpeg, and exist on the server.  Normally the input file is deleted (moved),
// but it can be left alone by seting $delete=false.
function StorePicture($tempfile, $delete=true)
{
    $loc = "piclib.php->StorePicture";
    $tstart = microtime(true);  // Time the entire operation... 

    // Make sure all the directories exist.
    CheckPicDirs();
    
    if(empty($tempfile))
    {
        log_error($loc, "Empty temp file!");
        return false;
    }
    if(!file_exists($tempfile))
    {
        log_error($loc, "Temp file does not exists ( " . $tempfile . ')');
        return false;
    }
    
    $imginfo = @getimagesize($tempfile);
    if($imginfo === false)
    {
        log_error($loc, 
           'Pic file appears unreadable.  Getimagesize() failed reading ' . $tempfile);
        return false;
    }
    $width  = $imginfo[0];
    $height = $imginfo[1];
    $type   = $imginfo[2];
    if($type != IMG_JPG)
    {
        log_error($loc, 
          'Pic file does not seem to be a jpg.  Output of getimagesize = ' . print_r($imginfo, true));
        return false;
    }
    if($width < 10 || $height < 10)
    {
        log_error($loc, 
          'Invalid Width and/or Height sizes (' . $width .', ' . $height . ') for ' . $tempfile);
        return false;
    }
    $filesize = @filesize($tempfile);
    if($filesize === false)
    {
        log_error($loc, 'Unable to get the file size for ' . $tempfile);
        return false;
    }
    if($filesize > 10000000)
    {
        log_error($loc,
        'File size for picture is too big (>10MB).  Size= ' . $filesize . ', tempfile= ' . $tempfile);
        return false;
    }
    
    // All seems okay... Lets create the database entry.

    $sql  = 'INSERT INTO Pictures (DateOfUpload, FileStatus, FileSize, Width, Height) VALUES (';
    $sql .= '"' . DateTimeForSQL(UnixTimeNow()) . '"';   // DateOfUpload
    $sql .= ', 0';                                       // FileStatus
    $sql .= ', ' . intval($filesize);                    // FilsSize
    $sql .= ', ' . intval($width);                       // Width
    $sql .= ', ' . intval($height);                      // Height
    $sql .= ')';
    $result = SqlQuery($loc, $sql);
    $id = GetSqlConnection()->insert_id;

    // Now that we have the ID, we can put the picture in it's place, and resize it.
    // Copy the input to the 'orig' folder.
    $origfile = PicPathName($id, 'orig');
    if($delete) {$result   = @rename($tempfile, $origfile); }
    else        {$result   = @copy($tempfile, $origfile); }
    if($result === false)
    {
        log_error($loc,
        'Unable to move/copy file from ' . $tempfile . ' to ' . $origfile . '.');
        return false;
    }
   
    // Now that we have the original in place, all the others can be resized from it.
    $result = PicResizeAll($id, $width, $height);
    if($result === false) return false;
    
    // Now that all the files are in their correct places, update the file status in
    // the database.
    $sql  = 'UPDATE Pictures SET FileStatus=1 WHERE PicID=' . intval($id);
    $result = SqlQuery($loc, $sql);
    
    $telp = (microtime(true) - $tstart) * 1000.0;
    
    log_msg($loc, "Pic ID " . $id . 
        " Successfully Stored on server. (Elp=" . sprintf("%6.2f", $telp) . " ms.)");
        
    return $id;
}

// --------------------------------------------------------------------
// Helper function for StorePicture().  Takes the original file
// and resizes it into the various stored sizes, according to the
// list of sub directories.  Errors are logged. False is returned on error,
// true for success.
function PicResizeAll($id, $width, $height)
{
    $loc = "piclib.php->PicResizeAll";
    $origfile = PicPathName($id, "orig");
    if(!file_exists($origfile))
    {
        log_error($loc,
            array("Original file does not exist!", "Origfile= " . $origfile));
        return false;
    }

    // Read the original image 
    $img = @imagecreatefromjpeg($origfile);
    if($img === FALSE)
    {
        log_error($loc,
            array("Unable to Resize Image!", "Origfile= " . $origfile,
            "imagecreatefromjpeg failed."));
        return false;
    }
    
    // Now we resize the pic into all the possibile sizes.
    $folders = PicFolderList();
    foreach($folders as $f)
    {
        if(substr($f, 0, 1) != "w") continue;
        // Resize each file here.
        $new_width = intval(substr($f, 1));
        if($new_width < 10 || $new_width > 2000) continue;
        $ratio = $new_width / $width;
        $new_height = intval($height * $ratio);
        $new_file = PicPathName($id, $f);
        $new_img = @imagecreatetruecolor($new_width, $new_height);
        if($new_img === false)
        {
            log_error($loc,
                array("Unable to Resize Image!", "Origfile= " . $origfile,
               "imagecreatetruecolor failed."));
            return false;
        }
        $result = @imagecopyresampled($new_img, $img, 0, 0, 0, 0, 
            $new_width, $new_height, $width, $height);
        if($result === false)
        {
            log_error($loc,
               array("Unable to Resize Image!", "Origfile= " . $origfile,
               "Resize Folder=" . $f));            
            return false;
        }
        $result = @imagejpeg($new_img, $new_file, 100);
        if($result === false)
        {
            log_error($loc,
               array("Unable to Saved Resized Image!", "Origfile= " . $origfile,
               "NewFile=" . $new_file));            
            return false;
        }
        
        // Optimization trick:  Use the new image next time as the original 
        // instead of always starting over with the super sized original.
        // This saves about 1/3 of the processing time.
        $img = $new_img;
        $width = $new_width;
        $height = $new_height;
    }
    return true;
}

// --------------------------------------------------------------------
// Stores a pic and associates it with a user, and retures a new ID for
// the picture. On error, false is returned and error is logged.  Picture
// file must be jpeg, and exist on the server.  The input file in moved. 
function StoreUserPic($tempfile, $userid)
{
    $loc = "piclib.php->StoreUserPic";
    $id = StorePicture($tempfile);
    if($id === false) return false;
    
    // Delete old pic, if any...
    $sql = 'DELETE FROM UserPics WHERE UserID=' . intval($userid);
    SqlQuery($loc, $sql);

    $sql  = 'INSERT INTO UserPics (PicId, UserID) VALUES (';
    $sql .= '  '  . intval($id);                     // PicID
    $sql .= ', '  . intval($userid);                 // UserID
    $sql .= ')';
    $result = SqlQuery($loc, $sql);
    log_msg($loc, 'User Picture Successfully Stored. PicID= '. $id . ', UserID = ' . $userid);
    return $id;
}

// --------------------------------------------------------------------
// Returns the picture ID associated with a user, or zero if none.
function GetPicIDForUserID($userid)
{
    $loc = 'piclib.php->GetPicIDForUserID';
    $sql = 'SELECT PicID From UserPics WHERE UserID=' . intval($userid);
    $result = SqlQuery($loc, $sql);
    while($row = $result->fetch_assoc())
    {
        $picid = $row["PicID"];
        return $picid;
    }
    return 0;
}

?>