<?php
session_start();
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Syötteiden käsittely ja validointi
    $fname = htmlspecialchars($_POST['fname']);
    $lname = htmlspecialchars($_POST['lname']);
    $email = htmlspecialchars($_POST['email']);
    $username = htmlspecialchars($_POST['login']); 
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    // Tarkistetaan salasanan ja vahvistussalasanan täsmääminen
    if ($password !== $cpassword) {
        $_SESSION['ERRMSG_ARR'] = ["Salasanat eivät täsmää."];
        header("Location: register.php");
        exit();
    }

    // Salasanan hash
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        // SQL-kysely käyttäjän lisäämiseksi
        $sql = "INSERT INTO Users (first_name, last_name, email, username, password_hash, role) 
                VALUES (:fname, :lname, :email, :username, :password_hash, :role)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':fname' => $fname,
            ':lname' => $lname,
            ':email' => $email,
            ':username' => $username,
            ':password_hash' => $hashed_password,
            ':role' => 'customer'
        ]);

        $_SESSION['SUCCESS_MSG'] = "Rekisteröinti onnistui!";
        header("Location: login.php");
        exit();

    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $_SESSION['ERRMSG_ARR'] = ["Sähköposti tai käyttäjätunnus on jo rekisteröity."];
        } else {
            $_SESSION['ERRMSG_ARR'] = ["Jotain meni vikaan: " . $e->getMessage()];
        }
        header("Location: register.php");
        exit();
    }
} else {
    header("Location: register.php");
    exit();
}
?>
