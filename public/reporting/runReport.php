<?php
  // Get our utilities in place asap.

  include_once __DIR__ . "/functions/eventFunctions.php";
  include_once __DIR__ . "/../../functions/generalFunctions.php";

  // Load local vars for use (urls, ports, etc)
  require_once __DIR__ . "/../../config/api.php";
  checkCookie($_COOKIE);
  echo "<br><br><br>";
  // Grab our POSSIBLE values so users can choose what they change
  $headers = array();
  $headers[] = 'Authorization: Bearer ' . $_COOKIE['token'];
  $cookieTimezone = $_COOKIE['clientTimezone'];
  $post = array();  // We are using post, so give it an empty array to post with
  $quitEarly = 0;
  $rawPost = json_decode($_POST['template'],true);
  $template = $rawPost['template'];
  $usedVars = json_decode($rawPost['usedVars'],true);

  debugger($_POST);
  debugger($usedVars);

