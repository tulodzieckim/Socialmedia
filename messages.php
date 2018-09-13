<?php
include("includes/header.php");

$message_obj = new Message($con, $userLoggedIn);

if (isset($_GET['u'])) {
   $user_to = $_GET['u'];
} else {
   $user_to = $message_obj->getMostRecentUser();
   if ($user_to == false) {
      $user_to = "new";
   }
}

if ($user_to != "new") {
   $user_to_obj = new User($con, $user_to);
}

if(isset($_POST['post_message'])) {
   if(isset($_POST['message_body'])) {
      $body = mysqli_real_escape_string($con, $_POST['message_body']);
      $date = date("Y-m-d H:i:s");
      $message_obj->sendMessage($user_to, $body, $date);
   }
}
?>

<div class="container content-section">
   <div class="row">
      <div class="col-md-4">
         <!-- USER DETAILS  -->
         <div class="row user-details-section bg-light p-2 section">
            <a href="<?= $userLoggedIn ?>"><img src="<?= $user['profile_pic'] ?>" alt="Profile Picture"></a>
            <div class="align-items-center mt-3 w-auto">
               <a class="d-flex d-md-block d-lg-flex" href="<?= $userLoggedIn ?>">
                  <span class="ml-2 text-primary d-block">
                     <?= $user['first_name'] ?></span>
                  <span class="ml-2 text-primary d-block">
                     <?= $user['last_name'] ?></span>
               </a>
               <span class="ml-2 d-block">Posts:
                  <?= $user['num_posts'] ?></span>
               <span class="ml-2 d-block">Likes:
                  <?= $user['num_likes'] ?></span>
            </div>
         </div>
         <div class="row section">
            <div class="col " id="conversations">
               <h4>Conversations</h4>
               <hr>
               <div id="loaded-conversations">
                  <?=$message_obj->getConversations() ?>
                  <hr class="mb-1 mt-3 py-0">
                  <a href="messages.php?u=new">New Message</a>
               </div>
            </div>
         </div>
      </div>
      <div class="col-md-8">
         <div class="bg-light p-2 section wall-posts-section justify-content-center">
            
            <?php
            if ($user_to != "new") {
               echo "<h4>You and <a href='$user_to'>" . $user_to_obj->getFirstAndLastName() . "</a></h4><hr>";
               echo "<div id='loaded-messages'>";
                  echo $message_obj->getMessages($user_to);
               echo "</div>";
            } else {
               echo "<h4>New Message</h4>";
            }
            ?>
            <div class="message-post">
               <?php
                  if($user_to == "new") {
                     ?>
                     <form action='' method='POST' class=''>
                     <p>Select the friend you would like to send message</p>
                     <label>To</label><input type='text' class='form-control mb-2' name='q' placeholder='Name' autocomplete='off' id='search-text-input' 
                     onkeyup="getUsers(this.value, '<?= $userLoggedIn ?>')">
                     <div id='results'></div>
                                        
                     <?php
                  } else {
                     ?>
                     <form action='' method='POST' class='d-flex'>
                     <textarea name='message_body' id='message-textarea' placeholder='Write your message!' class='form-control'></textarea>
                     <input type='submit' name='post_message' class='info btn btn-primary ml-1' id='message-submit' value='Send'>
                     <?php
                  }
                  ?>
                  
               </form>
            </div>
         </div>
      </div>
   </div>
</div>

<script>
   // scroll down messages window after reload page
   const div = $('#loaded-messages');
   
   if(Boolean(div[0]))
      div[0].scrollTop = div[0].scrollHeight;
</script>

</body>

</html>