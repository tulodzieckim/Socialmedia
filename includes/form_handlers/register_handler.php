<?php
// Declaring variables
$fname = ""; //Firstname
$lname = ""; // Lastname
$em = ""; //email
$em2 = ""; // confirmed email
$password = ""; //Password
$password2 = ""; // confirmed password
$date = ""; // sign up date
$error_array = array(); //holds error messages

if (isset($_POST['register_button'])) {

      // Registration form values

      // First name
   $fname = strip_tags($_POST['reg_fname']); // Remove html tags
   $fname = str_replace(' ', '', $fname); // remove spaces
   $fname = ucfirst(strtolower($fname)); // Uppercase first letter
   $_SESSION['reg_fname'] = $fname; // Stores first name into session variavble
   
      // Last name
   $lname = strip_tags($_POST['reg_lname']); // Remove html tags
   $lname = str_replace(' ', '', $lname); // remove spaces
   $lname = ucfirst(strtolower($lname)); // Uppercase first letter
   $_SESSION['reg_lname'] = $lname; // Stores last name into session variavble
      
      // Email
   $em = strip_tags($_POST['reg_email']); // Remove html tags
   $em = str_replace(' ', '', $em); // remove spaces
   $em = ucfirst(strtolower($em)); // Uppercase first letter
   $_SESSION['reg_email'] = $em; // Stores email into session variavble
      
      // Email 2
   $em2 = strip_tags($_POST['reg_email2']); // Remove html tags
   $em2 = str_replace(' ', '', $em2); // remove spaces
   $em2 = ucfirst(strtolower($em2)); // Uppercase first letter
   $_SESSION['reg_email2'] = $em2; // Stores email2 into session variavble
       
      // Password
   $password = strip_tags($_POST['reg_password']); // Remove html tags

      // Password 2
   $password2 = strip_tags($_POST['reg_password2']); // Remove html tags

   $date = date("Y-m-d"); // Current date

   if ($em === $em2) {
      if (filter_var($em, FILTER_VALIDATE_EMAIL)) {
         $em = filter_var($em, FILTER_VALIDATE_EMAIL);
         // Check if email already exists
         $e_check = mysqli_query($con, "SELECT email FROM users WHERE email='$em'");
         // Count the number o rows returned
         $num_rows = mysqli_num_rows($e_check);
         if ($num_rows > 0) {
            array_push($error_array, "Email already in use<br>");
         }
      } else {
         array_push($error_array, "Invalid email format<br>");
      }
   } else {
      array_push($error_array, "Emails don't match<br>");
   }

   if (strlen($fname) > 25 || strlen($fname) < 2) {
      array_push($error_array, "Your first name must be between 2 and 25 characters<br>");
   }

   if (strlen($lname) > 25 || strlen($lname) < 2) {
      array_push($error_array, "Your last name must be between 2 and 25 characters<br>");
   }

   if ($password !== $password2) {
      array_push($error_array, "Your passwords do not match<br>");
   } else {
      if (preg_match('/[^A-Za-z0-9]/', $password)) {
         array_push($error_array, "Your passord can only contains english characters or numbers<br>");
      }
   }

   if ((strlen($password) > 30) || (strlen($password) < 5)) {
      array_push($error_array, "Your password must be between 5 and 30 characters<br>");
   }

   if (empty($error_array)) {
      $password = md5($password); // Encrypt password before sending to he database

      // Generate username by concatenating first name and last name
      $username = strtolower($fname . "_" . $lname);
      $check_username_query = mysqli_query($con, "SELECT username FROM users WHERE username='$username'");

      $i = 0;
      $modified_username = "";
      // if usrname exists and number to username
      while (mysqli_num_rows($check_username_query) !== 0) {
         $i++;
         $modified_username = $username . "_" . $i;
         $check_username_query = mysqli_query($con, "SELECT username FROM users WHERE username='$modified_username'");
      }  
     
      if($modified_username != "") {
         $username = $modified_username;
      }

      // Profile picture assignment
      $rand = rand(1, 2);

      if ($rand === 1) {
         $profile_pic = "assets/images/profile_pics/defaults/head_deep_blue.png";
      } else if ($rand === 2) {
         $profile_pic = "assets/images/profile_pics/defaults/head_emerald.png";
      }

      $query = mysqli_query($con, "INSERT INTO users VALUES (NULL, '$fname', '$lname', '$username', '$em', '$password', '$date', '$profile_pic', '0', '0', 'no', ',')");
      array_push($error_array, "<span style='color:#14c800'>You are all set. Go ahead and login</span><br>");

      // Clear session variable
      $_SESSION['reg_fname'] = '';
      $_SESSION['reg_lname'] = '';
      $_SESSION['reg_email'] = '';
      $_SESSION['reg_email2'] = '';
   }



}

?>