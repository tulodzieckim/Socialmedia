<?php
include("../../config/config.php");
include("../classes/User.php");
include("../classes/Message.php");

$limit = 5;

$message = new Message($con, $_REQUEST['userLoggedIn']); // REQUEST comes from AJAX call
echo $message->getConversationsDropdown($_REQUEST, $limit);

?>