<?php
// --------------------------------------------------------------------
// workorders_create_form.php -- form to add work orders.
//
// Created: 11/07/15 PR
// --------------------------------------------------------------------
if(!empty($success_msg))
{
    echo '<div class="inputform_msg" id="inputform_success_msg" >' . $success_msg. "</div>";
}
if(!empty($error_msg))
{
    echo '<div class="inputform_msg" id="inputform_error_msg" >' . $error_msg . '</div>';
}
echo '<form action= "workorders_addnew.php" method="post">';
echo '<div class="content_area">';
echo "<h1>Create new workorder:</h1>";
echo "<div style=\"float:left;width:450px;height:575px;padding:10px;border:10px solid black;\">";
echo "--------------------Workorder information--------------------";
echo "<br>";
echo "Workorder name:";
echo "<input type=\"text\" name=\"WorkOrderName\" placeholder=\"Name\">";
echo "<br>";
echo "Workorder description:";
echo "<br>";
echo "<textarea rows=\"4\" cols=\"50\" placeholder=\"Description\" name=\"WorkOrderName\">";
echo "</textarea>";
echo "<br>";
echo "<br>";
echo "Workorder due date (MM/DD/YYYY):  <select name=\"DueDay\">";
echo "  <option value=\"01\">01</option>";
echo "  <option value=\"02\">02</option>";
echo "  <option value=\"03\">03</option>";
echo "  <option value=\"04\">04</option>";
echo "  <option value=\"05\">05</option>";
echo "  <option value=\"06\">06</option>";
echo "  <option value=\"07\">07</option>";
echo "  <option value=\"08\">08</option>";
echo "  <option value=\"09\">09</option>";
echo "  <option value=\"10\">10</option>";
echo "  <option value=\"11\">11</option>";
echo "  <option value=\"12\">12</option>";
echo "</select> / <select name=\"DueMonth\">";
echo "  <option value=\"01\">01</option>";
echo "  <option value=\"02\">02</option>";
echo "  <option value=\"03\">03</option>";
echo "  <option value=\"04\">04</option>";
echo "  <option value=\"05\">05</option>";
echo "  <option value=\"06\">06</option>";
echo "  <option value=\"07\">07</option>";
echo "  <option value=\"08\">08</option>";
echo "  <option value=\"09\">09</option>";
echo "  <option value=\"10\">10</option>";
echo "  <option value=\"11\">11</option>";
echo "  <option value=\"12\">12</option>";
echo "  <option value=\"13\">13</option>";
echo "  <option value=\"14\">14</option>";
echo "  <option value=\"15\">15</option>";
echo "  <option value=\"16\">16</option>";
echo "  <option value=\"17\">17</option>";
echo "  <option value=\"18\">18</option>";
echo "  <option value=\"19\">19</option>";
echo "  <option value=\"20\">20</option>";
echo "  <option value=\"21\">21</option>";
echo "  <option value=\"22\">22</option>";
echo "  <option value=\"23\">23</option>";
echo "  <option value=\"24\">24</option>";
echo "  <option value=\"25\">25</option>";
echo "  <option value=\"26\">26</option>";
echo "  <option value=\"27\">27</option>";
echo "  <option value=\"28\">28</option>";
echo "  <option value=\"29\">29</option>";
echo "  <option value=\"30\">30</option>";
echo "  <option value=\"31\">31</option>";
echo "</select> / <select name=\"DueYear\">";
echo "  <option value=\"2015\">2015</option>";
echo "  <option value=\"2016\">2016</option>";
echo "  <option value=\"2017\">2017</option>";
echo "  <option value=\"2018\">2018</option>";
echo "  <option value=\"2018\">2018</option>";
echo "  <option value=\"2019\">2019</option>";
echo "  <option value=\"2020\">2020</option>";
echo "  <option value=\"2021\">2021</option>";
echo "  <option value=\"2022\">2022</option>";
echo "  <option value=\"2023\">2023</option>";
echo "  <option value=\"2024\">2024</option>";
echo "  <option value=\"2025\">2025</option>";
echo "</select>";
echo "<br>";
echo "Workorder priority:";
echo " <select name=\"Priority\">";
echo "<option value=\"hot\">High</option>";
echo "<option value=\"routine\">Routine</option>";
echo "<option value=\"low\">Low</option>";
echo "</select> ";
echo "<br>";
echo "Job name:";
echo "<input type=\"text\" placeholder=\"Job name\" name=\"JobName\">";
echo "<br>";
echo "Prerequisite workorder (If any):";
echo "<select  name=\"Prereq\"> <!-- This will include a list of all un-completed workorders in the database -->";
$sql = 'SELECT WorkOrderName from WorkOrders';
$result = SqlQuery($loc, $sql);
if ($result->num_rows > 0)
{
        while ($row = $result->fetch_assoc())
        {
         $WorkOrderName = $row["WorkOrderName"];
         echo "<option value=\"$WorkOrderName\">$WorkOrderName</option>";
        }
echo "</select> ";
}
else{
echo "No un-completed workorders currently exist.";
}
echo "<br>";
echo "<br>";
echo "--------------------Workorder is assigned to:--------------------";
echo "<br>";
echo "Assigned to IPT group: <select name=\"ReceivingIPTGroup\" > ";
echo "<option value=\"ceo\">CEO</option>";
echo "<option value=\"cad\">CAD</option>";
echo "<option value=\"design\">Design</option>";
echo "<option value=\"elect\">Electronics</option>";
echo "<option value=\"3d\">3D Printing</option>";
echo "<option value=\"bus\">Business</option>";
echo "<option value=\"log\">Logistics</option>";
echo "<option value=\"strat\">Strategy / Systems</option>";
echo "<option value=\"web\">Web / Media</option>";
echo "<option value=\"safety\">Safety</option>";
echo "<option value=\"it\">IT</option>";
echo " </select> <!-- For now, all IPT groups are hard-coded into the code. However, in the future, they will be input by the database. -->";
echo "<br>";
echo "<br>";
echo "--------------------Workorder is assigned by:--------------------";
echo "<br>";
echo "Requesting member:";
echo "<br>";
echo "Requesting IPT group: <select name=\"RequestingIPTGroup\">";
echo "<option value=\"ceo\">CEO</option>";
echo "<option value=\"cad\">CAD</option>";
echo "<option value=\"design\">Design</option>";
echo "<option value=\"elect\">Electronics</option>";
echo "<option value=\"3d\">3D Printing</option>";
echo "<option value=\"bus\">Business</option>";
echo "<option value=\"log\">Logistics</option>";
echo "<option value=\"strat\">Strategy / Systems</option>";
echo "<option value=\"web\">Web / Media</option>";
echo "<option value=\"safety\">Safety</option>";
echo "<option value=\"it\">IT</option>";
echo "</select>";
echo "<br>";
echo "Has this workorder been approved by requesting IPT group's IPT Leader?  <select  name=\"RequestingIPTLeadApproval\">";
echo "<option value=\"1\">Yes</option>";
echo "<option value=\"0\">No</option>";
echo "</select>";
echo "<br>";
echo "<input type=\"submit\" value=\"Submit workorder\">";
echo "</div>";
echo '</div' . "\n";
?>
