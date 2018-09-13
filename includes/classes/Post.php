<?php

class Post
{
   private $user_obj;
   private $con;

   public function __construct($con, $username)
   {
      $this->con = $con;
      $this->user_obj = new User($con, $username);
   }

   public function submitPost($body, $user_to)
   {
      $body = strip_tags($body); // removes html tags
      $body = mysqli_real_escape_string($this->con, $body);
      $check_empty = preg_replace('/\s+/', '', $body);

      if ($check_empty !== "") {

         $body_array = preg_split("/\s+/", $body);

         foreach($body_array as $key => $value) {

            // if((strpos($value, ".jpg") !== false)) {
            //    $value = substr($value, 0, (strpos($value, ".jpg") + 4));
            //    $value = "<img src=${value}>";
            //    $body_array[$key] = $value;
            // }

            if(strpos($value, "www.youtube.com/watch?v=") !== false) {

               $link = preg_split("!&!", $value);
               $value = preg_replace("!watch\?v=!", "embed/", $link[0]);
               $value = "<iframe class=\'youtube-movie\' width=\'420\' height=\'315\' src=\'${value}\'></iframe>";
               $body_array[$key] = $value;
            }
         }
         $body = implode(" ", $body_array);

   
         $date_added = date("Y-m-d H:i:s");
         $added_by = $this->user_obj->getUsername();
         
         // If user is on own profile, user_to is 'none'
         if ($user_to === $added_by) {
            $user_to = "none";
         }

         // insert post
         $query = mysqli_query($this->con, "INSERT INTO posts VALUES(null, '$body', '$added_by', '$user_to', '$date_added', 'no', 'no', '0' )");
         $returned_id = mysqli_insert_id($this->con);

         // Insert notification
         if ($user_to !== 'none') {
            $notification = new Notification($this->con, $added_by);
            $notification->insertNotification($returned_id, $user_to, "profile_post");
         }

         // Update post count for user
         $num_posts = $this->user_obj->getNumPosts();
         $num_posts++;
         $update_query = mysqli_query($this->con, "UPDATE users SET num_posts='$num_posts' WHERE username='$added_by'");

         

         header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
      }
   }

   public function loadPostsFriends($data, $limit)
   {

      $page = $data['page'];
      $userLoggedIn = $this->user_obj->getUsername();

      if ($page === 1) {
         $start = 0;
      } else {
         $start = ($page - 1) * $limit;
      }

      $str = ""; // string to return
      $posts_query_result = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' ORDER BY id DESC");

      if (mysqli_num_rows($posts_query_result) > 0) {

         $num_iterations = 0;
         $count = 1;

         while ($row = mysqli_fetch_array($posts_query_result)) {
            $id = $row['id'];
            $body = $row['body'];
            $added_by = $row['added_by'];
            $date_time = $row['date_added'];

         // Prepare user_to string so it can be included even if not posted to user
            if ($row['user_to'] === "none") {
               $user_to = "";
            } else {
               $user_to_obj = new User($this->con, $row['user_to']);
               $user_to_name = $user_to_obj->getFirstAndLastName();
               $user_to = "<span class='text-muted'>to</span> <a href='" . $row['user_to'] . "'>" . $user_to_name . "</a>";
            }

         // Check if user posted, has their account closed
            $added_by_obj = new User($this->con, $added_by);
            if ($added_by_obj->isClosed()) {
               continue;
            }


            $user_logged_obj = new User($this->con, $userLoggedIn);

            if ($user_logged_obj->isFriend($added_by)) {

               if ($num_iterations++ < $start) {
                  continue;
               }
               if ($count > $limit) {
                  break;
               } else {
                  $count++;
               }

               if ($userLoggedIn === $added_by) {
                  $delete_button = "<button class='btn-deletePost btn btn-danger btn-sm ml-auto' id='post$id' data-id='$id' onclick='onClickDeleteBtn(event, $id)'>&times</button>";
               } else {
                  $delete_button = "";
               }

               $user_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM users WHERE username='$added_by'");
               $user_row = mysqli_fetch_array($user_details_query);
               $first_name = $user_row['first_name'];
               $last_name = $user_row['last_name'];
               $profile_pic = $user_row['profile_pic'];
               ?>
                 <script>
                    // Comments - on /off
                     function toggle<?= $id ?>() {
                        const $element = $('#toggleComment<?= $id ?>').toggle();
                        const $hrLine = $('#hr-line<?= $id ?>').toggle();
                     }

                     // Prepare for ajax
                     // TODO
                     // function onTestBtnClick(event, id) {
                     //    console.log(id);
                     //    $.ajax({
                     //       url: "like2.php",
                     //       type: "get",
                     //       async: 'false',
                     //       data: {
                     //          "post_id": id
                     //       }
                     //    }).done(resp => {
                     //       console.log(resp);
                     //       event.target.innerText = JSON.parse(resp).name;
                     //    }).fail((jqXHR, textStatus) => {
                     //       console.log('error');
                     //       console.log(jqXHR);
                     //       console.log(textStatus);
                     //    })
                     // }
                  </script>

               <?php

               $comments_query = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id'");
               $comments_number = mysqli_num_rows($comments_query);


               // Timeframe
               $date_time_now = date("Y-m-d H:i:s");
               $start_date = new DateTime($date_time); // Time of posts 
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

               $str .= "<div class='card status-post'>
                  <div class='card-body'>
                     <div class='card-text'>
                        <img src='$profile_pic' class='img-fluid d-none d-sm-block float-left mr-2' alt='Profile Picture'>
                        <div>
                           <div class='pb-2 ml-1'>
                             <a href='$added_by'>$first_name $last_name</a> $user_to <small class='text-muted'>$time_message</small>                  
                           </div>
                           <hr class='p-0 m-0 mb-2'>
                           <div>
                              $body 
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class='card-footer bg-white'>
                     <div class='mb-2 d-flex '>
                        <div class='text-comments text-primary mr-3' onclick='toggle$id()'>Comments($comments_number)</div>
                        <div>
                           <iframe src='like.php?post_id=$id' class='iframe-likes'></iframe>
                        </div> 
                        $delete_button                       
                     </div>  
                     <hr id='hr-line$id' style='display: none;'>                   
                     <div id='toggleComment$id' class='post-comment w-100' style='display:none;'>
                        <iframe src='comment_frame.php?post_id=$id' id='comment-iframe' frameborder='0' class='w-100 h-100'></iframe>
                     </div>
                  </div>
               </div>";
            }
            ?>
               <!-- Modals - confirm delete the post - different modal for every posts -->
               <div class="modal fade" id="delete-modal<?= $id ?>" data-post-id="<?= $id ?>" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
               <div class="modal-dialog modal-sm">
                  <div class="modal-content">
                     <div class="modal-header">
                        <div class="modal-title h6">
                           Are you sure to delete the post?
                        </div>
                     </div>
                     <div class="modal-body text-center">
                        <div class="btn-group">
                           <button class="btn btn-sm btn-success mr-2 yees-btn" data-id="<?= $id ?>" onclick="onYesBtn(event)">Yes</button>
                           <button class="btn btn-sm btn-danger no-btn" data-id="<?= $id ?>" onclick="onNoBtn(event)">No</button>
                        </div>
                     </div>
                  </div>
               </div>
               </div>
            <?php

         }

         if ($count > $limit) {
            $str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
                     <input type='hidden' class='noMorePosts' value='false'>";
         } else {
            $str .= "<input type='hidden' class='noMorePosts' value='true'>
                     <p class='text-center'>No more posts to show.</p>";
         }


      }
      echo $str;
   }


   public function loadProfilePosts($data, $limit)
   {

      $page = $data['page'];
      $profileUser = $data['profileUsername'];
      $userLoggedIn = $this->user_obj->getUsername();

      if ($page === 1) {
         $start = 0;
      } else {
         $start = ($page - 1) * $limit;
      }

      $str = ""; // string to return
      $posts_query_result = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' AND ((user_to='none' AND added_by='$profileUser') OR user_to='$profileUser') ORDER BY id DESC");

      if (mysqli_num_rows($posts_query_result) > 0) {

         $num_iterations = 0;
         $count = 1;

         while ($row = mysqli_fetch_array($posts_query_result)) {
            $id = $row['id'];
            $body = $row['body'];
            $added_by = $row['added_by'];
            $date_time = $row['date_added'];


            $user_logged_obj = new User($this->con, $userLoggedIn);

            if ($num_iterations++ < $start) {
               continue;
            }
            if ($count > $limit) {
               break;
            } else {
               $count++;
            }

            if ($userLoggedIn === $added_by) {
               $delete_button = "<button class='btn-deletePost btn btn-danger btn-sm ml-auto id='post$id' data-id='$id' onclick='onClickDeleteBtn(event, $id)'>&times</button>";
            } else {
               $delete_button = "";
            }

            $user_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM users WHERE username='$added_by'");
            $user_row = mysqli_fetch_array($user_details_query);
            $first_name = $user_row['first_name'];
            $last_name = $user_row['last_name'];
            $profile_pic = $user_row['profile_pic'];
            ?>
                 <script>
                    // Comments - on /off
                     function toggle<?= $id ?>() {
                        const $element = $('#toggleComment<?= $id ?>').toggle();
                        const $hrLine = $('#hr-line<?= $id ?>').toggle();
                     }

                     // Prepare for ajax
                     // TODO
                     // function onTestBtnClick(event, id) {
                     //    console.log(id);
                     //    $.ajax({
                     //       url: "like2.php",
                     //       type: "get",
                     //       async: 'false',
                     //       data: {
                     //          "post_id": id
                     //       }
                     //    }).done(resp => {
                     //       console.log(resp);
                     //       event.target.innerText = JSON.parse(resp).name;
                     //    }).fail((jqXHR, textStatus) => {
                     //       console.log('error');
                     //       console.log(jqXHR);
                     //       console.log(textStatus);
                     //    })
                     // }
                  </script>

               <?php

               $comments_query = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id'");
               $comments_number = mysqli_num_rows($comments_query);


               // Timeframe
               $date_time_now = date("Y-m-d H:i:s");
               $start_date = new DateTime($date_time); // Time of posts 
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

               $str .= "<div class='card status-post'>
                  <div class='card-body'>
                     <div class='card-text'>
                        <img src='$profile_pic' class='img-fluid d-none d-sm-block float-left mr-2' alt='Profile Picture'>
                        <div>
                           <div class='pb-2 ml-1'>
                             <a href='$added_by'>$first_name $last_name</a> <small class='text-muted'>$time_message</small>                  
                           </div>
                           <hr class='p-0 m-0 mb-2'>
                           <div>
                              $body 
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class='card-footer bg-white'>
                     <div class='mb-2 d-flex'>
                        <div class='text-comments text-primary mr-3' onclick='toggle$id()'>Comments($comments_number)</div>
                        <div>
                           <iframe src='like.php?post_id=$id' class='iframe-likes'></iframe>
                        </div> 
                        $delete_button                       
                     </div>  
                     <hr id='hr-line$id' style='display: none;'>                   
                     <div id='toggleComment$id' class='post-comment w-100' style='display:none;'>
                        <iframe src='comment_frame.php?post_id=$id' id='comment-iframe' frameborder='0' class='w-100 h-100'></iframe>
                     </div>
                  </div>
               </div>";

               ?>
               <!-- Modals - confirm delete the post - different modal for every posts -->
               <div class="modal fade" id="delete-modal<?= $id ?>" data-post-id="<?= $id ?>" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
               <div class="modal-dialog modal-sm">
                  <div class="modal-content">
                     <div class="modal-header">
                        <div class="modal-title h6">
                           Are you sure to delete the post?
                        </div>
                     </div>
                     <div class="modal-body text-center">
                        <div class="btn-group">
                           <button class="btn btn-sm btn-success mr-2 yees-btn" data-id="<?= $id ?>" onclick="onYesBtn(event)">Yes</button>
                           <button class="btn btn-sm btn-danger no-btn" data-id="<?= $id ?>" onclick="onNoBtn(event)">No</button>
                        </div>
                     </div>
                  </div>
               </div>
               </div>
            <?php

         }

         if ($count > $limit) {
            $str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
                     <input type='hidden' class='noMorePosts' value='false'>";
         } else {
            $str .= "<input type='hidden' class='noMorePosts' value='true'>
                     <p class='text-center'>No more posts to show.</p>";
         }


      }
      echo $str;
   }

   public function getSinglePost($post_id)
   {
      $userLoggedIn = $this->user_obj->getUsername();
      
      $opened_query = mysqli_query($this->con, "UPDATE notifications SET opened='yes' WHERE user_to='$userLoggedIn' AND link LIKE '%=$post_id'");

      $str = ""; // string to return
      $posts_query_result = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' AND id = '$post_id' ORDER BY id DESC");

      if (mysqli_num_rows($posts_query_result) > 0) {


         $row = mysqli_fetch_array($posts_query_result);

         $id = $row['id'];
         $body = $row['body'];
         $added_by = $row['added_by'];
         $date_time = $row['date_added'];

         // Prepare user_to string so it can be included even if not posted to user
         if ($row['user_to'] === "none") {
            $user_to = "";
         } else {
            $user_to_obj = new User($this->con, $row['user_to']);
            $user_to_name = $user_to_obj->getFirstAndLastName();
            $user_to = "<span class='text-muted'>to</span> <a href='" . $row['user_to'] . "'>" . $user_to_name . "</a>";
         }

         // Check if user posted, has their account closed
         $added_by_obj = new User($this->con, $added_by);
         if ($added_by_obj->isClosed()) {
            return;
         }

         $user_logged_obj = new User($this->con, $userLoggedIn);

         if ($user_logged_obj->isFriend($added_by)) {

            if ($userLoggedIn === $added_by) {
               $delete_button = "<button class='btn-deletePost btn btn-danger btn-sm ml-auto' id='post$id' data-id='$id' onclick='onClickDeleteBtn(event, $id)'>&times</button>";
            } else {
               $delete_button = "";
            }

            $user_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM users WHERE username='$added_by'");
            $user_row = mysqli_fetch_array($user_details_query);
            $first_name = $user_row['first_name'];
            $last_name = $user_row['last_name'];
            $profile_pic = $user_row['profile_pic'];
            ?>
               <script>
                  // Comments - on /off
                  function toggle<?= $id ?>() {
                     const $element = $('#toggleComment<?= $id ?>').toggle();
                     const $hrLine = $('#hr-line<?= $id ?>').toggle();
                  }
               </script>

            <?php

            $comments_query = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id'");
            $comments_number = mysqli_num_rows($comments_query);


            // Timeframe
            $date_time_now = date("Y-m-d H:i:s");
            $start_date = new DateTime($date_time); // Time of posts 
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

            $str .= "<div class='card status-post'>
               <div class='card-body'>
                  <div class='card-text'>
                     <img src='$profile_pic' class='img-fluid d-none d-sm-block float-left mr-2' alt='Profile Picture'>
                     <div>
                        <div class='pb-2 ml-1'>
                           <a href='$added_by'>$first_name $last_name</a> $user_to <small class='text-muted'>$time_message</small>                  
                        </div>
                        <hr class='p-0 m-0 mb-2'>
                        <div>
                           $body 
                        </div>
                     </div>
                  </div>
               </div>
               <div class='card-footer bg-white'>
                  <div class='mb-2 d-flex '>
                     <div class='text-comments text-primary mr-3' onclick='toggle$id()'>Comments($comments_number)</div>
                     <div>
                        <iframe src='like.php?post_id=$id' class='iframe-likes'></iframe>
                     </div> 
                     $delete_button                       
                  </div>  
                  <hr id='hr-line$id' style='display: none;'>                   
                  <div id='toggleComment$id' class='post-comment w-100' style='display:none;'>
                     <iframe src='comment_frame.php?post_id=$id' id='comment-iframe' frameborder='0' class='w-100 h-100'></iframe>
                  </div>
               </div>
            </div>";

            ?>
               <!-- Modals - confirm delete the post - different modal for every posts -->
               <div class="modal fade" id="delete-modal<?= $id ?>" data-post-id="<?= $id ?>" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
               <div class="modal-dialog modal-sm">
                  <div class="modal-content">
                     <div class="modal-header">
                        <div class="modal-title h6">
                           Are you sure to delete the post?
                        </div>
                     </div>
                     <div class="modal-body text-center">
                        <div class="btn-group">
                           <button class="btn btn-sm btn-success mr-2 yees-btn" data-id="<?= $id ?>" onclick="onYesBtn(event)">Yes</button>
                           <button class="btn btn-sm btn-danger no-btn" data-id="<?= $id ?>" onclick="onNoBtn(event)">No</button>
                        </div>
                     </div>
                  </div>
               </div>
               </div>

            <?php

         } else {
            echo "<p>You cannot see the pos because you are not friend with this user.</p>";
            return;
         }
         echo $str;
      } else {
         echo "<p>No post found.</p>";
         return;
      }

   }
}
?>
