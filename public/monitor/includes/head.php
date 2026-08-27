<?php
$theme = $_COOKIE['theme'] ?? 'dark';
$bg = ($theme === 'dark') ? '#0b0d10' : '#f8f9fa'; // pick your exact page bg
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <!-- event/include/head.php -->
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="Vigilare NMS" />
  <meta name="author" content="Chris Hubbard" />

  <meta http-equiv="Cache-control" content="no-store, no-cache, must-revalidate" />
  <meta http-equiv="Cache-control" content="post-check=0, pre-check=0", false />
  <title><?php echo $title; ?></title>

  <link id="bootstrap-css" rel="stylesheet" href="/css/bootstrap/bootstrap.min.css">
  <!-- theme defined in index page and used here to set our colors -->
  <link id="<?php echo $theme; ?>-theme-css" rel="stylesheet" href="/css/<?php echo $theme; ?>/vigilare-dashboard.css">
  <style>html,body{background: <?= $bg ?>}</style>


<!--  <link href="/css/styles.css" rel="stylesheet">     -->
<!--  <link href="/js/bootstrap-5/css/bootstrap.min.css" rel="stylesheet">     -->
  <script src="/js/cookie/checkCookie.js"></script>
  <script src="/js/bootstrap-5/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</head>
