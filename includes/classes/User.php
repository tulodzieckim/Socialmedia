<?php

class User
{
   private $user;
   private $con;

   public function __construct($con, $username) {
      $this->con = $con;
      $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$username'");
      $this->user = mysqli_fetch_array($user_details_query);
   }

   public function getFirstAndLastName() {
      $firstName = $this->user['first_name'];
      $lastName = $this->user['last_name'];
      return $firstName . " " . $lastName;
   }

   public function getUsername() {
      return $this->user['username'];
   }

   public function getNumPosts() {
      return $this->user['num_posts'];
   }

   public function isClosed() {
      return $this->user['user_closed'] === "no" ? false : true;
   }

   // public function isFriend($username_to_check) {
   //    $usernameComma = "," . $username_to_check . ",";

   //    if (strstr($this->user['friend_array'], $usernameComma)) {
   //       return true;
   //    } else {
   //       return false;
   //    }
   // }
   public function isFriend($username_to_check) {
      $usernameComma = "," . $username_to_check . ",";

      if (strstr($this->user['friend_array'], $usernameComma) || ($username_to_check == $this->user['username'])) {
         return true;
      } else {
         return false;
      }
   }

   public function getProfilePic() {
      return $this->user['profile_pic'];
   }

   public function getFriendArray() {
      return $this->user['friend_array'];
   }

   public function howManyFriends() {
      return (substr_count($this->user['friend_array'], ",")) - 1;
   }

   public function didReceiveRequest($user_from) {
      $user_to = $this->getUsername();
      $check_request_query = mysqli_query($this->con, "SELECT * FROM friend_requests WHERE user_to='$user_to' AND user_from='$user_from'");
      if(mysqli_num_rows($check_request_query) > 0) {
         return true;
      } else {
         return false;
      }
   }

   public function didSendRequest($user_to) {
      $user_from = $this->getUsername();
      $check_request_query = mysqli_query($this->con, "SELECT * FROM friend_requests WHERE user_to='$user_to' AND user_from='$user_from'");
      if(mysqli_num_rows($check_request_query) > 0) {
         return true;
      } else {
         return false;
      }
   }

   public function removeFriend($user_to_remove) {
      $username_loggedIn = $this->getUsername();
      $query = mysqli_query($this->con, "SELECT friend_array FROM users WHERE username='$user_to_remove' ");
      $row = mysqli_fetch_array($query);
      $friend_array_userToRemoved = $row['friend_array'];

      // Remove from LoggedInUser array
      $new_friend_array = str_replace($user_to_remove . ",", "", $this->user['friend_array']);
      $remove_friend_query = mysqli_query($this->con, "UPDATE users SET friend_array = '$new_friend_array' WHERE username='$username_loggedIn'");

      // Remove from RemovedUser array
      $new_friend_array = str_replace($username_loggedIn . ",", "", $friend_array_userToRemoved);
      $remove_friend_query = mysqli_query($this->con, "UPDATE users SET friend_array = '$new_friend_array' WHERE username='$user_to_remove'");
   }

   public function sendRequest($user_to) {
      $user_from = $this->getUsername();
      $query = mysqli_query($this->con, "INSERT INTO friend_requests VALUES (null, '$user_to', '$user_from')");
   }

   public function getMutualFriends($user_to_check) {
      $mutualFriends = 0;
      $user_array = $this->user['friend_array'];
      $user_array_explode = explode(",", $user_array);

      $query = mysqli_query($this->con, "SELECT friend_array FROM users WHERE username='$user_to_check'");
      $row = mysqli_fetch_array($query);
      $user_to_check_array = $row['friend_array'];
      $user_to_check_array_explode = explode(",", $user_to_check_array);

      foreach($user_array_explode as $i) {
         foreach($user_to_check_array_explode as $j) {
            if($i === $j && $i != "") {
               $mutualFriends++;
            }
         }
      }
      return $mutualFriends;
   }

   public function getNumberOfFriendRequests() {
      $username = $this->user['username'];
      $query = mysqli_query($this->con, "SELECT * FROM friend_requests WHERE user_to='$username'");
      return mysqli_num_rows($query);
   }
}



?>