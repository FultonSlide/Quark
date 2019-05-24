<?php
    session_start(); //start session
    if($_SESSION['login'] != true)
    {
        header("location: index.php"); // if login isn't true the user can't access this page so it redirects back to the home page
    } else { //if logged in it shows the logged in navbar
        $login = "";
        $logout = "<span id='nav-element'><a href='logout.php'>LOGOUT</a></span>";
        $friendadd = "<span id='nav-element'><a href='friendadd.php'>ADD</a></span>";
        $friendlist = "<span id='nav-element'><a href='friendlist.php'>FRIENDS</a></span>";
        $loginDisplay = "<p><span id='login-display'>Logged in as: " . $_SESSION['name'] . "</span></p>";
    }

    require_once("settings.php");

    $conn = @mysqli_connect($host, $user, $pswd) or die("Failed to connect to the server, Debugging errno: " . mysqli_connect_errno() . PHP_EOL);

    @mysqli_select_db($conn, $dbnm) or die("Database not available");

    if(isset($_POST['unfriend'])) //if the unfriend button was clicked
    {
        $unfriendID = $_POST['unfriend']; //id of the user in the database to unfriend
        $userID = $_SESSION['id']; //logged in users id

        $query = "DELETE FROM myfriends WHERE friend_id1=$unfriendID AND friend_id2=$userID OR friend_id1=$userID AND friend_id2=$unfriendID";
        mysqli_query($conn, $query); //query that deletes the row where the unfriended users id from the myfriends table matches with the logged in users id

        unset($_POST['unfriend']); //unsets the post variable
    }

    $numQuery = "SELECT * FROM myfriends WHERE friend_id1=" . $_SESSION['id'] . " OR friend_id2=" . $_SESSION['id'];
    $num = mysqli_query($conn, $numQuery); //query that gets the number of friends of the logged in user

    if($num != false) { //if the $num contains an mysqli_object
        $total = mysqli_num_rows($num);
    } else {
        $total = 0;
    }

    $numOfFriendQuery = "UPDATE friends SET num_of_friends=$total WHERE friend_id=" . $_SESSION['id'];
    mysqli_query($conn, $numOfFriendQuery); //updates the number of friends of the logged in user in the database

    $_SESSION['friend-num'] = $total; //updates the session variable friend-num of the logged in user

    mysqli_free_result($num); //frees the result in memory
    mysqli_close($conn);//closes the connections
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>QUARK</title>
    <meta charset="utf-8">
    <meta name="Author" content="William Qoro">
    <meta name="Desciption" content="Assignment 2 Friends List page">
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

            <div id="body">
                <p><span id="details">FRIEND LIST</span></p>
                <div id="body-top">
                    <p><span id="body-highlight">WELCOME <?php echo $_SESSION['name']; ?>!</span></p>
                </div>
                <p>Number of friends: <?php echo $_SESSION['friend-num']; ?></p>
                <?php
                    function getName($conn, $friendID){ //this function takes in an id and returns the name associated with it in the friends table
                        $query = "SELECT * FROM friends WHERE friend_id='$friendID'";
                        $result = mysqli_query($conn, $query);
                        $details = mysqli_fetch_row($result);

                        $name = $details[3];

                        return $name;
                    }

                    function unfriendDisplay($conn, $friendID) { //this function takes in an id and creates the unfriend display on the webpage with buttons and values up to date

                        $query = "SELECT * FROM myfriends WHERE friend_id1=$friendID OR friend_id2=$friendID";
                        $result = mysqli_query($conn, $query);

                        $ids = mysqli_fetch_row($result);
                        $allIDs = array();
                        $otherIDs = array();

                        while($ids)
                        {
                            array_push($allIDs, $ids[0], $ids[1]);
                            $ids = mysqli_fetch_row($result);
                        }

                        for($i = 0; $i < count($allIDs); $i++)
                        {
                            if($allIDs[$i] != $friendID)
                            {
                                array_push($otherIDs, $allIDs[$i]);
                            }
                        }

                        for($i = 0; $i < count($otherIDs); $i++)
                        {
                            echo "<form action='friendlist.php' method='post' id='friend-list'>
                                    <p>
                                        <div>
                                            <p><span id='rd'><label id='row-display'>" . getName($conn, $otherIDs[$i]) . "</label></span></p>
                                            <input type='hidden' value='" . $otherIDs[$i]  . "' name='unfriend'>
                                            <p><span id='bd'><input type='submit' value='UNFRIEND' id='unfriend-btn'></span></p>
                                        </div>
                                    </p>
                                  </form>";
                        }
                    }

                    require_once("settings.php");

                    $conn = @mysqli_connect($host, $user, $pswd) or die("Failed to connect to the server, Debugging errno: " . mysqli_connect_errno() . PHP_EOL);

                    @mysqli_select_db($conn, $dbnm) or die("Database not available");

                    unfriendDisplay($conn, $_SESSION['id']); //call to the unfriendDisplay function

                    mysqli_close($conn);//closes the connection
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
