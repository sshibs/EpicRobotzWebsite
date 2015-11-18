<?php

require_once 'libs/all.php';
if(isset($_POST['completed']) && 
   $_POST['completed'] == 'Yes') 
{
	echo "Work Order Added";
    	JumpToPage("workorders_thisuser.php");
}

?>
