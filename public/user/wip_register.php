<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="Register account" />
  <meta name="author" content="Guyverix" />
  <title>NMS Account Requests</title>
  <link href="/css/styles.css" rel="stylesheet" />
  <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>

  <style>
    body {
      background-image: url("../../images/background/background.jpg");
      background-repeat: repeat-n;
      background-size: cover;
    }
  </style>
</head>
  <body>
<!--        <div id="layoutAccount">  -->
<!--            <div id="layoutAccount_content">  -->
                <main>
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-5">
                                <div class="card shadow-lg border-0 rounded-lg mt-5 bg-light">
                                    <div class="card-header"><h3 class="text-center font-weight-light my-4">Update Settings</h3></div>
                                    <div class="card-body">
                                        <form action="" method="POST"> 
                                            <div>User Number:</div>
                                            <div class="form-floating mb-3">
                                                <input class="form-control" id="id" name="id" type="text" placeholder="id" />
                                                <label text-dark for="id">New User</label>
                                            </div>
                                            <div>User Id:</div>
                                            <div class="form-floating mb-3">
                                                <input class="form-control" id="username" name="username" type="text" placeholder="userName" readonly/>
                                                <label for="username">Enter Username</label>
                                            </div>
                                            <div>Full Real Name:</div>
                                            <div class="form-floating mb-3">
                                                <input class="form-control" id="fullName" name="fullName" type="text" placeholder="fullName" />
                                                <label for="fullName">Enter Full name</label>
                                            </div>
                                            <div>Email Address:</div>
                                            <div class="form-floating mb-3">
                                                <input class="form-control" id="email" name="email" type="email" placeholder="Email Address" />
                                                <label for="email">Enter valid Email address</label>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                                <button type="submit" class="btn btn-primary">Submit</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>


<?php
  // There is no JS in here
  include __DIR__ . ("/../shared/footer.php");
?>
