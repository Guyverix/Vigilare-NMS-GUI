<?php
  include_once __DIR__ . ("/../functions/generalFunctions.php"); // DEBUG ONLY
echo '<br><br><br>';
?>

  <div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-xl-2 col-md-2">
        <div class="card bg-transparent mb-3 ">
          <div class="card-body">
            <h5 class="card-title">Reporting Health</h5>
             <?php
               include (__DIR__ . '/graph/reportingPie.php');
             ?>
             <a class="small link" href="/reporting/index.php">Reports</a>
             <div class="small text-light"><i class="fas fa-angle-right"></i></div>
          </div> <!-- card-body -->
        </div>  <!-- card -->
      </div> <!-- col -->
      <div class="col-xl-2 col-md-2">
        <div class="card bg-transparent mb-3">
          <div class="card-body">
            <h5 class="card-title">Operations Active Events</h5>
             <?php
               include (__DIR__ . '/graph/eventPie.php');
             ?>
             <a class="small link" href="/event/index.php?&page=event.php">Event Details</a>
             <div class="small text-light"><i class="fas fa-angle-right"></i></div>
           </div>
         </div>
       </div>
       <div class="col-xl-2 col-md-2">
         <div class="card bg-transparent mb-3">
           <div class="card-body">
             <h5 class="card-title">Public Visible Events</h5>
              <?php
               include (__DIR__ . '/graph/publicPie.php');
              ?>
              <a class="small link" href="/reporting/index.php?&page=searchExistingReporting.php">View Reports (fake, needs ECE)</a>
              <div class="small text-light"><i class="fas fa-angle-right"></i></div>
            </div>
          </div>
        </div>
     </div> <!-- row end -->
     <div class="row justify-content-center">
       <div class="col-xl-2 col-md-2">
         <div class="card bg-transparent mb-3">
           <div class="card-body">
              <h5 class="card-title">Application Affecting Events</h5>
              <?php
                include (__DIR__ . '/graph/applicationPie.php');
              ?>
              <a class="small link" href="#">Application Report Details (fake, needs ECE)</a>
              <div class="small text-light"><i class="fas fa-angle-right"></i></div>
            </div> <!-- card-body -->
          </div> <!-- card -->
        </div> <!-- col -->
      </div> <!-- row end -->
    </div> <!-- container -->
