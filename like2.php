<?php
// TODO
// <!-- CHANGE like.php to use ajax in Post.php -->

   require 'config/config.php';
   include("includes/classes/User.php");
   include("includes/classes/Post.php");

   if (isset($_SESSION['username'])) {
      $userLoggedIn = $_SESSION['username'];
      $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$userLoggedIn'");
      $user = mysqli_fetch_array($user_details_query);
   } else {
      header("Location: register.php");
   }

   // Get id of post
   // id is sent via request - look 'str' in Post.php
   if (isset($_GET['post_id'])) {
      $post_id = $_GET['post_id'];
   }

   $get_likes_query = mysqli_query($con, "SELECT likes, added_by FROM posts WHERE id='$post_id'");
   $row = mysqli_fetch_array($get_likes_query);
   $total_likes = $row['likes'];
   $user_liked = $row['added_by'];

   $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$user_liked'");
   $row = mysqli_fetch_array($user_details_query);
   $total_user_likes = $row['num_likes'];

   // Like button
   if (isset($_POST['like_button'])) {
      $total_likes++;
      $query = mysqli_query($con, "UPDATE posts SET likes = '$total_likes' WHERE id='$post_id'");
      $total_user_likes++;
      $user_likes_query = mysqli_query($con, "UPDATE users SET num_likes = '$total_user_likes' WHERE username='$user_liked'");
      $insert_user = mysqli_query($con, "INSERT INTO likes VALUES (null, '$userLoggedIn', '$post_id')");

      // Insert notification
   }
   // Unlike button
   if (isset($_POST['unlike_button'])) {
      $total_likes--;
      $query = mysqli_query($con, "UPDATE posts SET likes = '$total_likes' WHERE id='$post_id'");
      $total_user_likes--;
      $user_likes_query = mysqli_query($con, "UPDATE users SET num_likes = '$total_user_likes' WHERE username='$user_liked'");
      $insert_user = mysqli_query($con, "DELETE FROM likes WHERE user='$userLoggedIn' AND post_id='$post_id'");

      // Insert notification
   }

   // Check for previous likes
   $check_query = mysqli_query($con, "SELECT * FROM likes WHERE user='$userLoggedIn' AND post_id='$post_id'");
   $num_rows = mysqli_num_rows($check_query);

   
   // if ($num_rows > 0) {
   //    $output = '<form action="like.php?post_id=' . $post_id . '" method="POST">
   //             <input type="submit" class="comment-like" name="unlike_button" value="Unlike">
   //             <div class="like-value">
   //                ' .  $total_likes . ' Likes
   //             </div>
   //          </form>';
   // } else {
   //    $output = '<form action="like.php?post_id=' . $post_id . '" method="POST">
   //             <input type="submit" class="comment-like" name="like_button" value="Like">
   //             <div class="like-value">
   //                ' . $total_likes . ' Likes
   //             </div>
   //          </form>';
   // }
   $myObj = new \stdClass();
   if ($num_rows > 0) {
      $myObj->name = "Unlike";
      $myObj->number = $total_likes;
   } else {
      $myObj->name = "Like";
      $myObj->number = $total_likes;
   }

   echo json_encode($myObj);

   ?>