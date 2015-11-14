
<?php
require_once "libs/all.php";
/*$y = 0;
for ($x = 0; $x<=8; $x++) {
	$y++;
	if ($y <=8)
	{
	$b1 = "B00".$y;
	$b2 = "B00".$y++;
	$b3 = "B00".$y++;
	$b4 = "B00".$y++;
	$b5 = "B00".$y++;
	$b6 = "B00".$y++;
	$b7 = "B00".$y++;
	$b8 = "B00".$y++;
	}
	if($y == 9){
	$b1 = "B00".$y;
	$b2 = "B0".$y++;
	$b3 = "B0".$y++;
	$b4 = "B0".$y++;
	$b5 = "B0".$y++;
	$b6 = "B0".$y++;
	$b7 = "B0".$y++;
	$b8 = "B0".$y++;
}
	else {
	$b1 = "B0".$y;
	$b2 = "B0".$y++;
	$b3 = "B0".$y++;
	$b4 = "B0".$y++;
	$b5 = "B0".$y++;
	$b6 = "B0".$y++;
	$b7 = "B0".$y++;
	$b8 = "B0".$y++;
}*/
$testSheet = "Badges_2015-2016_";
$badgelist=array("B001", "B002", "B003", "B004", "B005", "B006", "B007", "B008");
MakePrintSheet($badgelist, $testSheet."1");
$badgelist=array("B009", "B010", "B011", "B012", "B013", "B014", "B015", "B016");
MakePrintSheet($badgelist, $testSheet."2");
$badgelist=array("B017", "B018", "B019", "B020", "B021", "B022", "B023", "B024");
MakePrintSheet($badgelist, $testSheet."3");
$badgelist=array("B025", "B026", "B027", "B028", "B029", "B030", "B031", "B032");
MakePrintSheet($badgelist, $testSheet."4");
$badgelist=array("B033", "B034", "B035", "B036", "B037", "B038", "B039", "B040");
MakePrintSheet($badgelist, $testSheet."5");
$badgelist=array("B041", "B042", "B043", "B044", "B045", "B046", "B047", "B048");
MakePrintSheet($badgelist, $testSheet."6");
$badgelist=array("B049", "B050", "B051", "B052", "B053", "B054", "B055", "B056");
MakePrintSheet($badgelist, $testSheet."7");
//}
?>
