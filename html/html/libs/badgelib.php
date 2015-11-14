l<?php
// --------------------------------------------------------------------
// badgelib.php:  library fucntions that deal with making badges.
//
// Created: 12/30/14 DLB
// --------------------------------------------------------------------

// These functions deal with making badges. 
//
// Badges are "made" from the data about a user.  A made badge is
// two jpg files, named in the form of "A000_f.jpg" and "A000_b.jpg".
// The file names are named after the Badge's ID. The "_f" and 
// "_b" denote the front and back image, respectively. Badge files
// are stored in the uploads directory, under the badge folder. 
//
// A badge can be remade at any time, and if so, it overwrites
// a previous version of the badge.  It is up to other program 
// logic to know when to make a badge... These functions do not
// keep track of changes to user data that would necessiate a new
// badge being made.
//

require_once "libs/all.php";
require_once "libs/php-barcode.php";

// --------------------------------------------------------------------
// Makes a badge given user data.  $data is an associtive array
// that should have at least the following keys:  BadgeID, FirstName,
// LastName.  Other optional keys are: Title, NickName, and PicID.
// Serious errors will die.  Non-serious errors, but failure, will
// return a message string explaining the failure.  True is returned
// on success.
function MakeBadge($data)
{
    global $config;
    $loc = 'badgeli.php->MakeBadge'; 
    if(!isset($data["BadgeID"])) return 'BadgeID not given.';
    if(!isset($data["FirstName"])) return 'FirstName not given.';
    if(!isset($data["LastName"])) return 'LastName not given.';

    $badgeid = $data["BadgeID"];
    $firstname = $data["FirstName"];
    $lastname  = $data["LastName"];
    
    $title = "";
    $nickname = "";
    $picid = 0;
    
    if(isset($data["Title"]))    $title    = $data["Title"];
    if(isset($data["NickName"])) $nickname = $data["NickName"];
    if(isset($data["PicID"]))    $picid    = intval($data["PicID"]);
    
    if(!VerifyBadgeFormat($badgeid)) return 'Bad Badge Format. Must be in form of "A000".';
    
    CheckBadgeDir();

    // Remove the old files.
    DeleteBadge($badgeid);

    // This program makes credit sized badges: 2.125x3.375 inches, @ 300 dots/inch.
    // Therfore, the output image is 637x1012 pixels.
    $w = 638;
    $h = 1013;

    $result = MakeBadgeBack($w, $h, $badgeid, $firstname . ' ' . $lastname);
    if($result == false)
    {
        // Delete fragments so system will not think there is a valid badge.
        DeleteBadge($badgeid);
        return 'Unable to make badge back side (see system log for more info).';
    }

    $result = MakeBadgeFront($w, $h, $badgeid, $picid, $firstname, $lastname, $nickname, $title);
    if($result == false)
    {
        // Delete fragments so system will not think there is a valid badge.
        DeleteBadge($badgeid);
        return 'Unable to make badge back side (see system log for more info).';
    }
    log_msg($loc, 'Badge Successfully made for User= ' . $firstname . ' ' . $lastname . ', BadgeID=' . $badgeid . '.');
    return true;
}

// --------------------------------------------------------------------
// Deletes the badge files.
function DeleteBadge($badgeid)
{
    $loc = 'badgelib.php->DeleteBadge';
    $r1 = @unlink(GetBadgeFile($badgeid, 'front'));
    $r2 = @unlink(GetBadgeFile($badgeid, 'back'));
    
    if(($r1 === false) || ($r2 === false))
    {
        log_msg($loc, 'Unable to delete badge files, for BadgeID=' . $badgeid);
    }
}


// --------------------------------------------------------------------
// Draws text, and centerers it around $fx,$fy.  Input is the $text, the
// coordinates ($fx, $fy), and the $params.  The coordinates are given as
// ratio of width and height.  $params is an assoctive array
// with the following required keys: image.  Optional keys are fontsize,
// angle, color, and fontfile.  If omitted, the defaults are: 12, 0 degrees,
// blank, and the built in font #1.

function DrawTextCentered($params, $text, $fx, $fy)
{
    if(!isset($params['image'])) return;
    $img = $params['image'];

    if(isset($params['color'])) $color = $params['color'];
    else $color = ImageColorAllocate($img,0x00,0x00,0x00);

    if(isset($params['angle'])) $angle = $params['angle'];
    else $angle = 0.0;

    if(isset($params['fontsize'])) $fontsize = $params['fontsize'];
    else $fontsize = 12;
    
    if(isset($params['fontfile'])) $fontfile = $params['fontfile'];
    else $fontfile = 'fonts/default.ttf';
    
    $w = imagesx($img);
    $h = imagesy($img);
    $xc = intval($fx * $w);
    $yc = intval($fy * $h);
    $box = imagettfbbox($fontsize, $angle, $fontfile, $text);
    $xsize = $box[4] - $box[0];
    $ysize = $box[1] - $box[5];
    $x0 = intval($xc - $xsize/2);
    $y0 = intval($yc - $ysize/2);
    $box = imagefttext($img, $fontsize, $angle, $x0, $y0, $color, $fontfile, $text);
    return $box;
}

// --------------------------------------------------------------------
// Draws the barcode.  The barcode is centered on the coordiates ($fx, $fy)
// given as a ratio of image width and height.  $params is an associative
// array with the following keys: image, color, angle, height, width.  The
// image is required, the others are optional.  Height and width are given
// in pixels.  The height is the length of each bar.  The width is the number
// of pixels for one bar.

function DrawBarCode($params, $text, $fx, $fy)
{
    if(!isset($params['image'])) return;
    $img = $params['image'];
    $w = imagesx($img);
    $h = imagesy($img);

    if(isset($params['color'])) $color = $params['color'];
    else $color = ImageColorAllocate($img,0x00,0x00,0x00);

    if(isset($params['angle'])) $angle = $params['angle'];
    else $angle = 0.0;

    if(isset($params['height'])) $barheight = $params['height'];
    else $barheight = 0.2 * $h;    // about 1/5 of the image size.

    if(isset($params['width']))  $barwidth = $params['width'];
    else $barwidth = $w / 200;     // Just a guess.  Works out to be about 3px.

    $type = 'code128';
    $x0 = intval($fx * $w);
    $y0 = intval($fy * $h);
    $data = Barcode::gd($img, $color, $x0, $y0, $angle, $type,
            array('code'=>$text), $barwidth, $barheight);
}

// --------------------------------------------------------------------
// Helper function to draw the front side of the badge.  Returns false
// on failure, true on success.
// This version uses Will's hole image.
function MakeBadgeFront($w, $h, $badgeid, $picid, $firstname, $lastname, $nickname, $title)
{
    $loc = 'badgelib.php->MakeBadgeFront';
    $img    = imagecreatetruecolor($w, $h);
    $black  = ImageColorAllocate($img,0x00,0x00,0x00);
    $white  = ImageColorAllocate($img,0xff,0xff,0xff);
    $red    = ImageColorAllocate($img,0xff,0x00,0x00);
    $blue   = ImageColorAllocate($img,0x00,0x00,0xff);
    imagefilledrectangle($img, 0, 0, $w, $h, $white);
    
    $picid = intval($picid);
    if($picid > 0) 
    {
        // We have a image to put on the badge!
        // It should be placed at (151,188) to (483x520). It should be 332x332.
        $picfile = PicPathName($picid, 'orig');  // Get the original.
        $imginfo = @getimagesize($picfile);
        if($imginfo === false)
        {
            log_error($loc, 'Getimagesize() failed on our image: ' . $picfile);
            return false;
        }
        $picwidth  = $imginfo[0];
        $picheight = $imginfo[1]; 
        $picimg = @imagecreatefromjpeg($picfile);
        if($picimg === false)
        {
            log_error($loc, 'imagecreatefromjpeg() failed on our image: ' . $picfile);
            return false;
        }
        
        $result = @imagecopyresampled($img, $picimg, 151, 188, 0, 0, 
                   332, 332, $picwidth, $picheight);
        if($result === false)
        {
            log_error($loc, 'imagecopyresized() failed for PidId=' . $picid);
            return false;
        } 
    }
    
    // Now we slap the entire badge image with the hole over the headshot.
    
    $backfile = 'img/badge_with_hole.png';
    if(file_exists($backfile))
    {
        $backimg = @imagecreatefrompng($backfile);
        if($backimg === false)
        {
            log_error($loc, 'imagecreatefrompng() failed on our image: ' . $backfile);
        }
        else
        {
            $xsz = imagesx($img);
            $ysz = imagesy($img);
            $result = @imagecopyresampled($img, $backimg, 0, 0, 0, 0, $w, $h, $xsz, $ysz);
        }
    }
    
    $iparam['image'] = $img;
    $iparam['fontsize'] = 60.0;
    $iparam['color'] = $red;
    $iparam['angle'] = 0.0;
    $iparam['fontfile'] = 'fonts/prototype.ttf';
 
    //DrawTextCentered($iparam, 'EPIC Robotz', 0.5, 0.18);
    //$iparam['fontsize'] = 40.0;
    //DrawTextCentered($iparam, 'Team 4415', 0.5, 0.23); 
    
    if(!empty($nickname)) $fn = $nickname;
    else                  $fn = $firstname;

    $ink   = ImageColorAllocate($img,0x00,0x00,0x66);
    
    if(empty($title)) $p = 0.02;
    else $p = 0.00;
    
    $iparam['fontsize'] = 60.0;
    $iparam['color'] = $ink;
    DrawTextCentered($iparam, $fn, 0.5, 0.74 + $p);
    $iparam['fontsize'] = 45.0;
    DrawTextCentered($iparam, $lastname, 0.5, 0.79 + $p);
    $iparam['fontsize'] = 25.0;
    $iparam['color'] = $black;
    if(!empty($title)) DrawTextCentered($iparam, $title, 0.5, 0.82);
    
    $text = 'AE ' . $badgeid . 'F';
    $iparam['color'] = $black;
    $iparam['height'] = $h * 0.15;
    $iparam['width'] = 4;
    DrawBarCode($iparam, $text, 0.5, 0.90); 

    $filename = GetBadgeFile($badgeid, 'front');
    $okay = imagejpeg($img, $filename, 100);
    if($okay === false)
    {
        log_msg($loc, 'Unable to write jpg output file (' . $filename . '). Badge Failed.)');
        return false;
    }
    return true;
}

// --------------------------------------------------------------------
// Helper function to draw the front side of the badge.  Returns false
// on failure, true on success.
function MakeBadgeFront_Orig($w, $h, $badgeid, $picid, $firstname, $lastname, $nickname, $title)
{
    $loc = 'badgelib.php->MakeBadgeFront';
    $img    = imagecreatetruecolor($w, $h);
    $black  = ImageColorAllocate($img,0x00,0x00,0x00);
    $white  = ImageColorAllocate($img,0xff,0xff,0xff);
    $red    = ImageColorAllocate($img,0xff,0x00,0x00);
    $blue   = ImageColorAllocate($img,0x00,0x00,0xff);
    imagefilledrectangle($img, 0, 0, $w, $h, $white);
    
    $backfile = 'img/badge_back_1.jpg';
    if(file_exists($backfile))
    {
        $backimg = @imagecreatefromjpeg($backfile);
        if($backimg === false)
        {
            log_error($loc, 'imagecreatefromjpeg() failed on our image: ' . $backfile);
        }
        else
        {
            $xsz = imagesx($img);
            $ysz = imagesy($img);
            $result = @imagecopyresampled($img, $backimg, 0, 0, 0, 0, $w, $h, $xsz, $ysz);
        }
    }
    
    $iparam['image'] = $img;
    $iparam['fontsize'] = 60.0;
    $iparam['color'] = $red;
    $iparam['angle'] = 0.0;
    $iparam['fontfile'] = 'fonts/prototype.ttf';
 
    DrawTextCentered($iparam, 'EPIC Robotz', 0.5, 0.18);
    $iparam['fontsize'] = 40.0;
    DrawTextCentered($iparam, 'Team 4415', 0.5, 0.23); 
    
    $picid = intval($picid);
    if($picid > 0) 
    {
        // We have a image to put on the badge!
        $picfile = PicPathName($picid, 'standard');  // Standard should have more than enough resolution.
        $imginfo = @getimagesize($picfile);
        if($imginfo === false)
        {
            log_error($loc, 'Getimagesize() failed on our image: ' . $picfile);
            return false;
        }
        $picwidth  = $imginfo[0];
        $picheight = $imginfo[1]; 
        $picimg = @imagecreatefromjpeg($picfile);
        if($picimg === false)
        {
            log_error($loc, 'imagecreatefromjpeg() failed on our image: ' . $picfile);
            return false;
        }
        $xmax = $w - intval(0.1*$w);
        $ymax = intval(0.32*$h);
        $xscale = $xmax / $picwidth;
        $yscale = $ymax / $picheight;
        $scale = $xscale;
        if($yscale < $xscale) $scale = $yscale;
        $xsize = intval($picwidth * $scale);
        $ysize = intval($picheight * $scale);
        $x0 = intval(($w - $xsize)/2);
        $y0 = intval(0.23 * $h);
        
        // Fill a rect behind the image to produce a boader.
        imagefilledrectangle($img, $x0-4, $y0-4, $x0 + $xsize + 4, $y0 + $ysize + 4, $red);
        
        $result = @imagecopyresampled($img, $picimg, $x0, $y0, 0, 0, 
                   $xsize, $ysize, $picwidth, $picheight);
        if($result === false)
        {
            log_error($loc, 'imagecopyresized() failed for PidId=' . $picid);
            return false;
        } 
    }

    if(!empty($nickname)) $fn = $nickname;
    else                  $fn = $firstname;

    
    $iparam['fontsize'] = 60.0;
    $iparam['color'] = $blue;
    DrawTextCentered($iparam, $fn, 0.5, 0.68);
    $iparam['fontsize'] = 45.0;
    DrawTextCentered($iparam, $lastname, 0.5, 0.75);
    $iparam['fontsize'] = 25.0;
    $iparam['color'] = $black;
    DrawTextCentered($iparam, $title, 0.5, 0.80);
    
    $text = 'AE ' . $badgeid . 'F';
    $iparam['color'] = $black;
    $iparam['height'] = $h * 0.15;
    $iparam['width'] = 4;
    DrawBarCode($iparam, $text, 0.5, 0.90); 

    $filename = GetBadgeFile($badgeid, 'front');
    $okay = imagejpeg($img, $filename, 100);
    if($okay === false)
    {
        log_msg($loc, 'Unable to write jpg output file (' . $filename . '). Badge Failed.)');
        return false;
    }
    return true;
}

// --------------------------------------------------------------------
// Helper function to draw the back side of the badge.  Returns false
// on failure, true on success.
function MakeBadgeBack($w, $h, $badgeid, $name)
{
    $loc = 'badgelib.php->MakeBadgeBack';
    $img    = imagecreatetruecolor($w, $h);
    $black  = ImageColorAllocate($img,0x00,0x00,0x00);
    $white  = ImageColorAllocate($img,0xff,0xff,0xff);
    $red    = ImageColorAllocate($img,0xff,0x00,0x00);
    $blue   = ImageColorAllocate($img,0x00,0x00,0xff);
    $blue   = ImageColorAllocate($img,0x00,0x00,0xff);
    imagefilledrectangle($img, 0, 0, $w, $h, $white);
    
    $iparam['image']    = $img;
    $iparam['color']    = $black;
    $iparam['angle']    = 0.0;
    $iparam['fontfile'] = 'fonts/prototype.ttf';
    
    $text = 'AE ' . $badgeid . 'B';
    $iparam['color'] = $black;
    $iparam['height'] = $h * 0.15;
    $iparam['width'] = 4;
    DrawBarCode($iparam, $text, 0.5, 0.90);
    
    $iparam['color']    = ImageColorAllocate($img,0xfe,0x9a,0x2e);
    $iparam['fontsize'] = 25.0;
    $iparam['fontfile'] = 'fonts/prototype.ttf';
    DrawTextCentered($iparam, 'Valley Christian High School', 0.5, 0.20);
    DrawTextCentered($iparam, '2015', 0.5, 0.30);

    $iparam['fontsize'] = 25.0;
    $iparam['color']    = $blue;
    DrawTextCentered($iparam, $name, 0.5, 0.75);  
    $iparam['color']    = $black;
    DrawTextCentered($iparam, $badgeid, 0.5, 0.82);  
    
    $filename = GetBadgeFile($badgeid, 'back');
    $okay = imagejpeg($img, $filename, 100);
    if($okay === false)
    {
        log_msg($loc, 'Unable to write jpg output file (' . $filename . '). Badge Failed.)');
        return false;
    }
    return true;
}

// --------------------------------------------------------------------
// Returns true if the badge exits.
function BadgeExists($badgeid)
{
    if(empty($badgeid)) return false;
    if(!file_exists(GetBadgeFile($badgeid, 'front'))) return false;
    if(!file_exists(GetBadgeFile($badgeid, 'back'))) return false;
    return true;
}

// --------------------------------------------------------------------
// Returns the file path to a badge, given the BadgeID and side.  The
// side can be 'front' or 'back'.
function GetBadgeFile($badgeid, $side="front")
{
    global $config;
    $p = $config["UploadDir"] . 'badges/';
    $s = '_f.jpg';
    if($side == 'back') $s = '_b.jpg';
    $file = $p . $badgeid . $s;
    return $file;
}
// --------------------------------------------------------------------
// Returns the file path to a badge, given the StickerID. 
function GetStickerFile($stickerid)
{
    global $config;
    $p = $config["UploadDir"] . 'stickers/';
    $s = '.gif';
    $file = $p . $stickerid . $s;
    return $file;
}
// --------------------------------------------------------------------
// Returns the URL to a badge, given the BadgeID and side.  The side
// can be 'front' or 'back'.
function GetBadgeUrl($badgeid, $side="front")
{
    global $config;
    $p = $config["UploadUrl"] . 'badges/';
    $s = '_f.jpg';
    if($side == 'back') $s = '_b.jpg';
    $url = $p . $badgeid . $s;
    return $url;
}

// --------------------------------------------------------------------
// Makes sure that the badge directory exits.
function CheckBadgeDir()
{
    global $config;
    $loc = 'badgelib.php->CheckBadgeDir';
    $pt = $config["UploadDir"];
    if(!file_exists($pt))
    {
        $result = @mkdir($pt, 0764);
        if($result === false)
        {
            DieWithMsg($loc, "Unable to Create Folder: " . $pt);
        }
    }
    $pt = $config["UploadDir"] . 'badges/';
    if(!file_exists($pt))
    {
        $result = @mkdir($pt, 0764);
        if($result === false)
        {
            DieWithMsg($loc, "Unable to Create Folder: " . $pt);
        }
    }
    $pt = $config["UploadDir"] . 'gifs/';
    if(!file_exists($pt))
    {
        $result = @mkdir($pt, 0764);
        if($result === false)
        {
            DieWithMsg($loc, "Unable to Create Folder: " . $pt);
        }
    }

}

// --------------------------------------------------------------------
// Makes a gif image for the user, to be used by the reader.  The input
// is an associtive array that should have at least the following keys:
// BadgeID, UserID.  Serious errors will die.  Non-serious errors,
// but failure, will return a message string explaining the failure.
// True is returned on success.
function MakeGif($data)
{
    global $config;
    $loc = 'badgeli.php->MakeGif';

    if(isset($data["UserID"])) $userid  = intval($data["UserID"]);
    else {$msg = 'UserID not given.';  log_error($loc, $msg); return $msg; }
 
    if(!isset($data["BadgeID"])) 
    {
        $msg = 'BadgeID not given for UserID = ' . $userid;
        log_error($loc, $msg); 
        return $msg;
    }
    if(!isset($data["PicID"])) 
    {
        $msg = 'PicID not given for UserID = ' . $userid;
        log_error($loc, $msg); 
        return $msg; 
    }
    
    $badgeid = $data["BadgeID"];
    $picid   = intval($data["PicID"]);

    if(!VerifyBadgeFormat($badgeid)) 
    {   
        $msg = 'Bad Badge Format. Must be in form of "A000".';
        log_error($loc, $msg);
        return $msg;
    }
    if($picid <= 0) 
    {
        $msg = 'User ' . $userid . ' does not have a picture.';
        log_error($loc, $msg);
        return $msg;
    }
    
    CheckBadgeDir();
    
    // We have a image to put on the badge!
    $picfile = PicPathName($picid, 'standard');  // Standard should have more than enough resolution.
    $imginfo = @getimagesize($picfile);
    if($imginfo === false)
    {
        $msg = 'Getimagesize() failed on our image: ' . $picfile;
        log_error($loc, $msg);
        return $msg;
    }
    
    $picwidth  = $imginfo[0];
    $picheight = $imginfo[1]; 
    $picimg = @imagecreatefromjpeg($picfile);
    if($picimg === false)
    {
        $msg = 'imagecreatefromjpeg() failed on our image: ' . $picfile;
        log_error($loc, $msg);
        return $msg;
    }
    $scale = 260 / $picheight;
    $xsize = intval($picwidth * $scale);
    $ysize = intval($picheight * $scale);
    $img    = imagecreatetruecolor($xsize, $ysize);
    $result = @imagecopyresampled($img, $picimg, 0, 0, 0, 0, 
                   $xsize, $ysize, $picwidth, $picheight);
    if($result === false)
    {
        $msg = 'imagecopyresized() failed for PidId=' . $picid;
        log_error($loc, $msg);
        return $msg;
    } 
    $outfile = $config["UploadDir"] . 'gifs/' . $badgeid . '.gif';
    $result = imagegif($img, $outfile);
    if($result === false)
    {
        $msg = 'imagegif() failed for PicID=' . $picid;
        log_error($loc, $msg);
        return $msg;
    }
    
    log_msg($loc, 'Image Successfully made for BadgeID= ' . $badgeid . '.');
    return true;
}

// --------------------------------------------------------------------
// Make a print image for one badge on 4x6 teslin paper.
// The page is 300dpi, zero boards on all sides.  The image is placed
// in the center.  The urls of the output files are returned as
// an array of two elements (front image and back image). 
function MakePrintImageForOneBadge($badgeid)
{
    global $config;
    $w = 4 * 300;
    $h = 6 * 300;

    $img_f    = @imagecreatetruecolor($w, $h);
    $img_b    = @imagecreatetruecolor($w, $h);
    $white_f  = ImageColorAllocate($img_f,0xff,0xff,0xff);
    $red_f    = ImageColorAllocate($img_f,0xff,0x00,0x00);
    $blue_f   = ImageColorAllocate($img_f,0x00,0x00,0xff);
    $black_f  = ImageColorAllocate($img_f,0x00,0x00,0x00);
    $white_b  = ImageColorAllocate($img_b,0xff,0xff,0xff);
    $red_b    = ImageColorAllocate($img_b,0xff,0x00,0x00);
    $blue_b   = ImageColorAllocate($img_b,0x00,0x00,0xff);
    $black_b  = ImageColorAllocate($img_b,0x00,0x00,0x00);
    imagefilledrectangle($img_f, 0, 0, $w, $h, $white_f);
    imagefilledrectangle($img_b, 0, 0, $w, $h, $white_b);

    $file_f = GetBadgeFile($badgeid, $side="front");
    $file_b = GetBadgeFile($badgeid, $side="back");

    // A Badge should be 2.128 x 3.375 inches, which is 637 x 1012.
    // Calculate the position of the upper left corner:
    $x0 = intval(($w - 637.5)/2);
    $y0 = intval(($h - 1012.5)/2);
    //$x0 = intval(300 * (15.0/16.0));
    //$y0 = intval(300 * (21.0/16.0));
    $swell = 2;  // 
    PaintBadgeOnSheet($img_f, $file_f, $x0, $y0, $swell);
    PaintBadgeOnSheet($img_b, $file_b, $x0, $y0, $swell);
    
    $name_f = $badgeid . '_f.jpg';
    $name_b = $badgeid . '_b.jpg';
    
    SaveSheetImg("prints", $img_f, $name_f);
    SaveSheetImg("prints", $img_b, $name_b);
    
    $url_f = $config["UploadUrl"] . "prints/" . $name_f;
    $url_b = $config["UploadUrl"] . "prints/" . $name_b;
    
    return array($url_f, $url_b);
}

// --------------------------------------------------------------------
// Make contact print sheet.  Makes a page, suitable for printing
// the actual badges... 
//
// The page is 300dpi, with a border of 0.625" (187.5px) on all sides.
// The padding between the badges is 7/16" (131.25px).  Up to 8 badges
// can be printed on a 8.5x11 page.  Size of each badge should be
// (637.5x1012.5) pixels.
//
//  The badges will be layed out as follows:
// 
//   Front:                Back:
//   -----------------     -----------------
//   | 0 | 1 | 2 | 3 |     | 3 | 2 | 1 | 0 |
//   -----------------     -----------------
//   | 4 | 5 | 6 | 7 |     | 7 | 6 | 5 | 4 |
//   -----------------     -----------------

function MakePrintSheet($badgelist, $basefilename)
{
    global $config;
    $swell = 6; // Number of pixels that we enlarge each badge around it's border.
    $w = intval(4*(637.5) + 3*(131.25)) + 1 + 2*$swell;
    $h = intval(2*(1012.5) + (131.25)) + 1 + 2*$swell;
    
    $img_f = @imagecreatetruecolor($w, $h);
    $img_b = @imagecreatetruecolor($w, $h);
    $white_f  = ImageColorAllocate($img_f,0xff,0xff,0xff);
    $red_f    = ImageColorAllocate($img_f,0xff,0x00,0x00);
    $blue_f   = ImageColorAllocate($img_f,0x00,0x00,0xff);
    $black_f  = ImageColorAllocate($img_f,0x00,0x00,0x00);
    $white_b  = ImageColorAllocate($img_b,0xff,0xff,0xff);
    $red_b    = ImageColorAllocate($img_b,0xff,0x00,0x00);
    $blue_b   = ImageColorAllocate($img_b,0x00,0x00,0xff);
    $black_b  = ImageColorAllocate($img_b,0x00,0x00,0x00);
    imagefilledrectangle($img_f, 0, 0, $w, $h, $white_f);
    imagefilledrectangle($img_b, 0, 0, $w, $h, $white_b);
    
    for($i = 0; $i < 4; $i++)
    {
        for($j = 0; $j < 2; $j++)
        {
            // Establish top,left corner of each image.
            $x0 = $swell + intval($i*(637.5+131.25));
            $y0 = $swell + intval($j*(1012.5+131.25));
            
            // Draw a box around the image...
            // This box should get covered up because of the swell.
            ImageBox($img_f, $x0, $y0, $black_f);
            ImageBox($img_b, $x0, $y0, $red_b);
            
            // Get the badgeId for this position.
            $badge_f = "";
            $badge_b = "";
            $index_f = $i + 4*$j;
            $index_b = (3-$i) + 4*$j; 
            $badgeid_f = "";
            $badgeid_b = "";
            $file_f = "";
            $file_b = "";
            if(isset($badgelist[$index_f])) $badgeid_f = $badgelist[$index_f];
            if(isset($badgelist[$index_b])) $badgeid_b = $badgelist[$index_b];
        
            // Get the file names.
            if(!empty($badgeid_f)) $file_f = GetBadgeFile($badgeid_f, $side="front");
            if(!empty($badgeid_b)) $file_b = GetBadgeFile($badgeid_b, $side="back");
            PaintBadgeOnSheet($img_f, $file_f, $x0, $y0, $swell);
            PaintBadgeOnSheet($img_b, $file_b, $x0, $y0, $swell);
        }
    }
    SaveSheetImg("sheets", $img_f, $basefilename . '_f.jpg');
    SaveSheetImg("sheets", $img_b, $basefilename . '_b.jpg');
}

function PaintBadgeOnSheet($img, $filename, $x0, $y0, $swell)
{
    $loc = 'badeglib.php->PaintBadgeOnSheet';
    if(!file_exists($filename)) return;
    $bimg = @imagecreatefromjpeg($filename);
    if($bimg === false)
    {
        $msg = 'imagecreatefromjpeg() failed on our image: ' . $filename;
        log_error($loc, $msg);
        return $msg;
    }
    $xsz = imagesx($bimg);
    $ysz = imagesy($bimg);
    
    $result = @imagecopyresampled($img, $bimg, 
                $x0 - $swell, $y0 - $swell,       // Destination start
                0, 0,                             // Source start
                637 + 2*$swell, 1012 + 2*$swell,  // Destinatin size
                $xsz, $ysz);                      // Source Size
    if($result === false)
    {
        $msg = 'imagecopyresampled() failed on our image: ' . $filename;
        log_error($loc, $msg);
        return $msg;
    }
}
function PaintStickerOnSheet($img, $filename, $x0, $y0, $swell)
{
    $loc = 'badeglib.php->PaintBadgeOnSheet';
    if(!file_exists($filename)) return;
    $bimg = @imagecreatefromjpeg($filename);
    if($bimg === false)
    {
        $msg = 'imagecreatefromjpeg() failed on our image: ' . $filename;
        log_error($loc, $msg);
        return $msg;
    }
    $xsz = imagesx($bimg);
    $ysz = imagesy($bimg);
    
    $result = @imagecopyresampled($img, $bimg, 
                $x0 - $swell, $y0 - $swell,       // Destination start
                0, 0,                             // Source start
                175 + 2*$swell, 215 + 2*$swell,  // Destinatin size
                $xsz, $ysz);                      // Source Size
    if($result === false)
    {
        $msg = 'imagecopyresampled() failed on our image: ' . $filename;
        log_error($loc, $msg);
        return $msg;
    }
}

function ImageBox($img, $x0, $y0, $color)
{
    imageline($img, $x0,     $y0,      $x0 + 637, $y0,      $color);
    imageline($img, $x0,     $y0,      $x0,       $y0+1012, $color);
    imageline($img, $x0+637, $y0+1012, $x0 + 637, $y0,      $color);
    imageline($img, $x0+637, $y0+1012, $x0,       $y0+1012, $color);
}
// --------------------------------------------------------------------
// Make sticker print sheet.  Makes a page, suitable for printing
// 	stickers of top half of badge.
//
// The page is 300dpi, with a border of 0.625" (187.5px) on all sides.
// The padding between the Stickers is 7/16" (131.25px).  Up to 12 Stickers
// can be printed on a 8.5x11 page.  Size of each Sticker should be
// (637.5x1012.5) pixels.
//
//  The Stickers will be layed out as follows:
// 
//   Front:               
//   -----------------   
//   | 0 | 1 | 2 | 3 |    
//   -----------------     
//   | 4 | 5 | 6 | 7 |   
//   -----------------
//   | 8 | 9 | 10| 11|   
//   -----------------  

function MakeStickerPrintSheet($badgelist, $basefilename)
{
    global $config;
    $swell = 6; // Number of pixels that we enlarge each badge around it's border.
    $w = intval(4*(175.5) + 3*(131.25)) + 1 + 2*$swell;
    $h = intval(2*(220.5 + 131.25)) + 1 + 2*$swell;

    $img = @imagecreatetruecolor($w, $h);
    $white  = ImageColorAllocate($img,0xff,0xff,0xff);
    $red    = ImageColorAllocate($img,0xff,0x00,0x00);
    $blue   = ImageColorAllocate($img,0x00,0x00,0xff);
    $black  = ImageColorAllocate($img,0x00,0x00,0x00);

    imagefilledrectangle($img, 0, 0, $w, $h, $white);

    for($i = 0; $i < 4; $i++)
    {
        for($j = 0; $j < 3; $j++)
        {
            // Establish top,left corner of each image.
            $x0 = $swell + intval($i*(175.5+75.25));
            $y0 = $swell + intval($j*(220.5));

            // Draw a box around the image...
            // This box should get covered up because of the swell.
            StickerImageBox($img, $x0, $y0, $black);


            // Get the badgeId for this position.
            $badge = "";
            $index = $i + 4*$j;
            $badgeid = "";
            $file = "";
            if(isset($badgelist[$index])) $stickerid = $badgelist[$index];

            // Get the file names.
            if(!empty($stickerid)) $file = GetStickerFile($stickerid);
            PaintStickerOnSheet($img, $file, $x0, $y0, $swell);
        }
    }
    SaveSheetImg("sheets", $img, $basefilename . '.jpg');
}

function StickerImageBox($img, $x0, $y0, $color)
{
    imageline($img, $x0,     $y0,      $x0 + 175, $y0,      $color);
    imageline($img, $x0,     $y0,      $x0,       $y0+200, $color);
    imageline($img, $x0+175, $y0+200, $x0 + 175, $y0,      $color);
    imageline($img, $x0+175, $y0+200, $x0,       $y0+200, $color);
}
// --------------------------------------------------------------------
// Saved an image in a folder under the given name.  The folder is 
// created if it does not exist.
function SaveSheetImg($folder, $img, $fname)
{
    $loc = 'badgelib.php->SaveSheetImg';
    global $config;
    $pt = $config["UploadDir"] . $folder . '/';
    if(!file_exists($pt))
    {
        $result = @mkdir($pt, 0764);
        if($result === false)
        {
            DieWithMsg($loc, "Unable to Create Folder: " . $pt);
        }
    }
    $pt .= $fname;
    $result = imagejpeg($img, $pt, 100);
    if($result === false)
    {
        $msg = 'imagejpeg() failed for ' . $pt;
        log_error($loc, $msg);
    }
}

// --------------------------------------------------------------------
// Make label sheets. 
// Currently for CD Stomper paper, 4 labels per sheet.
function MakePrintLabels($UserNames, $basefilename)
{
    $box_size_x = 1.96875;  // 590 pixels
    $box_size_y = 2.375;    // 712 pixels
    $page_size_x = 11.0;
    $page_size_y = 8.5;
    $page_margin_x = 31/32;
    $page_margin_y = 0.5;
    $boxlocs= array( 
             array($page_margin_x, $page_margin_y),
             array($page_margin_x + $box_size_x, $page_margin_y),
             array($page_size_x - $page_margin_x - 2*$box_size_x, $page_size_y-$page_margin_y-$box_size_y),
             array($page_size_x - $page_margin_x - $box_size_x, $page_size_y-$page_margin_y-$box_size_y));
    $sheetnum = 1;
    $pos = 0;
    $w = intval(11.0*300);
    $h = intval(8.5*300);
    $sheet_img = imagecreatetruecolor($w, $h);
    $white  = ImageColorAllocate($sheet_img,0xff,0xff,0xff);
    imagefilledrectangle($sheet_img, 0, 0, $w, $h, $white);
    
    foreach($UserNames as $u)
    {
        $userid = GetUserIDFromName($u);
        if($userid <= 0) continue;
        $data = GetUserInfo($userid);
        if($data === false) continue;
        $picid = $data["PicID"];
        $badgeid = $data["BadgeID"];
        $firstname = $data["FirstName"];
        $lastname = $data["LastName"];
        $title    = $data["Title"];
        $labelimg = CreateLabelPic($picid, $firstname, $lastname, $title);
        $x0 = intval($boxlocs[$pos][0] * 300);
        $y0 = intval($boxlocs[$pos][1] * 300);
        $result = @imagecopyresampled($sheet_img, $labelimg, $x0, $y0, 0, 0, 590, 712, 590, 712);
        $pos += 1;
        if($pos >= count($boxlocs))
        {
            $fname = 'label_' . $sheetnum . '.jpg';
            SaveSheetImg($sheet_img, $fname);
            $sheetnum += 1;
            $pos = 0;
            imagefilledrectangle($sheet_img, 0, 0, $w, $h, $white);
        }
    }
    if($pos != 0)
    {
        $fname = 'label_' . $sheetnum . '.jpg';
        SaveSheetImg($sheet_img, $fname);
    }
}

// --------------------------------------------------------------------
// Creates a label that is associated with a badge.  Returns the url.
function CreateLabelFile($badgeid, $picid, $firstname, $lastname, $title)
{
    global $config;
    
    $img = CreateLabelPic($picid, $firstname, $lastname, $title);
    $filename = $lastname . '_' . $firstname . '.jpg';
    SaveSheetImg("stickers", $img, $filename);
    $url = $config["UploadUrl"] . "stickers/" . $filename;
    return $url;
}

// --------------------------------------------------------------------
// Creats a label that is associated with a badge.  Can be used
// to print on a sticky paper.  Output is an image.
function CreateLabelPic($picid, $firstname, $lastname, $title)
{
    $w = 590;
    $h = 712;
    $loc = 'badgelib.php->CreateLabelPic';
    $img    = imagecreatetruecolor($w, $h);
    $black  = ImageColorAllocate($img,0x00,0x00,0x00);
    $white  = ImageColorAllocate($img,0xff,0xff,0xff);
    $red    = ImageColorAllocate($img,0xff,0x00,0x00);
    $blue   = ImageColorAllocate($img,0x00,0x00,0xff);
    imagefilledrectangle($img, 0, 0, $w, $h, $white);
    
    $picid = intval($picid);
    if($picid > 0) 
    {
        // We have a image to put on the badge!
        // It should be placed at (124x117 and sized to be 350x350
        $picfile = PicPathName($picid, 'orig');  // Get the original.
        $imginfo = @getimagesize($picfile);
        if($imginfo === false)
        {
            log_error($loc, 'Getimagesize() failed on our image: ' . $picfile);
            return false;
        }
        $picwidth  = $imginfo[0];
        $picheight = $imginfo[1]; 
        $picimg = @imagecreatefromjpeg($picfile);
        if($picimg === false)
        {
            log_error($loc, 'imagecreatefromjpeg() failed on our image: ' . $picfile);
            return false;
        }
        
        $result = @imagecopyresampled($img, $picimg, 124, 117, 0, 0, 
                   350, 350, $picwidth, $picheight);
        if($result === false)
        {
            log_error($loc, 'imagecopyresized() failed for PidId=' . $picid);
            return false;
        } 
    }
    
    // Now we slap the entire badge image with the hole over the headshot.
    
    $backfile = 'img/label_with_hole.png';
    if(file_exists($backfile))
    {
        $backimg = @imagecreatefrompng($backfile);
        if($backimg === false)
        {
            log_error($loc, 'imagecreatefrompng() failed on our image: ' . $backfile);
        }
        else
        {
            $xsz = imagesx($img);
            $ysz = imagesy($img);
            $result = @imagecopyresampled($img, $backimg, 0, 0, 0, 0, $w, $h, $xsz, $ysz);
        }
    }
    
    $iparam['image'] = $img;
    $iparam['fontsize'] = 60.0;
    $iparam['color'] = $red;
    $iparam['angle'] = 0.0;
    $iparam['fontfile'] = 'fonts/prototype.ttf';
 
    if(!empty($nickname)) $fn = $nickname;
    else                  $fn = $firstname;

    $ink   = ImageColorAllocate($img,0x00,0x00,0x66);
    
    if(empty($title)) $p = 0.02;
    else $p = 0.00;
    
    $iparam['fontsize'] = 60.0;
    $iparam['color'] = $ink;
    DrawTextCentered($iparam, $fn, 0.5, 0.87);
    $iparam['fontsize'] = 45.0;
    DrawTextCentered($iparam, $lastname, 0.5, 0.94);
    $iparam['fontsize'] = 25.0;
    $iparam['color'] = $black;
    if(!empty($title)) DrawTextCentered($iparam, $title, 0.5, 0.97);
   
    return $img;
}


?>

