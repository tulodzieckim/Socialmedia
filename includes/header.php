<?php
require 'config/config.php';
include("includes/classes/User.php");
include("includes/classes/Post.php");
include("includes/classes/Message.php");
include("includes/classes/Notification.php");


if (isset($_SESSION['username'])) {
   $userLoggedIn = $_SESSION['username'];
   $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$userLoggedIn'");
   $user = mysqli_fetch_array($user_details_query);
} else {
   header("Location: register.php");
}

?>

   <!DOCTYPE html>
   <html lang="en">

   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta http-equiv="X-UA-Compatible" content="ie=edge">
      <title>Welcome to private </title>
      <!-- CSS -->
      <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
      <link rel="stylesheet" href="./assets/css/style.css">      
      <link rel="stylesheet" href="./assets/css/jquery.Jcrop.css">
      <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ"
         crossorigin="anonymous">
      <link rel="stylesheet" href="./assets/css/font-awesome-animation.min.css">
      <!-- JS -->
      <script src="./assets/js/jquery-3.3.1.min.js"></script>
      <script src="./assets/js/jquery.Jcrop.js"></script>
      <script src="./assets/js/jcrop_bits.js"></script>
      <script src="./assets/js/popper.min.js"></script>
      <script src="./assets/js/bootstrap.min.js"></script>
      <script src="./assets/js/socialmedia.js"></script>


   </head>

   <body>

      <nav class="navbar navbar-expand-md navbar-dark bg-primary py-1 fixed-top">
         <div class="container-fluid">
            <a class="navbar-brand" href="index.php">SocialMedia</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavId" aria-controls="collapsibleNavId"
               aria-expanded="false" aria-label="Toggle navigation">
               <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse mr-auto" id="collapsibleNavId">
               <!-- SEARCH FORM -->
               <div id="form-search-container" class="m-auto">
                  <form class="mx-auto" method="GET" action="search.php" name="search_form">
                     <div class="input-group">
                        <input type="text" class="form-control my-auto" placeholder="Search for user..." onkeyup="getLiveSearchUsers(this.value, '<?= $userLoggedIn ?>')" name="q" autocomplete="off" id="search-text-input">
                        <div class="input-group-append">
                           <div class="input-group-text" id="search-icon">
                              <i class="fas fa-search"></i>
                           </div>
                        </div>
                     </div>
                  </form>
                  <div id="search-results-dropdown" class="navbar-nav dropdown">
                     <button id="search-dropdown-toggle" class="dropdown-toggle d-none" data-toggle="dropdown"></button>
                     <div class="dropdown-menu" id="search-results"></div>
                     <div id="search-results-footer" class="empty"></div>
                  </div>
               </div>
                             
               <!-- ICONS -->
               <ul class="navbar-nav ml-auto">
                  <li class="nav-item active my-auto">
                     <a class="nav-link" href="<?= $userLoggedIn ?>">
                        <span>
                           <?= $user['first_name'] ?>                    
                        </span>
                        <span class="align-self-center d-inline d-sm-none"><?= $user['last_name'] ?><span>
                     </a>
                  </li>
                  <li class="nav-item active">
                     <a class="nav-link d-flex" href="index.php">
                        <i class="fas fa-home fa-2x faa-pulse animated-hover"></i>
                        <span class="align-self-center d-inline d-md-none ml-3">Home<span>
                     </a>
                  </li>
                  <li class="nav-item active dropdown">
                     <a class="nav-link d-flex dropdown-toggle position-relative" href="#" onclick="getDropdownData('<?= $userLoggedIn ?>', 'message', '#data-dropdown-menu-messages')">
                        <i class="fas fa-envelope fa-2x faa-shake animated-hover"></i>

                        <?php
                        // Unseen messages
                        $message = new Message($con, $userLoggedIn);
                        $num_messages = $message->getUnreadNumber();

                        if($num_messages > 0 )
                           echo "<span class='badge' id='unread-messages'>${num_messages}</span>"
                        ?>

                        <span class="align-self-center d-inline d-md-none ml-3">Messages<span>  
                     </a> 
                     <div class="dropdown-menu" id="data-dropdown-menu-messages"></div>
                     <input type="hidden" id='dropdown-data-type' value="">                  
                  </li>
                  <li class="nav-item active dropdown">
                     <a class="nav-link d-flex dropdown-toggle position-relative" href="#" onclick="getDropdownData('<?= $userLoggedIn ?>', 'notification', '#data-dropdown-menu-notifications')">
                        <i class="fas fa-bell fa-2x faa-ring animated-hover"></i>

                        <?php
                        // Unseen notifications
                        $notification = new Notification($con, $userLoggedIn);
                        $num_notifications = $notification->getUnreadNumber();
                        if($num_notifications > 0 )
                           echo "<span class='badge' id='unread-notifications'>${num_notifications}</span>";
                        ?>

                        <span class="align-self-center d-inline d-md-none ml-3">Notifications<span>
                     </a>
                     <div class="dropdown-menu" id="data-dropdown-menu-notifications"></div>
                     <input type="hidden" id='dropdown-data-type' value="">         
                  </li>
                  <li class="nav-item active">
                     <a class="nav-link d-flex position-relative" href="requests.php">
                        <i class="fas fa-users fa-2x faa-tada animated-hover"></i>

                        <?php
                        // Unaswered requests
                        $user_obj = new User($con, $userLoggedIn);
                        $num_requests = $user_obj->getNumberOfFriendRequests();
                        if($num_requests > 0 )
                           echo "<span class='badge' id='unaswered-requests'>${num_requests}</span>";
                        ?>

                        <span class="align-self-center d-inline d-md-none ml-3">Friend Requests<span>
                     </a>
                  </li>
                  <li class="nav-item active">
                     <a class="nav-link d-flex" href="settings.php">
                        <i class="fas fa-cog fa-2x faa-spin animated-hover"></i>
                        <span class="align-self-center d-inline d-md-none ml-3">Settings<span>
                     </a>
                  </li>
                  <li class="nav-item active">
                     <a class="nav-link d-flex" href="includes/handlers/logout.php">
                        <i class="fas fa-sign-in-alt fa-2x faa-horizontal animated-hover"></i>
                        <span class="align-self-center d-inline d-md-none ml-3">Logout<span>
                     </a>
                  </li>
               </ul>
            </div>
         </div>
      </nav>

   <script>
      let userLoggedIn = '<?= $userLoggedIn ?>';

      let scrollTimeoutDropdown;

      $('#data-dropdown-menu-messages').scroll(function () {
         
         if (scrollTimeoutDropdown) {
            clearTimeout(scrollTimeoutDropdown);
         }
         scrollTimeoutDropdown = setTimeout(function() {
            const innerHeight = $('#data-dropdown-menu-messages').innerHeight();
            const scrollTop = $('#data-dropdown-menu-messages').scrollTop();
            const page = $('#data-dropdown-menu-messages').find('.nextPageDropdownData').val();
            const noMoreData = $('#data-dropdown-menu-messages').find('.noMoreDropdownData').val();


            if ((scrollTop + innerHeight >= $('#data-dropdown-menu-messages')[0].scrollHeight) && noMoreData === 'false') {
               let pageName;
               let type =  $('#dropdown-data-type').val();

               switch (type) {
                  case 'notification':
                     pageName = 'ajax_load_notification.php';
                     break;
                  case 'message':
                     pageName = 'ajax_load_messages.php';
                     break;
               }

               const ajaxReq = $.ajax({
                  url: 'includes/handlers/' + pageName,
                  type: 'POST',
                  data: {
                     page: page,
                     userLoggedIn: userLoggedIn,
                  },
                  cache: false
               }).done(function (response) {
                  $('#data-dropdown-menu-messages').find('.nextPageDropdownData').remove();
                  $('#data-dropdown-menu-messages').find('.noMoreDropdownData').remove();

                  $('#data-dropdown-menu-messages').append(response);
               });
            };
         }, 100);

      });


      $('#data-dropdown-menu-notifications').scroll(function () {
         
         if (scrollTimeoutDropdown) {
            clearTimeout(scrollTimeoutDropdown);
         }
         scrollTimeoutDropdown = setTimeout(function() {
            const innerHeight = $('#data-dropdown-menu-notifications').innerHeight();
            const scrollTop = $('#data-dropdown-menu-notifications').scrollTop();
            const page = $('#data-dropdown-menu-notifications').find('.nextPageDropdownData').val();
            const noMoreData = $('#data-dropdown-menu-notifications').find('.noMoreDropdownData').val();


            if ((scrollTop + innerHeight >= $('#data-dropdown-menu-notifications')[0].scrollHeight) && noMoreData === 'false') {
               let pageName;
               let type =  $('#dropdown-data-type').val();

               switch (type) {
                  case 'notification':
                     pageName = 'ajax_load_notification.php';
                     break;
                  case 'message':
                     pageName = 'ajax_load_messages.php';
                     break;
               }

               const ajaxReq = $.ajax({
                  url: 'includes/handlers/' + pageName,
                  type: 'POST',
                  data: {
                     page: page,
                     userLoggedIn: userLoggedIn,
                  },
                  cache: false
               }).done(function (response) {
                  $('#data-dropdown-menu-notifications').find('.nextPageDropdownData').remove();
                  $('#data-dropdown-menu-notifications').find('.noMoreDropdownData').remove();

                  $('#data-dropdown-menu-notifications').append(response);
               });
            };
         }, 100);

      });
   
   </script>