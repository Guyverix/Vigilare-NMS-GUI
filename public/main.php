            <div class="container-fluid px-4">
              <h1 class="mt-4"></h1>
              <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item active">Home</li>
              </ol>
              <div class="row">
                <div class="col-xl-3 col-md-6">
                  <div class="card bg-success text-white mb-4">
                    <div class="card-body">Reporting Health (engine live)</div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                      <div>Placeholder: No known failures recorded</div>
                      <a class="small text-white stretched-link" href="/reporting/index.php">View Details</a>
                      <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                  </div>
                </div>
                <div class="col-xl-3 col-md-6">

                 <!-- Create the list of active event severities here -->
                  <?php
                  // include_once __DIR__ . ("/../functions/generalFunctions.php"); // DEBUG ONLY
                  // $_COOKIE['token'] = 'put token here';                          // DEBUG ONLY
                  $headers = array();
                  $headers[] = 'Content-length: 0';
                  $headers[] = 'Content-type: application/json';
                  $headers[] = 'Authorization: Bearer ' . $_COOKIE['token'];
                  $output = callApiGet("/events/activeEventCountList", $headers);
                  // we SHOULD have gotten an array.... but if not convert it back to one
                  if (! is_array($output)) {
                    $output = json_decode($output, true);
                  }

                  $outputFiltered = json_decode($output['response'], true);
                  $eventCount=count($outputFiltered);

                  $bgCardColor="bg-success";
                  $crit=0;
                  $err=0;
                  $warn=0;
                  $info=0;
                  $debug=0;
                  if ( ! empty($outputFiltered)) {
                    foreach($outputFiltered as $counter) {
                      foreach($counter as $event) {
                        switch ($event['severity']) {
                          case "5":
                            $crit=$event['count'];
                            break;
                          case "4":
                            $err=$event['count'];
                            break;
                          case "3":
                            $warn=$event['count'];
                            break;
                          case "2":
                            $info=$event['count'];
                            break;
                          case "1":
                            $debug=$event['count'];
                            break;
                        } // end switch
                      } // end foreach
                    } // end foreach
                  } // end if

                  // Find out our background color now via loop
                  if ($crit > 0)     { $bgCardColor="bg-danger"; }
                  elseif ($err > 0)  { $bgCardColor="bg-warning"; }
                  elseif ($warn > 0) { $bgCardColor="bg-info"; }
                  // info and debug are NOT display worthy, as something to worry about
                  // elseif ($info > 0) { $bgCardColor="bg-primary"; }
                  // elseif ($debug > 0){ $bgCardColor="bg-secondary"; }
                  ?>
                  <?php echo '<div class="card ' . $bgCardColor .' text-white mb-4">'; ?>
                    <div class="card-body">Operations Visible Events</div>
                    <div class="card-footer d-flex align-items-center justify-content-between">

                      <?php
                      echo "CRITICAL: " . $crit ." ERROR: " . $err ." WARNING: " . $warn . " INFO: " . $info . " DEBUG: " . $debug;
                      ?>

                      <a class="small text-white stretched-link" href="/event/index.php?&page=event.php">View Details</a>
                      <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                  </div>
                </div>
                <div class="col-xl-3 col-md-6">
                  <div class="card bg-success text-white mb-4">
                    <div class="card-body">Public Visible Events in the last 24 hours (todo:SLA)</div>
                  <div class="card-footer d-flex align-items-center justify-content-between">
                    <div>No known failures recorded</div>
                    <a class="small text-white stretched-link" href="/reporting/index.php">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                  </div>
                </div>
              </div>
              <div class="col-xl-3 col-md-6">
                <div class="card bg-danger text-white mb-4">
                  <div class="card-body">Application Health in the last 24 hours (todo:application filtering)</div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                  <div>Found 1 (fake) critical application event</div>
                  <a class="small text-white stretched-link" href="#">View Details</a>
                  <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
              </div>
             </div>
           </main>
         </div>
       </div>
     </div>
   </div>
