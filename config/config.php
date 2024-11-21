<?php
define('DB_HOST', 'localhost'); 
define('DB_USER', 'kuvelm');      
define('DB_PASS', 'Kaut123!');  
define('DB_NAME', 'kuvelm'); 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Tietokantayhteys epäonnistui: " . $e->getMessage());
}
?>