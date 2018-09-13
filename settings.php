<?php
include("includes/header.php");
include("includes/form_handlers/settings_handler.php");

?>

<!-- OPEN TAGS in header.php -->
<div class="container content-section">
   <div class="row">
      <!-- SETTINGS SECTION -->
      <div class="col-sm-12 ml-auto">
         <div class="bg-light section container justify-content-center">
            <h3 class="text-center py-3">Account Settings</h3>
            <!-- image -->
            <div class="d-flex flex-column">
               <img src="<?=$user['profile_pic']?>" alt="Profile Pic" class="d-block m-auto border">
               <a href="upload.php" class="text-center">Upload new profile picture</a>
            </div>
            <!-- FORMS -->
            <div>
               <p class="h5 pt-3 pb-2 text-center" data-toggle="collapse" data-target="#change-details" style="cursor: pointer;">Change personal data</p>
               <div id="change-details" class="collapse show">

                  <?php
                     $user_data_query = mysqli_query($con, "SELECT first_name, last_name, email FROM users WHERE username='$userLoggedIn'");
                     $row = mysqli_fetch_array($user_data_query);

                     $first_name = $row['first_name'];
                     $last_name = $row['last_name'];
                     $email = $row['email'];
                  ?>

                  <form action="settings.php" method="POST">
                     <div class="form-group row">
                        <label for="first-name" class="col-sm-3 col-form-label">First Name: </label>
                        <input type="test" class="form-control col-sm-8" id="first-name" name="first_name" value="<?= $first_name ?>">
                     </div>
                     <div class="form-group row">
                        <label for="last-name" class="col-sm-3 col-form-label">Last Name: </label>
                        <input type="test" class="form-control col-sm-8" id="last-name" name="last_name" value="<?= $last_name ?>">
                     </div>
                     <div class="form-group row">
                        <label for="email" class="col-sm-3 col-form-label">Email: </label>
                        <input type="test" class="form-control col-sm-8" id="email" name="email" value="<?= $email ?>">
                     </div>
                     <div><?=$message ?></div>
                     <div class="row">
                        <div class="col-8 offset-3 pr-0">
                           <input type="submit" value="Update Details" class="btn btn-primary d-block ml-auto" name="update_details" id="update-details">        
                        </div>
                     </div>
                  </form>
               </div>

               <p class="h5 pt-3 pb-2 text-center" data-toggle="collapse" data-target="#change-password" style="cursor: pointer;">Change Password</p>
               <div id="change-password" class="collapse show">
                  <form action="settings.php" method="POST">
                     <div class="form-group row">
                        <label for="old-password" class="col-sm-3 col-form-label">Old Password: </label>
                        <input type="password" class="form-control col-sm-8" id="old-password" name="old_password">
                     </div>
                     <div class="form-group row">
                        <label for="new-password-1" class="col-sm-3 col-form-label">Confirm Password: </label>
                        <input type="password" class="form-control col-sm-8" id="new-password-1" name="new_password_1">
                     </div>
                     <div class="form-group row">
                        <label for="new-password-2" class="col-sm-3 col-form-label">Confirm Password: </label>
                        <input type="password" class="form-control col-sm-8" id="new-password-2" name="new_password_2">
                     </div>
                     <div><?=$password_message ?></div>
                     <div class="row">
                        <div class="col-8 offset-3 pr-0">
                           <input type="submit" value="Update Password" class="btn btn-primary d-block ml-auto" name="update_password" id="update-password"> 
                        </div>
                     </div>     
                  </form>
               </div>

               <p class="h5 pt-3 pb-2 text-center" data-toggle="collapse" data-target="#close-account" style="cursor: pointer;">Close Account</p>
               <div id="close-account" class="collapse show">
                  <form action="settings.php" method="POST" class="form-group">
                     <input type="submit" value="Close Account" class="btn btn-danger btn-block" name="close_account" id="close-account-btn">        
                  </form>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>