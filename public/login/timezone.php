<?php
    if (! isset($_SESSION)) {
      session_start();
      $_SESSION['time'] = $_GET['time'];
    }

?>
<head>
</head>
<body>
<pre>
<?php
//var_dump($_SESSION);
//var_dump($_SERVER);
//var_dump($_GET);
?>
</pre>
</body>


