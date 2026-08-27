<?php
/*
   This is FUGLY as hell, but simple enough to let it ride for
   now.  clean this up at a later date however
*/

/*
  Comment this out when you actually have <main> content.
  This echo is for when debugging stuff so the footer
  is visible and not behind any other display output
*/
echo '<!-- Remove the <br> values when not debugging -->';
//echo "<br><br><br>";


echo '
  <!-- bottomFooter.php -->
  <footer class="py-4 bg-light mt-auto">
    <div class="container-flex px-4">
      <div class="d-flex align-items-center justify-content-between small">
';?>
        <?php echo '<div class="text-muted">&copy; Vigilare ' . date("Y") . '&nbsp </div>'; ?>

<?php
echo '
        <div>
          <a href="/support/privacyPolicy.html">Privacy Policy</a>
          &nbsp&middot&nbsp;
          <a href="/support/termsAndConditions.html">Terms &amp; Conditions</a>
        </div>
      </div>
    </div>
  </footer>
';

echo '
  <!-- All JS defined here when possible -->
  <!-- Remember to be anti-social when possible.  Dont pull from extnernal sources.  An NMS needs to be as stand-alone as possible -->
  <script src="/js/scripts.js"></script>
  <script src="/js/cookie/checkCookie.js"></script>
  <script src="/js/font-awesome/all.min.js" crossorigin="anonymous"></script>
  <script src="js/css-mode-change/change-css.js" crossorigin="anonymous"></script>
<!--   <script src="/js/light-switch-bootstrap-main/switch.js"></script>   -->
</body>
</html>
';
?>
