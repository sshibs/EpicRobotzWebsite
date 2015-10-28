/*********************************************************
** Creates Tables used for tracking actual attendance.
*/

Use EpicAdmin;
/*drop Table EventTimes;*/
create Table EventTimes
(
   Name char(40),         /* Usually an abbrivation of the date of the meeting */
   StartTime datetime,    /* Starttime of the event. */
   EndTime datetime,      /* Endtime of the event. */
   Type char(20),         /* Regular, "Manditory", "Optional" */
   Purpose char(100)      /* Description about the event */
);

/*drop Table RawScans;*/
create Table RawScans
(
	BadgeID char(4),    /* Should be in the form of 'a000' */
    ScanTime datetime,
    Direction int,      /* 0=in, 1=out, 2=Unknown */
    Flags char(10),     /* okay, test, etc.. */
    Method char(10),    /* RT-Auto, Log, Manual */
    ReaderID char(10) 
);

drop Table Prefs;
create Table Prefs
(
   UserID int,
   PrefName varchar(32),     /* Name of the preference */
   PrefValue varchar(256)    /* Value of the preference.  All preferences are stored as strings. */
);
