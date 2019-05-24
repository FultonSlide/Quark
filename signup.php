<?php
    session_start(); //starts the session
    if(!isset($_SESSION['login'])) //if the login session variable hasn't been set it is set to false by default
    {
        $_SESSION['login'] = false;
    }

    if($_SESSION['login'] == true) //if the user is logged in a different navbar is displayed to them compared to if they were not logged in
    {
        $login = "";
        $logout = "<span id='nav-element'><a href='logout.php'>LOGOUT</a></span>";
        $friendadd = "<span id='nav-element'><a href='friendadd.php'>ADD</a></span>";
        $friendlist = "<span id='nav-element'><a href='friendlist.php'>FRIENDS</a></span>";
        $loginDisplay = "<p><span id='login-display'>Logged in as: " . $_SESSION['name'] . "</span></p>";
    } else {
        $login = "<span id='nav-element'><a href='login.php'>LOGIN</a></span>";
        $logout = "";
        $friendadd = "";
        $friendlist = "";
        $loginDisplay = "";
    }

    if(!isset($_SESSION['signup-email'])) //if the signup-email session variable hasn't been set it is set to null by default
    {
        $_SESSION['signup-email'] = null;
    }

    if(isset($_POST['email']) && !empty($_POST['email'])) //if the email field iset and not blank the session variable is set to its contents
    {
        $_SESSION['signup-email'] = $_POST['email'];
    }

    if(!isset($_SESSION['signup-name']))//if the signup-name session variable is not set it is set null by default
    {
        $_SESSION['signup-name'] = null;
    }

    if(isset($_POST['profile-name']) && !empty($_POST['profile-name']))//if the name field isset and not blank the session variable is set to its contents
    {
        $_SESSION['signup-name'] = $_POST['profile-name'];
    }

    //local variables of session variables
    $fillName = $_SESSION['signup-name'];
    $fillEmail = $_SESSION['signup-email'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>QUARK</title>
    <meta charset="utf-8">
    <meta name="Author" content="William Qoro">
    <meta name="Desciption" content="Assignment 2 Sign Up">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style/style.css">
    <link rel="icon" type="style/png" href="images/favicon.png">
</head>

<body>
    <div id="page-container">
        <div id="content-container">
            <div id="header">
                <header>
                    <h1 id="quark"><a href="index.php">QUARK</a></h1>

                    <div id="hamburger-menu">
                        <input type="checkbox" id="toggle">
                        <label for="toggle" id="hb-btn"><span id="hburger">&#9776;</span></label>
                        <div id="navbar">
                            <p>
                                <span id="nav-element"><a href="index.php">HOME</a></span>
                                <span id="nav-element"><a href="signup.php">SIGN UP</a></span>
                                <?php echo $login; ?>
                                <?php echo $friendadd; ?>
                                <?php echo $friendlist; ?>
                                <span id="nav-element"><a href="about.php">ABOUT</a></span>
                                <?php echo $logout; ?>
                            </p>
                        </div>
                    </div>
                </header>
            </div>

            <div id="body-signup">
                <form action="signup.php" method="post" id="sign-up">
                    <p><span id="sign-up-title">SIGN UP</span><p>
                    <div id="input-fields">
                        <p><input type="text" name="email" placeholder="Email" value=<?php echo "'$fillEmail'"; ?>></p>
                        <p><input type="text" name="profile-name" placeholder="Profile Name" value=<?php echo "'$fillName'"; ?>></p>
                        <p><input type="password" name="password" placeholder="Password"></p>
                        <p><input type="password" name="password-check" placeholder="Confirm Password"></p>
                        <p><input type="submit" name="Submit" value="SIGN UP"></p>
                        <input type="hidden" name="hidden-field" content="load"/>
                    </div>
                </form>

                <?php
                    function setDetails($conn, $email, $pswd){ //function that sets all the details of the signing up user in session variables
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

                    if(isset($_POST['hidden-field'])) //if the hidden field is not set the php script dosen't run, to stop the script from running and throwing errors of non set fields on inital page load
                    {
                        if(isset($_POST['email']) && !empty($_POST['email']) && isset($_POST['profile-name']) && !empty($_POST['profile-name']) && isset($_POST['password']) && !empty($_POST['password']) && isset($_POST['password-check']) && !empty($_POST['password-check']))
                        {
                            $email = $_POST['email'];
                            $profileName = $_POST['profile-name'];
                            $password = $_POST['password'];
                            $pswdCheck = $_POST['password-check'];
                            $date = date('Y-m-d');

                            require_once("settings.php");

                            $conn = @mysqli_connect($host, $user, $pswd) or die("Failed to connect to the server, Debugging errno: " . mysqli_connect_errno() . PHP_EOL);

                            @mysqli_select_db($conn, $dbnm) or die("Database not available");

                            $emailQuery = "SELECT * FROM friends WHERE friend_email='$email'";
                            $emailResult = mysqli_query($conn, $emailQuery);//query checks if provided email exists in friends table already
                            $rows = mysqli_fetch_row($emailResult);
                            $emailVal = filter_var($email, FILTER_VALIDATE_EMAIL); //filters the email input using the validate email php filter
                            $nameReplace = str_replace(" ", "", $profileName);//replace the spaces in the profile name input with nothing for validation later
                            $errMsg = "";

                            if($emailVal == false) //if email filter returns false
                            {
                                $errMsg = $errMsg . "<p><span id='err-msg'>Please enter a valid email</span></p>";
                            }

                            if(!ctype_alpha($nameReplace)) //if the name space replaced contains anything other than letters the error message will be stored in $errMsg
                            {
                                $errMsg = $errMsg . "<p><span id='err-msg'>Profile name can only contain letters</span></p>";
                            }

                            if(!ctype_alnum($password))//if the password contains anything other than letters and numbers the error message will be stored in $errMsg
                            {
                                $errMsg = $errMsg . "<p><span id='err-msg'>Password must contain only numbers and letters</span></p>";
                            }

                            if($rows != 0)//if the database returns a row when queried for the entered email the error message will be stored in $errMsg
                            {
                                $errMsg = $errMsg . "<p><span id='err-msg'>There is an account already associated with this email</span></p>";
                            }

                            if($password != $pswdCheck) //if the password and password check fields don't match the error message will be stored in $errMsg
                            {
                                $errMsg = $errMsg . "<p><span id='err-msg'>Password dosen't match</span></p>";
                            }

                            if($errMsg == "") //if the error message is empty add the new users details to the database then log them in and redirect them to friendadd.php
                            {
                                $signupQuery = "INSERT INTO friends(friend_email, friend_password, profile_name, date_started, num_of_friends) VALUES('$email', '$password', '$profileName', '$date', 0)";
                                mysqli_query($conn, $signupQuery);
                                setDetails($conn, $email, $password);
                                $_SESSION['login'] = true;
                                header("location:friendadd.php");
                            } else { //display the error messages
                                echo $errMsg;
                            }

                            mysqli_close($conn); //closes the connection
                        } else {
                            echo "<p><span id='err-msg'>Please do not leave any fields blank</span></p>";
                        }
                    }
                ?>
            </div>

            <div id="body-login-display">
                <?php echo $loginDisplay; ?>
            </div>
        </div>
        <div id="footer">
            <footer>
                <p></p>
            </footer>
        </div>
    </div>
</body>
</html>
