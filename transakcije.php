<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once("config.php");
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    $db = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    if ($db === false) {
        die("ERROR: Could not connect. " . mysqli_connect_error());
    }
    $db->query("SET NAMES utf8");
    $sql = "SELECT username, balance FROM users WHERE idusers = " . $_SESSION['id'] . ";";
    $balance = $db->query($sql);
    $bal = $balance->fetch_assoc();

    $_SESSION["balance"] = $bal['balance'];

    $balance->free();
    $db->close();
}

if (!isset($_SESSION['loggedin'])) {
    header('Location: ./login/');
}

$username = $mail = $password = $confirm_password = $amount = "";
$username_err = $mail_err = $password_err = $confirm_password_err = $amount_err = "";

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Gorenjska Banka</title>
    <link rel="icon" type="image/x-icon" href="ngb.png" style="border-radius:15%;">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            overflow-y: scroll;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        body::-webkit-scrollbar {
            display: none;
        }
    </style>
</head>

<body style="max-width: 100%; overflow-x:hidden;">
    <div class="row">
        <div class="col-2 bg-dark min-vh-100">
            <img src="ngb.png" alt="logo"
                style="max-width: 100px; margin: auto; display: block; padding-top:15px; padding-bottom:10px; border-radius:15%;">
            <div class="nav d-flex flex-column">
                <a href="index.php" class="btn <?php if (basename($_SERVER['PHP_SELF']) == 'index.php') {
                    echo 'nav-active';
                } ?>">Domov</a>
                <a href="transakcije.php" class="btn <?php if (basename($_SERVER['PHP_SELF']) == 'transakcije.php') {
                    echo 'nav-active';
                } ?>">Transakcije</a>
                <a href="nastavitve.php" class="btn <?php if (basename($_SERVER['PHP_SELF']) == 'nastavitve.php') {
                    echo 'nav-active';
                } ?>">Nastavitve</a>
            </div>
        </div>
        <div class="col">

            <br><br>
            <div class="summary" style="margin:auto;">

                <?php
                require_once("config.php");
                if (!isset($_SESSION['loggedin'])) {
                    header('Location: ./login/');
                }

                $connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
                if ($connection === false) {
                    die("ERROR: Could not connect. " . mysqli_connect_error());
                }
                mysqli_query($connection, "SET NAMES utf8");

                $query = "SELECT u1.username AS sender, u2.username AS receiver, t.amount, t.tr_date FROM transakcije t
                    JOIN users u1 ON t.sender = u1.idusers
                    JOIN users u2 ON t.receiver = u2.idusers
                    WHERE t.sender = " . $_SESSION['id'] . " OR t.receiver = " . $_SESSION['id'] . "
                    ORDER BY t.tr_date DESC";
                $result = mysqli_query($connection, $query);

                echo '<div class="sum-title text-center mb-3" style="margin:auto;">Vse Transakcije</div>';
                if (mysqli_num_rows($result) > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="sum-item text-center">';
                        if ($row["sender"] == $_SESSION['username']) {
                            echo '<div class="i-title">Denar ste poslali na ime:  <span class="text-info">' . $row["receiver"] . '</span></div>';
                            echo '<div class="i-ammount text-danger">' . $row["amount"] . '€</div>';
                        } else {
                            echo '<div class="i-title">Denar ste prejeli od:  <span class="text-info">' . $row["sender"] . '</span></div>';
                            echo '<div class="i-ammount text-success">' . $row["amount"] . '€</div>';
                        }
                        echo '<div class="i-date">' . $row["tr_date"] . '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<div class="sum-item">';
                    echo '<div class="i-title">No transactions yet.</div>';
                    echo '</div>';
                }

                mysqli_free_result($result);
                mysqli_close($connection);
                ?>

            </div><br><br>
        </div>
    </div>
</body>

</html>