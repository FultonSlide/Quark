<?php
    session_start(); //starts the session
    //sets all the session variables used on the site to empty arrays
    $_SESSION['login'] = array();
    $_SESSION['fill-email'] = array();
    $_SESSION['id'] = array();
    $_SESSION['email'] = array();
    $_SESSION['password'] = array();
    $_SESSION['name'] = array();
    $_SESSION['date-started'] = array();
    $_SESSION['friend-num'] = array();
    $_SESSION['first'] = array();
    $_SESSION['last'] = array();
    $_SESSION['next'] = array();
    $_SESSION['prev'] = array();
    $_SESSION['page-num'] = array();
    $_SESSION['addfriend-array'] = array();
    $_SESSION['signup-name'] = array();
    $_SESSION['signup-email'] = array();
    session_destroy(); //destroys the current session
    header("location:index.php"); //redirects to the homepage
?>
