<?php
    session_start(); //starts the session
    if(!isset($_SESSION['login'])) //checks if the session variable is set, if not it sets a default value of false
    {
        $_SESSION['login'] = false;
        $_SESSION['pswd'] = "";
    }

    if($_SESSION['login'] == true) //if the user is logged in it shows a different navbar to non logged in users
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

    if(!isset($_SESSION['fill-email'])) //if the fill email session variable hasn't been set it gets set as null by default
    {
        $_SESSION['fill-email'] = null;
    }

    if(isset($_POST['email']) && !empty($_POST['email']))//if the email has been set and isn't empty the fill-email session variable is set as equal as the users input to the email field
    {
        $_SESSION['fill-email'] = $_POST['email'];
    }

    //local variable taking the session login variables value
    $isAuth = $_SESSION['login'];
    //local fill-email variable taking the session variables value
    $fillEmail = $_SESSION['fill-email'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>QUARK</title>
    <meta charset="utf-8">
    <meta name="Author" content="William Qoro">
    <meta name="Desciption" content="Assignment 2 Login">
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

            <div id="body-login">
                <form action="login.php" method="post" id="login">
                    <p><span id="login-title">LOGIN</span><p>
                    <div id="login-fields">
                        <p><input type="text" name="email" placeholder="Email" value=<?php echo"'$fillEmail'"; ?>></p>
                        <p><input type="password" name="password" placeholder="Password"></p>
                        <p><input type="submit" name="login" value="LOGIN"><input type="reset" name="reset" value="RESET"></p>
                        <input type="hidden" name="hidden-field" content="load"/>
                        <?php
                            function setDetails($conn, $email, $pswd){ //function that gets the logging in users details from the dtatabase then sets these details in session variables
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

                            if(isset($_POST['hidden-field'])) //the hidden varuable is only not null when the form is submitted, this is to stop the php script from running and throwing errors on the inital load of the page
                            {
                                if(isset($_POST['email']) && !empty($_POST['email']) && isset($_POST['password']) && !empty($_POST['password'])) //if the fields aren't blank
                                {
                                    //;oacl variables of the post variables from the users input into the form
                                    $email = $_POST['email'];
                                    $password = $_POST['password'];

                                    require_once("settings.php");

                                    $conn = @mysqli_connect($host, $user, $pswd) or die("Failed to connect to the server, Debugging errno: " . mysqli_connect_errno() . PHP_EOL);

                                    @mysqli_select_db($conn, $dbnm) or die("Database not available");

                                    $queryEmail = "SELECT friend_email FROM friends WHERE friend_email='$email'";
                                    $queryPswd = "SELECT friend_password FROM friends WHERE friend_password='$password'";
                                    $queryPswdEmail = "SELECT * FROM friends WHERE friend_email='$email' AND friend_password='$password'";

                                    $emailResult = mysqli_query($conn, $queryEmail);
                                    $pswdResult = mysqli_query($conn, $queryPswd);
                                    $pswdEmailResult = mysqli_query($conn, $queryPswdEmail);

                                    //if database contains an exact email password match
                                    if(mysqli_num_rows($pswdEmailResult) > 0)
                                    {
                                        //set local authentication variable as true
                                        $isAuth = true;
                                        //set session variable to local auth variable
                                        $_SESSION['login'] = $isAuth;
                                        //call setDetails function and set all the users details in the database in session variables
                                        setDetails($conn, $email, $password);
                                        //redirect to friends list page
                                        header("location:friendlist.php");
                                    } else {
                                        $isAuth = false;
                                        $_SESSION['login'] = $isAuth;
                                    }

                                    //if email is not in database
                                    if(mysqli_num_rows($emailResult) == 0)
                                    {
                                        echo "<p><span id='err-msg'>Incorrect email</span></p>";
                                    }

                                    //if password is not in databse
                                    if(mysqli_num_rows($pswdResult) == 0)
                                    {
                                        echo "<p><span id='err-msg'>Incorrect password</span></p>";
                                    }

                                    //free memory space from the reults from the database
                                    mysqli_free_result($emailResult);
                                    mysqli_free_result($pswdResult);

                                    //close the connection
                                    mysqli_close($conn);
                                } else {
                                    //if email not set or empty echo error to user
                                    if(!isset($_POST['email']) || empty($_POST['email']))
                                    {
                                        echo "<p><span id='err-msg'>Please enter a valid email address</span></p>";
                                    }

                                    //if password not set or empty echo error to user
                                    if(!isset($_POST['password']) || empty($_POST['password']))
                                    {
                                        echo "<p><span id='err-msg'>Please enter your password</span></p>";
                                    }
                                }
                            }
                        ?>
                    </div>
                </form>
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
