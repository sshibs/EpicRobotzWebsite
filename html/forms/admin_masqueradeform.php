<!--
** --------------------------------------------------------------------
** admin_masqueradeform.php -- HTML fragment to show a masquerade form.
**
** Created: 12/29/14 DLB
** --------------------------------------------------------------------
-->

<div class="content_area">

<h2 class="page_title">Masquerade as a Different User</h2>

<?php
if(!empty($success_msg))
{
    echo '<div class="inputform_msg" id="inputform_success_msg" >' . $success_msg . "</div>";
}
if(!empty($error_msg))
{
    echo '<div class="inputform_msg" id="inputform_error_msg" >' . $error_msg . "</div>";
}
?>

<div class="admin_selection_area">
<form action="admin_masquerade.php" method="post">

<input class="admin_masquerade_button" type="submit" value="Masquerade">

<div class="admin_data_area">
<div class="admin_datalabel">UserName: </div>
<div class="admin_data_field"> <input type="text" name="UserName" <?php if(isset($username)) { echo 'value="' . $username . '"'; } ?> > </div>
</div>

</form>
</div>

</div>