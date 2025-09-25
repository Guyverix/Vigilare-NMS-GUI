<?php
  /*
    Use template page for the same feel across all pages
    Load boilerplate first, then focus on data

    Remember that this template may need alteration to the shared path as I cannot know what
    directory level you are using it at.
  */

  require_once(__DIR__ . '/../../functions/generalFunctions.php');
  checkCookie($_COOKIE);  // disable check here to test 401 responses elsewhere due to expired stuff
  checkTimer($_COOKIE);

  // Load local vars for use (urls, ports, etc)
  require_once __DIR__ . "/../../config/api.php";

  /*
    This is the boilerplate that all pages need to adhere to.
    Only if there is custom work will it read the  includes page.
    Otherwise every page should use the same set of templates
    for consistency.

    At this point, we know that we had a valid cookie set at page load
    so we can begin the HTML and display.
  */

  /*
    Optional if we want a different page "title"
    This is expected to be defined in custom head pages
  */
  $title = 'Vigilare NMS - Template (changeme, duh)';

  // Set our css colors to what is in the cookie and default to dark if null
  $theme=$_COOKIE['theme'] ?? 'dark';

  if (isset($_GET['page'])) {
    $base = __DIR__;
    if (is_readable($_GET['page'])) {
      $page = $_GET['page'];
    }
    else {
      loadIncomplete("Missing called page $_GET['page']");
      show404();
    }
  }
  else {
    $base = __DIR__;
    // We are working only in the current directory
    $candidates = [
      "$base/main.php" ,
      "$base/main.html" ,
    ];
    $loaded = false;
    foreach ($candidates as $path) {
      if (is_readable($path)) {
        $page = $path;
      }
      $loaded = true;
    }
    if (!$loaded) {
      loadIncomplete("main file is missing");
    }
  }

  // begin loading page since we have valid cookies
  $base = __DIR__;
  $candidates = [
    "$base/includes/head.php" ,
    "$base/includes/head.html",
    dirname($base) . "/../shared/head.html",
  ];

  $loaded = false;
  foreach ($candidates as $path) {
    if (is_readable($path)) {
      // execute PHP files, stream HTML files
      if (str_ends_with($path, '.php')) {
        include $path;
      }
      else {
        readfile($path);   // or: include $path;
      }
      $loaded = true;
      break;
    }
  }
  if (!$loaded) {
    loadIncomplete("head file is missing");
  }

  /*
    Set the body of the HTML now
    this will also look for cookie expiration and forward back to login
    once the cookie has expired.

    This needs to be set in any page that is being created, or users
    who have expired credentials will still be able to see the page
    and possibly interact with some portions of the site when they
    should not be able to.

    This is more of a sanity check than a true security feature.
  */
  echo '<!-- Check login cookie every 15 seconds --><body class="nav-fixed" onload="setInterval(checkCookieExpiration, 15000)" >';

  /*
    All navigation needs to be defined before we begin our main page
    display.  Try like hell for a universal solution so we can have
    consistency, but still allow for custom "things" to be added
    as they are needed
  */

  echo '<!-- Any <nav> goes here including user options -->';
  echo '<nav class="topnav navbar navbar-expand">';

  /*
    Load any overrides we have now to the template
    We must have consistency in the overall look of the template output.
  */

  // Top navbar horizontal
  $base = __DIR__;
  $candidates = [
    "$base/includes/topNav.php" ,
    "$base/includes/topNav.html",
    dirname($base) . "/shared/topNav.html",
  ];

  $loaded = false;
  foreach ($candidates as $path) {
    if (is_readable($path)) {
      // execute PHP files, stream HTML files
      if (str_ends_with($path, '.php')) {
        include $path;
      }
      else {
        readfile($path);   // or: include $path;
      }
      $loaded = true;
      break;
    }
  }
  if (!$loaded) {
    loadIncomplete("topNav Include file is missing");
  }

  // Top search controls
  // Also right justifies the top bar
  $base = __DIR__;
  $candidates = [
    "$base/includes/search.php" ,
    "$base/includes/search.html",
    dirname($base) . "/shared/search.php",
    dirname($base) . "/shared/search.html",
  ];

  $loaded = false;
  foreach ($candidates as $path) {
    if (is_readable($path)) {
      // execute PHP files, stream HTML files
      if (str_ends_with($path, '.php')) {
        include $path;
      }
      else {
        readfile($path);   // or: include $path;
      }
      $loaded = true;
      break;
    }
  }
  if (!$loaded) {
    loadIncomplete("search Include file is missing");
  }

  // Top right user controls
  $base = __DIR__;
  $candidates = [
    "$base/includes/userControls.php" ,
    "$base/includes/userControls.html",
    dirname($base) . "/shared/userControls.php",
    dirname($base) . "/shared/userControls.html",
  ];

  $loaded = false;
  foreach ($candidates as $path) {
    if (is_readable($path)) {
      // execute PHP files, stream HTML files
      if (str_ends_with($path, '.php')) {
        include $path;
      }
      else {
        readfile($path);   // or: include $path;
      }
      $loaded = true;
      break;
    }
  }
  if (!$loaded) {
    loadIncomplete("userControls Include file is missing");
  }

  // Close off our NAV section now and begin to show our page
  echo '</nav>';
?>
  <!-- Add Main panel content here -->
  <div id="layoutSidenav_content">
    <main>
      <!-- This is where you can add your page data easiest -->
      <!-- I am not fond of breadcrubs, but if you are, feel free to define like this -->
      <!-- If no breadcrumb exists, it will silently pass it by -->
      <?php
        $base = __DIR__;
        $candidates = [
          "$base/includes/breadcrumb.php" ,
          "$base/includes/breadcrumb.html",
        ];
        foreach ($candidates as $path) {
          if (is_readable($path)) {
            // execute PHP files, stream HTML files
            if (str_ends_with($path, '.php')) {
              include $path;
            }
            else {
              readfile($path);   // or: include $path;
            }
          }
        }
      ?>
      <!-- Include somefile.php here :) -->
      <!-- Primary page display  -->
      <?php if ( preg_match('/html/', $page)) {
              readfile( $page );
            }
            else {
              include  __DIR__ . "/$page";
            }
      ?>


    </main>
  </div>
<?php
  /*
    Load our Javascript and footers at this point.
    Any JS changes require the bottomFooter to be loaded from the includes, instead of shared

    Remember that this template may need alteration to the shared path as I cannot know what
    directory level you are using it at.
  */
  $base = __DIR__;
  $candidates = [
    "$base/includes/bottomFooter.php" ,
    "$base/includes/bottomFooter.html",
    dirname($base) . "/shared/bottomFooter.php",
    dirname($base) . "/shared/bottomFooter.html",
  ];

  $loaded = false;
  foreach ($candidates as $path) {
    if (is_readable($path)) {
      // execute PHP files, stream HTML files
      if (str_ends_with($path, '.php')) {
        include $path;
      }
      else {
        readfile($path);   // or: include $path;
      }
      $loaded = true;
      break;
    }
  }
  if (!$loaded) {
    loadIncomplete("bottomFooter Include file is missing");
  }
?>
</body>
</html>
