<?php


  //Start session for keeping user info across pages
  session_start();

  // Include config file
  require_once 'config.php';


  /* Have access to these session variables
  User birthday = $_SESSION['s_bday']
  User ID = $_SESSION['s_user']
  User Avg Age days = $_SESSION['s_avgAge']
  User age now days = $_SESSION['s_ageNow']
  User base remaining days = $_SESSION['s_baseDays']
  */

  //Find net effect of behaviors
  $sql = "SELECT SUM(b.effect), SUM(g.effect) FROM user u
  LEFT JOIN user_behavior ub on u.id = ub.uid
  LEFT JOIN behavior b on ub.bid = b.id
  LEFT JOIN user_genetic ug on u.id = ug.uid
  LEFT JOIN genetic g on ug.gid = g.id
  WHERE u.id = ".$_SESSION['s_user'].";";

  //Whitelist data to prevent sql injection
  //https://stackoverflow.com/questions/60174/how-can-i-prevent-sql-injection-in-php
  if((gettype($_SESSION['s_user'])) !== "integer") {
    //Exit page and exit website
    echo "<script type=\"text/javascript\">
          window.location.replace('http://www.thedeathTest.com');
          </script>";
    exit();
  }

  //Retrieve query, save net effect of behaviors
  $result = mysqli_query($link, $sql);
  if(!$result){
    echo "<script type=\"text/javascript\">
          window.location.replace('http://www.thedeathTest.com');
          </script>";
    exit();
  }
  $resultArray = mysqli_fetch_array($result);
  $netEffect = $resultArray[0] + $resultArray[1];
  if($netEffect <= -1){
    $netEffect = -.994;
  }

  //Adjust base days according to net effect of behaviors and genetics
  $_SESSION['s_baseDays'] = (int)((float)$_SESSION['s_baseDays'] + ((float)$_SESSION['s_baseDays'] * $netEffect));

  //Convert birthday to string for date_diff function
  $strBday = (string)$_SESSION['s_bday'];

  //Convert birthday, length of age to string for strtotime function
  $strBday = (string)$_SESSION['s_bday'];
  $strAvg = (string)$_SESSION['s_baseDays'];
  //Find deathdate
  $deathDate = date('F jS, Y', strtotime("+ $strAvg DAYS"));
  $deathDateSql = date('Y-m-d', strtotime("+ $strAvg DAYS"));

  //Find death age
  $DA_bday = date_create($strBday);
  $DA_dday = date_create($deathDate);
  $deathAge = date_diff($DA_dday,$DA_bday);
  $deathAgeDays = intval($deathAge->format("%a"));


  //Insert death info into database
  $sql2 = "INSERT INTO death (uid, deathAge, deathDate)
  VALUES (?,?,?);";

  if($stmt = mysqli_prepare($link, $sql2)){
    //Bind variables to sql statemetn
    mysqli_stmt_bind_param($stmt,"iis", $param_userID, $param_dage, $param_dday);

    //set params
    $param_userID = $_SESSION['s_user'];
    $param_dage = $deathAgeDays;
    $param_dday = $deathDateSql;

    //If successful
    $deathAdded = (mysqli_stmt_execute($stmt));

    if(!$deathAdded){
        echo (" Error #: ".mysqli_error($link));
        exit();
    } else {

        mysqli_stmt_close($stmt);
    }

  }




?>

<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="A death test quiz">
    <meta name="author" content="Zach Mitchell">

    <title>The Death Test: Find Out When You Will Die</title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom fonts for this template -->
    <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Lora:400,700,400italic,700italic" rel="stylesheet" type="text/css">
    <link href='https://fonts.googleapis.com/css?family=Cabin:700' rel='stylesheet' type='text/css'>

    <!-- Custom styles for this template -->
    <link href="css/grayscale.min.css" rel="stylesheet">



  </head>

  <body id="page-top">

    <!-- Navigation-->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top" id="mainNav">
      <div class="container">
        <a class="navbar-brand js-scroll-trigger" href="index.php">The Death Test</a>

      </div>
    </nav>


    <!-- About Section -->
    <section id="thebasics" class="content-section text-center">
      <div class="container">
        <div class="row">
          <div class="col-lg-8 mx-auto">
            <h1>Set your Calendar</h1></br>

            <?php
                  echo "<h4>You will die on </br>".$deathDate."</h4>";
                  echo "<h4>at ".$deathAge->format("%Y")." years old.</h4>";
            ?>
            <div class = "text-center">
              <canvas  class="img-fluid" id="myCanvas" width="750" height="750" style="display: block;"></canvas>
              <div>
              <h4>Causes of death</h4>

                <?php

                // Select current items in the table
                $sql = "SELECT ub.id, b.cod, b.cod_chance
                FROM user u
                LEFT JOIN user_behavior ub ON u.id = ub.uid
                LEFT JOIN behavior b ON b.id = ub.bid
                WHERE u.id = ".$_SESSION['s_user']."
                ORDER BY b.cod_chance DESC LIMIT 5;";


                //As long as table not empty create table and display results
                if($result = mysqli_query($link, $sql)){
                    //The Title of the table
                    if(mysqli_num_rows($result) > 0){

                            //Flesh out table with result from mysql query
                            while($row = mysqli_fetch_array($result)){
                                    echo "<h5>" . $row[1] . "   <span style='color: #42DCA3'>". (int)((float)$row[2] * 100) . "%</span></h5>";
                            }

                        // Free result set
                        mysqli_free_result($result);
                        //Nothing in table
                    } else{
                        echo "<h2 class='lead'>Old age, I guess.</h2>";
                    }
                } else{
                    echo "ERROR: Could not execute $sql. " . mysqli_error($link);
                }
                // End request
                mysqli_close($link);
                ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>





    <!-- Footer -->
    <footer>
      <div class="container text-center">
        <p>Copyright &copy; TheDeathTest.com 2018</p>
      </div>
    </footer>

    <!-- Bootstrap core JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Plugin JavaScript -->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for this template -->
    <script src="js/grayscale.min.js"></script>

  </body>
  <script>
    var canvas = document.getElementById('myCanvas');
        var context = canvas.getContext('2d');
        var imageObj = new Image();
        //var f = new FontFace('test', 'url(x)');

        imageObj.onload = function() {
          context.drawImage(imageObj, 0, 0);
          context.font = 'bold 40pt Helvetica Neue';
          context.fillStyle = "black";
        context.fillText("<?php echo $DA_bday->format('Y'); ?>",312,400);
        context.fillText("-",361,450);
        context.fillText("<?php echo $DA_dday->format('Y'); ?>",312,500);
        };
        imageObj.src = '/img/headstone_transparent.png';
  </script>
</html>
<?php
    session_unset();
    session_write_close();
 ?>
