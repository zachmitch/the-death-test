<?php

  //Start session for keeping user info across pages
  session_start();

  //Include lifespan calculator algorithm that
  include 'lifespan.php';

  // Include config file
  require_once 'config.php';

  //Define variables and initialize as empty
  $bday = $country = $yChrom = "";
  $bday_err = $country_err = $yChrom_err = "";

  //Processing form data
  if($_SERVER["REQUEST_METHOD"]=="POST") {

      //validate birthday
      $input_bday = trim($_POST["bday"]);
      if(empty($input_bday)){
        //error handling
        $bday_err = "Please enter your birthday.";
      } else {
        $bday = $input_bday;
        //Also save bday in session for later comparison
        $_SESSION["s_bday"] = $input_bday;
      }

      //Validate country
      $input_country = trim($_POST["country"]);
      if(empty($input_country)){
        //error handling
        $country_err = "Please choose your country.";
      } else {
        $country = $input_country;
      }

      //Validate yChrom
      $input_yChrom = trim($_POST["yChrom"]);
      if(empty($input_yChrom)){
        //error handling
        $yChrom_err = "Please choose your sex.";
      } else {
        $yChrom = $input_yChrom;
      }

      // Confirm we have a birthday before moving ahead
      if(empty($bday_err)){
        //Prepare sql insert
        $sql = "INSERT INTO user (bday, countryC, yChrom)
        VALUES (?,?,?);";

        //More prep before execution
        if($stmt = mysqli_prepare($link, $sql)){
          //Bind variables to prepared sql statement
          mysqli_stmt_bind_param($stmt, "sis",$param_bday, $param_country, $param_yChrom);

          //Set params
          $param_bday = $bday;
          $param_country = $country;
          $param_yChrom = $yChrom;

          //Attempt to execute now-ready sql statement
          if(mysqli_stmt_execute($stmt)){
            //New user added to db successfully if we're in here
            //Grab the last user.id immediately after add(last submitted in user table)
            //*** This might not work if there are two people doing at same time ? **
            $sql2 = "SELECT MAX(id) FROM user;";
            $result = mysqli_query($link, $sql2);
            $resultArray = mysqli_fetch_array($result);
            $_SESSION['s_user'] = intVal($resultArray[0]);

            //Free result
            mysqli_free_result($result);


            //Get base age, now that we have the user.id for math calculations on next page
            $sql3 = "SELECT $yChrom FROM user
            INNER JOIN country on country.id = user.countryC
            WHERE user.id = ".$_SESSION['s_user'].";";

            //Whitelist data to prevent sql injection
            //https://stackoverflow.com/questions/60174/how-can-i-prevent-sql-injection-in-php
            if($yChrom !== 'oAge' && $yChrom !== 'fAge' && $yChrom !== 'mAge'){
              //Exit page and exit website
              echo "<script type=\"text/javascript\">
                    window.location.replace('http://www.thedeathtest.com');
                    </script>";
              exit();
            }

            //Whitelist data to prevent sql injection
            //https://stackoverflow.com/questions/60174/how-can-i-prevent-sql-injection-in-php
            if((gettype($_SESSION['s_user'])) !== "integer") {
              //Exit page and exit website

              echo "<script type=\"text/javascript\">
                    window.location.replace('http://www.thedeathtest.com');
                    </script>";
              exit();
            }

            //Retrieve query, save avg lifespan for country/gender
            $result = mysqli_query($link, $sql3);
            $resultArray = mysqli_fetch_array($result);
            $_SESSION['s_avgAge'] = $resultArray[0];

            //Convert birthday to string for date_diff function
            $strBday = (string)$_SESSION['s_bday'];
            //$_curAge_bd = strtotime($strBday);

            //Get current time, convert to date object
            $now = new DateTime("now");
            $_curAge_bd = new DateTime($strBday);

            //Find current age in days
            $findAgeDays = $_curAge_bd->diff($now);
            $_SESSION['s_ageNow'] = (int)$findAgeDays->days;

            //Find base remaining days
            $_SESSION['s_baseDays'] = remainingLife($_SESSION['s_ageNow'],$_SESSION['s_avgAge']);


            //Free result
            mysqli_free_result($result);

            //Redirect to next page after successfully doing all the things

            echo "<script type=\"text/javascript\">
                  window.location.replace('/basic_q_test.php');
                  </script>";
            exit();

          } else{
              echo "Something went wrong.";
              echo "<a href='http://www.TheDeathTest.com'>Click here to go back to The Death Test</a>";
          }
        }

        //Close statement
        mysqli_stmt_close($stmt);

    }
      //Close connection
      mysqli_close($link);

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


    <!-- Date picker-->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	  <script src="js/jquery-dropdate.js"></script>
    <script src="js/date.format.js"></script>


  </head>

  <body id="page-top">

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top" id="mainNav">
      <div class="container">
        <a class="navbar-brand js-scroll-trigger" href="index.php">The Death Test</a>

      </div>
    </nav>


    <!-- Get Basic info -->
    <section id="thebasics" class="content-section text-center">
      <div class="container">
        <div class="row">
          <div class="col-lg-8 mx-auto">
            <h1>The Basics</h1></br>

              <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                  <!--GET BIRTHDAY-->
                <div class="form-group <?php echo (!empty($bday_err)) ? 'has-error' : ''; ?>" >
                  <h4>Your birthday: </h4>
                  <input type="text" name="bday" value="" class="dropdate" style="margin: 10px;">
                  <div>
                    <span class="help-block err-show"><?php echo $bday_err;?></span>
                  </div>
                </div>
                  <!--GET COUNTRY-->
                <div class="form-group">
                    <h4>Choose your country of residence: </h4>
                    <select name="country" value="<?php echo $country; ?>">
                      <option value=188>United States</option>
                      <option value=1>Afghanistan</option>
                      <option value=2>Albania</option>
                      <option value=3>Algeria</option>
                      <option value=4>Angola</option>
                      <option value=5>Argentina</option>
                      <option value=6>Armenia</option>
                      <option value=7>Aruba</option>
                      <option value=8>Australia</option>
                      <option value=9>Austria</option>
                      <option value=10>Azerbaijan</option>
                      <option value=11>Bahamas</option>
                      <option value=12>Bahrain</option>
                      <option value=13>Bangladesh</option>
                      <option value=14>Barbados</option>
                      <option value=15>Belarus</option>
                      <option value=16>Belgium</option>
                      <option value=17>Belize</option>
                      <option value=18>Benin</option>
                      <option value=19>Bhutan</option>
                      <option value=20>Bolivia</option>
                      <option value=21>Bosnia and Herzegovina</option>
                      <option value=22>Botswana</option>
                      <option value=23>Brazil</option>
                      <option value=24>Brunei</option>
                      <option value=25>Bulgaria</option>
                      <option value=26>Burkina Faso</option>
                      <option value=27>Burundi</option>
                      <option value=28>Cambodia</option>
                      <option value=29>Cameroon</option>
                      <option value=30>Canada</option>
                      <option value=31>Cape Verde</option>
                      <option value=32>Central African Republic</option>
                      <option value=33>Chad</option>
                      <option value=34>Channel Islands</option>
                      <option value=35>Chile</option>
                      <option value=36>China</option>
                      <option value=37>Colombia</option>
                      <option value=38>Comoros</option>
                      <option value=39>Congo</option>
                      <option value=40>Costa Rica</option>
                      <option value=41>Croatia</option>
                      <option value=42>Cuba</option>
                      <option value=43>Cyprus</option>
                      <option value=44>Czech Republic</option>
                      <option value=45>Côte d'Ivoire</option>
                      <option value=46>Dem. Republic of the Congo</option>
                      <option value=47>Denmark</option>
                      <option value=48>Djibouti</option>
                      <option value=49>Dominican Republic</option>
                      <option value=50>Ecuador</option>
                      <option value=51>Egypt</option>
                      <option value=52>El Salvador</option>
                      <option value=53>Equatorial Guinea</option>
                      <option value=54>Eritrea</option>
                      <option value=55>Estonia</option>
                      <option value=56>Ethiopia</option>
                      <option value=57>Fed. States of Micronesia</option>
                      <option value=58>Fiji</option>
                      <option value=59>Finland</option>
                      <option value=60>France</option>
                      <option value=61>French Guiana</option>
                      <option value=62>French Polynesia</option>
                      <option value=63>Gabon</option>
                      <option value=64>Gambia</option>
                      <option value=65>Georgia</option>
                      <option value=66>Germany</option>
                      <option value=67>Ghana</option>
                      <option value=68>Greece</option>
                      <option value=69>Grenada</option>
                      <option value=70>Guadeloupe</option>
                      <option value=71>Guam</option>
                      <option value=72>Guatemala</option>
                      <option value=73>Guinea</option>
                      <option value=74>Guinea-Bissau</option>
                      <option value=75>Guyana</option>
                      <option value=76>Haiti</option>
                      <option value=77>Honduras</option>
                      <option value=78>Hong Kong</option>
                      <option value=79>Hungary</option>
                      <option value=80>Iceland</option>
                      <option value=81>India</option>
                      <option value=82>Indonesia</option>
                      <option value=83>Iran</option>
                      <option value=84>Iraq</option>
                      <option value=85>Ireland</option>
                      <option value=86>Israel</option>
                      <option value=87>Italy</option>
                      <option value=88>Jamaica</option>
                      <option value=89>Japan</option>
                      <option value=90>Jordan</option>
                      <option value=91>Kazakhstan</option>
                      <option value=92>Kenya</option>
                      <option value=93>Kuwait</option>
                      <option value=94>Kyrgyzstan</option>
                      <option value=95>Laos</option>
                      <option value=96>Latvia</option>
                      <option value=97>Lebanon</option>
                      <option value=98>Lesotho</option>
                      <option value=99>Liberia</option>
                      <option value=100>Libya</option>
                      <option value=101>Lithuania</option>
                      <option value=102>Luxembourg</option>
                      <option value=103>Macau</option>
                      <option value=104>Macedonia</option>
                      <option value=105>Madagascar</option>
                      <option value=106>Malawi</option>
                      <option value=107>Malaysia</option>
                      <option value=108>Maldives</option>
                      <option value=109>Mali</option>
                      <option value=110>Malta</option>
                      <option value=111>Martinique</option>
                      <option value=112>Mauritania</option>
                      <option value=113>Mauritius</option>
                      <option value=114>Mayotte</option>
                      <option value=115>Mexico</option>
                      <option value=116>Moldova</option>
                      <option value=117>Mongolia</option>
                      <option value=118>Montenegro</option>
                      <option value=119>Morocco</option>
                      <option value=120>Mozambique</option>
                      <option value=121>Myanmar</option>
                      <option value=122>Namibia</option>
                      <option value=123>Nepal</option>
                      <option value=124>Netherlands</option>
                      <option value=125>Netherlands Antilles</option>
                      <option value=126>New Caledonia</option>
                      <option value=127>New Zealand</option>
                      <option value=128>Nicaragua</option>
                      <option value=129>Niger</option>
                      <option value=130>Nigeria</option>
                      <option value=131>North Korea</option>
                      <option value=132>Norway</option>
                      <option value=133>Oman</option>
                      <option value=134>Pakistan</option>
                      <option value=135>Palestine</option>
                      <option value=136>Panama</option>
                      <option value=137>Papua New Guinea</option>
                      <option value=138>Paraguay</option>
                      <option value=139>Peru</option>
                      <option value=140>Philippines</option>
                      <option value=141>Poland</option>
                      <option value=142>Portugal</option>
                      <option value=143>Puerto Rico</option>
                      <option value=144>Qatar</option>
                      <option value=145>Romania</option>
                      <option value=146>Russian Federation</option>
                      <option value=147>Rwanda</option>
                      <option value=148>Réunion</option>
                      <option value=149>St Lucia</option>
                      <option value=150>St Vincent & Grenadines</option>
                      <option value=151>Samoa</option>
                      <option value=152>Saudi Arabia</option>
                      <option value=153>Senegal</option>
                      <option value=154>Serbia</option>
                      <option value=155>Sierra Leone</option>
                      <option value=156>Singapore</option>
                      <option value=157>Slovakia</option>
                      <option value=158>Slovenia</option>
                      <option value=159>Solomon Islands</option>
                      <option value=160>Somalia</option>
                      <option value=161>South Africa</option>
                      <option value=162>South Korea</option>
                      <option value=163>Spain</option>
                      <option value=164>Sri Lanka</option>
                      <option value=165>Sudan</option>
                      <option value=166>Suriname</option>
                      <option value=167>Swaziland</option>
                      <option value=168>Sweden</option>
                      <option value=169>Switzerland</option>
                      <option value=170>Syrian Arab Republic</option>
                      <option value=171>São Tomé and Príncipe</option>
                      <option value=172>Taiwan</option>
                      <option value=173>Tajikistan</option>
                      <option value=174>Tanzania</option>
                      <option value=175>Thailand</option>
                      <option value=176>Timor-Leste</option>
                      <option value=177>Togo</option>
                      <option value=178>Tonga</option>
                      <option value=179>Trinidad and Tobago</option>
                      <option value=180>Tunisia</option>
                      <option value=181>Turkey</option>
                      <option value=182>Turkmenistan</option>
                      <option value=183>U.S. Virgin Islands</option>
                      <option value=184>Uganda</option>
                      <option value=185>Ukraine</option>
                      <option value=186>United Arab Emirates</option>
                      <option value=187>United Kingdom</option>
                      <option value=189>Uruguay</option>
                      <option value=190>Uzbekistan</option>
                      <option value=191>Vanuatu</option>
                      <option value=192>Venezuela</option>
                      <option value=193>Viet Nam</option>
                      <option value=194>Western Sahara</option>
                      <option value=195>Yemen</option>
                      <option value=196>Zambia</option>
                      <option value=197>Zimbabwe</option>
                    </select>
                </div>
                  <!--ADD SEX-->
                <div class="form-group">
                    <h4>Do you have a Y-Chromosome? (Male/Female)</h4>
                    <select name="yChrom" value="<?php echo $yChrom; ?>">
                      <option value=fAge>Female</option>
                      <option value=mAge>Male</option>
                    </select>
                </div>

                <input type="submit" class="btn btn-primary" style="background-color: #46ba88; border-color: #fff;"  value="Submit">
              </form>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->

    <script>
          $(document).ready(function(){

              $('.dropdate').dropdate({
                dateFormat:'mm/dd/yyyy'
              });
          });
    </script>

    <footer>
      <div class="container text-center">
        <p>Copyright &copy; TheDeathTest.com 2018</p>
      </div>
    </footer>

    <!-- Bootstrap core JavaScript -->
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Plugin JavaScript -->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>


    <!-- Custom scripts for this template -->
    <script src="js/grayscale.min.js"></script>

  </body>

</html>
