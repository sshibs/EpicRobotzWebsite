<!--
** --------------------------------------------------------------------
** admin_logdata.php -- HTML fragment to show log data.
**
** Created: 12/05/14 DLB
** Updated: 12/29/14 DLB
** --------------------------------------------------------------------
-->

<?php
    if(isset($error_msg))
    {
        echo '<div class="showlog_error_message">' . $error_msg . '</div>';
    }
    if(isset($output_lines))
    {
        echo '<div class="showlog_data">' . "\n";
        foreach($output_lines as $line)
        {
            echo $line . "<br>\n";
        }
        echo "</div>\n";
    }
    echo "</body>\n";  // Required because this is an extension to a normal page.
    echo "</html>\n";
?>