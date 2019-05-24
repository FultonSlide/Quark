<?php
    session_start(); //starts the session
    if($_SESSION['login'] != true)
    {
        header("location: index.php"); //redirects the user to the home page if their not logged im
    } else { //updates the navbar for the user to the logged in navbar
        $login = "";
        $logout = "<span id='nav-element'><a href='logout.php'>LOGOUT</a></span>";
        $friendadd = "<span id='nav-element'><a href='friendadd.php'>ADD</a></span>";
        $friendlist = "<span id='nav-element'><a href='friendlist.php'>FRIENDS</a></span>";
        $loginDisplay = "<p><span id='login-display'>Logged in as: " . $_SESSION['name'] . "</span></p>";
    }

    function getFriendArray($conn, $friendID){ //function that takes in an id and returns the array of ids that are not associated with the logged in users id in the myfriends table
        $idQuery = "SELECT friend_id FROM friends";
        $idResult = mysqli_query($conn, $idQuery); //query gets athe object of all the ids from the friend table

        $friIDs = mysqli_fetch_row($idResult); //query gets the individual ids in an row
        $allFriendIDs = array();

        while($friIDs) //takes all the ids and puts them in the array $allFriendIDs
        {
            array_push($allFriendIDs, $friIDs[0]);
            $friIDs = mysqli_fetch_row($idResult);
        }

        $query = "SELECT * FROM myfriends WHERE friend_id1=$friendID OR friend_id2=$friendID";
        $result = mysqli_query($conn, $query); //query the gets all the ids in the myfriend table thats associated with the passed in id

        $ids = mysqli_fetch_row($result); //gets the individual ids in a row
        $myFriendIDs = array();
        $addFriendIDs = array();

        if(!in_array($_SESSION['id'], $myFriendIDs)) //if myfriendIds dosen't contain the users id
        {
            array_push($myFriendIDs, $_SESSION['id']);
        }

        while($ids) //adds all the ids to the myfriednIds array
        {
            array_push($myFriendIDs, $ids[0], $ids[1]);
            $ids = mysqli_fetch_row($result);
        }

        for($i = 0; $i < count($allFriendIDs); $i++) //gets all the ids of users that arent the currently logged in users friends and puts them in an array called addfreindIDs
        {
            if(!in_array($allFriendIDs[$i], $myFriendIDs))
            {
                array_push($addFriendIDs, $allFriendIDs[$i]);
            }
        }

        return $addFriendIDs;
    }

    require_once("settings.php");

    $conn = @mysqli_connect($host, $user, $pswd) or die("Failed to connect to the server, Debugging errno: " . mysqli_connect_errno() . PHP_EOL);

    @mysqli_select_db($conn, $dbnm) or die("Database not available");

    if(isset($_POST['addfriend'])) //if the addfriend button was clicked has been sent via post
    {
        $addfriendID = $_POST['addfriend'];
        $loggedInID = $_SESSION['id'];

        $query = "INSERT INTO myfriends(friend_id1, friend_id2) VALUES('$loggedInID', '$addfriendID')";
        mysqli_query($conn, $query); //query that adds a friend t the current logged in user into the myfriends table

        $_POST['addfriend'] = null; //unsets the addfriend post variable
    }

    $numQuery = "SELECT * FROM myfriends WHERE friend_id1=" . $_SESSION['id'] . " OR friend_id2=" . $_SESSION['id'];
    $num = mysqli_query($conn, $numQuery); //query that gets the object for the number of friends the users currently has

    if($num != false) {
        $total = mysqli_num_rows($num);
    } else {
        $total = 0;
    }

    $numOfFriendQuery = "UPDATE friends SET num_of_friends=$total WHERE friend_id=" . $_SESSION['id'];
    mysqli_query($conn, $numOfFriendQuery); //updates the number of friends of the currently logged in user

    $_SESSION['friend-num'] = $total; //updates the session variable of the users number of friends

    $_SESSION['addfriend-array'] = getFriendArray($conn, $_SESSION['id']); //call to getfriednarray() function

    if(isset($_POST['next'])) //if the next button was clicked
    {
        if($_SESSION['end'] == 0) //end represents whether the user is at the end of the pages of friedns, if its equal to 0 the user isn't at the end of pages yet
        {
            $_SESSION['first'] += 5; //increment the array element of the first friend to be displayed to the user by 5
            $_SESSION['last'] += 5; //increment the array element of the last friend to be displayed to the user by 5
            $_SESSION['page-num']++; //increment the page number
        }

        if($_SESSION['last'] >= count($_SESSION['addfriend-array'])) //if the last page incremeneted in the last block exceeds the count of friends to display array the last elemetn is equal to the count of the friends to display array
        {
            $_SESSION['end'] = count($_SESSION['addfriend-array']);
            $_SESSION['next'] = ""; //in this block were on the 'last' page so the next button isn't displayed
            $_SESSION['prev'] = "PREV";
        } else {
            $_SESSION['end'] = 0; //in this block we're not on the last page so the end session variable is set to zero
            $_SESSION['next'] = "NEXT";
            $_SESSION['prev'] = "PREV";
        }

        unset($_POST['next']);
    } else if(isset($_POST['previous'])) //if the prev button is clicked
    {
        if($_SESSION['page-num'] != 1) //if the user isn't on the first page
        {
            $_SESSION['page-num']--; //decrement the page number
            $_SESSION['next'] = "NEXT";
            $_SESSION['prev'] = "PREV";
        } else {
            $_SESSION['next'] = "NEXT"; //if the user is on the first page don't display the previous button
            $_SESSION['prev'] = "";
        }

        if($_SESSION['first'] > 1) //if the first element is greater then one then decrement the range of friends to dispaly from the friend array
        {
            $_SESSION['first'] -= 5;
            $_SESSION['last'] -= 5;
        }

        unset($_POST['previous']);
    } else { //if nothing has been selected, ie the page is refreshed or an addfriend button has been clicked default to the first page
        $_SESSION['first'] = 0;
        $_SESSION['last'] = 5;
        $_SESSION['next'] = "NEXT";
        $_SESSION['prev'] = "";
        $_SESSION['page-num'] = 1;
        $_SESSION['end'] = 0;
    }

    if($_SESSION['page-num'] == 1) //if the users on the first page
    {
        $_SESSION['next'] = "NEXT";
        $_SESSION['prev'] = "";
        if(count($_SESSION['addfriend-array']) < 6) //if the array of friends to display is less than six then theres only 1 page to display so no pageination controls are displayed
        {
            $_SESSION['next'] = "";
            $_SESSION['prev'] = "";
            $_SESSION['page-num'] = "";
        }
    }

    if($_SESSION['last'] >= count($_SESSION['addfriend-array'])) //if the count of the friends to display array is less than the 5 friends to be displayed per page it only displays then number of friends left in the array
    {
        $_SESSION['end'] = count($_SESSION['addfriend-array']);
    } else {
        $_SESSION['end'] = 0;
    }

    if($_SESSION['end'] > 0) //if the user isn't at the end of pages
    {
        $lastDisplay = $_SESSION['end'];
    } else {
        if(count($_SESSION['addfriend-array']) == 0) //if the user is at the end of pages and the array of friends to display is empty then there are no friends to display
        {
            $lastDisplay = 0;
        } else {
            $lastDisplay = $_SESSION['last'];
        }
    }
    //sets local variables from the session variables
    $pageNum = $_SESSION['page-num'];
    $firstDisplay = $_SESSION['first'];
    $next = $_SESSION['next'];
    $previous = $_SESSION['prev'];

    mysqli_free_result($num);
    mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>QUARK</title>
    <meta charset="utf-8">
    <meta name="Author" content="William Qoro">
    <meta name="Desciption" content="Assignment 2 Friends Add page">
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
                <p><span id="details">ADD FRIENDS</span></p>
                <div id="body-top">
                    <p><span id="body-highlight">WELCOME <?php echo $_SESSION['name']; ?>!</span></p>
                </div>
                <p>Number of friends: <?php echo $_SESSION['friend-num']; ?></p>
                <?php
                    function getMutFriend($conn, $otherID){ //function that gets all of the mutual friends of a the logged in user and a passed in id
                      $myID = $_SESSION['id'];
                      $myQuery = "SELECT * FROM myfriends WHERE friend_id1=$myID OR friend_id2=$myID";
                      $myResult = mysqli_query($conn, $myQuery); //query that gets all the friend ids associated with them in the myfriends table
                      $otherQuery = "SELECT * FROM myfriends WHERE friend_id1=$otherID OR friend_id2=$otherID";
                      $otherResult = mysqli_query($conn, $otherQuery); //query that gets all the friend ids associated with the other user in the myfriends table
                      $myIDs = mysqli_fetch_row($myResult);
                      $otherIDs = mysqli_fetch_row($otherResult);
                      //initializations
                      $allMyIDs = array();
                      $myFriendIDs = array();
                      $allOtherIDs = array();
                      $otherFriendIDs = array();
                      $mutualFriends = 0;

                      while($myIDs) //gets an array of all ids associated with the user in myffriends table
                      {
                          array_push($allMyIDs, $myIDs[0], $myIDs[1]);
                          $myIDs = mysqli_fetch_row($myResult);
                      }

                      for($i = 0; $i < count($allMyIDs); $i++) //gets an array of all the users friends
                      {
                          if($allMyIDs[$i] != $myID)
                          {
                              array_push($myFriendIDs, $allMyIDs[$i]);
                          }
                      }

                      while($otherIDs) //gets an array of all ids associated with the other user in myffriends table
                      {
                          array_push($allOtherIDs, $otherIDs[0], $otherIDs[1]);
                          $otherIDs = mysqli_fetch_row($otherResult);
                      }

                      for($i = 0; $i < count($allOtherIDs); $i++) //gets an array of all the other users friends
                      {
                          if($allOtherIDs[$i] != $otherID)
                          {
                              array_push($otherFriendIDs, $allOtherIDs[$i]);
                          }
                      }

                      for($i = 0; $i < count($otherFriendIDs); $i++) //checks if any of the other users friend ids are containted within the users friend id array
                      {
                          if(in_array($otherFriendIDs[$i], $myFriendIDs))
                          {
                            $mutualFriends++;
                          }
                      }

                      return $mutualFriends;
                    }

                    function getName($conn, $friendID){ //function that gets the name of the passed in id from the friends table
                        $query = "SELECT * FROM friends WHERE friend_id='$friendID'";
                        $result = mysqli_query($conn, $query);
                        $details = mysqli_fetch_row($result);

                        $name = $details[3];

                        return $name;
                    }

                    function addFriendDisplay($conn, $first, $last, $prev, $pNum, $nex) { //function that display all the forms required for the adding of friends as well as next, previous and page number for pageination

                        for($i = $first; $i < $last; $i++)
                        {
                            echo "<form action='friendadd.php' method='post' id='friend-list'>
                                    <p>
                                        <div>
                                            <p><span id='rd'><label id='row-display'>" . getName($conn, $_SESSION['addfriend-array'][$i]) . "</label></span></p>
                                            <p><span id='mutual'><label id='mf-display'> Mutual Friends: " . getMutFriend($conn, $_SESSION['addfriend-array'][$i]) . "</label></span></p>
                                            <span></span>
                                            <input type='hidden' value='" . $_SESSION['addfriend-array'][$i]  . "' name='addfriend'>
                                            <p><span id='add'><input type='submit' value='ADD FRIEND' id='addfriend-btn'></span></p>
                                        </div>
                                    </p>
                                  </form>";
                        }

                        echo "<form action='friendadd.php' method='post' id='pagination'>
                                <p>
                                  <span id='left-edge'><input type='submit' id='previous' value='$prev' name='previous'></span>
                                  <span id='page-num'>$pNum</span>
                                  <span id='right-edge'><input type='submit' id='next' value='$nex' name='next'></span>
                                </p>
                              </form>";

                    }

                    require_once("settings.php");

                    $conn = @mysqli_connect($host, $user, $pswd) or die("Failed to connect to the server, Debugging errno: " . mysqli_connect_errno() . PHP_EOL);

                    @mysqli_select_db($conn, $dbnm) or die("Database not available");

                    addFriendDisplay($conn, $firstDisplay, $lastDisplay, $previous, $pageNum, $next);

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
