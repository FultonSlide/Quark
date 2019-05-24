<?php
    session_start();
    function setDetails($conn, $email, $pswd){ //function that sets all the setails of the logging in user from the friends table as session variables
        $query = "SELECT * FROM friends WHERE friend_email='$email' AND friend_password='$pswd'";
        $result = mysqli_query($conn, $query);
        $details = mysqli_fetch_row($result);

        $_SESSION['id'] = $details[0];
        $_SESSION['email'] = $details[1];
        $_SESSION['password'] = $details[2];
        $_SESSION['name'] = $details[3];
        $_SESSION['date-started'] = $details[4];
        $_SESSION['friend-num'] = $details[5];
    }

    require_once("settings.php");

    $conn = @mysqli_connect($host, $user, $pswd) or die("Failed to connect to the server, Debugging errno: " . mysqli_connect_errno() . PHP_EOL);

    @mysqli_select_db($conn, $dbnm) or die("Database not available");

    $_SESSION['login'] = true;
    setDetails($conn, "Tester@hotmail.com", "Test1234");

    mysqli_close($conn);

    header("location:friendlist.php");
?>
