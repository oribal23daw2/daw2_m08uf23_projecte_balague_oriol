<?php
setcookie("userloged", "", time() - 3600, "/");

header("location: login.php");
exit;
?>
