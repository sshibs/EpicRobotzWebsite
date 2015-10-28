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
        $x = CalculateAttendanceScore($badgeid, $meetingtable, $lastday, $c);
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
    $c = $ac[0] + $ac[1];   // Count of manditory and Regular meetings.
    $s = CalculateAttendanceScore($badgeid, $meetingtable, $lastday, $c);
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
function CalculateAttendanceScore($badgeid, $meetingtable, $lastday='', $count=1)
{
    $loc = 'readerlib.php->CalculateAttendanceScore';
    $sql = 'SELECT * from RawScans WHERE BadgeID="' . $badgeid . '" ORDER BY ScanTime';
    $result = SqlQuery($loc, $sql);
    $scantable = array();
    while($row = $result->fetch_assoc()) $scantable[] = $row;

    $segs = ScanSegments($scantable);
    
    $segs = ScanCorrection($segs, $meetingtable);
    
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
        $score = AttendanceScoreForEvent($segs, $event);
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
function AttendanceScoreForEvent($segs, $event)
{
    //dumpit($event);
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
    foreach($segs as $s)
    {
        $t = SegOverlap($s, $datestart, $dateend);
        //echo '</br>Overlap for ' . date("Y-m-d H:i:s" , $s["Time"]) . ', Elp=' . $s["Elapse"] . ' => ' . $t;
        
        $h += $t;
        if($t > 0.001 && isset($s["Correction"])) $c = $s["Correction"];
        
        $x = SegOverlap($s, $starttime, $endtime);
        if($x != 'none') $p = true;
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
// Apply corrections to the scan data.  One correction is to not allow
// a segment to extend past midnight. 
function ScanCorrection($segs, $meetingtable)
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
            $scans[] = array('Time' => $t0, 'Elapse' => $elp, 'Date' => date($fm, $t0));
            $t0 = $tm;
            $state = $d;
        }
        else  // Process with $state == 'out'
        {
            $t0 = $tm;
            if($d == 'out')
            {
                $scans[] = array('Time' => $t0, 'Elapse' => 0.0, 'Date' => date($fm, $t0));
            }
            else
            {
                $state = 'in';
            }
        }
    }
    if($state == 'in') $scans[] = array('Time' => $t0, 'Elapse' => 0.0, 'Date' => date($fm, $t0));
    return $scans;
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
