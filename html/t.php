<?php
// --------------------------------------------------------------
// t.php -- Simple test page to perform one-time testing.
//
// Created: 12/29/14 DLB
// --------------------------------------------------------------------

require_once "libs/all.php";
session_start();
log_page();
$timer = new Timer();
$loc = "t.php";

include "forms/header.php";
include "forms/navform.php";
echo '<div class="content_area">';

//AddCorrection("DeleteScan", "*", "2015-01-03 10:00:00", "Just Testing.");

$a = array(array("name"=>"Dal", "age" => 58), array("name"=>"Keith", "age"=>55), array("name"=>"Gail", "age"=>53));
dumpit($a);
usort($a, "dcmp");
dumpit($a);


function dcmp($a, $b)
{
    if($a["age"] == $b["age"]) return 0;
    if($a["age"] > $b["age"]) return 1;
    else return -1;
}

//dumpit(CalculateAllScores());

//dumpit(CalculateScoreForOne("a004"));

//dumpit(GetLastDayForAttendance());

//$lastday = "1/14/2015";
//$x = EventCounts($lastday);
//$c = $x[0] + $x[1];
//$meetingtable = GetSqlTable("EventTimes");
//$score = CalculateAttendanceScore("a004", $meetingtable, $lastday, $c);
//dumpit($score);

//$badgeid = "a004";
//$sql = "SELECT * FROM RawScans";
//$result = SqlQuery($loc, $sql);
//while($row = $result->fetch_assoc()) $meetingtable[] = $row;
//dumpit($meetingtable);

//$score = CalculateAttendanceScore($badgeid, $meetingtable, $params);
//dumpit($score);


//$sql = 'SELECT * from RawScans WHERE BadgeID="' . $badgeid . '"';
//$result = SqlQuery($loc, $sql);
//$scantable = array();
//while($row = $result->fetch_assoc()) $scantable[] = $row;
//dumpit($scantable);
//$x = ScanSegments($scantable);
//dumpit($x);


//$x1 = array("A001", "A002", "A003", "A004", "A005", "A006", "A007", "A008");  // Sheet 1
//$x2 = array("A009", "A010", "A011", "A012", "A013", "A014", "A015", "A016");  // Sheet 2
//$x3 = array("A017", "A018", "A019", "A020", "A021", "A022", "A023", "A024");  // Sheet 3
//$x4 = array("A025", "A026", "A027", "A028", "A029", "A030", "A031", "A032");  // Sheet 4
//$x5 = array("A033", "A034", "A035", "A036", "A037", "A038", "A039", "A040");  // Sheet 5
//$x6 = array("A041", "A042", "A043", "A044", "A045", "A046", "A047", "A048");  // Sheet 6
//$x7 = array("A049", "A050", "A051", "A052", "A053", "A054", "A055", "A056");  // Sheet 7
//
//$sql = "SELECT UserName from Users";
//$result = SqlQuery($loc, $sql);
//$usernames = array();
//while($row = $result->fetch_assoc())
//{
//    $usernames[] = $row["UserName"];
//}
//echo '<br>Number=' . count($usernames);
//MakePrintLabels($usernames, 'labels');
//echo '<br>All Done.';
//

//MakePrintSheet($x1, 'sheet_1');
//MakePrintSheet($x2, 'sheet_2');
//MakePrintSheet($x3, 'sheet_3');
//MakePrintSheet($x4, 'sheet_4');
//MakePrintSheet($x5, 'sheet_5');
//MakePrintSheet($x6, 'sheet_6');
//MakePrintSheet($x7, 'sheet_7');

//$data = array('BadgeID' => 'A010', 'FirstName' => 'Dal', 'LastName'=>'Brandon', 
//              'Title' => 'Software Mentor', 'NickName' => 'Dal', 'PicID' => 51);
//
//echo '<br>MakeBadge($data)=' . MakeBadge($data);
//echo '<br>';
//echo '<img src="' . GetBadgeUrl('A010', 'front') . '" style="width: 200px; height: auto; border: 1px; border-style: solid; border-color: black;">';             
//echo '<img src="' . GetBadgeUrl('A010', 'back') . '" style="width: 200px; height: auto; border: 1px; border-style: solid; border-color: black;">';             


//echo '<br>A123  =' . TFstr(VerifyBadgeFormat("A123"));
//echo '<br>22331 =' . TFstr(VerifyBadgeFormat("22331"));
//echo '<br>Dal3  =' . TFstr(VerifyBadgeFormat("Dal3"));
//echo '<br>C0    =' . TFstr(VerifyBadgeFormat("C0"));
//echo '<br>C12   =' . TFstr(VerifyBadgeFormat("C12"));
//echo '<br>C123  =' . TFstr(VerifyBadgeFormat("C123"));
//
//$data["Dal"] = "brandon";
//$data["Gail"] = "dubel";
//$data["Keith"] = "reimer";
//foreach($data as $d) echo '<br>' . $d;
//echo '<br>';
// 
//echo '<br>' . tfstr(true);
//echo '<br>' . TFstr(true);
//echo '<br>' . TFStr(true);
//echo '<br>' . TFSTR(true);
//echo '<br>';
//
//$pwHash = crypt("epic", $config["Salt"]);
//echo $pwHash;
//
//$d = null;
//echo '<br>isset($d)=' . TFstr(isset($d));
//echo '<br>isset($asdf)=' . TFstr(isset($asdf));
//echo '<br>isset($_POST["asdfad"])=' . TFstr(isset($_POST['asdfad']));
//echo '<br>isset($_POST)=' . TFstr(isset($_POST));
//echo '<br>';
//

//for($i = 0; $i < 10; $i++){
//echo '<p>This is some content. </p>';
//

//$timer = new Timer();

//$data = array("BestPicID" => 23, "NickName" => "BestKid");
//UpdateTeam(1, $data);
//$v = GetTeamInfo(1);
//dumpit($v);


//$p = GetTeamReport();
//echo '<br>' . $timer->MsStr();

//SqlClean(15);

//dumpit(SqlClean(15));

//dumpit(TFstr(IsSqlTextOkay("0")));

//echo '<br>is_bool("")      =' . TFstr(is_bool(""));
//echo '<br>is_bool("0")     =' . TFstr(is_bool("0"));
//echo '<br>is_bool("true")  =' . TFstr(is_bool("true"));
//echo '<br>is_bool("false") =' . TFstr(is_bool("false"));
//echo '<br>is_bool(true)    =' . TFstr(is_bool(true));
//echo '<br>is_bool(false)   =' . TFstr(is_bool(false));
//
//echo '<br>blank("0")   =' . TFStr(blank("0"));
//echo '<br>blank(0)     =' . TFStr(blank(0));
//echo '<br>blank(0.0)   =' . TFStr(blank(0.0));
//echo '<br>blank(null)  =' . TFStr(blank(null));
//echo '<br>blank(1)     =' . TFStr(blank(1));
//echo '<br>blank("")    =' . TFStr(blank(""));
//echo '<br>blank(55)    =' . TFStr(blank(55));
//echo '<br>blank(false) =' . TFStr(blank(false));
//echo '<br>blank(true)  =' . TFStr(blank(true));
//echo '<br>blank("hi")  =' . TFStr(blank("hi"));
//echo '<br>blank($sdadf)=' . TFStr(blank($sdafd));

//dumpit(SqlClean('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQURSTUVWXYZ0123456789'));
//dumpit(SqlClean('~`!@#$%^&*()-_=+[]{}\|;:,.<>/? '));
//dumpit(SqlClean("This is a break. \n Next Line."));
//dumpit(SqlClean("Hello There.  Carol said 'I love you'"));
//dumpit(SqlClean('Hello There.  Carol said "I love you"'));

//dumpit($_POST);
//dumpit(TFStr(IsSqlTextOkay($_POST)));

//echo '<form action="test2.php" method="post">';
//echo '<input type="textbox" name="MyStuff"/>';
//echo '<input type="submit" value="Process" name="submit" />';

 
//<form action="test2.php" method="post" enctype="multipart/form-data">
//Test File<input type ="file" name ="MyFile"></input>
// <input type="submit" value="Process" name="submit" />
// </form>

// 


//dumpit(GetUserIDFromName("DavidF"));

//dumpit(GetUserInfo(1));

//$pwHash = crypt("loveroot", $config["Salt"]);
//echo $pwHash;

// Shows what wrappers are working:
/*
$w = stream_get_wrappers();
echo '<pre>';
echo 'openssl: ',  extension_loaded  ('openssl') ? 'yes':'no', "\n";
echo 'http wrapper: ', in_array('http', $w) ? 'yes':'no', "\n";
echo 'https wrapper: ', in_array('https', $w) ? 'yes':'no', "\n";
echo 'wrappers: ', var_dump($w);
echo '</pre>';
*/

//$dir = $config["UploadDir"] . "temp";
//mkdir($dir);

//$target = $config["UploadDir"] . "temp/.pic_01.jpg";
//$source = 'https://dl.dropboxusercontent.com/u/2783543/EpicPics/IMG_5689.JPG';
//echo "<br>Target=" . $target;
//echo "<br>Source=" . $source;
//echo "<br>";
//copy($source, $target);

//DieNice("editteamphoto.php",
// 'Sorry, this page is not avaliable to you ' .
// 'because you are not a Scout or an Editor.');


//$origfile = PicPathName(1, "orig");
//dumpit("info", getimagesize($origfile));
//dumpit("IMG_JPG", IMG_JPG);

//$img = imagecreatefromjpeg($origfile);
//dumpit("img", $img);
//$sizes = @getimagesize($origfile);
//dumpit("sizes", $sizes);

//$v = getimagesize($origfile);
//var_dump($v);

//$id = CreatePicID(GetUserID(), 123, "This is a Caption", "Robot", "Front", "No Comment.");
//$result = FinishUpload($id);
//echo "<br>ID = " . $id;
//echo "<br>FinishUpLoad Result = " . TFstr($result);


//$extension = '.' . pathinfo($origfile)["extension"];
//$basename  = basename($origfile, $extension);
//echo "<br> orig=" . $origfile;
//echo "<br> ext=" . $extension;
//echo "<br> name=" . $basename;
//echo "<br>";
//echo var_dump(pathinfo($origfile));


//CheckPicDirs();

//echo var_dump(get_loaded_extensions());
//echo "<br> UploadDir=" . file_exists($config["UploadDir"]);
//echo "<br> orig=" . file_exists($config["UploadDir"] . "orig");
//echo "<br> w1024=" . file_exists($config["UploadDir"] . "w1024");
//echo "<br>";

//$v = getimagesize(PicPathName(1, "orig", ".jpg"));
//echo "<br>width=" . $v[0];
//echo "<br>height=" . $v[1] . "<br>";
//echo var_dump(getimagesize(PicPathName(1, "orig", ".jpg")));
//echo "<br>Change Reporting<br>";
//$oldrpt = error_reporting();
//error_reporting(E_ERROR);
//$v = getimagesize("C:\\ThisMachinesWebSite\\WebRoot\\Uploads/orig/junk.txt");
//var_dump($v);

//error_reporting($oldrpt);
//echo "<br>Do it again:";
//$v = getimagesize("C:\\ThisMachinesWebSite\\WebRoot\\Uploads/orig/junk.txt");
//var_dump($v);
//$id = CreateUploadID(".jpg", 4, 56, "A Caption", "Robot", "Front", "More Comments");
//echo "<br>ID = " . $id ."<br>";
//echo "<br>Base File = " . PicPathName($id, "orig", ".jpg");


// Test Time Functions...
//$t = UnixTimeNow();
//echo "TimeStamp=" . $t . "<br>";
//echo date("Y-m-d H:i:s", $t) . "<br>";
//echo DateTimeForSQL($t). "<br>";
//echo "TimeStamp=" . DateTimeFromSQL(DateTimeForSQL($t)) . "<br>";

// Test GET and PUT stuff.
//<form name=myform action="test2.php" method="post">
//<select name=mysel>
//<option name=none value=vone> one </option>
//<option name=ntwo value=vtwo> two </option>
//<option name=nthree value=vthree> three </option>
//<option name=nfour value=vfour> four </option>
//</select>
//<input class="inputform_submit_button" type="submit" value="Submit">
//</form>

echo '</div>';
include "forms/footer.php";

?>