<?php
    session_start(); //starts the current session
    if(!isset($_SESSION['login'])) //id the login session hasn't been set it sets a default value of false
    {
        $_SESSION['login'] = false;
    }

    if($_SESSION['login'] == true) //if the user is logged in the login session variable will be true so their navigation bar will change to display pages that they can now access because they are logged in
    {
        $login = "";
        $logout = "<span id='nav-element'><a href='logout.php'>LOGOUT</a></span>";
        $friendadd = "<span id='nav-element'><a href='friendadd.php'>ADD</a></span>";
        $friendlist = "<span id='nav-element'><a href='friendlist.php'>FRIENDS</a></span>";
        $loginDisplay = "<p><span id='login-display'>Logged in as: " . $_SESSION['name'] . "</span></p>";
    } else {
        $login = "<span id='nav-element'><a href='login.php'>LOGIN</a></span>"; //When not logged in users can't access member only pages
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
    <meta name="Desciption" content="Assignment 2 Home page">
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

            <div id="body">
                <p><span id="details">DETAILS</span></p>
                <div id="body-top">
                    <p><span id="body-highlight">Name: </span>William Qoro</p>
                </div>
                <p><span id="body-highlight">Student ID: </span>100676265</p>
                <p><span id="body-highlight">Email: </span>100676265@student.swin.edu.au</p>
                <p><span id="declaration">I declare that this assignment is my individual work. I have not worked collaboratively nor have I copied from any other student’s work or from any other source.</span></p>

                <?php
                    require_once("settings.php");

                    $conn = @mysqli_connect($host, $user, $pswd) or die("Failed to connect to the server, Debugging errno: " . mysqli_connect_errno() . PHP_EOL); //connects to the server

                    @mysqli_select_db($conn, $dbnm) or die("Database not available"); //selects the database

                    function NumOfFriends($connection, $friendID) { //This function counts the num of friends an id has then returns the total
                        $numQuery = "SELECT * FROM myfriends WHERE friend_id1=$friendID OR friend_id2=$friendID";
                        $num = mysqli_query($connection, $numQuery);

                        if($num != false) {
                            $total = mysqli_num_rows($num);
                        } else {
                            $total = 0;
                        }

                        return $total;
                    }

                    //Arrays containing details of the intial population of members
                    $ChrisH = "'CHemsworth@hotmail.com', 'LordOfThunder1', 'Chris Hemsworth', '" . date("Y-m-d") . "', " . "'" . 0 . "'";
                    $BrieL = "'BLarson@gamil.com', 'capnNum1', 'Brie Larson', '" . date("Y-m-d") . "', " . "'" . 0 . "'";
                    $AnthonyR = "'ARusso@hotmail.com', 'Russo2', 'Anthony Russo', '" . date("Y-m-d") . "', " . "'" . 0 . "'";
                    $DenisV = "'DVilleneuve@mail.com', 'Incideraries', 'Denis Villeneuve', '" . date("Y-m-d") . "', " . "'" . 0 . "'";
                    $GretaG = "'GGerwig@hotmail.com', 'LadyBird', 'Greta Gerwig', '" . date("Y-m-d") . "', " . "'" . 0 . "'";
                    $KathrynB = "'KBigelow@hotmail.com', 'HurtLocker', 'Kathryn Bigelow', '" . date("Y-m-d") . "', " . "'" . 0 . "'";
                    $ImogenP = "'IPoots@hotmail.com', 'TheGreenRoom', 'Imogen Poots', '" . date("Y-m-d") . "', " . "'" . 0 . "'";
                    $ChiwetalE = "'CEjiofor@hotmail.com', '12YearsASlave', 'Chwietel Ejiofor', '" . date("Y-m-d") . "', " . "'" . 0 . "'";
                    $AngL = "'ALee@hotmail.com', 'LifeOfPi', 'Ang Lee', '" . date("Y-m-d") . "', " . "'" . 0 . "'";
                    $Tester = "'Tester@hotmail.com', 'Test1234', 'Tester', '" . date("Y-m-d") . "', " . "'" . 0 . "'";

                    //Array that holds all of the intial population of memebrs details
                    $dataArray = [$ChrisH, $BrieL, $AnthonyR, $DenisV, $GretaG, $KathrynB, $ImogenP, $ChiwetalE, $AngL, $Tester];

                    //if the friends table dosen't already exist this query creates it
                    $friendsQuery = "CREATE TABLE IF NOT EXISTS friends (
                        friend_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        friend_email varchar(50) NOT NULL,
                        friend_password varchar(20) NOT NULL,
                        profile_name varchar(30) NOT NULL,
                        date_started DATE NOT NULL,
                        num_of_friends INT UNSIGNED
                        )";

                    //if myfriends table doesn't exist this query creates it
                    $myfriendsQuery = "CREATE TABLE IF NOT EXISTS myfriends (
                        friend_id1 INT NOT NULL,
                        friend_id2 INT NOT NULL
                        )";

                    //executions of the queries
                    mysqli_query($conn, $friendsQuery);
                    mysqli_query($conn, $myfriendsQuery);

                    //query that asks for all the rows of the friends table, if it has already been successfully populated it will have more than one row
                    $popQuery = mysqli_query($conn, ("SELECT * FROM friends"));
                    $isPopulated = mysqli_num_rows($popQuery) > 0;

                    //checks if the friends table has been populated
                    if(!$isPopulated)
                    {
                        //if friends table hasn't been populated this for loop populates it using the $dataArray values
                        for($i = 0; $i < count($dataArray); $i++)
                        {
                            $query = "INSERT INTO friends(friend_email, friend_password, profile_name, date_started, num_of_friends) VALUES($dataArray[$i])";
                            mysqli_query($conn, $query);
                        }
                    }


                    $myPopQuery = mysqli_query($conn, ("SELECT * FROM myfriends"));
                    $isMyPopulated = mysqli_num_rows($myPopQuery) > 0;

                    //Checks if the myfriends table is already populated
                    if(!$isMyPopulated)
                    {
                        //populates myfriends table with values of ID's of the users populated in the friends table to represent friendships
                        $query = "INSERT INTO myfriends(friend_id1, friend_id2) VALUES(1, 9)";
                        $query2 = "INSERT INTO myfriends(friend_id1, friend_id2) VALUES(2, 8)";
                        $query3 = "INSERT INTO myfriends(friend_id1, friend_id2) VALUES(3, 6)";
                        $query4 = "INSERT INTO myfriends(friend_id1, friend_id2) VALUES(7, 4)";
                        $query5 = "INSERT INTO myfriends(friend_id1, friend_id2) VALUES(5, 1)";
                        $query6 = "INSERT INTO myfriends(friend_id1, friend_id2) VALUES(3, 7)";
                        $query7 = "INSERT INTO myfriends(friend_id1, friend_id2) VALUES(3, 5)";
                        $query8 = "INSERT INTO myfriends(friend_id1, friend_id2) VALUES(9, 7)";
                        $query9 = "INSERT INTO myfriends(friend_id1, friend_id2) VALUES(6, 1)";
                        $query10 = "INSERT INTO myfriends(friend_id1, friend_id2) VALUES(10, 9)";
                        $query11 = "INSERT INTO myfriends(friend_id1, friend_id2) VALUES(2, 9)";
                        $query12 = "INSERT INTO myfriends(friend_id1, friend_id2) VALUES(2, 7)";
                        $query13 = "INSERT INTO myfriends(friend_id1, friend_id2) VALUES(3, 4)";
                        $query14 = "INSERT INTO myfriends(friend_id1, friend_id2) VALUES(7, 10)";
                        $query15 = "INSERT INTO myfriends(friend_id1, friend_id2) VALUES(5, 4)";
                        $query16 = "INSERT INTO myfriends(friend_id1, friend_id2) VALUES(2, 1)";
                        $query17 = "INSERT INTO myfriends(friend_id1, friend_id2) VALUES(6, 7)";
                        $query18 = "INSERT INTO myfriends(friend_id1, friend_id2) VALUES(8, 3)";
                        $query19 = "INSERT INTO myfriends(friend_id1, friend_id2) VALUES(4, 9)";
                        $query20 = "INSERT INTO myfriends(friend_id1, friend_id2) VALUES(8, 4)";
                        mysqli_query($conn, $query);
                        mysqli_query($conn, $query2);
                        mysqli_query($conn, $query3);
                        mysqli_query($conn, $query4);
                        mysqli_query($conn, $query5);
                        mysqli_query($conn, $query6);
                        mysqli_query($conn, $query7);
                        mysqli_query($conn, $query8);
                        mysqli_query($conn, $query9);
                        mysqli_query($conn, $query10);
                        mysqli_query($conn, $query11);
                        mysqli_query($conn, $query12);
                        mysqli_query($conn, $query13);
                        mysqli_query($conn, $query14);
                        mysqli_query($conn, $query15);
                        mysqli_query($conn, $query16);
                        mysqli_query($conn, $query17);
                        mysqli_query($conn, $query18);
                        mysqli_query($conn, $query19);
                        mysqli_query($conn, $query20);
                    }

                    //a for loop that checks then updates the number of friends each user has
                    for($i = 1; $i <= count($dataArray); $i++)
                    {
                        $query = "UPDATE friends SET num_of_friends=" . NumofFriends($conn, $i) . " WHERE friend_id=$i";
                        mysqli_query($conn, $query);
                    }

                    //queries asking to SHOW the 2 tables
                    $friends = mysqli_query($conn, ("SHOW TABLES LIKE 'friends'"));
                    $myfriends = mysqli_query($conn, ("SHOW TABLES LIKE 'myfriends'"));

                    //queries asking for all rows of the 2 tables
                    $popQuery = mysqli_query($conn, ("SELECT * FROM friends"));
                    $myPopQuery = mysqli_query($conn, ("SELECT * FROM myfriends"));

                    //checks if friends and myfriends have been successfully created
                    $FriendsExists = mysqli_num_rows($friends) > 0;
                    $MyfriendsExists = mysqli_num_rows($myfriends) > 0;

                    //checks friends and myfriends have been successfully populated
                    $isPop = mysqli_num_rows($popQuery) > 1;
                    $isMyPop = mysqli_num_rows($myPopQuery) > 1;

                    //takes the previous checks and displays the apporpriate message to the user
                    if($FriendsExists && $MyfriendsExists && $isPop && $isMyPop)
                    {
                        echo "<p><span id='php-echo'><strong>(Tables 'friends' & 'myfriends' were created and populated)</strong></span></p>";
                    } else {
                        echo "<p><span id='php-echo'><strong>(Err: error occured in table creation)</strong></span></p>";
                    }

                    //frees the memory of the results fetched from the database
                    mysqli_free_result($friends);
                    mysqli_free_result($myfriends);

                    //closes the donnection to the database and server
                    mysqli_close($conn);
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
