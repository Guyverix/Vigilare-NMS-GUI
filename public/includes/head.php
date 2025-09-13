<?php
$theme = $_COOKIE['theme'] ?? 'dark';
$bg = ($theme === 'dark') ? '#0b0d10' : '#f8f9fa'; // pick your exact page bg
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <!-- includes/head.php -->
  <meta charset="utf-8" />
  <meta http-equiv="Cache-control" content="max-age=86400">
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes" />
  <meta name="description" content="Vigliare NMS" />
  <meta name="author" content="Chris Hubbard" />
  <title>Vigilare NMS Dashboard</title>
  <link id="bootstrap-css" rel="stylesheet" href="/css/bootstrap/bootstrap.min.css">
  <!-- theme defined in index page and used here to set our colors -->
  <link id="<?php echo $theme; ?>-theme-css" rel="stylesheet" href="/css/<?php echo $theme; ?>/vigilare-dashboard.css">
  <style>html,body{background: <?= $bg ?>}</style>
  <script src="/js/bootstrap-5/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src="/js/cookie/checkCookie.js"></script>
</head>

<?php
