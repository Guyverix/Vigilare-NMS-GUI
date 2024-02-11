<?php
  /*
    Primary landing page to display monitoring and mapping pages
  */

  // We now have to be authenticated to see stuff.
  // We use cookies to validate access is visible.
  // No cookie redirect.  We check expired cookies elsewhere (for now)

  require_once(__DIR__ . '/../../functions/generalFunctions.php');
  checkCookie($_COOKIE);  // disable check here to test 401 responses elsewhere due to expired stuff

  // Load local vars for use (urls, ports, etc)
  require_once __DIR__ . "/../../config/api.php";

  // begin loading page since we have valid cookies
  readfile(__DIR__ . '/includes/head.html');
?>

<?php
echo '<!-- Check login cookie every 15 seconds --><body class="sb-nav-fixed" onload="setInterval(checkCookieExpiration, 15000)" >';
//echo '<body >';

?>

<!-- Add Main panel content here -->
<?php
?>


<?php
  readfile(__DIR__ . '/includes/topNavNoSearch.html');  // specific to Mapping
  readfile(__DIR__ . '/includes/topSearch.html');       // generic search
  include __DIR__ . ("/includes/navBar.php");       // general user and search
//  readfile (__DIR__ . "/includes/breadcrumb.html");
?>

  <div id="layoutSidenav_content">
  <main>

<?php
include(__DIR__ . '/includes/bottomFooter.php');       // end of page
?>
</body>
</html>
