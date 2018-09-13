<?php
include("../../config/config.php");
include("../classes/User.php");
include("../classes/Notification.php");

$limit = 5;

$notification = new Notification($con, $_REQUEST['userLoggedIn']); // REQUEST comes from AJAX call
echo $notification->getNotificationsDropdown($_REQUEST, $limit);

?>