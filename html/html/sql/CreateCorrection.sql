/*********************************************************
** Creates Tables used for correcting attendance.
*/

Use EpicAdmin;
/* drop Table EventTimes; */
create Table Corrections
(
   Action varchar(20),    /* Action: DeleteScan, AddScanIn, AddScanOut */
   BadgeID char(4),       /* Should be in the form of 'a000', or '*' */
   ScanTime datetime,     /* The time of the correction. */
   Reason varchar(200)    /* A commment on the reason behind the correction */
);
