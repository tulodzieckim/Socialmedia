<?php
include("includes/header.php");

if(isset($_GET['id'])) {
   $id = $_GET['id'];  
} else {
   $id = 0;
}
?>

<!-- OPEN TAGS in header.php -->
<div class="container content-section">
   <div class="row">
      <div class="col-sm-4">
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
      </div>
      <!-- POSTS SECTION -->
      <div class="col-sm-8 ml-auto">
         <div class="bg-light p-2 section wall-posts-section justify-content-center">
            <div class="posts-area">

               <?php

               $post = new Post($con, $userLoggedIn);
               $post->getSinglePost($id);

               ?>

            </div>
         </div>
      </div>
   </div>
</div>