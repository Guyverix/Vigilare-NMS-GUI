<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta http-equiv="Cache-control" content="max-age=86400">
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="Vigilare NMS Documentation" />
  <meta name="author" content="Chris Hubbard" />
  <title>Vigilare NMS</title>
  <link href="/css/styles.css" rel="stylesheet">
  <link href="/js/bootstrap-5/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="sb-nav-fixed">
  <nav>
    <!-- Navbar -->
    <!-- Navbar Brand-->
    <a class="navbar-brand ps-3" href="index.php"> Vigilare NMS </a>

    <!-- Sidebar Toggle-->
    <!-- This does nothing if we do not have the leftVerticalMenu.??? loaded  either via shared, or includes  -->
    <!-- Current intention is custom per path we are on, so implies using includes/leftVerticalMenu.??? in each subdirectory so we can have unique help per path -->
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>

    <!-- Top row of links would go here -->
    <a class="navbar-brand ps-3" href="/index.php">Main Dashboard</a>
    <!-- User controls would go here  -->
    <!-- leftVerticialMenu would go here -->
  </nav>

  <div id="layoutSidenav_content">
    <main>
      <div class="container-fluid">
       <div class="row justify-content-center">
          <!-- foreach loop against filestem to retrieve docs -->
          <?php
          if (isset($_GET['page'])) {
echo '          <div class="col-xl-6 col-md-6">' . "\n";
echo '            <h3><center><p class="output">Navigation: <a href="javascript:history.back()">go back</a> or <a href="/index.php">Go to the main page</a></p></center></h3>';
echo '            <div class="card bg-info text-dark mb-3 ">' . "\n";
echo '              <div class="card-body">' . "\n";
                      readfile ($_GET['page']);
echo '                </center></p>' . "\n";
echo '              </div>' . "\n";
echo '            </div>' . "\n";
echo '          </div>' . "\n";
          }
          else {
            $fileList = array();
            $handle = opendir('.');
            if ($handle) {
              while (( $entry = readdir($handle)) !== FALSE) {
                if ( is_file($entry)) {
                  if ( $entry !== "index.php" && (strpos($entry, '.php') !== 'false' || strpos($entry, '.html') !== 'false')) {
                    $fileList[] = $entry;
                  }
                }
              }
            }
            closedir($handle);
            foreach ($fileList as $file) {
echo '          <div class="col-lg-2 col-md-2">' . "\n";
echo '            <div class="card bg-info text-dark mb-3 ">' . "\n";
echo '              <div class="card-body">' . "\n";
// Cleanup the file name to make something readable
$fileClean = preg_replace('/\.html/', '', $file);
$fileClean = preg_replace('/(?<!^)([A-Z])/', ' $1', $fileClean);
$fileClean = strtolower($fileClean);
echo '                <p class="card-text"><center>Page: ' . $fileClean .'</center></p>' . "\n";
echo '                <a href="/support/index.php?&page=' . $file . '" class="stretched-link"></a>' . "\n";
echo '              </div>' . "\n";
echo '            </div>' . "\n";
echo '          </div>' . "\n";
            }
          }
          ?>
        </div>
      </div>
    </main>
  </div>

  <!-- spacer so empty output does not shove the footer to the complete top of the page -->
  <br>
  <br>
  <br>
  <!-- bottomFooter.php -->
  <footer class="py-4 bg-light mt-auto">
    <div class="container-flex px-4">
      <div class="d-flex align-items-center justify-content-between small">
        <?php echo '<div class="text-muted">&copy; Vigilare NMS Monitoring ' . date("Y") . ' </div>'; ?>
      <div>
        <a href="/support/privacyPolicy.html">Privacy Policy</a>
        &nbsp&middot&nbsp;
        <a href="/support/termsAndConditions.html">Terms &amp; Conditions</a>
      </div>
    </div>
  </footer>

