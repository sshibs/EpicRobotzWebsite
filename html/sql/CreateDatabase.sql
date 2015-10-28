drop Database EpicAdmin;
Create Database EpicAdmin;
Use EpicAdmin;
create Table Users
(
   UserID int AUTO_INCREMENT PRIMARY KEY,
   UserName varchar(40) NOT NULL UNIQUE,
   PasswordHash varchar(40),
   LastName varchar(80),
   FirstName varchar(80),
   NickName varchar(80),
   Title varchar(80),
   BadgeID char(10),
   Email varchar(200),
   Tags varchar(120),
   Active boolean
);

/*insert into Users (UserName, PasswordHash, LastName, FirstName, NickName, Title, BadgeID, Email, Tags, Active)
    values("dal", "41kULJSKsq756", "Brandon", "Dalbert", "Dal", "Mentor", "B001", "dalbrandon@gmail.com", "Member/Editor/Admin", TRUE);
insert into Users (Username, PasswordHash, LastName, FirstName, NickName, Title, BadgeID, Email, Tags, Active)
    values("sarah", "41w0Haer3yB3.", "Shibley", "Sarah", "Sarah", "Mentor", "B015", "sarahshib@hotmail.com", "Mentor/Admin/Member/Editor", TRUE);
insert into Users (Username, PasswordHash, LastName, FirstName, NickName, Title, BadgeID, Email, Tags, Active)
    values("brian","41w0Haer3yB3.", "Madrid", "Brian", "Brian", "Mentor", "B013", "bmad@ucla.edu", "Member/Scout/Editor/Admin", TRUE);
insert into Users (Username, PasswordHash, LastName, FirstName, NickName, Title, BadgeID, Email, Tags, Active)
    values("nathan", "41w0Haer3yB3.", "Gardner", "Nathan", "Nathan", "Mentor", "B022", "brytstahr@yahoo.com", "Member/Admin/Editor", TRUE);*/
insert into Users (Username, PasswordHash, LastName, FirstName, NickName, Title, BadgeID, Email, Tags, Active)
    values("patrick", "41w0Haer3yB3.", "Rose", "Patrick", "Patrick", "", "B041", "", "Member/Admin/Editor", TRUE);
    
create Table Prefs
(
   UserID int,
   PrefName varchar(32),     /* Name of the preference */
   PrefValue varchar(256),    /* Value of the preference.  All preferences are stored as strings. */
   CONSTRAINT fk_Users FOREIGN KEY (UserID) REFERENCES Users(UserID)
);    
    
create Table Pictures
(
   PicID int AUTO_INCREMENT PRIMARY KEY,  /* From this, path and URLs can be calculated. */
   DateOfUpload datetime,    /* Date the the file was uploaded */
   FileStatus int,           /* 0=no file yet, pending upload, 1=file okay, 2=deleted/error */
   FileSize int,             /* Original Filesize */
   Width int,                /* Original Width of the photo */
   Height int                /* Original Height of the photo */
);

create Table UserPics
(
   PicID int,                /* Foregin key, PicID of existing picture */
   UserID int                /* Foregin key, ID of the user  who is the subject of the pic. */
);

Create View UserView As
Select Users.UserID, Users.UserName, Users.PasswordHash, Users.LastName, Users.FirstName, Users.NickName, 
       Users.Title, Users.BadgeID, Users.Email, Users.Tags, Users.Active, UserPics.PicID
       FROM Users
       LEFT JOIN UserPics ON UserPics.UserID = Users.UserID;
       



/*
create Table Badges
(
   UserID int,                       // The owner/user of the badge. 
   Title varchar(80),                // Title to put on the badge. 
   BadgeID char(4) NOT NULL UNIQUE   // The badge ID 
);

create View BadgeView AS
Select Badges.UserID, Badges.Title, Badges.BadgeID, 
       Users.UserName, Users.LastName, Users.FirstName, Users.NickName, Users.Tags, Users.Email, Users.Active,
       UserPics.PicID  FROM Badges
       JOIN Users ON Users.UserID = Badges.UserID
       JOIN UserPics ON UserPics.UserID = Badges.UserID
       ORDER BY Users.LastName, Users.FirstName;
*/
