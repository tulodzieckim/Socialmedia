<?php 
include("includes/header.php");

if (isset($_POST['accept_request'])) {
   
   $sended_username = $_POST['user_from_username'];

   $add_friend_query = mysqli_query($con, "UPDATE users SET friend_array=CONCAT(friend_array, '$sended_username,') WHERE username='$userLoggedIn'");
   $add_friend_query = mysqli_query($con, "UPDATE users SET friend_array=CONCAT(friend_array, '$userLoggedIn,') WHERE username='$sended_username'");
   $delete_query = mysqli_query($con, "DELETE FROM friend_requests WHERE user_to='$userLoggedIn' AND user_from='$sended_username'");
   echo "You are now friends!";
   header("Location: requests.php");
}

if (isset($_POST['ignore_request'])) {
   $sended_username = $_POST['user_from_username'];

   $delete_query = mysqli_query($con, "DELETE FROM friend_requests WHERE user_to='$userLoggedIn' AND user_from='$sended_username'");
   echo "Request ignored!";
   header("Location: requests.php");
}
?>

<div class="container">
   <div class="col-sm-12 section content-section">
      <h4 class="text-center display-4" style="font-size: 2rem">Friend Requests</h4>

      <?php

      $query = mysqli_query($con, "SELECT * FROM friend_requests WHERE user_to='$userLoggedIn'");

      if (mysqli_num_rows($query) === 0) {
         echo "<div class='text-center'>You have no friend requests!</div>";
      } else {

         while ($row = mysqli_fetch_array($query)) {
            $user_from_username = $row['user_from'];
            $user_from = new User($con, $user_from_username);
            $user_from_friend_array = $user_from->getFriendArray();
            ?>

                  <div class="friend-request card">
                     <div class="card-body p-2 pb-1">
                        <div class="card-text">
                           <img src="<?= $user_from->getProfilePic() ?>" alt="Profile Pic" class="float-left mr-2 border rounded">
                           <a href="<?= $user_from->getUsername() ?>">
                              <?= $user_from->getFirstAndLastName() ?>
                           </a>sent you a friend request!
                           <div class="d-flex pt-2">
                              <form action="requests.php" method="POST" class="mr-2">
                                 <input type="hidden" name="user_from_username" value="<?= $user_from_username ?>">
                                 <input type="submit" value="Accept" name="accept_request" class="btn btn-success">
                              </form>
                              <form action="requests.php" method="POST">
                                 <input type="hidden" name="user_from_username" value="<?= $user_from_username ?>">
                                 <input type="submit" value="Ignore" name="ignore_request" class="btn btn-danger">
                              </form>
                           </div>
                        </div>
                     </div>
                  </div>
                 &nbsp;


               <?php
            }
         }

         ?>



   </div>
</div>