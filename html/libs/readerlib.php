<?php
// --------------------------------------------------------------------
// readerlib.php:  library fucntions that deal with the reader.
//
// Created:  1/15/14 DLB
// --------------------------------------------------------------------

// These functions deal with the reader.
//
// Currently, the reader makes log files.  We read the log files
// and fed the data into the SQL database.

require_once "libs/all.php";
require_once "libs/php-barcode.php";

// --------------------------------------------------------------------
// Returns an array of all scores for all events up to the last day,
// as set in the preferences... Use dumpit() to understand the output.
function CalculateAllScores()
{
    $users = GetSqlTable("UserView", "ORDER BY LastName,FirstName");
    $lastday = GetLastDayForAttendance();
    $ac = EventCounts($lastday);
    $meetingtable = GetSqlTable("EventTimes", "ORDER BY StartTime");
    $corrections = GetSqlTable("Corrections");
    $c = $ac[0] + $ac[1];   // Count of manditory and Regular meetings.
    $ans = array();
    $nu = 0;
    foreach($users as $u)
    {
        $row = array();
        $badgeid = $u["BadgeID"];
        if(empty($badgeid)) continue;
        $r["Name"] = $u["LastName"] . ', ' . $u["FirstName"];
        $r["UserID"] = $u["UserID"];
        $x = CalculateAttendanceScore($badgeid, $meetingtable, $lastday, $c, $corrections);
        $row = array_merge($r, $x);
        $ans[] = $row;
        $nu++;
    }
    $summary["Counts"] = $ac;
    $summary["LastDay"] = $lastday;
    $summary["NUsers"] = $nu;
    return array_merge($summary, $ans);
}

// --------------------------------------------------------------------
// Calculates the attendance score for one person, according to badgeID.
// Do not use this for sets of people -- ineffiecent.
// The output is two arrays:
//   THe first element is an array with keys: Score, NumEvents, TotalHours, OutHours, InHours.
//   The second element is an array of arrays. Each element array has keys:
//           Name (strin), Hours (float), Present (T/F).
function CalculateScoreForOne($badgeid)
{
    $lastday = GetLastDayForAttendance();
    $ac = EventCounts($lastday);
    $meetingtable = GetSqlTable("EventTimes", "ORDER BY StartTime");
    $corrections = GetSqlTable("Corrections");
    $c = $ac[0] + $ac[1];   // Count of manditory and Regular meetings.
    $s = CalculateAttendanceScore($badgeid, $meetingtable, $lastday, $c, $corrections);
    $score["LastDay"] = $lastday;
    $score["Counts"] = $ac;
    return array_merge($score, $s);
}

// --------------------------------------------------------------------
// Calculates the attendance score for one user given a meeting table.
// The meeting table is an array of events, where one event is an associtive
// array with the following keys: Name, StartTime, EndTime, Type, and Purpose.
// The output is two arrays:
//   THe first element is an array with keys: Score, NumEvents, TotalHours, OutHours, InHours.
//   The second element is an array of arrays. Each element array has keys:
//           Name (strin), Hours (float), Present (T/F).
function CalculateAttendanceScore($badgeid, $meetingtable, $lastday='', $count=1, $corrections=null)
{
    $show = false;  // For debugging.
    //if($badgeid == 'a004' || $badgeid == 'A004') $show = true;

    $loc = 'readerlib.php->CalculateAttendanceScore';
    $sql = 'SELECT * from RawScans WHERE BadgeID="' . $badgeid . '" ORDER BY ScanTime';
    $result = SqlQuery($loc, $sql);
    $scantable = array();
    while($row = $result->fetch_assoc()) $scantable[] = $row;

    $scantable = ApplyCorrections($scantable, $badgeid, $corrections);
    
    $segs = ScanSegments($scantable);
    
    $segs = ScanCorrection_Midnight($segs, $meetingtable);
   
    $ans = array();
    $inhours = 0.0;
    $npresent = 0;
    $tstop = time();
    $ncount = 0;
    if(!empty($lastday)) { $tstop = strtotime($lastday); }
    foreach($meetingtable as $event)
    {
        $etime = strtotime($event["StartTime"]);
        if($etime > $tstop) break;
        $score = AttendanceScoreForEvent($segs, $event, $show);
        $score['Name'] = $event['Name'];
        $inhours += $score["Hours"];
        $ans[] = $score;
        if($score['Present'] === true) $npresent++;
        $ncount++;
    }
    
    $tsum = 0.0;
    foreach($segs as $s) {$tsum += $s["Elapse"]; }
    $tsum = $tsum / 3600.0;
    $outhours = $tsum - $inhours;
    if($outhours < 0.0) $outhours = 0.0;  // Take care of roundoff
    
    $summary = array("Score" =>  100*($npresent/$count),
                     "Present" => $npresent, 
                     "TotalHours" => $tsum, 
                     "OutHours" => $outhours, 
                     "InHours" => $inhours,
                     "NEvents" => $ncount);
    //return array($summary, $ans);
    return array_merge($summary, $ans);
}

// --------------------------------------------------------------------
// Returns the last day for attendance.  This is optained from the
// reader preference... If not found, the current time is used.
function GetLastDayForAttendance()
{
    $prefs = GetPrefsForUser(0);
    if(isset($prefs["LastDay"]))
    {
        $r = strtotime($prefs["LastDay"]);
        if($r === false) {return date("Y-m-d"); }
        return $prefs["LastDay"];
    }
    return date("Y-m-d");
}

// --------------------------------------------------------------------
// Calaculates the attendence score for an event.  An event is an array
// with the following keys: Name, StartTime, EndTime, Type, Purpose.
// A scantable is an array, where each element of the array contains an
// associtive array with the following keys: ScanTime, Direction, Flags.
// Output is an array with these keys: Present (T/F), Hours (Float).
// Note that Hours is all the time spent on the day of the event, not
// just during the event.
function AttendanceScoreForEvent($segs, $event, $show)
{
    // Work with Unix timestamps...
    $starttime = strtotime($event["StartTime"]);
    $endtime   = strtotime($event["EndTime"]);
    $duration  = $endtime - $starttime;
    
    $datestart = strtotime(date("Y-m-d 00:00:00", $starttime));
    
    $dateend = $datestart + 24.0*3600.0 - 1;
    //echo '<br> EventStart = ' . date("Y-m-d H:i:s", $starttime);
    //echo '<br> EventEnd   = ' . date("Y-m-d H:i:s", $endtime);
    //echo '<br> DateStart  = ' . date("Y-m-d H:i:s", $datestart);
    //echo '<br> DateEnd    = ' . date("Y-m-d H:i:s", $dateend);
    
    $p = false;    // If present at any time during event.
    $h = 0.0;      // Accured time on date of event.
    $c = false;    // If segment that triggered the event had a correction.
    
    if($show) 
    {
        echo '<br>Event:<br>';
        dumpit($event);
    }
    
    foreach($segs as $s)
    {
        $t = SegOverlap($s, $datestart, $dateend);
        //echo '</br>Overlap for ' . date("Y-m-d H:i:s" , $s["Time"]) . ', Elp=' . $s["Elapse"] . ' => ' . $t;
        
        $h += $t;
        if($t > 0.001 && isset($s["Correction"])) $c = $s["Correction"];
        
        $x = SegOverlap($s, $starttime, $endtime);
        if($x !== 'none') $p = true;
        if($show)
        {
            dumpit($s);
            dumpit($x);
            echo "p=" . strval($p) . '<br>';
        }
    }
    $score["Present"] = $p;
    $score["Hours"] = ($h / 3600.0);
    if($c) $score["Correction"] = $c;
    return $score;
}

// --------------------------------------------------------------------
// Returns the number of seconds that a segment overlaps the given
// event times.  If there is no overlap, "none" is returned.  Note that
// an overlap does occur if zero is returned.
function SegOverlap($seg, $starttime, $endtime)
{
    $t0 = $seg["Time"];
    $elp = $seg["Elapse"];
    $t1 = $t0 + $elp;
    
    if($t1 < $starttime) return "none";
    if($t0 > $endtime) return "none";
    if($t0 < $starttime) $t0 = $starttime;
    if($t1 > $endtime) $t1 = $endtime;
    return $t1 - $t0;
}

// --------------------------------------------------------------------
// Given a scan table from one badge, create a list of scan segments.
// The segments have two elements: a starttime (unix), and a length
// in seconds.  No corrective processing is done, except to end every
// segment on a new scan, weither or not that scan is a "scan-out";

function ScanSegments($scantable)
{
    // Loop through the scan table, finding valid scans, 
    // and convert the times to unix timestamps.
    $fm = 'Y-m-d H:i:s';
    $scans = array();
    $state = 'out';
    $t0 = 0.0;
    foreach($scantable as $s)
    {
        if(strtolower($s["Flags"]) != "okay") continue;
        $tm = strtotime($s["ScanTime"]);
        $d = "in";
        if($s["Direction"] != 0) $d = "out";
        if($state == 'in')
        {
            $elp = $tm - $t0;
            $sn = array('Time' => $t0, 'Elapse' => $elp, 'Date' => date($fm, $t0));
            if(isset($s["Correction"])) $sn["Correction"] = $s["Correction"];
            $scans[] = $sn;
            $t0 = $tm;
            $state = $d;
        }
        else  // Process with $state == 'out'
        {
            $t0 = $tm;
            if($d == 'out')
            {
                $sn = array('Time' => $t0, 'Elapse' => 0.0, 'Date' => date($fm, $t0));
                if(isset($s["Correction"])) $sn["Correction"] = $s["Correction"];
                $scans[] = $sn;
            }
            else
            {
                $state = 'in';
            }
        }
    }
    if($state == 'in') 
    {
        $scans[] = array('Time' => $t0, 'Elapse' => 25*3600.0, 'Date' => date($fm, $t0), 'Correction' => 'Scan out artificially added.');
    }
    return $scans;
}

// --------------------------------------------------------------------
// Apply corrections to the scan data.  One correction is to not allow
// a segment to extend past midnight. 
function ScanCorrection_Midnight($segs, $meetingtable)
{
    // Look for scans that extend past midnight.  Cut them back so
    // they end at the end of the meeting...
    $sc = array();
    foreach($segs as $s)
    {
        $t0 = $s["Time"];
        $sday = date("Y-m-d", $t0);
        $day = strtotime($sday);
        if($t0 + $s["Elapse"] < $day + 24*3600)
        {
            // All is okay.
            $sc[] = $s;
        }
        else // Try to fix it...
        {
            // This scan extends past midnight.
            // Find a meeting that is associated with it.
            $fixed = false;
            foreach($meetingtable as $m)
            {
                $tm0 = strtotime($m["StartTime"]);
                $mday = date("Y-m-d", $tm0);
                if($mday == $sday) 
                {
                    // Limit the scan to the end of the meeting.
                    $tm1 = strtotime($m["EndTime"]);
                    $e = $tm1 - $t0;  
                    if($e > 0.0)
                    {
                        $s["Elapse"] = $e;
                        $s["Correction"] = "No Out Scan before Midnight";
                        $sc[] = $s;
                    }
                    break;
                }
            }
        }
    }
    
    return $sc;
}

// --------------------------------------------------------------------
// Apply hand-made corrections from the database for one badge to the
// scantable.
function ApplyCorrections($scantable, $badgeid, $corrections)
{
    if($corrections == null) return $scantable;
    foreach($corrections as $c)
    {
        if($c["BadgeID"] == '*' || strtolower($badgeid) == strtolower($c["BadgeID"]))
        {   
            $scantable = MakeCorrection($scantable, $c, $badgeid);
        }
    }
    return $scantable;
}

// --------------------------------------------------------------------
// Apply a single correction for one badge to the scantable.
function MakeCorrection($scantable, $c, $badgeid)
{
    if(strtolower($c["Action"]) == "deletescan")
    {
        $d0 = strtotime(date("Y-m-d 00:00:00", strtotime($c["ScanTime"])));
        $d1 = $d0 + 24*3600.0;
        $newtable = array();
        foreach($scantable as $s)
        {
            $tme = strtotime($s["ScanTime"]);
            if($tme >= $d0 && $tme < $d1) 
            {
                $s["Flags"] = "deleted";
                $s["Correction"] = $c["Reason"];
            }
            $newtable[] = $s;
        }
        return $newtable;
    }
    if(strtolower($c["Action"]) == "addscanin")
    {
        $scan["BadgeID"] = $badgeid;
        $scan["ScanTime"] = date("Y-m-d H:i:s", strtotime($c["ScanTime"]));
        $scan["Direction"] = 0;
        $scan["Flags"] = "okay";
        $scan["Method"] = "Correction";
        $scan["ReaderID"] = 'none';
        $scan["Correction"] = $c["Reason"];
        $scantable[] = $scan;
        usort($scantable, "ScanTimeCompare");
        return $scantable;
    }
    if(strtolower($c["Action"]) == "addscanout")
    {
        $scan["BadgeID"] = $badgeid;
        $scan["ScanTime"] = date("Y-m-d H:i:s", strtotime($c["ScanTime"]));
        $scan["Direction"] = 1;
        $scan["Flags"] = "okay";
        $scan["Method"] = "Correction";
        $scan["ReaderID"] = 'none';
        $scan["Correction"] = $c["Reason"];
        $scantable[] = $scan;
        usort($scantable, "ScanTimeCompare");
        return $scantable;
    }
}

// --------------------------------------------------------------------
// Sort a scantable so that it is in time order.
function ScanTimeCompare($a, $b)
{
    if($a["ScanTime"] == $b["ScanTime"]) return 0;
    if($a["ScanTime"] < $b["ScanTime"]) return -1;
    return 1;
}

// --------------------------------------------------------------------
// Reads the database and preferences to get the current event counts.
// Returned array is: number of regular events, number of optional 
// events, and number of manditory events.  All these are counts until
// the last day given -- (as a time string).  If no last time given,
// then current time is used.
function EventCounts($lastday = "")
{
    $tstop = time();
    if(!empty($lastday)) $tstop = strtotime($lastday);
    $events = GetSqlTable("EventTimes", "ORDER BY StartTime");
    $nreg = 0;
    $nopt = 0;
    $nmad = 0;
    foreach($events as $e)
    {
        $t0 = strtotime($e["StartTime"]);
        if($t0 > $tstop) break;
        if(strtolower($e["Type"]) == "regular") $nreg++;
        if(strtolower($e["Type"]) == "optional") $nopt++;
        if(strtolower($e["Type"]) == "manditory") $nmad++;
    }
    return array($nmad, $nreg, $nopt);
}

// --------------------------------------------------------------------
// Input a log file into the database.  On success, returns an array
// of four elements: the number of lines processed, the number of
// new events added, events updated, and number of lines ignored. 
// On error, returns false.
function ProcessLogFile($filename)
{
    $loc = 'readerlib.php=>ProcessLogFile';
    $contents = file_get_contents($filename);
    if($contents === false) return false;
    $n = 0;
    $nignored = 0;
    $nadded = 0;
    $nupdated = 0;
    $lines = explode("\n", $contents);
    foreach($lines as $x)
    {
        $n++;
        $y = trim($x);
        if(substr($y, 0, 1) !== '=') { $nignored++; continue; }
        $y = trim(substr($y, 1));
        $fields = explode(',', $y);
        if(count($fields) < 6) {$nignored++; continue; }
        $tme       = $fields[0];
        $badgeid   = strtolower(trim($fields[1]));
        $dir       = strtolower(trim($fields[2]));
        $flag      = strtolower(trim($fields[3]));
        $firstname = trim($fields[4]);
        $lastname  = trim($fields[5]);
        
        $method = "log";
        $readerid = "B17";

        $ir = UpdateRawScan($tme, $badgeid, $dir, $flag, $method, $readerid);
        if($ir != 0) $nadded++;
        else         $nupdated++;
    }

    return array($n, $nadded, $nupdated, $nignored);
}

// --------------------------------------------------------------------
// Deletes all corrections in the database.
function DeleteAllCorrections()
{
    $loc = 'readerlib.php=>DeleteAllCorrections';
    $sql = 'DELETE FROM Corrections';
    SqlQuery($loc, $sql);
}

// --------------------------------------------------------------------
// Adds a correction into the database
function AddCorrection($action, $badgeid, $time, $reason)
{
    $loc = 'readerlib.php=>AddCorrection';
    $sql = 'INSERT INTO Corrections (Action, BadgeID, ScanTime, Reason) VALUES (';
    $sql .= '  "' . SqlClean(trim($action))  . '"';
    $sql .= ', "' . SqlClean(trim($badgeid)) . '"';
    $sql .= ', "' . SqlClean(trim($time))    . '"';
    $sql .= ', "' . SqlClean(trim($reason))  . '"';
    $sql .= ')';
    SqlQuery($loc, $sql);
}

// --------------------------------------------------------------------
// Input a corrections file into the database.  On success, returns
// an array of three elements: the number of lines processed, the number of
// corrections added, and number of lines ignored. 
// On error, returns false.
function ProcessCorrectionFile($filename)
{
    $loc = 'readerlib.php=>ProcessLogFile';
    
    $file = fopen($filename, "r");
    if($file === false)
    {
        $error_msg = "Unable to open file.";
        return false;
    }
    
    $ln = 1;
    $ncorrections = 0;
    $nbad = 0;
    // The first line is the column headers. 
    $header = fgetcsv($file); $ln++;
    if($header === false) 
    {
        $m = 'No header for input corrections file, line ' . $ln;
        log_msg($loc, $m);
        echo $m . "<br>";
        return false; 
    }
    // Trim the header values...
    $hh = array();
    foreach($header as $h)
    {
        $hh[] = trim($h);
    }
    $header = $hh;
    
    // Now, do some sanity checks to make sure we have
    // an appropriate file.
    if(!in_array("Action",   $header) ||
       !in_array("BadgeID",  $header) ||
       !in_array("T0",       $header) ||
       !in_array("Reason",   $header))
    {
        dumpit($header);
        $m = "Invalid columns for correction file, line " . $ln;
        log_msg($loc, $m);
        echo $m . "<br>";
        return false;
    }
    while(true)
    {
        $data = fgetcsv($file); $ln++;
        if($data === false) break;
        // Don't process blank lines.
        if(count($data) <= 0) continue;  
        if(is_null($data[0])) continue;
        
        // Organize the data into an associtive array
        $fields = JoinKeyValues($header, $data);
        
        // Make sure we have required data
        if(!isset($fields["Action"])  ||
           !isset($fields["BadgeID"])  ||
           !isset($fields["T0"]))
        {
            $m = 'Required data not found in correction file, line ' . $ln;
            log_msg($loc, $m);
            echo $m . "<br>";
            $nbad++;
            continue;
        }
        $action = $fields["Action"];
        $badgeid = strtolower($fields["BadgeID"]);
        if(isset($fields["Reason"])) $reason = $fields["Reason"];
        else $reason = "(Unknown)";
        
        // Make sure T0 is a valid time between 2000 and 2030.
        $tme = strtotime($fields["T0"]);
        if($tme === false) { $nbad++; continue; }
        $d0 = strtotime("2000-01-01");
        $d1 = strtotime("2030-01-01");
        if($tme < $d0 || $tme > $d1) {$nbad++; continue;}
        
        // Okay, store the record.
        AddCorrection($action, $badgeid, date("Y-m-d H:i:s", $tme), $reason);
        $ncorrections++;
    }

    return array($ln, $ncorrections, $nbad);
}

// --------------------------------------------------------------------
// Adds or Updates a RawScan record.  Returns 1 if a new record is
// created, or 0 if the record is updated.
function UpdateRawScan($tme, $badgeid, $dir, $flag, $method, $readerid)
{
    $loc = 'readerlib.php=>UpdateRawScan';

    $idir = 2;                       // Direction unknown
    if($dir == 'front') $idir = 0;   // Scan in
    if($dir == 'back') $idir = 1;    // Scan out

    $sql = 'SELECT * FROM RawScans WHERE ScanTime="' . $tme . '" AND BadgeID="' . $badgeid .'"';
    $result = SqlQuery($loc, $sql);
    if($result->num_rows == 0)
    {
        $sql = 'INSERT INTO RawScans (BadgeID, ScanTime, Direction, Flags, Method, ReaderID) ';
        $sql .= ' VALUES(';
        $sql .= '  "' . $badgeid  . '"';
        $sql .= ', "' . $tme      . '"';
        $sql .= ', '  . $idir;
        $sql .= ', "' . $flag     . '"';
        $sql .= ', "' . $method   . '"';
        $sql .= ', "' . $readerid . '"';
        $sql .= ')';
        SqlQuery($loc, $sql);
        return 1;
    }
    else 
    {
        $sql = 'UPDATE RawScans SET';
        $sql .= '  Direction=' . $idir;
        $sql .= ', Flags="' . $flag . '"';
        $sql .= ', Method="' . $method . '"';
        $sql .= ', ReaderID="' . $readerid . '"';
        $sql .= ' WHERE ScanTime="' . $tme . '" AND BadgeID="' . $badgeid . '"';
        SqlQuery($loc, $sql);
        return 0;
    }        
}

// --------------------------------------------------------------------
// Remove all events from the event table.
function RemoveAllEvents()
{
    $loc = 'readerlib.php=>RemoveAllEvents';
    $sql = 'Delete From EventTimes';
    SqlQuery($loc, $sql);
}

// --------------------------------------------------------------------
// Addes an event to the event table.  Input is an associtive array,
// where the keys are expected to be: Name, StartTime, EndTime, Type,
// and Purpose.
function StoreEvent($fields)
{
    $loc = 'readerlib.php=>StoreEvent';
    $sql = 'INSERT INTO EventTimes (Name, StartTime, EndTime, Type, Purpose) ';
    $sql .= 'VALUES (';
    $sql .= '  "' . SqlClean($fields["Name"]) . '"';
    $sql .= ', "' . SqlClean($fields["StartTime"]) . '"';
    $sql .= ', "' . SqlClean($fields["EndTime"]) . '"';
    $sql .= ', "' . SqlClean($fields["Type"]) . '"';
    $sql .= ', "' . SqlClean($fields["Purpose"]) . '"';
    $sql .= ')';
    SqlQuery($loc, $sql);
}
