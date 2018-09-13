<?php 
include("includes/header.php");

if (isset($_POST['post'])) {
   $post = new Post($con, $userLoggedIn);
   $post->submitPost($_POST['post_text'], 'none');
}
// session_destroy();

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
            <form action="index.php" method="POST" class="form-group d-flex">
               <textarea name="post_text" id="post_text" placeholder="Got something to say?" class="form-control"></textarea>
               <input type="submit" value="Post" name="post" class="btn btn-primary ml-1">
               <hr>
            </form>
            <div class="posts-area"></div>
            <div class="w-100 d-flex justify-content-center p-0">
               <img id="loading" src="./assets/images/icons/Loading_icon.gif" width="120" height="80" alt="Loading_icon" class="p-0">
            </div>
         </div>
      </div>
   </div>
</div>

<script>
   // const userLoggedIn = '<?= $userLoggedIn ?>'; already declared in header

   $(document).ready(function () {
      $('#loading').show();
   });

   // original ajax request for loading first posts
   $.ajax({
      url: 'includes/handlers/ajax_load_posts.php',
      type: 'POST',
      data: {
         page: 1,
         userLoggedIn: userLoggedIn,
      },
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
               url: 'includes/handlers/ajax_load_posts.php',
               type: 'POST',
               data: 'page=' + page + '&userLoggedIn=' + userLoggedIn,
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