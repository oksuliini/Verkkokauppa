<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_DATABASE', 'Verkkokauppa');

function getDbConnection() {
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_DATABASE);
    if (!$link) {
        die('Failed to connect to server: ' . mysqli_connect_error());
    }
    return $link;
}

?>

