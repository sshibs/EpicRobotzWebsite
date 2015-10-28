<?php
// --------------------------------------------------------------------
// footer.php -- footer template for all pages that use header.php
//
// Created 12/29/14 DLB
// --------------------------------------------------------------------
?>

</div> <?php // End of middle area ?>

<div style="clear: both; height: 2px"></div>

<div id="footer_area">
    <div id="footer_msg">
        VCHS 2014/2015 Season
    </div>

    <?php 
      if(IsMasquerading()) 
      {
          echo '<div id="footer_masquerader">[';
          echo GetMasquerader();
          echo ']</div>';
      }
      if(isset($timer))
      {
          echo '<div id="footer_timer">';
          echo $timer->Str();
          echo '</div>';
      }
    ?>
    
    
    <div id="footer_home_link">
        <?php // <a href="welcome.php">Home</a> ?>
    </div>
</div>

</div>    <?php // End of screen div ?>

<?php if(!isset($footer_extend)) {
echo '</body>';
echo '</html>';
} ?>
