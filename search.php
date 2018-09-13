<?php

include("includes/header.php");

if (isset($_GET['q'])) {
   $searchedValue = $_GET['q'];
} else {
   $searchedValue = "";
}

if (isset($_GET['type'])) {
   $type = $_GET['type'];
} else {
   $type = "name";
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
      <!-- SEARCH SECTION -->
      <div class="col-sm-8 ml-auto">
         <div class="bg-light p-2 section">
         
            <?php
            if ($searchedValue === "")
               echo "You must enter something in the search box";
            else {
               if ($type === 'username') {
               // If searchedValue contains '_' assume the 'username' is searched
                  $userReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE username LIKE '%$searchedValue%' AND user_closed='no'");
               } else {

                  $searchedValues = explode(" ", $searchedValue);

                  if (count($searchedValues) === 3)
                     $userReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '%$searchedValues[0]%' AND last_name LIKE '%$searchedValues[2]%') AND user_closed='no'");
                  else if (count($searchedValues) === 2)
                     $userReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '%$searchedValues[0]%' AND last_name LIKE '%$searchedValues[1]%') AND user_closed='no'");
                  else
                     $userReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '%$searchedValue%' OR last_name LIKE '%$searchedValue%') AND user_closed='no'");
               }

               // Check if results were found
               if (mysqli_num_rows($userReturnedQuery) === 0) {
                  echo "We cannot find anyone with a ${type} like ${searchedValue}";
               } else {
                  echo "<p id='search-results-num'>" . mysqli_num_rows($userReturnedQuery) . " results found: </p>";
               }
               ?>

               <p class='text-muted'>
                  Try searching for: <br>
                  <a href="search.php?q=<?= $searchedValue ?>&type=name">Name</a>, <a href="search.php?q=<?= $searchedValue ?>&type=username">Username</a>
               </p>
               <?php

               while ($row = mysqli_fetch_array($userReturnedQuery)) {
                  $user_obj = new User($con, $user['username']);
                  $button = "";
                  $mutual_friends = "";

                  if ($user['username'] !== $row['username']) {

                     // Generate button depending on friendship status
                     if ($user_obj->isFriend($row['username'])) {
                        $button = "<input type='submit' name='" . $row['username'] . "' class='btn btn-danger' value='Remove Friend'>";
                     } else if ($user_obj->didReceiveRequest($row['username'])) {
                        $button = "<input type='submit' name='" . $row['username'] . "' class='btn btn-info' value='Respond to request'>";
                     } else if ($user_obj->didSendRequest($row['username'])) {
                        // $button = "<input class='btn btn-secondary' value='Request Sent'>";
                        $button = "<button class='btn btn-secondary'>Request Sent</button>";
                     } else {
                        $button = "<input type='submit' name='" . $row['username'] . "' class='btn btn-success' value='Add Friend'>";
                     }

                     $mutual_friends = $user_obj->getMutualFriends($row['username']) . " friends in common";

                     // Button forms
                     if(isset($_POST[$row['username']])) {
                        if($user_obj->isFriend($row['username'])) {
                           $user_obj->removeFriend($row['username']);
                           header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
                        } else if ($user_obj->didReceiveRequest($row['username'])) {
                           header("Location: requests.php");
                        } else if ($user_obj->didSendRequest($row['username'])) {
                           // Do nothing 
                        } else {
                           $user_obj->sendRequest($row['username']);
                           header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
                        }
                     }

                  }
                  ?>
                   <div class='search-result card resultDisplay resultDisplay-search'>
                     <div class='card-body'>
                        <div class="card-text">
                           <a href="<?= $row['username'] ?>">
                                 <img src="<?= $row['profile_pic'] ?>" alt="Profile Pic" class="img-fluid">
                              </a>
                           <div><?= $row['first_name'] ?> <?= $row['last_name'] ?></div>
                           <div><?= $row['username'] ?></div>                          
                           <span class="text-muted"><?=$mutual_friends ?></span>
                        </div>
                        <form action='' method='POST'>
                           <?=$button ?>
                        </form>
                     </div>                         
                  </div>                 

               <?php

            }
         }

         ?>
         </div>
      </div>
   </div>
</div>

