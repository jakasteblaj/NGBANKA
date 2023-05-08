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
    <title> Nova Gorenjska Banka</title>
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
        <div class="col" style="display: flex; flex-direction: column; align-items: center; justify-content: center;">
            <form action="logic.php" method="post" class="send text-center">
                <div class="form-group">
                    <input type="text" name="mail" placeholder="Mail"
                        class="send-mail form-control <?php echo (!empty($mail_err)) ? 'is-invalid' : ''; ?>"
                        value="<?php echo $mail; ?>">
                    <span class="invalid-feedback">
                        <?php echo $mail_err; ?>
                    </span>
                </div><br>
                <div class="form-group">
                    <input type="text" name="amount" placeholder="Količina"
                        class="send-mail form-control <?php echo (!empty($amount_err)) ? 'is-invalid' : ''; ?>"
                        value="<?php echo $amount; ?>">
                    <span class="invalid-feedback">
                        <?php echo $amount_err; ?>
                    </span>
                </div><br>
                <div class="form-group">
                    <input name="send" type="submit" class="btn btn-success send-btn" value="POŠLJI" id="submit">
                    <?php if (isset($_GET['trsuccess']))
                        echo "<span class='bg-success status'>Transakcija uspela!</span>";
                    if (isset($_GET['trerror']))
                        echo "<span class='bg-danger status'>Transakcija ni uspela!</span>"; ?>
                </div>
            </form>

            <div class="bal d-flex flex-column">
                <span class="title-text">Tvoje stanje:</span>
                <div class="balance d-inline-block <?php if (intval($_SESSION['balance']) >= 0)
                    echo 'text-success';
                else
                    echo 'text-danger'; ?>">
                    <?php echo $_SESSION["balance"]; ?>€</div>
            </div>

            <div class="summary">
                <div class="sum-title">Nedavne dejavnosti</div>
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

                $query = "SELECT u.username, t.amount, t.tr_date, CASE WHEN t.sender = " . $_SESSION['id'] . " THEN 'sent' ELSE 'received' END AS operation FROM transakcije t
                    JOIN users u ON (CASE WHEN t.sender = " . $_SESSION['id'] . " THEN t.receiver ELSE t.sender END) = u.idusers
                    WHERE t.sender = " . $_SESSION['id'] . " OR t.receiver = " . $_SESSION['id'] . "
                    ORDER BY t.tr_date DESC LIMIT 5";
                $result = mysqli_query($connection, $query);
                if (mysqli_num_rows($result) > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="sum-item">';
                        if ($row["operation"] == "sent") {
                            echo '<div class="i-title">Denar ste poslali na ime:  <span class="text-info">' . $row["username"] . '</span></div>';
                            echo '<div class="i-amount text-danger">' . $row["amount"] . '€</div>';
                        } else {
                            echo '<div class="i-title">Denar ste prejeli od:  <span class="text-info">' . $row["username"] . '</span></div>';
                            echo '<div class="i-amount text-success">' . $row["amount"] . '€</div>';
                        }
                        echo '</div>';
                    }
                } else {
                    echo '<div class="sum-item">';
                    echo '<div class="i-title">No recent activity.</div>';
                    echo '</div>';
                }

                mysqli_free_result($result);
                mysqli_close($connection);
                ?>
            </div>
            <form action="./login/" method="post">
                <input class="btn btn-danger m-2" name="logout" type="submit" value="ODJAVA">
            </form>
        </div>
    </div>
</body>

</html>