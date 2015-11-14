<!--
** --------------------------------------------------------------------
** admin_logform.php -- HTML fragment to show the logs.
**
** Created: 12/05/14 DLB
** Updated: 12/29/14 DLB -- Hacked from Epic Scouts.
** --------------------------------------------------------------------
-->

<h2 class="page_title">Show Website Log</h2>

<div class="showlog_selection_area">
<form action="admin_showlog.php" method="post">

<input class="showlog_load_button" type="submit" value="Refresh">

<div class="showlog_date_area">
<div class="showlog_datelabel">Date: </div>
<div class="showlog_date_field"> <input type="text" name="Date" <?php if(isset($date)) { echo 'value="' . $date . '"'; } ?> > </div>
</div>

<div class="showlog_checkbox">
<input type="checkbox" name="Pages" value="Pages" <?php if(isset($b_pages) && $b_pages) {echo 'checked="checked"';} ?> >
<div class="showlog_label">Pages</div>
</div>

<div class="showlog_checkbox">
<input type="checkbox" name="Errors" value="Errors" <?php if(isset($b_errors) && $b_errors) {echo 'checked="checked"';} ?> >
<div class="showlog_label">Errors</div>
</div>

<div class="showlog_checkbox">
<input type="checkbox" name="General" value="General" <?php if(isset($b_general) && $b_general) {echo 'checked="checked"';} ?> >
<div class="showlog_label">General</div>
</div>

<div class="showlog_checkbox_with_text">
<input type="checkbox" name="OneUser" value="OneUser" <?php if(isset($b_oneuser) && $b_oneuser) {echo 'checked="checked"';} ?> >
<div class="showlog_label">One ID =</div>
<div class="showlog_uid_field"> <input type="text" name="UID" <?php if(isset($uid)) { echo 'value="' . $uid . '"'; } ?> > </div>
</div>

</form>

</div>
<div style="clear: both"> </div>

<div class="showlog_output_area">

</div>