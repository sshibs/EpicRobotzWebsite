<?php
// --------------------------------------------------------------------
// teamlib.php:  library functions to handle the Team table and data
//               necessary for team reports.
//
// Created: 12/23/14 DLB
// --------------------------------------------------------------------

require_once "config.php";
require_once "libs/loglib.php";
require_once "libs/databaselib.php";


// --------------------------------------------------------------------
// Inserts or updates a team with the given data in the associtive
// array.  Returns false if nothing to update, true otherwise.  Will
// die on serious errors.
function UpdateTeam($tn, $data)
{
    $loc = "teamlib.php->UpdateTeam";
    $fields = array(array("BestPicID", "int"),
                    array("NickName",  "str"));
    $tn = intval($tn);
    if($tn < 1 || $tn > 9999) DieWithMsg($loc, "illegal tn value.");
    $row = GetTeamInfo($tn);
    if($row == false)
    {
        // This will be the first insert!
        // Add the teamnumber field and data.
        $fields[] = array("TeamNumber", "int");
        $data["TeamNumber"] = $tn;
        $sql = "INSERT INTO Teams " . GenerateSqlInsert($data, $fields);
        SqlQuery($loc, $sql);
        return true;
    }
    else 
    {
        // This will be an update.
        $set = GenerateSqlSet($data, $fields);
        if($set == false) return false;
        $sql = "UPDATE Teams SET " . $set . " WHERE TeamNumber = " . intval($tn);
        SqlQuery($loc, $sql);
        return true;
    }
}

// --------------------------------------------------------------------
// Returns an associtive array that contains keys for each field in
// the Teams table, for a given team.  If the team is not found, false
// is returned.
function GetTeamInfo($tn)
{
    $loc = "teamlib.php->GetTeamInfo";
    $tn = intval($tn);
    if($tn < 1 || $tn > 9999) DieWithMsg($loc, "illegal tn value.");
    $loc = "teamlib.php->GetTeamInfo";
    $sql = "SELECT * from Teams WHERE TeamNumber=" . intval($tn);
    $result = SqlQuery($loc, $sql);
    if($result == false) return false;
    if($result->num_rows <= 0) return false;
    $row = $result->fetch_assoc();
    return $row;
}

// --------------------------------------------------------------------
// Creates the team info report.  Returns an array of TeamReportInfo
// objects, one for each known team in the system. A team is "known"
// if it appears in the TeamPic Table, the RawTeamInfo Table, or 
// the Team table.
function GetTeamReport()
{
    $loc = "reportlib.php->GetTeamReport";
    $timer = new Timer();
    $sql = "SELECT Distinct TeamNumber FROM TeamPics ";
    $sql .= "UNION SELECT Distinct TeamNumber From RawTeamInfo ";
    $sql .= "UNION SELECT Distinct TeamNumber From Teams ";
    $sql .= "ORDER BY TeamNumber";
    $result = SqlQuery($loc, $sql);
    $out = array();
    $n = 0;
    while($row = $result->fetch_assoc())
    {
        $t = new TeamReportInfo();
        $t->LoadData($row["TeamNumber"]);
        $out[] = $t;
        $n++;
    }
    log_msg($loc, 'Team report generated, with ' . $n . ' teams. Elp=' . $timer->MsStr() . '.');
    return $out;
}

// --------------------------------------------------------------------
// A container for info about one team.   Pulls data from various
// tables.
class TeamReportInfo
{
    private $loc = "reportlib.php->TeamReportInfo";
    public  $tn = 0;
    private $Scouts = array();
    private $BestPicID = 0;
    private $Wins = 0;
    private $Losses = 0;
    private $MatchReportIDs = array();
    private $PicIDs = array();
    private $NickName = "";
    
    // Loads all data for this object.  Does multiple SQL queries.  Must
    // be called on object creation to make a useful object.
    public function LoadData($tn)
    {
        $this->tn = $tn;
        $sql = 'SELECT Distinct ScoutID FROM RawTeamInfo WHERE TeamNumber=' . intval($tn);
        $result = SqlQuery($this->loc, $sql);
        $this->Scouts = array();
        while($row = $result->fetch_assoc())
        {
            $id = intval($row["ScoutID"]);
            $info = GetUserInfo($id);
            $this->Scouts[] = $info;
        }
        
        $sql = 'SELECT PicID FROM TeamPics WHERE TeamNumber=' . intval($tn);
        $result = SqlQuery($this->loc, $sql);
        $this->PicIDs = array();
        while($row = $result->fetch_assoc())
        {
            $this->PicIDs[] = intval($row["PicID"]);
        }
        
        $data = GetTeamInfo($tn);
        if(isset($data["BestPicID"])) $this->BestPicID = $data["BestPicID"];
        if(isset($data["NickName"]))  $this->NickName = $data["NickName"];
    }
    
    // Returns the team number of the data contained in this object.
    public function TeamNumber()
    {
        return $this->tn;
    }
    
    // Returns a URL to the best pic that represents this team/robot (as a thumbnail)
    // Always works, regardless if a best pic has not been chosen by an editor.
    public function BestPicUrl()
    {
        if($this->BestPicID == 0) return "img/nopic.jpg";
        else return PicUrl($this->BestPicID, "thumb");
    }
    
    // Returns the ID of the best pic to use, or zero if none.
    public function BestPicID()
    {
        return $this->BestPicID;
    }

    // Returns an array of associtive arrays, each of which has info
    // about the scouts that produced the pit reports.  The keys for
    // each scout are the same as those produced by GetUserInfo() in 
    // userlib.php.
    public function Scouts()
    {
        return $this->Scouts;
    }
   
    // Returns an array of picture IDs related to this team.
    public function PicList()
    {
        return $this->PicIDs;
    }
    
    // Returns the name that we have invented for this team.
    public function NickName()
    {   
        return $this->NickName;
    }

    // Returns an array of Match Reports ID related to this team.
    public function GetMatchReportIDs()
    {
        return $this->MatchReportIDs;
    }
    
    // Returns the number of matches won by this team.
    public function Wins()
    {
        return $this->Wins;
    }
    
    // Returns the number of matches lost by this team.
    public function Losses()
    {
        return $this->Losses;
    }

}


?>