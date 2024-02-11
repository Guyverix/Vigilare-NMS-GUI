<?php
  /*
    Attempt to create template page for the same feel across all pages
    Load boilerplate, then focus on data
  */

  require_once(__DIR__ . '/../../functions/generalFunctions.php');
  checkCookie($_COOKIE);  // disable check here to test 401 responses elsewhere due to expired stuff

  // Load local vars for use (urls, ports, etc)
  require_once __DIR__ . "/../../config/api.php";

  /*
    This is the boilerplate that all pages need to adhere to.
    Only if there is custom work will it read the page.  Otherwise
    every page will use the same set of templates for consistency

    At this point, we know that we had a valid cookie set at page load.
  */

  // begin loading page since we have valid cookies
  if ( file_exists (__DIR__ . '/includes/head.html')) {
    readfile(__DIR__ . '/includes/head.html');
  }
  else {
    readfile(__DIR__ . '/../shared/head.html');
  }

  /*
    Set the body of the HTML now
    this will also look for cookie expiration and forward back to login
    once the cookie has expired.
  */
  echo '<!-- Check login cookie every 15 seconds --><body class="sb-nav-fixed" onload="setInterval(checkCookieExpiration, 15000)" >';
  echo '<!-- Any <nav> goes here including user options -->';
  echo '<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">';

  /*
    Load any overrides we have now to the template
    We must have consistency in the overall look of the template output.

    this will also allow for custom CSS to work once we are on V2 and users
    can choose a color scheme.
  */

  // Top bar horizontal
  if ( file_exists(__DIR__ . '/includes/topNavNoSearch.html')) {
    readfile(__DIR__ . '/includes/topNavNoSearch.html');
  }
  else {
    readfile(__DIR__ . '/../shared/topNavNoSearch.html');
  }

  // Top search option
  if ( file_exists(__DIR__ . '/includes/topSearch.html')) {
    readfile(__DIR__ . '/includes/topSearch.html');
  }
  else {
    readfile(__DIR__ . '/../shared/topSearch.html');
  }

  // Top user controls
  if ( file_exists( __DIR__ . ("/includes/userControls.php"))) {
    include __DIR__ . ("/includes/userControls.php");
  }
  else {
    include __DIR__ . ('/../shared/userControls.php');
  }

  // Close off our NAV section now and begin to show our page
  echo '</nav>';

?>
  <!-- Add Main panel content here -->
  <div id="layoutSidenav_content">
  <main>


  </main>
  </div>
<?php
  /*
    Load our Javascript and footers at this point.
    Any JS changes require the bottomFooter to be loaded from the includes, instead of shared
  */
  if ( file_exists( __DIR__ . ("/includes/bottomFooter.php"))) {
    include __DIR__ . ('/includes/bottomFooter.php');
  }
  else {
    include __DIR__ . ('/../shared/bottomFooter.php');
  }
?>
</body>
</html>
