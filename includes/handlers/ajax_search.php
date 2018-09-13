<?php
require '../../config/config.php';
include("../classes/User.php");

$searchedValue = $_POST['searchedValue'];
$userLoggedIn = $_POST['userLoggedIn'];

$searchedValues = explode(" ", $searchedValue);


if (strpos($searchedValue, '_') !== false) {
   // If searchedValue contains '_' assume the 'username' is searched
   $userReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE username LIKE '%$searchedValue%' AND user_closed='no' LIMIT 8");
} else if (count($searchedValues) === 2) {
   // If there are two words, assume the first and last names respectively
   $userReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '%$searchedValues[0]%' AND last_name LIKE '%$searchedValues[1]%') AND user_closed='no' LIMIT 8");
} else if (count($searchedValues) === 1) {
   // If query has one word only, search first name or last name
   $userReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '%$searchedValue%' OR last_name LIKE '%$searchedValue%') AND user_closed='no' LIMIT 8");
}

if ($searchedValue !== "") {
   while ($row = mysqli_fetch_array($userReturnedQuery)) {
      $user = new User($con, $userLoggedIn);

      if ($row['username'] !== $userLoggedIn) {
         $mutual_friends = $user->getMutualFriends($row['username']) . " friends in common";
      } else {
         $mutual_friends = "";
      }

      // echo "<div class='resultDisplay'>
      //          <a href='" . $row['username'] . "'>
      //             <div class='liveSearchProfilePic'>
      //                <img src='" . $row['profile_pic'] . "'>
      //             </div>
      //             <div class='liveSearchText'>" . $row['first_name'] . " " . $row['last_name'] . "
      //                <p>" . $row['username'] . "</p>
      //                <p class='text-muted'>" . $mutual_friends . "</p>
      //          </a>
      //       </div>";

      echo "<a href='" . $row['username'] . "' class='card resultDisplay'>
               <div class='card-body p-0'>
                  <div class='card-text'>
                     <img src='" . $row['profile_pic'] . "'>
                     <div>" . $row['first_name'] . " " . $row['last_name'] . "</div>
                     <p>" . $row['username'] . "</p>
                     <p class='text-muted'>" . $mutual_friends . "</p>
                  </div>

               </div>
            </a>";
   }
}



?>