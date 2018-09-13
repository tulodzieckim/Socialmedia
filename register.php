<?php
require 'config/config.php';
require 'includes/form_handlers/register_handler.php';
require 'includes/form_handlers/login_handler.php';

?>

<!DOCTYPE html>
<html lang="en">

   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta http-equiv="X-UA-Compatible" content="ie=edge">
      <title>Welcome to Socialmedia</title>
      <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
      <link rel="stylesheet" href="./assets/css/register_style.css">
   </head>

   <body>
      <div class="wrapper">
         <div class="container">
            <div class="login-box card mt-5 col-12 col-md-8 col-xl-6 mt-5 mx-auto p-0">
               <div class="login-header card-header bg-primary text-center mb-1`">
                  <h1 class="display-3">SocialMedia</h1>
                  <p>Login or SignUp</p>
               </div>
               <div class="card-body py-0">

                  <div id="login-section" class="col-12 m-auto px-3 pt-0 pb-3 mt-0">
                     &nbsp;
                     <form action="register.php" method="POST">
                        <div class="form-group">
                           <input class="form-control" type="email" name="log_email" placeholder="Email adress" value="<?php if (isset($_SESSION['log_email'])) echo $_SESSION['log_email'] ?>"
                              required>
                        </div>
                        <div class="form-group">
                           <input class="form-control" type="password" name="log_password" placeholder="Password">
                        </div>
                        <?php if (in_array("Email or password was incorrect<br>", $error_array)) echo "Email or password was incorrect<br>"; ?>
                        <div class="form-group">
                           <input class="btn btn-primary" type="submit" name="login_button" value="Login">
                        </div>
                        <a href="#" id="toSignup" class="signup">Don't have an account? Click here to register!</a>
                     </form>
                  </div>

                  <div id="signup-section" class="col-12 m-auto px-3 pb-3 pt-0">
                     &nbsp;
                     <form action="register.php" method="POST">
                        <div class="form-group">
                           <input class="form-control" class="control-form" type="text" name="reg_fname" placeholder="First Name" value="<?php if (isset($_SESSION['reg_fname'])) echo $_SESSION['reg_fname'] ?>"
                              required>
                           <?php if (in_array("Your first name must be between 2 and 25 characters<br>", $error_array)) echo "Your first name must be between 2 and 25 characters<br>" ?>
                        </div>
                        <div class="form-group">
                           <input class="form-control" type="text" name="reg_lname" placeholder="Last Name" value="<?php if (isset($_SESSION['reg_lname'])) echo $_SESSION['reg_lname'] ?>"
                              required>
                           <?php if (in_array("Your last name must be between 2 and 25 characters<br>", $error_array)) echo "Your last name must be between 2 and 25 characters<br>" ?>
                        </div>
                        <div class="form-group">
                           <input class="form-control" type="email" name="reg_email" placeholder="Email" value="<?php if (isset($_SESSION['reg_email'])) echo $_SESSION['reg_email'] ?>"
                              required>
                        </div>
                        <div class="form-group">
                           <input class="form-control" type="email" name="reg_email2" placeholder="Confirm Email" value="<?php if (isset($_SESSION['reg_email2'])) echo $_SESSION['reg_email2'] ?>"
                              required>

                           <?php if (in_array("Email already in use<br>", $error_array)) echo "Email already in use<br>";
                           else if (in_array("Invalid email format<br>", $error_array)) echo "Invalid email format<br>";
                           else if (in_array("Emails don't match<br>", $error_array)) echo "Emails don't match<br>" ?>
                        </div>
                        <div class="form-group">

                           <input class="form-control" type="password" name="reg_password" placeholder="Password" required>
                        </div>
                        <div class="form-group">
                           <input class="form-control" type="password" name="reg_password2" placeholder="Confirm Password" required>

                           <?php if (in_array("Your passwords do not match<br>", $error_array)) echo "Your passwords do not match<br>";
                           else if (in_array("Your passord can only contains english characters or numbers<br>", $error_array)) echo "Your passord can only contains english characters or numbers<br>";
                           else if (in_array("Your password must be between 5 and 30 characters<br>", $error_array)) echo "Your password must be between 5 and 30 characters<br>" ?>
                        </div>

                        <div class="form-group">
                           <input class="btn btn-primary" type="submit" name="register_button" value="Register">
                        </div>

                        <?php if (in_array("<span style='color:#14c800'>You are all set. Go ahead and login</span><br>", $error_array)) echo "<span style='color:#14c800'>You are all set. Go ahead and login</span><br>"; ?>

                        <a href="#" id="toLogin" class="signup">Already have an account? Click here to login!</a>
                     </form>
                  </div>
               </div>
            </div>
         </div>



      <script src="./assets/js/jquery-3.3.1.min.js"></script>
      <script src="./assets/js/popper.min.js"></script>
      <script src="./assets/js/bootstrap.min.js"></script>
      <script src="./assets/js/register.js"></script>

   <?php
   if (isset($_POST['register_button'])) {
      echo '
   <script>

      $(document).ready(function() {
         $("#login-section").hide();
         $("#signup-section").show();
      });

   </script>
      ';
   }

   if (isset($_POST['login_button'])) {
      echo '
   <script>

      $(document).ready(function() {
         $("#login-section").show();
         $("#signup-section").hide();
      });

   </script>
      ';
   }
   ?>


   </body>

</html>