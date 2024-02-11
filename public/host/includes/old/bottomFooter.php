<?php
/*
   This is FUGLY as hell, but simple enough to let it ride for
   now.  clean this up at a later date however
*/

echo '
      <!-- LOAD LAST PHP REQUIRES: none -->
      </div>
    </main>
  </div>

  <footer class="py-4 bg-light mt-auto">
    <div class="container-flex px-4">
      <div class="d-flex align-items-center justify-content-between small">
';?>
        <?php echo ' <div class="text-muted">&copy; NMS Monitoring ' . date('Y') . ' </div>'; ?>

<?php
echo '
        <div>
          <a href="#">Privacy Policy</a>
          &middot;
          <a href="#">Terms &amp; Conditions</a>
        </div>
      </div>
    </div>
  </footer>

  <script src="/js/font-awesome/all.min.js" crossorigin="anonymous"></script>
  <script src="/js/bootstrap-5/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

  <script src="/js/simple-datatables/simple-datatables.js"></script>
  <script src="/js/simple-datatables/script.js"></script>
  <script src="/js/light-switch-bootstrap-main/switch.js"></script>
  <script src="/js/sb-demo/js/scripts.js"></script>

</body>
</html>
';
?>
