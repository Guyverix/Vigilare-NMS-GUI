<?php
  /*
    Use template page for the same feel across all pages
    Load boilerplate first, then focus on data
  */

  require_once(__DIR__ . '/../../functions/generalFunctions.php');
  checkCookie($_COOKIE);  // disable check here to test 401 responses elsewhere due to expired stuff
  checkTimer($_COOKIE);

  // Load local vars for use (urls, ports, etc)
  require_once __DIR__ . "/../../config/api.php";

  /*
    This is the boilerplate that all pages need to adhere to.
    Only if there is custom work will it read the  includes page.
    Otherwise every page will use the same set of templates
    for consistency.

    At this point, we know that we had a valid cookie set at page load
    so we can begin the HTML and display.
  */


  /*
    Optional if we want a different page "title"
    You must call the function with the $title var for this to work
  */
  $title = 'Vigilare - User Pages';

  if (isset($_GET['page'])) {
    $page = $_GET['page'];
  }
  else {
    $page = 'main.html';
  }

  // begin loading page since we have valid cookies
  if ( file_exists (__DIR__ . '/includes/head.html')) {
    readfile(__DIR__ . '/includes/head.html');
  }
  else {
    if (isset($title)) {
      includeHead($title);
    }
    else {
      readfile(__DIR__ . '/shared/head.html');
    }
  }

  /*
    Set the body of the HTML now
    this will also look for cookie expiration and forward back to login
    once the cookie has expired.

    This needs to be set in any page that is being created, or users
    who have expired credentials will still be able to see the page
    and possibly interact with some portions of the site when they
    should not be able to.
  */
  echo '<!-- Check login cookie every 15 seconds --><body class="sb-nav-fixed" onload="setInterval(checkCookieExpiration, 15000)" >';

  /*
    All navigation needs to be defined before we begin our main page
    display.  Try like hell for a universal solution so we can have
    consistency, but still allow for custom "things" to be added
    as they are needed
  */

  echo '<!-- Any <nav> goes here including user options -->';
  echo '<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">';

  /*
    Load any overrides we have now to the template
    We must have consistency in the overall look of the template output.

    this will also allow for custom CSS to work once we are on V2 and users
    can choose a color scheme.
  */

  // Top bar horizontal
  if ( file_exists(__DIR__ . '/includes/topNav.html')) {
    readfile(__DIR__ . '/includes/topNav.html');
  }
  else {
    readfile(__DIR__ . '/../shared/topNav.html');
  }

  // Top search option
  if ( file_exists(__DIR__ . '/includes/search.html')) {
    readfile(__DIR__ . '/includes/search.html');
  }
  else {
    readfile(__DIR__ . '/../shared/search.html');
  }

  // Top user controls
  /*
    Note this uses include instad of readfile.  Real
    PHP pages should include, so we get their settings and results.
    readfile will simply spew out whatever is in the file, and does
    not actually interpet anything
  */
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
      <!-- This is where you can add your page data easiest -->
      <!-- I am not fond of breadcrubs, but if you are, feel free to define like this -->
      <?php // if ( file_exists(__DIR__ . '/includes/breadcrumb.html')) { echo "<br><br><br>"; readfile( __DIR__ . '/includes/breadcrumb.html'); } ?>

      <!-- Include somefile.php here :) -->
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
