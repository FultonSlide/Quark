<?php
    session_start(); //starts the session
    if(!isset($_SESSION['login'])) //if the login session variable isn't set its gets set to false as default
    {
        $_SESSION['login'] = false;
    }

    if($_SESSION['login'] == true) //if the user is logged in a differnet navbar will be displayed to them compared to if they were not logged in
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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>QUARK</title>
    <meta charset="utf-8">
    <meta name="Author" content="William Qoro">
    <meta name="Desciption" content="Assignment 2 About">
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

            <div id="body-about">
                <p><span id="details">ABOUT</span></p>
                <div id="about-content">
                    <p><strong>What tasks have you not attempted or not completed?</strong></p>
                    <p>None, I have attempted and completed all tasks.</p>
                    <p><strong>What special features have you done, or attempted, in creating the site that we should know about? </strong></p>
                    <p>When you navigate using the friend list and add friends links below it navigates the user to a php script that logs them in as a tester complete with their own testing details, then redirects to the friends list and add friends pages respectively, so that no manual login is required for testing.</p>
                    <p><strong>Which parts did you have trouble with?</strong></p>
                    <p>Pagination took the longest amount of time just trying to get the numbers of friends displaying correctly with each action of page change and friend added.</p>
                    <p><strong>What would you like to do better next time?</strong></p>
                    <p>Push the CSS further to make the user experience a bit more dynamic and finish off the partially mobile responsivness I've implemented on some pages but not all as I ran out of time to finish that off.</p>
                    <p><strong>What additional features did you add to the assignment? (if any)</strong></p>
                    <p>When you navigate using the friend list and add friends links below it navigates the user to a php script that logs them in as a tester complete with their own testing details, then redirects to the friends list and add friends pages respectively, so that no manual login is required for testing.</p>
                    <p><strong>screen shot of a discussion response that answered someone’s thread in the unit’s discussion board for Assignment 2</strong></p>
                    <p><img src="images/DiscussionParticipation.png"></p>
                    <p><a href="guestloginfriendlist.php">FRIEND LIST</a><a href="guestloginaddfriends.php">ADD FRIENDS</a><a href="index.php">HOME</a></p>
                </div>
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
