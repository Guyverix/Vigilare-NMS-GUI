<?php
  include_once __DIR__ . ("/../functions/generalFunctions.php"); // DEBUG stuff, not really needed
  /* Got to give a little gap below the top bar */
  echo '<br><br><br>';
?>
  <!-- Main display area -->
  <div class="container-fluid">
    <div class="row row-cols-1 justify-content-center">
    <!-- Any PHP files we find in graphs directory we will show here, even if they are not graphs -->
    <!-- Critical to note, that you are only going to have a square box, so small stuff is recommended -->
    <?php
      $files = glob(__DIR__ . '/graph/*.php');
      // debugger($files);  // Dont need this normally if we can find the files
      foreach ($files as $file) {
        unset ($link); unset ($title); unset ($linkName);
        echo '      <div class="col col-sm-2">';
        echo '        <div class="card rounded mb-3">';
        echo '          <div class="card-body opacity-75">';

        include ($file);

        echo '             <a class="small link" href="' . $link . '">' . $linkName . '</a>';
        echo '          </div> <!-- card-body -->';
        echo '        </div>  <!-- card -->';
        echo '      </div> <!-- col -->';
      }
    ?>
    </div> <!-- row end -->
  </div> <!-- container -->
