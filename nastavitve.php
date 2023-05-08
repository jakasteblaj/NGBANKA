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

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!empty(trim($_POST["new_password"]))) {
        $new_password = trim($_POST["new_password"]);
        if (strlen($new_password) < 6) {
            $password_err = "Geslo naj ima vsaj 6 znakov.";
        } else {
            $password = $new_password;
        }
    } else {
        $password_err = "Vnesite novo geslo.";
    }

    if (empty($password_err)) {
        $db = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
        if ($db === false) {
            die("ERROR: Could not connect. " . mysqli_connect_error());
        }
        $db->query("SET NAMES utf8");
        $sql = "UPDATE users SET password = ? WHERE idusers = ?";
        if ($stmt = $db->prepare($sql)) {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt->bind_param("si", $hashed_password, $_SESSION['id']);
            if ($stmt->execute()) {
                session_destroy();
                header("location: ./login/");
                exit();
            } else {
                echo "Nekaj je šlo narobe. Prosimo poizkusite ponovno.";
            }
            $stmt->close();
        }
        $db->close();
    }
}
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
                <a href="index.php"
                    class="btn <?php if (basename($_SERVER['PHP_SELF']) == 'index.php') {
                        echo 'nav-active';
                    } ?>">Domov</a>
                <a href="transakcije.php"
                    class="btn <?php if (basename($_SERVER['PHP_SELF']) == 'transakcije.php') {
                        echo 'nav-active';
                    } ?>">Transakcije</a>
                <a href="nastavitve.php"
                    class="btn <?php if (basename($_SERVER['PHP_SELF']) == 'nastavitve.php') {
                        echo 'nav-active';
                    } ?>">Nastavitve</a>
            </div>
        </div>
        <div class="col">

            <br><br>
            <div class="summary" style="margin:auto;">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group" style="padding: 20px;">
                        <label for="new_username" class="form-label"
                            style="display: block; text-align: center; margin-bottom: 10px; color: whitesmoke;">Novo
                            uporabniško ime:</label>
                        <input type="text" name="new_username"
                            class="send-mail form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>"
                            value="<?php echo htmlspecialchars($username); ?>">
                        <span class="invalid-feedback">
                            <?php echo $username_err; ?>
                        </span>
                    </div>

                    <div class="form-group" style="text-align:center; padding-bottom:20px;">
                        <input type="submit" name="change_username" class="btn btn-success send-btn" value="Spremeni">
                    </div>
                </form>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group" style="padding: 20px;">
                        <label for="new_password" class="form-label"
                            style="display: block; text-align: center; margin-bottom: 10px; color: whitesmoke;">Novo
                            geslo:</label>
                        <input type="password" name="new_password"
                            class="send-mail form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>"
                            value="<?php echo htmlspecialchars($password); ?>">
                        <span class="invalid-feedback">
                            <?php echo $password_err; ?>
                        </span>
                    </div>

                    <div class="form-group" style="text-align:center; padding-bottom:20px;">
                        <input type="submit" name="change_password" class="btn btn-success send-btn" value="Spremeni">
                    </div>
                </form>
            </div><br><br>
        </div>
    </div>
</body>

</html>