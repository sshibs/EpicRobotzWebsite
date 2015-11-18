<?php
echo '<div class="content_area">';
echo '<form action= "workorders_selectipt.php" method="post">';

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
echo "<br>";
echo "<input type=\"submit\" value=\"Submit\">";

?>
