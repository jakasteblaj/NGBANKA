<?php
session_start();
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'jsteblaj');
define('DB_PASSWORD', 'HnjJkkEr567');
define('DB_NAME', 'jsteblaj');

if (isset($_POST["send"]) && isset($_POST["mail"]) && isset($_POST["amount"])) {
    $db = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    if ($db === false) {
        die("ERROR: Could not connect. " . mysqli_connect_error());
    }
    $sql = "UPDATE users SET balance = balance + " . $_POST['amount'] . " WHERE mail = '" . $_POST['mail'] . "'";
    $db->query($sql);
    if ($db->affected_rows == 1) {
        $sql = "UPDATE users SET balance = balance - " . $_POST['amount'] . " WHERE idusers = '" . $_SESSION["id"] . "'";
        $db->query($sql);
        $user = $db->query("SELECT idusers FROM users WHERE mail = '" . $_POST['mail'] . "'")->fetch_assoc();
        $sql = "INSERT INTO transakcije(sender, receiver, amount) VALUES ($_SESSION[id], $user[idusers],  $_POST[amount])";
        $db->query($sql);
        header('Location: .?trsuccess');
        exit;
    }
    header('Location: .?trerror');

    $db->close();
}
?>