<?php 
include("includes/header.php");

if (isset($_GET['profile_username'])) {
   $profile_username = $_GET['profile_username'];
   $profile_user = new User($con, $profile_username);
   $loggedIn_user = new User($con, $userLoggedIn);
   $message_obj = new Message($con, $userLoggedIn);

   // to get data - no function in class User.php to get every needed data
   $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$profile_username'");
   $user = mysqli_fetch_array($user_details_query);

   $num_friends = $profile_user->howManyFriends();

   if ($profile_user->isClosed()) {
      header("Location: user_closed.php");
   }

   if (isset($_POST['remove_friend'])) {
      $loggedIn_user->removeFriend($profile_username);
      header("Location: $profile_username");
   }

   if (isset($_POST['add_friend'])) {
      $loggedIn_user->sendRequest($profile_username);
      header("Location: $profile_username");
   }

   if (isset($_POST['respond_request'])) {
      header("Location: requests.php");
   }

   if (isset($_POST['post_message'])) {
      if(isset($_POST['message_body'])) {
         $body = mysqli_real_escape_string($con, $_POST['message_body']);
         $date = date("Y-m-d H:i:s");
         $message_obj->sendMessageFromProfilePage($profile_username, $body, $date);
      }
   }

   if (isset($_GET['tab'])) {
      if ($_GET['tab'] === 'm') {
          $messages_area_id = "#profile-messages-area";
         ?>
         <script>
            $(function() {
               $('#nav-tab a[href="#profile-messages-area"]').tab('show');
                
                  // Change url back to /username eg. /albert_krecik from /profile.php?profile_username=albert_krecik&tab=m
                  history.pushState(null, null, '<?= $profile_username ?>');

                  // scroll down messages window after reload page
                  const div = document.getElementById("loaded-messages");  
                  setTimeout(() => {
                     div.scrollTop = div.scrollHeight;
                  }, 500);

            });

         </script>
         <?php
      }
   }
  
}
?>    


<div id="profile-wrapper">

   <div id="profile-sidebar" class="pt-0 mt-0">
      <div id="profile-sidebar-content" class="mt-3 mx-0 d-flex flex-column">
         <img src="<?= $user['profile_pic'] ?>" alt="Profile Pic" class="rounded rounded-circle mb-2 img-fluid align-self-center">
         <div class="profile-info align-self-center">
            <ul class="list-group-flush text-center px-3">
               <li class="list-group-item bg-primary">
                  Posts:
                  <?= $user['num_posts'] ?>
               </li>
               <li class="list-group-item bg-primary">
                  Likes:
                  <?= $user['num_likes'] ?>
               </li>
               <li class="list-group-item bg-primary">
                  Friends:
                  <?= $num_friends ?>
               </li>
            </ul>
            <form action="<?= $profile_username ?>" method="POST">
               <?php

               if ($profile_username !== $userLoggedIn) {
                  if ($loggedIn_user->isFriend($profile_username)) {
                     echo '<input type="submit" name="remove_friend" class="btn btn-danger mx-3" value="Remove friend">';
                  } else if ($loggedIn_user->didReceiveRequest($profile_username)) {
                     echo '<input type="submit" name="respond_request" class="btn btn-info mx-3" value="Respond to request">';
                  } else if ($loggedIn_user->didSendRequest($profile_username)) {
                     echo '<input type="submit" name="" class="btn btn-secondary mx-3" value="Request Sent">';
                  } else {
                     echo '<input type="submit" name="add_friend" class="btn btn-success mx-3" value="Add Friend">';
                  }
               }
               ?>
            </form>

            <input type="submit" class="btn btn-deepblue mt-1 mx-auto d-block" data-toggle="modal" data-target="#post-form" value="Post something">
            
            <?php
            if ($userLoggedIn !== $profile_username) {
               echo '<div class="mt-3 mx-auto text-center">' . $loggedIn_user->getMutualFriends($profile_username) . ' Mutual Friends</div>';
            }
            ?>

         </div>
         <button id="collapse-btn"><i class="fas fa-bars fa-2x"></i></span></button>
      </div>
   </div>
   <!-- MAIN COLUMN -->
   <div id="profile-content" class="container">
      <div class="offset-0 col-12 offset-md-3 col-md-9 offset-xl-0 col-xl-12 px-2">
         <!-- TABS -->
         <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
               <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#profile-post-area" role="tab" aria-controls="nav-home" aria-selected="true">Posts</a>
               <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile-messages-area" role="tab" aria-controls="nav-profile" aria-selected="false">Messages</a>
            </div>
         </nav>
         <div class="section content-sction tabs-section p-2">
            <!-- TABS  -->
            <div class="tab-content">
               <!-- TAB 1 -->
               <div class="tab-pane fade show active" id="profile-post-area" role="tabpanel">
                  <div class="posts-area"></div>
                  <div class="w-100 d-flex justify-content-center p-0" >
                     <img id="loading" src="./assets/images/icons/Loading_icon.gif" width="120" height="80" alt="Loading_icon" class="p-0">
                  </div>
               </div>
               <!-- TAB 2 -->
               <div class="tab-pane fade" id="profile-messages-area" role="tabpanel">               
                  <h4>You and <a href="$profile_username"></a> <?= $profile_user->getFirstAndLastName() ?></a></h4>
                  <hr>
                  <div id="loaded-messages">
                  <?= $message_obj->getMessages($profile_username) ?>
                  </div>
                  <div class="message-post">
                     <form action='' method='POST' class='d-flex'>
                        <textarea name='message_body' id='message-textarea' placeholder='Write your message!' class='form-control'></textarea>
                        <input type='submit' name='post_message' class='info btn btn-primary ml-1' id='message-submit' value='Send'>               
                     </form>        
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<!-- Modal -->
<div class="modal fade" id="post-form" tabindex="-1" role="dialog" aria-labelledby="postModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="postModalLabel">Post something</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p>This will apear on user's profile page and also their newsfeed for your friends to see!</p>

            <form action="" class="profile-post" method="POST">
               <div class="form-group">
                  <textarea name="post_body" id="" cols="30" rows="5" class="form-control"></textarea>
                  <input type="hidden" name="user_from" value="<?= $userLoggedIn ?>">
                  <input type="hidden" name="user_to" value="<?= $profile_username ?>">
               </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" id="submitProfilePost" name="post_button">Post</button>
          </div>
        </div>
      </div>
    </div>


<script>
 // const userLoggedIn = '<?= $userLoggedIn ?>'; // already declared in header script
 const profileUsername = '<?= $profile_username ?>'

// $(document).ready(function () {
   $('#loading').show();

   // // scroll down messages window after reload page
   // const div = $('#loaded-messages');  
   // if(Boolean(div[0]))
   //    div[0].scrollTop = div[0].scrollHeight;
// });

// original ajax request for loading first posts
$.ajax({
   url: 'includes/handlers/ajax_load_profile_posts.php',
   type: 'POST',
   data: 'page=1&userLoggedIn=' + userLoggedIn + '&profileUsername=' + profileUsername,
   cache: false
}).done(function (response) {
   $('#loading').hide();
   $('.posts-area').html(response);
});

let scrollTimeout;

$(window).scroll(function () {

   if (scrollTimeout) {
      clearTimeout(scrollTimeout);
   }
   scrollTimeout = setTimeout(function() {
      const height = $('.posts-area').height();
      const scrollTop = $(this).scrollTop();
      const page = $('.posts-area').find('.nextPage').val();
      const noMorePosts = $('.posts-area').find('.noMorePosts').val();

      if ((window.innerHeight + window.pageYOffset) >= document.body.offsetHeight && noMorePosts === 'false') {
         $('#loading').show();

         const ajaxReq = $.ajax({
            url: 'includes/handlers/ajax_load_profile_posts.php',
            type: 'POST',
            // data: 'page=' + page + '&userLoggedIn=' + userLoggedIn + '&profileUsername=' + profileUsername,
            data: {
               page: page,
               userLoggedIn: userLoggedIn,
               profileUsername: profileUsername
            },
            cache: false
         }).done(function (response) {
            $('.posts-area').find('.nextPage').remove();
            $('.posts-area').find('.noMorePosts').remove();

            $('#loading').hide();
            $('.posts-area').append(response);
         });
      };
   }, 300);

});

</script>


</body>

</html>