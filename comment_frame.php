<!DOCTYPE html>
<html lang="en" style="height: 100%">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta http-equiv="X-UA-Compatible" content="ie=edge">
   <title>Document</title>

   <!-- CSS -->
   <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
   <link rel="stylesheet" href="./assets/css/style.css">
   <link rel="stylesheet" href="./assets/css/font-awesome-animation.min.css">
   <!-- JS -->
   <script src="./assets/js/jquery-3.3.1.min.js"></script>
   <script src="./assets/js/popper.min.js"></script>
   <script src="./assets/js/bootstrap.min.js"></script>
</head>

<body style="height: 100px">
   <?php
   require 'config/config.php';
   include("includes/classes/User.php");
   include("includes/classes/Post.php");
   include("includes/classes/Notification.php");

   if (isset($_SESSION['username'])) {
      $userLoggedIn = $_SESSION['username'];
      $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$userLoggedIn'");
      $user = mysqli_fetch_array($user_details_query);
   } else {
      header("Location: register.php");
   }
   ?>

   <script>
      function toggle() {
         const $element = $('#comment-section').toggle();
      }
   </script>

   <?php
   // Get id of post
   // id is sent via request - look 'str' in Post.php
   if (isset($_GET['post_id'])) {
      $post_id = $_GET['post_id'];
   }

   $user_query = mysqli_query($con, "SELECT added_by, user_to FROM posts WHERE id='$post_id'");
   $row = mysqli_fetch_array($user_query);

   $posted_to = $row['added_by'];
   $user_to = $row['user_to'];

   if (isset($_POST['postComment' . $post_id])) {
      $post_body = $_POST['post_body'];
      $post_body = mysqli_real_escape_string($con, $post_body);
      $date_time_now = date("Y-m-d H:i:s");
      $insert_post = mysqli_query($con, "INSERT INTO comments VALUES (null, '$post_body', '$userLoggedIn', '$posted_to', '$date_time_now', 'no', '$post_id')");

      // Insert notification
      if($posted_to !== $userLoggedIn) {
         $notification = new Notification($con, $userLoggedIn);
         $notification->insertNotification($post_id, $posted_to, "comment");
      }
      if ($user_to !== 'none' && $user_to !== $userLoggedIn) {
         $notification = new Notification($con, $userLoggedIn);
         $notification->insertNotification($post_id, $user_to, "profile_comment");
      }

      $get_commenters = mysqli_query($con, "SELECT * FROM comments WHERE post_id='$post_id'");
      $notified_users = array();

      while($row = mysqli_fetch_array($get_commenters)) {
         if($row['posted_by'] !== $posted_to && 
            $row['posted_by'] !== $user_to &&
            $row['posted_by'] !== $userLoggedIn &&
            !in_array($row['posted_by'], $notified_users)) {

               $notification = new Notification($con, $userLoggedIn);
               $notification->insertNotification($post_id, $row['posted_by'], "comment_non_owner");
               array_push($notified_users, $row['posted_by']);
         }
      }


      echo "<p>Comment Posted!</p>";
   }





   ?>
   <div class="container-fluid mt-2">
      <form action="comment_frame.php?post_id=<?= $post_id ?>" method="POST" id="comment-form" name="postComment<?= $post_id ?>" class="form-group d-flex">
         <textarea name="post_body" id="" rows="2" class="form-control mr-1"></textarea>
         <input type="submit" value="Comment" name="postComment<?= $post_id ?>" class="btn btn-primary p-1">
   </form>
   </div>


   <!-- Load comments  -->
   <?php

   $get_comments = mysqli_query($con, "SELECT * FROM comments WHERE post_id='$post_id' ORDER BY id ASC");
   $numberOfPosts = mysqli_num_rows($get_comments);

   if ($numberOfPosts != 0) {
      while ($comment = mysqli_fetch_array($get_comments)) {
         $comment_body = $comment['post_body'];
         $posted_to = $comment['posted_to'];
         $posted_by = $comment['posted_by'];
         $date_added = $comment['date_added'];
         $removed = $comment['removed'];

         // Timeframe
         $date_time_now = date("Y-m-d H:i:s");
         $start_date = new DateTime($date_added); // Time of posts 
         $end_date = new DateTime($date_time_now); // Current time
         $interval = $start_date->diff($end_date); // Difference between dates
         if ($interval->y >= 1) {
            if ($interval->y === 1) {
               $time_message = $interval->y . "year ago";
            } else {
               $time_message = $interval->y . "years ago";
            }
         } else if ($interval->m >= 1) {
            if ($interval->d === 0) {
               $days = " ago";
            } else if ($interval->d === 1) {
               $days = $interval->d . "day ago";
            } else {
               $days = $interval->d . "days ago";
            }

            if ($interval->d === 1) {
               $time_message = $interval->d . " month " . $days;
            } else {
               $time_message = $interval->d . " months " . $days;
            }
         } else if ($interval->d >= 1) {
            if ($interval->d === 1) {
               $time_message = "Yesterday";
            } else {
               $time_message = $interval->d . "days ago";
            }
         } else if ($interval->h >= 1) {
            if ($interval->h === 1) {
               $time_message = "one hour ago";
            } else {
               $time_message = $interval->h . " hours ago";
            }
         } else if ($interval->i >= 1) {
            if ($interval->i === 1) {
               $time_message = "one minute ago";
            } else {
               $time_message = $interval->i . " minutes ago";
            }
         } else {
            if ($interval->s <= 30) {
               $time_message = "Just now";
            } else {
               $time_message = $interval->s . " minutes ago";
            }
         }

         $user_obj = new User($con, $posted_by);

         ?>

         <div class="container-fluid">
            <div class="comment-section">     
               <a href="<?= $posted_by ?>" target="_parent" class="float-left mr-1">
                  <img src="<?= $user_obj->getProfilePic() ?>" alt="Profile Pic" title="<?= $posted_by ?>" height="30">
               </a>
               <div>
                  <div>
                     <a href="<?= $posted_by ?>" target="_parent"><b><?= $user_obj->getFirstAndLastName() ?></b></a> <span class="text-muted"> <?= $time_message ?></span>
                  </div>
                  <hr class="p-0 m-0">
                  <div>
                     <?= $comment_body ?>
                  </div>         
               </div>
               <hr>
            </div>
         </div>

         <?php
   
      }
   } else {
      echo "<div class='comment-section w-100 text-center'>No comments to show!</div>";
   }


   ?>


</body>

</html>