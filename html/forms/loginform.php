<!--
** --------------------------------------------------------------------
** loginform.php -- HTML fragment to show the login form.
**
** Created: 12/29/14 DLB
** --------------------------------------------------------------------
-->

<?php
if(isset($ShowError) && $ShowError == true)
{
    echo '<div id="login_fail">Login failed.  Try again. </div>';
    echo '<div style="clear: left"></div>';
}
?>

<div class="login_area">
<form action="login.php" method="post">
<div class="login_label"> Name: </div> 
<div class="login_field"> <input type="text" name="name"> </div>
<div style="clear: both;"></div>
<div class="login_label"> Password: </div> 
<div class="login_field"> <input type="password" name="password"> </div>
<div style="clear: both;"></div>
<div class="login_button">
<input type="submit" value="Log In">
</div>
</div>

</form>