<?php
// We now have to be authenticated to see stuff.
// We use cookies to validate access is visible.
// No cookie redirect.  We check expired cookies elsewhere (for now)
require_once(__DIR__ . '/../functions/generalFunctions.php');
checkCookie($_COOKIE);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="Main landing page for NMS" />
  <meta name="author" content="Guyverix" />

  <title>NMS Main Page</title>

  <link href="/js/bootstrap-5/css/bootstrap.min.css" rel="stylesheet" />
  <link href="/css/styles.css" rel="stylesheet" />
</head>

<!-- fas == font awesome javascript.  Has nice icons, etc -->
<!-- https://fontawesome.com/search?m=free  choose icon, and find the name.  Call in the i class= to integrate in -->

<!-- Check login cookie every 15 seconds -->
<body class="sb-nav-fixed" onload="setInterval(checkCookieExpiration, 15000)";
<?php

  // Top bar horizontal
  include __DIR__ . ("/shared/navBar.php");
  //  readfile("shared/navBar.html");  // PHP version has user name
  // left bar vertical
  readfile("shared/leftMenu.html");
?>

        <!-- This is the main working area -->
        <div id="layoutSidenav_content">
          <main>
            <div class="container-fluid px-4">
              <h1 class="mt-4">Dashboard</h1>
              <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item active">Home</li>
              </ol>
              <div class="row">
                <div class="col-xl-3 col-md-6">
                  <div class="card bg-success text-white mb-4">
                    <div class="card-body">Reporting Health (todo:reporting engine)</div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                      <div>No known failures recorded</div>
                      <a class="small text-white stretched-link" href="/reporting/index.php">View Details</a>
                      <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                  </div>
                </div>
                <div class="col-xl-3 col-md-6">
 
                 <!-- Create the list of active event severities here -->
                  <?php
                  require_once __DIR__ . '/../config/api.php';
                  $ch=curl_init();
                  curl_setopt($ch, CURLOPT_URL, $apiUrl . ":" . $apiPort . "/events/activeEventCountList");
                  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                  $output = curl_exec($ch) ;
                  $output = json_decode($output, true);
                  $eventCount=count($output);

                  $bgCardColor="bg-success";
                  $crit=0;
                  $err=0;
                  $warn=0;
                  $info=0;
                  $debug=0;
                  foreach($output as $counter) {
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
                      }
                    }
                  }

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

                      <a class="small text-white stretched-link" href="/event/event.php">View Details</a>
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

<?php
  // There is no JS in here
  include __DIR__ . ("/shared/footer.php");
?>

  <script src="/js/cookie/checkCookie.js"></script>
  <script src="/js/font-awesome/all.min.js" crossorigin="anonymous"></script>
  <script src="/js/bootstrap-5/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>  <!-- Yes, anybody can pull our bootstrap js -->
  <script src="/js/light-switch-bootstrap-main/switch.js"></script>
  <script src="/js/scripts.js"></script>
</body>
</html>
