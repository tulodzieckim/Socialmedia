<?php

class Notification
{
   private $user_obj;
   private $con;

   public function __construct($con, $user)
   {
      $this->con = $con;
      $this->user_obj = new User($con, $user);
   }

   public function getUnreadNumber()
   {
      $userLoggedIn = $this->user_obj->getUsername();
      $query = mysqli_query($this->con, "SELECT * FROM notifications WHERE viewed='no' and user_to='$userLoggedIn'");
      return mysqli_num_rows($query);
   }

   public function insertNotification($post_id, $user_to, $type)
   {
      $userLoggedIn = $this->user_obj->getUsername();
      $userLoggedInName = $this->user_obj->getFirstAndLastName();

      $date_time = date("Y-m-d H:i:s");

      switch ($type) {
         case 'comment':
            $message = $userLoggedInName . " commented your post";
            break;
         case 'like':
            $message = $userLoggedInName . " liked your post";
            break;
         case 'profile_post':
            $message = $userLoggedInName . " posted on your profile";
            break;
         case 'comment_non_owner':
            $message = $userLoggedInName . " commented on a post you commented on";
            break;
         case 'profile_comment':
            $message = $userLoggedInName . " commented on your profile post";
            break;
      }

      $link = "post.php?id=" . $post_id;

      $insert_query = mysqli_query($this->con, "INSERT INTO notifications VALUES(null, '$user_to', '$userLoggedIn', '$message', '$link', '$date_time', 'no', 'no')");

   }

   public function getNotificationsDropdown($data, $limit)
   {

      $page = $data['page'];
      $userLoggedIn = $this->user_obj->getUsername();
      $return_string = '';

      if ($page === 1) {
         $start = 0;
      } else {
         $start = ($page - 1) * $limit;
      }

      $set_viewed_query = mysqli_query($this->con, "UPDATE notifications SET viewed='yes' WHERE user_to='$userLoggedIn'");

      $query = mysqli_query($this->con, "SELECT * FROM notifications WHERE user_to='$userLoggedIn' ORDER BY id DESC");

      if (mysqli_num_rows($query) === 0) {
         echo "You have no notifications";
         return;
      }

      $num_iterations = 0;
      $count = 1;
      $a = 0;


      while ($row = mysqli_fetch_array($query)) {

         if ($num_iterations++ < $start) {
            continue;
         }

         if ($count > $limit) {
            break;
         } else {
            $count++;
         }

         $user_from = $row['user_from'];
         $users_query = mysqli_query($this->con, "SELECT * FROM users WHERE username='$user_from'");
         $user_data = mysqli_fetch_array($users_query);

         // Timeframe
         $date_time_now = date("Y-m-d H:i:s");
         $start_date = new DateTime($row['datetime']); // Time of posts 
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
         
         $opened = $row['opened'];
         $style_class = ($row['opened'] === 'no') ? "notification-unopened" : "";


         $return_string .= "<a href='" . $row['link'] . "' class='card ${style_class} resultDisplay'>
                              <div class='notification card-body p-0'>
                                 <div class='card-text'>
                                 <img src='" . $user_data['profile_pic'] . "'><span><small class='timestamp-smaller text-muted'>{$time_message}</small></span>
                                 <p class='my-0 text-dark'>{$row['message']}</p>
                                 </div>
                              </div>
                           </a>";
      }

      if ($count > $limit) {
         $return_string .= "<input type='hidden' class='nextPageDropdownData' value='" . ($page + 1) . "'><input type='hidden' class='noMoreDropdownData' value='false'>";
      } else {
         $return_string .= "<input type='hidden' class='noMoreDropdownData' value='true'><p>No more notifications to load</p>";
      }


      return $return_string;
   }

}

?>