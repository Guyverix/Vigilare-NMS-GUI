<?php
// We now have to be authenticated to see stuff.
// We use cookies to validate access is visible.
// No cookie redirect.  We check expired cookies elsewhere (for now)
require_once(__DIR__ . '/../../functions/generalFunctions.php');
checkCookie($_COOKIE);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="User settings page for NMS" />
  <meta name="author" content="Guyverix" />

  <title>NMS Main Page</title>

  <link href="/js/bootstrap-5/css/bootstrap.min.css" rel="stylesheet" />
  <link href="/css/styles.css" rel="stylesheet" />

</head>

<!-- fas == font awesome javascript.  Has nice icons, etc -->
<!-- https://fontawesome.com/search?m=free  choose icon, and find the name.  Call in the i class= to integrate in -->

<!-- Check login cookie every 15 seconds -->
<body class="sb-nav-fixed" onload="setInterval(checkCookieExpiration, 15000)";
<?php

  // Top bar horizontal
  readfile ("../shared/navBar.html");
  // left bar vertical
  readfile("../shared/leftMenu.html");

  // We have cookie set, so use the info provided here
  $email=$_COOKIE['email'];
  $userId=$_COOKIE['userId'];
  $id=$_COOKIE['id'];
  $fullName=$_COOKIE['realName'];
?>

                    <div class="container">
                        <div class="row justify-content-right">
                            <div class="col-lg-5">
                                <div class="card shadow-lg border-0 rounded-lg mt-5 bg-light">
                                    <div class="card-header"><h3 class="text-center font-weight-light my-4">Update Settings</h3></div>
                                    <div class="card-body bg-light">
                                        <form action="" method="POST"> 
                                            <div>User Number:</div>
                                            <div class="form-floating mb-3 text-dark">
                                                <input class="form-control" id="id" name="id" type="text" placeholder="id" text-dark readonly/>
                                                <label text-dark for="id"><?php echo $id; ?></label>
                                            </div>
                                            <div>User Id:</div>
                                            <div class="form-floating mb-3 text-dark">
                                                <input class="form-control" id="username" name="username" type="text" placeholder="userName" readonly/>
                                                <label for="username"><?php echo $userId; ?></label>
                                            </div>
                                            <div>Full Real Name:</div>
                                            <div class="form-floating mb-3 text-dark">
                                                <input class="form-control" id="fullName" name="fullName" type="text" placeholder="fullName" />
                                                <label for="fullName"><?php echo $fullName ?></label>
                                            </div>
                                            <div>Current Password:</div>
                                            <div class="form-floating mb-3 text-dark">
                                                <input class="form-control" id="oldPassword" name="oldPassword" type="password" placeholder="Current Password" />
                                                <label for="oldPassword">Current Password</label>
                                            </div>
                                            <div>New Password:</div>
                                            <div class="form-floating mb-3 text-dark">
                                                <input class="form-control" id="newPassword" name="newPassword" type="password" placeholder="New Password" />
                                                <label for="newPassword">New Password</label>
                                            </div>
                                            <div>Email Address:</div>
                                            <div class="form-floating mb-3 text-dark">
                                                <input class="form-control" id="email" name="email" type="email" placeholder="Email Address" />
                                                <label for="email"><?php echo $email; ?></label>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                                <button type="submit" class="btn btn-primary">Update</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



<?php
  // There is no JS in here
  include __DIR__ . ("/../shared/footer.php");
?>
  <script src="/js/light-switch-bootstrap-main/switch.js"></script>

  <script src="/js/cookie/checkCookie.js"></script>
  <script src="/js/font-awesome/all.min.js" crossorigin="anonymous"></script>
  <script src="/js/bootstrap-5/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>  <!-- Yes, anybody can pull our bootstrap js -->
<!--  <script src="/js/scripts.js"></script>  -->
</body>
</html>
