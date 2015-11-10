<h1>Create new workorder:</h1>
<div style="float:left;width:450px;height:575px;padding:10px;border:10px solid black;">
--------------------Workorder information--------------------
<br>
Workorder number:
<br>
Workorder name:
<input type="text" name="WorkOrderName" placeholder="Name">
<br>
Workorder description:
<br>
<textarea rows="4" cols="50" placeholder="Description" name="WorkOrderName">
</textarea>
<br>
Workorder request date (MM/DD/YYYY): [DATE]
<br>
Workorder due date (MM/DD/YYYY):  <select>
name="DueDay" 
  <option value="jan">01</option>
  <option value="feb">02</option>
  <option value="mar">03</option>
  <option value="apr">04</option>
  <option value="may">05</option>
  <option value="jun">06</option>
  <option value="jul">07</option>
  <option value="aug">08</option>
  <option value="sep">09</option>
  <option value="oct">10</option>
  <option value="nov">11</option>
  <option value="dec">12</option>
</select> / <select>
name="DueMonth"
  <option value="01">01</option>
  <option value="02">02</option>
  <option value="03">03</option>
  <option value="04">04</option>
  <option value="05">05</option>
  <option value="06">06</option>
  <option value="07">07</option>
  <option value="08">08</option>
  <option value="09">09</option>
  <option value="10">10</option>
  <option value="11">11</option>
  <option value="12">12</option>
  <option value="13">13</option>
  <option value="14">14</option>
  <option value="15">15</option>
  <option value="16">16</option>
  <option value="17">17</option>
  <option value="18">18</option>
  <option value="19">19</option>
  <option value="20">20</option>
  <option value="21">21</option>
  <option value="22">22</option>
  <option value="23">23</option>
  <option value="24">24</option>
  <option value="25">25</option>
  <option value="26">26</option>
  <option value="27">27</option>
  <option value="28">28</option>
  <option value="29">29</option>
  <option value="30">30</option>
  <option value="31">31</option>
</select> / <select>
name="DueYear"
  <option value="2015">2015</option>
  <option value="2016">2016</option>
  <option value="2017">2017</option>
  <option value="2018">2018</option>
  <option value="2018">2018</option>
  <option value="2019">2019</option>
  <option value="2020">2020</option>
  <option value="2021">2021</option>
  <option value="2022">2022</option>
  <option value="2023">2023</option>
  <option value="2024">2024</option>
  <option value="2025">2025</option>
</select>
<br>
Workorder priority:
 <select>
name="Priority"
  <option value="hot">High</option>
  <option value="routine">Routine</option>
  <option value="low">Low</option>
</select> 
<br>
Job name:
<input type="text" placeholder="Job name" name="JobName">
<br>
Prerequisite workorder (If any): <select> <option value="wip">WIP</option> name="Prereq"
</select> <!-- This will include a list of all un-completed workorders in the database -->
<br>
<br>
--------------------Workorder is assigned to:--------------------
<br>
Assigned to IPT group: <select> <option value="wip">WIP</option> name="ReceivingIPTGroup" </select> <!-- For now, all IPT groups are hard-coded into the code. However, in the future, they will be input by the database. -->
<br>
<br>
--------------------Workorder is assigned by:--------------------
<br>
Requesting member:
<br>
Requesting IPT group: <select>
<option value=wip>WIP</option> name="RequestingIPTGroup"
</select>
<br>
Has this workorder been approved by requesting IPT group's IPT Leader? <select>
  <option value="true">Yes</option>
  <option value="false">No</option> name="RequestingIPTLeadApproval"
</select>
<br>
<input type="submit" value="Submit workorder">
</div>
