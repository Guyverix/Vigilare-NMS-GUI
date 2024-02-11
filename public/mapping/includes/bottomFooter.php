<?php
/*
   This is FUGLY as hell, but simple enough to let it ride for
   now.  clean this up at a later date however
*/

echo "<br><br><br>";

echo '
  <!-- bottomFooter.php -->
  <footer class="py-4 bg-light mt-auto">
    <div class="container-flex px-4">
      <div class="d-flex align-items-center justify-content-between small">
';?>
        <?php echo ' <div class="text-muted">&copy; NMS Monitoring ' . date("Y") . ' </div>'; ?>

<?php
echo '
        <div>
          <a href="/support/privacyPolicy.html">Privacy Policy</a>
          &middot;
          <a href="/support/termsAndConditions.html">Terms &amp; Conditions</a>
        </div>
      </div>
    </div>
  </footer>
  <!-- All JS defined here when possible -->
  <script src="/js/cookie/checkCookie.js"></script>
  <script src="/js/font-awesome/all.min.js" crossorigin="anonymous"></script>
  <script src="/js/bootstrap-5/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src="/js/light-switch-bootstrap-main/switch.js"></script>
  <script src="/js/scripts.js"></script>
</body>
</html>
';
?>
