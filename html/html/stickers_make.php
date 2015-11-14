<?php
require_once "libs/all.php";

$folder = "/stickers";
$loc = "stickers_make.php";
$sql = "SELECT NickName, LastName, Title FROM Users;";
$result =SqlQuery($loc, $sql);
$picid = 1;
$rowCount = $result->num_rows;
if ($rowCount > 0) {
    	// output data of each row
    	while($row = $result->fetch_assoc()) {
		$firstname = $row["NickName"];
		$lastname = $row["LastName"];
		$title = $row["Title"];

		$fname = "ID_" . $picid . ".gif";
		if($picid ==1){
			$img = CreateLabelPic(56, $firstname, $lastname, $title);
		}
		else{
			$img = CreateLabelPic($picid-1, $firstname, $lastname, $title);
		}
		SaveSheetImg($folder, $img, $fname);
		$picid++;
	}
}
$stickerCount = 1;
$pageCount = 0;
while($stickerCount < $rowCount-1){
	$badgelist=array();
	for($sIndex = 0; $sIndex < 12; $sIndex++){
		$stick = "ID_" . $stickerCount;
		array_push($badgelist, $stick);
		$stickerCount++;
	//echo "pushing picture id";
	}
	$StickerSheet = "Stickers_2015-2016_" . $pageCount;
	$pageCount++;
	MakeStickerPrintSheet($badgelist, $StickerSheet);


}
?>
