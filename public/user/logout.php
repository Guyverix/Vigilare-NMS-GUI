<?php
  /*
    Basic logout page.  No frills.
    Auto redirection to login page.
  */


  unset($_COOKIE['token']);
  setcookie('token', '','-1', '/');

  unset($_COOKIE['Authorization']);
  setcookie('Authorization', '','-1', '/');

  unset($_COOKIE['apiServer']);
  setcookie('apiServer', '','-1', '/');

  unset($_COOKIE['email']);
  setcookie('email', '','-1', '/');

  unset($_COOKIE['realName']);
  setcookie('realName', '','-1', '/');

  unset($_COOKIE['id']);
  setcookie('id', '','-1', '/');

  unset($_COOKIE['userId']);
  setcookie('userId', '','-1', '/');

  unset($_COOKIE['clientTimezone']);
  setcookie('clientTimezone', '','-1', '/');

  session_abort();
  header('Location: /login/login.php');  // After we have saved the nuked cookies go to login page

?>

