<?php

echo "BEGIN <br><br>";
session_start();
echo '<script src="/js/jquery/jquery-1.7.1.min.js"></script>';
  echo <<<EOE
   <script type="text/javascript">
     if (navigator.cookieEnabled)
       document.cookie = "tzo="+ (- new Date().getTimezoneOffset());
   </script>
EOE;

  if (!isset($_COOKIE['tzo'])) {
    echo <<<EOE
      <script type="text/javascript">
        if (navigator.cookieEnabled) document.reload();
        else alert("Cookies must be enabled!");
      </script>
EOE;
    die();
  }
  $ts = new DateTime('now', new DateTimeZone('GMT'));
  $ts->add(DateInterval::createFromDateString($_COOKIE['tzo'].' minutes'));

echo "HERE ". var_dump($ts) . "\n";

?>
