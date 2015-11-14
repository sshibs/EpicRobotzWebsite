
Use EpicAdmin;

  drop table WorkOrders;  
create Table WorkOrders (
   WorkOrderID int AUTO_INCREMENT PRIMARY KEY,
   WorkOrderName varchar(40) NOT NULL UNIQUE,
   DateRequested DATE,
   DateNeeded date,
   Priority varchar(10),
   DayEstimate int,
   Revision varchar(1),
   Requestor varchar(80),
   RequestingIPTLeadApproval boolean,
   AssignedIPTLeadApproval boolean,
   Project varchar(100),
   RequestingIPTGroup varchar(80),
   ReceivingIPTGroup varchar(80),
   ProjectOfficeApproval boolean,
   ReviewedBy varchar(80),
   AssignedTo varchar(80),
   Completed boolean,
   CompletedOn date ); 

 drop table WorkOrderTasks; 
create Table WorkOrderTasks (
   TaskID	int AUTO_INCREMENT PRIMARY KEY,
   WorkOrderID int,
   Quantity int,
   Description varchar(150),
   UnitPrice decimal(4,2)
 
);
  drop table Prerequisites; 
 create Table Prerequisites (
   PrereqID	int AUTO_INCREMENT PRIMARY KEY,
   PrevWorkOrderID int,
   WorkOrderID int
); 

 drop table RelatedFiles;
create Table RelatedFiles (
   FileID	int AUTO_INCREMENT PRIMARY KEY,
   WorkOrderID int,
   FilePath varchar(200) );
