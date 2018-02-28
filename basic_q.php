<?php

  //Used as reference for PHP, Tutorial Republic
  //https://www.tutorialrepublic.com/php-tutorial/php-mysql-crud-application.php

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

  //Once the submit button is pressed
  if($_SERVER["REQUEST_METHOD"]=="POST") {


      if(!empty($_POST['genetic'])){
        //Prepare to insert genetic for user
        $sql = "INSERT INTO user_genetic (uid, gid)
        VALUES (?,?);";

        //Prep mysqli statement
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $param_userID, $param_genID);

        //Attach params for passing to mysql
        $param_userID = $_SESSION['s_user'];

        //push each set of values to mysql db
        foreach($_POST['genetic'] as $disease){
          $param_genID = $disease;
          mysqli_stmt_execute($stmt);
        }

        //Close statement
        mysqli_stmt_close($stmt);
      }

      if(!empty($_POST['behavior'])) {
        //Prepare to insert behavior for user
        $sql2 = "INSERT INTO user_behavior (uid, bid)
        VALUES (?,?);";

        //Prep mysqli statement
        $stmt = mysqli_prepare($link, $sql2);
        mysqli_stmt_bind_param($stmt, "ii", $param_userID, $param_behID);

        //Attach params for passing to mysql
        $param_userID = $_SESSION['s_user'];

        //Push each set of values to mysql db
        foreach($_POST['behavior'] as $disease){
          $param_behID = $disease;
          mysqli_stmt_execute($stmt);
        }
        //Close statement
        mysqli_stmt_close($stmt);
      }


      echo "<script type=\"text/javascript\">
            window.location.replace('/result.php');
            </script>";
      exit();

  }

  //Close connection
  mysqli_close($link);

?>

<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="A death test quiz">
    <meta name="author" content="Zach Mitchell">

    <title>The Death Quiz: Find Out When You Will Die</title>

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

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top" id="mainNav">
      <div class="container">
        <a class="navbar-brand js-scroll-trigger" href="index.php">The Death Quiz</a>

      </div>
    </nav>


    <!-- About Section -->
    <section id="thebasics" class="content-section text-center">
      <div class="container">
        <div class="row">
          <div class="col-lg-8 mx-auto">
            <h1>Genetics and Behaviors</h1></br>

              <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                  <!--GET HEALTHSCALE-->
                <!--
                <div class="form-group <?php echo (!empty($healthScale_err)) ? 'has-error' : ''; ?>">
                    <h5>Your health on a scale from 1-10 (10 better than 1):</h5>
                    <input type=number  min="1" max="10" name="healthScale" value="<?php echo $healthScale; ?>">
                    <span class="help-block"><?php echo $healthScale_err;?></span>
                </div>

                <div class="form-group <?php echo (!empty($yChrom_err)) ? 'has-error' : ''; ?>">
                    <h5>Input your height & weight</h5></br>
                    <label>Height </label><input type=number min= value= ></br>
                    <label>Weight </label><input type=number min= value= >
                    <span class="help-block"><?php echo $yChrom_err;?></span>
                </div>-->

                <div class="form-group <?php echo (!empty($yChrom_err)) ? 'has-error' : ''; ?>">
                    <h5>Do you smoke cigarettes (past or present)?</h5></br>
                    <label for="smokerY1">Yes, MORE than half a pack (> 10 cigarettes) per day</label><input type="radio" id="smokerY1" name="behavior[]" value=3>
                    <label for="smokerY2">Yes, LESS than half a pack (<= 10 cigarettes) per day</label><input type="radio" id="smokerY2" name="behavior[]" value=4>
                    <label for="smokerO">Occassionally, every once in a while I smoke</label><input type="radio" id="smokerO" name="behavior[]" value=5>
                    <label for="smokerN">I don't smoke cigarettes</label><input type="radio" id="smokerO" name="noSmoke" value="x">
                    <span class="help-block"><?php echo $smoke_err;?></span>
                </div>
                  <!--GET CARDIO-->
                  <!--GET FAMILY HISTORY DISEASE-->
                  <!--GET DISEASE-->
                  <div class="form-group <?php echo (!empty($genetic_err)) ? 'has-error' : ''; ?>">
                      <h5>Do you have any of these diseases?</h5></br>
                      <label for="1">DIABETES 1</label><input type="checkbox" id="d1" name="genetic[]" value=1>
                      <label for="2">DIABETES 2</label><input type="checkbox" id="d2" name="genetic[]" value=2>
                      <label for="3">HYPERTENSION</label><input type="checkbox" id="hypertension" name="genetic[]" value=3>
                      <span class="help-block"><?php echo $genetic_err;?></span>
                  </div>


                  <div class="form-group <?php echo (!empty($genetic_err)) ? 'has-error' : ''; ?>">
                      <h5>Do any of these describe you?</h5></br>
                      <label for="1">Exercise 5+ days/week</label><input type="checkbox" id="e1" name="behavior[]" value=6>
                      <label for="2">Exercise 2-4 days/week</label><input type="checkbox" id="e2" name="behavior[]" value=7>
                      <label for="3">Exercise once/week</label><input type="checkbox" id="e3" name="behavior[]" value=8>
                      <span class="help-block"><?php echo $genetic_err;?></span>
                  </div>


                  <!--GET ACTIVITIES/BEHAVIORS-->
                <input type="submit" class="btn btn-primary" value="Submit">
              </form>

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

    <!-- Google Maps API Key - Use your own API key to enable the map feature. More information on the Google Maps API can be found at https://developers.google.com/maps/ -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCRngKslUGJTlibkQ3FkfTxj3Xss1UlZDA&sensor=false"></script>

    <!-- Custom scripts for this template -->
    <script src="js/grayscale.min.js"></script>

  </body>

</html>
