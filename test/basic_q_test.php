<?php

  //Used as reference for PHP, Tutorial Republic
  //https://www.tutorialrepublic.com/php-tutorial/php-mysql-crud-application.php

  //Start session for keeping user info across pages
  session_start();


  if((gettype($_SESSION['s_user'])) !== "integer") {
    //Exit page

    echo "<script type=\"text/javascript\">
          window.location.replace('http://www.thedeathTest.com');
          </script>";
    exit();
  }
  

  // Include config file
  require_once 'config.php';

  include 'lifespan.php';

  /* Have access to these session variables
  User birthday = $_SESSION['s_bday']
  User ID = $_SESSION['s_user']
  User Avg Age days = $_SESSION['s_avgAge']
  User age now days = $_SESSION['s_ageNow']
  User base remaining days = $_SESSION['s_baseDays']
  */

  $m = $w = $m_height = $m_weightOrWaste = $bmiOrWth = "";


  //Once the submit button is pressed
  if($_SERVER["REQUEST_METHOD"]=="POST") {


      $m_height = trim(($_POST['measurement2'][0])); // if 1||3, inches, else centimeters
      $m_weightOrWaste = trim(($_POST['measurement4'][0])); // 1-lb, 2-k, 3-in, 4-cm

      //validate our bmi/wth
      //height, whichever one was input
      $input_h = (($m_weightOrWaste == 1 || $m_weightOrWaste == 2 ) ? trim($_POST['measurement1'][0]) : trim($_POST['measurement1'][1]));
      if(empty($input_h)){
        $input_h_error = "Enter your height.";
      } else {
        $h = $input_h;
      }

      //weight or waist - - - -  1 or 2 -> weight, 3 or 4 -> waist
      $input_w = (($m_weightOrWaste == 1 || $m_weightOrWaste == 2 ) ? trim($_POST['measurement3'][0]) : trim($_POST['measurement3'][1]));
      if(empty($input_w)){
        $input_w_error = "Enter weight or waste size.";
      } else {
        $w = $input_w;
      }


      if(empty($input_h_error) && empty($input_w_error)) {



        $user_bmi = $user_wth = "";


        //Convert height & weight(if entered bmi) to metric
        if($m_height == 1 || $m_height == 3) {
          $h = ($h * 2.54);
        }

        //weight to metric kilogram
        if($m_weightOrWaste == 1) {
          $w = ($w * 0.453592);
          $user_bmi = bmi($h, $w);
          $bmiOrWth = 1;
        }

        //Waist to metric centimeter
        if($m_weightOrWaste == 3) {
          $w = ($w * 2.54);
          $user_wth = wth($h, $w);
          $bmiOrWth = 0;
        }

        //Assign sql id to variable to affect user life expectancy
        $bmiOrWth = $bmiOrWth ? $user_bmi : $user_wth;


        if(!empty($_POST['behavior']) && !empty($_POST['exercise']) && !empty($_POST['sleep']) && !empty($_POST['alcohol'])) {
          //Prepare to insert behavior for user
          $sql2 = "INSERT INTO user_behavior (uid, bid)
          VALUES (?,?);";

          //Prep mysqli statement
          $stmt = mysqli_prepare($link, $sql2);
          mysqli_stmt_bind_param($stmt, "ii", $param_userID, $param_behID);

          //Attach params for passing to mysql
          $param_userID = $_SESSION['s_user'];
          $param_behID = $bmiOrWth;
          mysqli_stmt_execute($stmt);

          //Push each set of values to mysql db
          foreach($_POST['behavior'] as $disease){
            $param_behID = $disease;
            mysqli_stmt_execute($stmt);
          }
          //Push each set of values to mysql db
          foreach($_POST['exercise'] as $disease){
            $param_behID = $disease;
            mysqli_stmt_execute($stmt);
          }

          //Push each set of values to mysql db
          foreach($_POST['sleep'] as $disease){
            $param_behID = $disease;
            mysqli_stmt_execute($stmt);
          }

          //Push each set of values to mysql db
          foreach($_POST['alcohol'] as $disease){
            $param_behID = $disease;
            mysqli_stmt_execute($stmt);
          }
          //Close statement
          mysqli_stmt_close($stmt);
        }


        echo "<script type=\"text/javascript\">
              window.location.replace('/result_test.php');
              </script>";
        exit();


      }
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

    <title>The Death Test: Find Out When You Will Die</title>


    <!--Sliders-->
    <link href="css/nouislider.css" rel="stylesheet">

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
            <h1>Basic Test Questions</h1></br>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">


                <!--Get BMI-or-WTH-->
                <h4><span style="display: block;">Enter your </span>height & weight<span style="color: white; text-decoration:underline; display: block;">OR</span> height & waist size<span style = "display: block;">(more accurate)</span></h4>
                <div class="form-group <?php echo (!empty($input_h_error) && !empty($input_w_error) ) ? 'has-error' : ''; ?>">
                  <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                      <a class="nav-link active" id="bmi-tab" data-toggle="tab" href="#bmi" role="tab" aria-controls="bmi" aria-selected="false">Height & Weight</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="wth-tab" data-toggle="tab" href="#wth" role="tab" aria-controls="wth" aria-selected="true">Height & Waist Size</a>
                    </li>
                  </ul>
                  <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="bmi" role="tabpanel" aria-labelledby="bmi-tab">
                      <div class="inside_tab">
                        <div style="display: block;">
                          <div class="inputBlock">
                            <label style="color: #42DCA3; white-space:nowrap; display: block;"for="hw1">Enter your Height</label><input type="number" id="hw1" name="measurement1[]" min=10 max=300 >
                            </br><span class="help-block err-show"><?php echo $input_h_error;?></span>
                          </div>
                          <div class="inputBlock">
                            <input type="radio" id="hw2" name="measurement2[]" value=1  checked="checked" required><label style="white-space:nowrap; display:inline-block"for="hw2">Inches</label>
                          </div>
                          <div class="inputBlock">
                            <input type="radio" id="hw3" name="measurement2[]" value=2 required><label style="white-space:nowrap; display:inline-block"for="hw3">Centimeters</label>
                          </div>
                        </div>
                        <div style="display: block;">
                          <div style="display: block;">
                            <label style="color: #42DCA3; white-space:nowrap; display:block;"for="hw4">Enter your Weight</label><input type="number" id="hw4" name="measurement3[]" min=10 max=800 >
                            </br><span class="help-block err-show"><?php echo $input_w_error;?></span>
                          </div>
                          <div class="inputBlock">
                            <input type="radio" id="hw5" name="measurement4[]" value=1 checked="checked" required><label style="white-space:nowrap; display:inline-block"for="hw5">Pounds</label>
                          </div>
                          <div class="inputBlock">
                            <input type="radio" id="hw6" name="measurement4[]" value=2 required><label style="white-space:nowrap; display:inline-block"for="hw6">Kilos</label>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="tab-pane fade" id="wth" role="tabpanel" aria-labelledby="wth-tab">
                      <div class="inside_tab">
                        <div style="display: block;">
                          <div style="display: block;">
                            <label style="color: #42DCA3; white-space:nowrap; display:block;"for="wth1">Enter your Height</label><input type="number" id="wth1" name="measurement1[]" min=10 max=300 >
                            </br><span class="help-block err-show"><?php echo $input_h_error;?></span>
                          </div>
                          <div class="inputBlock">
                            <input type="radio" id="wth2" name="measurement2[]" value=3  required><label style="white-space:nowrap; display:inline-block"for="wth2">Inches</label>
                          </div>
                          <div class="inputBlock">
                            <input type="radio" id="wth3" name="measurement2[]" value=4 required><label style="white-space:nowrap; display:inline-block"for="wth3">Centimeters</label>
                          </div>
                        </div>
                        <div style="display: block;">
                          <div style="display: block;">
                            <label style="color: #42DCA3; white-space:nowrap; display:block;"for="wth4">Waist size <span style="display: block;"> (measure around you)</span></label><input type="number" id="wth4" name="measurement3[]" min=10 max=150 >
                            </br><span class="help-block err-show"><?php echo $input_w_error;?></span>
                          </div>
                          <div class="inputBlock">
                            <input type="radio" id="wth5" name="measurement4[]" value=3  required><label style="white-space:nowrap; display:inline-block"for="wth5">Inches</label>
                          </div>
                          <div class="inputBlock">
                            <input type="radio" id="wth6" name="measurement4[]" value=4 required><label style="white-space:nowrap; display:inline-block"for="wth6">Centimeters</label>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>



                <!-- Get health slider -->
                <div class="form-group <?php echo (!empty($general_err)) ? 'has-error' : ''; ?>">
                  <h4>How healthy are you on a scale from 1-10?</h4>
                  <div class="content-slider" id="slider-step-value" style="font-size: 50px; margin: 10px;"></div>
                  <div id="slider-step"><input type="hidden" id="healthScale" name="behavior[]" ></div>
                </div>

                <!-- Get risk slider -->
                <div class="form-group <?php echo (!empty($general_err)) ? 'has-error' : ''; ?>">
                  <h4>Do you avoid risk or are you a risk-seeker?</h4>
                  <div class="content-slider" id="slider-risk-value" style="font-size: 25px; margin: 15px;"></div>
                  <div id="slider-risk"><input type="hidden" id="riskScale" name="behavior[]" ></div>
                </div>


                <!-- Cigarette-->
                <div class="form-group <?php echo (!empty($smoke_err)) ? 'has-error' : ''; ?>">
                    <h4>Do you smoke cigarettes (past or present)?</h4>
                    <div class="inputBlock">
                      <input type="radio" id="smokerY1" name="behavior[]" value=17 required><label style="white-space:nowrap; display:inline-block"for="smokerY1">Yes, 10+ cigarettes a day</label>
                    </div>
                    <div class="inputBlock">
                      <input type="radio" id="smokerY2" name="behavior[]" value=18 required><label style="white-space:nowrap; display:inline-block"for="smokerY2">Yes, a few cigarettes a day</label>
                    </div>
                    <div class="inputBlock">
                      <input type="radio" id="smokerO" name="behavior[]" value=19 required><label style="white-space:nowrap; display:inline-block"for="smokerO">Yes, occasionally I smoke</label>
                    </div>
                    <div class="inputBlock">
                      <input type="radio" id="smokerN" name="behavior[]" value=20 required><label style="white-space:nowrap; display:inline-block"for="smokerN">No, I don't smoke </label>
                    </div>
                    <span class="help-block"><?php echo $smoke_err;?></span>
                </div>

                <!--alcohol -->
                <div class="form-group <?php echo (!empty($alcohol_err)) ? 'has-error' : ''; ?>">
                    <h4>How much alcohol do you drink?</h4>
                    <div class="inputBlock">
                      <input type="radio" id="a1" name="alcohol[]" value=25 required><label style="white-space:nowrap; display:inline-block"for="a1">50+ drinks a week</label>
                    </div>
                    <div class="inputBlock">
                      <input type="radio" id="a2" name="alcohol[]" value=26 required><label style="white-space:nowrap; display:inline-block"for="a2">20-40 drinks a week</label>
                    </div>
                    <div class="inputBlock">
                      <input type="radio" id="a3" name="alcohol[]" value=27 required><label style="white-space:nowrap; display:inline-block"for="a3">5-10 drinks a week</label>
                    </div>
                    <div class="inputBlock">
                      <input type="radio" id="a4" name="alcohol[]" value=28 required><label style="white-space:nowrap; display:inline-block"for="a4">I rarely drink</label>
                    </div>
                    <span class="help-block"><?php echo $alcohol_err;?></span>
                </div>

                <!-- Exercise-->
                <div class="form-group <?php echo (!empty($exercise_err)) ? 'has-error' : ''; ?>">
                    <h4>How often do you exercise?</h4>
                    <div class="inputBlock">
                      <input type="radio" id="e1" name="exercise[]" value=21 required><label style="white-space:nowrap; display:inline-block"for="e1">Exercise 5+ days/week</label>
                    </div>
                    <div class="inputBlock">
                      <input type="radio" id="e2" name="exercise[]" value=22 required><label style="white-space:nowrap; display:inline-block"for="e2">Exercise 2-4 days/week</label>
                    </div>
                    <div class="inputBlock">
                      <input type="radio" id="e3" name="exercise[]" value=23 required><label style="white-space:nowrap; display:inline-block"for="e3">Exercise once/week</label>
                    </div>
                    <div class="inputBlock">
                      <input type="radio" id="e4" name="exercise[]" value=24 required><label style="white-space:nowrap; display:inline-block"for="e4">I don't exercise</label>
                    </div>
                    <span class="help-block"><?php echo $exercise_err;?></span>
                </div>

                <!--Sleep-->
                <div class="form-group <?php echo (!empty($sleep_err)) ? 'has-error' : ''; ?>">
                    <h4>On average, how much sleep do you get?</h4>
                    <div class="inputBlock">
                      <input type="radio" id="s1" name="sleep[]" value=33 required><label style="white-space:nowrap; display:inline-block"for="s1">Less than 5 hours a night</label>
                    </div>
                    <div class="inputBlock">
                      <input type="radio" id="s2" name="sleep[]" value=34 required><label style="white-space:nowrap; display:inline-block"for="s2">6 - 9 hours a night</label>
                    </div>
                    <div class="inputBlock">
                      <input type="radio" id="s3" name="sleep[]" value=35 required><label style="white-space:nowrap; display:inline-block"for="s3">10+ hours a night</label>
                    </div>
                    <span class="help-block"><?php echo $sleep_err;?></span>
                </div>

                  <!--GET ACTIVITIES/BEHAVIORS-->
                <input type="submit" class="btn btn-primary" style="background-color: #46ba88; border-color: #fff;" value="Submit">
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

    <!-- Slider js -->
    <script src="js/nouislider.js"></script>
    <!--<script src="js/nouislider.min.js"></script>-->


    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Plugin JavaScript -->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>


    <!-- Custom scripts for this template -->
    <script src="js/grayscale.min.js"></script>

  </body>

</html>
