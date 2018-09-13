<?php
include("includes/header.php");


if(isset($_POST['cancel'])) {
   header("Location: settings.php");
}

if(isset($_POST['close_account'])) {
   $close_query = mysqli_query($con, "UPDATE users SET user_closed='yes' WHERE username='$userLoggedIn'");
   session_destroy();
   header("Location: register.php");
}
?>


<!-- OPEN TAGS in header.php -->
<div class="container content-section">
   <div class="row">
      <!-- CLOSE ACCOUNT SECTION -->
      <div class="col-sm-12 ml-auto">
         <div class="bg-light container section justify-content-center">
            <h3 class="text-center py-3">Close Account</h3>
            <p class="lead">
               Are you sure to close your account?<br>
               Closing account will hide your profile and all activities from other users.<br>
               You can re-open your account by logging in.
            </p>
            <div>
               <form action="close_account.php" method="POST">
                  <input type="submit" name="close_account" id="close-account" class="btn btn-danger btn-block" value="Yes, close my account">
                  <input type="submit" name="cancel" id="cancel" class="btn btn-success btn-block mb-2" value="No, I changed my mind">
                  &nbsp;
               </form>
            </div>
         </div>
      </div>
   </div>
</div>