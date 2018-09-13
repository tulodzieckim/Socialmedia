<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta http-equiv="X-UA-Compatible" content="ie=edge">
   <title>Document</title>

   <!-- CSS -->
   <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
   <link rel="stylesheet" href="./assets/css/style.css">      
   <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ"
      crossorigin="anonymous">
   <link rel="stylesheet" href="./assets/css/font-awesome-animation.min.css">
</head>
<body class="iframe-body">

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
      if($user_liked !== $userLoggedIn) {
         $notification = new Notification($con, $userLoggedIn);
         $notification->insertNotification($post_id, $user_liked, "like");
      }
   }
   // Unlike button
   if (isset($_POST['unlike_button'])) {
      $total_likes--;
      $query = mysqli_query($con, "UPDATE posts SET likes = '$total_likes' WHERE id='$post_id'");
      $total_user_likes--;
      $user_likes_query = mysqli_query($con, "UPDATE users SET num_likes = '$total_user_likes' WHERE username='$user_liked'");
      $insert_user = mysqli_query($con, "DELETE FROM likes WHERE user='$userLoggedIn' AND post_id='$post_id'");

   }

   // Check for previous likes
   $check_query = mysqli_query($con, "SELECT * FROM likes WHERE user='$userLoggedIn' AND post_id='$post_id'");
   $num_rows = mysqli_num_rows($check_query);


   if ($num_rows > 0) {
      $output = '<form action="like.php?post_id=' . $post_id . '" method="POST">
               <div class="d-flex comment-like-container">
               <input type="submit" class="comment-like-button" name="unlike_button" value="Unlike">
               <div class="like-value">
                  <b>' . $total_likes . '</b> Likes
               </div>
               </div>
            </form>';
   } else {
      $output = '<form action="like.php?post_id=' . $post_id . '" method="POST">
               <div class="d-flex comment-like-container">
                  <input type="submit" class="comment-like-button" name="like_button" value="Like">
                  <div class="like-value">
                     ' . $total_likes . ' Likes
                  </div>
               </div>

            </form>';
   }
   echo $output;

   ?>






   <!-- JS -->
   <script src="./assets/js/jquery-3.3.1.min.js"></script>
   <script src="./assets/js/popper.min.js"></script>
   <script src="./assets/js/bootstrap.min.js"></script>
</body>
</html>