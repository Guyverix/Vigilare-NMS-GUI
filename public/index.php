<?php
  /*
    Use template page for the same feel across all pages
    Load boilerplate first, then focus on data
  */

  // Load local vars for use (urls, ports, etc)
  require_once __DIR__ . "/../config/api.php";


  require_once(__DIR__ . '/../functions/generalFunctions.php');
  checkCookie($_COOKIE);  // disable check here to test 401 responses elsewhere due to expired stuff
  checkTimer($_COOKIE);
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
  $title = 'Vigilare NMS - Main';

  // begin loading page since we have valid cookies
  echo '<!DOCTYPE html><META HTTP-EQUIV=Refresh CONTENT="120"> ';  // comment this out if index refresh gets annoying

  if ( file_exists (__DIR__ . '/includes/head.html')) {
    readfile(__DIR__ . '/includes/head.html');
  }
  else {
    if (isset($title)) {
      includeHead($title);  // calls head.php from the generalFunctions.php script
    }
    else {
      readfile(__DIR__ . '/shared/head.html');  // generic with generic title
    }
  }

  if ( ! isset($_SESSION)) {
    session_start();
  }

  if ( empty($_COOKIE['clientTimezone'])) {
    // This is specifically if it did not get set at login
    $timezone = $_SESSION['time'];
    if ( ! empty($timezone)) {
      setCookieSimple('clientTimezone', $timezone , '/', 864000);
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

//        echo '
 //       <style>
          /* https://www.cssmatic.com/gradient-generator */
 //         body {
  //          background: rgba(115,115,115,1);
   //         background: -moz-linear-gradient(top, rgba(115,115,115,1) 15%, rgba(110,110,110,1) 34%, rgba(109,109,109,1) 42%, rgba(105,105,105,0.79) 61%, rgba(128,128,128,0.58) 79%, rgba(128,128,128,0.54) 83%);
    //        background: -webkit-gradient(left top, left bottom, color-stop(15%, rgba(115,115,115,1)), color-stop(34%, rgba(110,110,110,1)), color-stop(42%, rgba(109,109,109,1)), color-stop(61%, rgba(105,105,105,0.79)), color-stop(79%, rgba(128,128,128,0.58)), color-stop(83%, rgba(128,128,128,0.54)));
     //       background: -webkit-linear-gradient(top, rgba(115,115,115,1) 15%, rgba(110,110,110,1) 34%, rgba(109,109,109,1) 42%, rgba(105,105,105,0.79) 61%, rgba(128,128,128,0.58) 79%, rgba(128,128,128,0.54) 83%);
      //      background: -o-linear-gradient(top, rgba(115,115,115,1) 15%, rgba(110,110,110,1) 34%, rgba(109,109,109,1) 42%, rgba(105,105,105,0.79) 61%, rgba(128,128,128,0.58) 79%, rgba(128,128,128,0.54) 83%);
       //     background: -ms-linear-gradient(top, rgba(115,115,115,1) 15%, rgba(110,110,110,1) 34%, rgba(109,109,109,1) 42%, rgba(105,105,105,0.79) 61%, rgba(128,128,128,0.58) 79%, rgba(128,128,128,0.54) 83%);
       //     background: linear-gradient(to bottom, rgba(115,115,115,1) 15%, rgba(110,110,110,1) 34%, rgba(109,109,109,1) 42%, rgba(105,105,105,0.79) 61%, rgba(128,128,128,0.58) 79%, rgba(128,128,128,0.54) 83%);
       //     filter: progid:DXImageTransform.Microsoft.gradient( startColorstr="#737373", endColorstr="#808080", GradientType=0 );
      //    }
     //  </style>';



  echo "<!-- Check login cookie every 15 seconds -->\n";
  echo '<body class="sb-nav-fixed" onload="setInterval(checkCookieExpiration, 15000)" >' . "\n";

  /*
    All navigation needs to be defined before we begin our main page
    display.  Try like hell for a universal solution so we can have
    consistency, but still allow for custom "things" to be added
    as they are needed
  */


  /*
    Load any overrides we have now to the template
    We must have consistency in the overall look of the template output.

    this will also allow for custom CSS to work once we are on V2 and users
    can choose a color scheme.
  */


  echo '<!-- Any <nav> goes here including user options -->' . "\n";
  echo '<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">' . "\n";

  // Top bar horizontal
  if ( file_exists(__DIR__ . '/includes/topNav.html')) {
    readfile(__DIR__ . '/includes/topNav.html');
  }
  else {
    readfile(__DIR__ . '/shared/topNav.html');
  }


  // Top search option
  if ( file_exists(__DIR__ . '/includes/search.html')) {
    readfile(__DIR__ . '/includes/search.html');
  }
  else {
    readfile(__DIR__ . '/shared/search.html');
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
    include __DIR__ . ('/shared/userControls.php');
  }

  // Close off our NAV section now and begin to show our page
  echo "</nav>\n";

  // Left side vertical menu must be defined before the main pages.  This is not nav
  if ( file_exists(__DIR__ . '/includes/leftVerticalMenu.html')) {
    readfile(__DIR__ . '/includes/leftVerticalMenu.html');
  }
  else {
    readfile(__DIR__ . '/shared/leftVerticalMenu.html');
  }

?>
  <!-- Add Main panel content here -->
  <div id="layoutSidenav_content">
    <main>
      <!-- This is where you can add your page data easiest -->
    <?php
      include_once __DIR__ . ("/main.php");
    ?>
    </main>
  </div>
</div> <!-- Appears to be unbalanced somewhere...  Still looking betting leftVerticalMenu -->
<?php
  /*
    Load our Javascript and footers at this point.
    Any JS changes require the bottomFooter to be loaded from the includes, instead of shared
  */
  if ( file_exists( __DIR__ . ("/includes/bottomFooter.php"))) {
    include __DIR__ . ('/includes/bottomFooter.php');
  }
  else {
    include __DIR__ . ('/shared/bottomFooter.php');
  }
?>
