<?php
session_destroy(); // Destroy all session data
header("Location: index.php?page=login");
exit();
?>