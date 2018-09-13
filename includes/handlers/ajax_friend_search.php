<?php
include("../../config/config.php");
include("../classes/User.php");

$query = $_POST['query'];
$userLoggedIn = $_POST['userLoggedIn'];

$names = explode(" ", $query);

if (strpos($query, "_") !== false) {
   $usersReturned = mysqli_query($con, "SELECT * FROM users WHERE username LIKE '$query%' AND user_closed='no' LIMIT 8");
} else if (count($names) === 2) {
   $usersReturned = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '%$names[0]%' AND last_name LIKE '%$names[1]%') AND user_closed='no' LIMIT 8");
} else {
   $usersReturned = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '%$names[0]%' OR last_name LIKE '%$names[0]%') AND user_closed='no' LIMIT 8");
}

if ($query !== "") {
   while ($row = mysqli_fetch_array($usersReturned)) {

      $user = new User($con, $userLoggedIn);

      if ($row['username'] !== $userLoggedIn) {
         $mutual_friends = $user->getMutualFriends($row['username']) . " friends in common";
      } else {
         $mutual_friends = "";
      }

      if ($user->isFriend($row['username'])) {
         ?>
            <div class="resultDisplay card">
               <div class="card-body p-1">
                  <div class="card-text">
                     <a href="messages.php?u=<?= $row['username'] ?>">
                        <div class="livesearch-profilePic">
                           <img class="float-left mr-2" src="<?= $row['profile_pic'] ?>">
                        </div>
                        <div class="livesearch-text">
                           <p>
                              <?= $row['first_name'] ?>
                              <?= $row['last_name'] ?>
                           </p>
                           <p>
                              <small><?= $row['username'] ?></small>
                           </p>                        
                           <p class='text-muted'>
                              <small><?= $mutual_friends ?></small>
                           </p>
                        </div>
                     </a>
                  </div>
               </div>
            </div>
         <?php
         // echo "<div class='resultDisplay card'>
         //          <div class='card-body'>
         //             <a class='card-text' href='messages.php?u=" . $row['username'] . 
         //             "
         //                <div class='livesearch-profilePic'>
         //                   <img src='" . $row['profile_pic'] . "'>
         //                </div>
         //                <div class='livesearch-text'>
         //                   ". $row['first_name'] . " " . $row['last_name'] . "
         //                   <p>" . $row['username'] . "</p>
         //                   <p class='text-muted'>" . $mutual_friends . "</p>
         //                </div>
         //             </a>
         //          </div>
         //       </div>";
      }


   }
}
?>