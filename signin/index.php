<?php
session_start();

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: ../");
    exit;
}

require_once "../config.php";

$username = $mail = $password = $confirm_password = "";
$username_err = $mail_err = $password_err = $confirm_password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty(trim($_POST["username"]))) {
        $username_err = "Prosimo vnesite uporabniško ime.";
    } else {

        $sql = "SELECT idusers FROM users WHERE username = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {

            mysqli_stmt_bind_param($stmt, "s", $param_username);

            $param_username = trim($_POST["username"]);

            if (mysqli_stmt_execute($stmt)) {

                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $username_err = "To uporabniško ime je že registrirano.";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            mysqli_stmt_close($stmt);
        }
    }

    if (empty(trim($_POST["mail"]))) {
        $mail_err = "Prosimo vnesite geslo.";
    } else {
        $mail = trim($_POST["mail"]);
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            $mail_err = "Nepravilen e-mail format";
        }
    }

    if (empty(trim($_POST["password"]))) {
        $password_err = "Prosimo vnesite geslo.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Geslo mora imeti vsaj 6 znakov.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Prosimo potrdite geslo.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Geslo se ne ujema.";
        }
    }

    if (empty($username_err) && empty($mail_err) && empty($password_err) && empty($confirm_password_err)) {

        $sql = "INSERT INTO users (username, mail, password) VALUES (?, ?, ?)";

        if ($stmt = mysqli_prepare($link, $sql)) {

            mysqli_stmt_bind_param($stmt, "sss", $param_username, $param_mail, $param_password);

            $param_username = $username;
            $param_mail = $mail;
            $param_password = password_hash($password, PASSWORD_DEFAULT);

            if (mysqli_stmt_execute($stmt)) {
                header("location: ../login/");
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($link);
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
    <link rel="stylesheet" href="stylessignin.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

</head>

<body><br>
    <div class="wrapper p-4">
        <h2>Registracija računa</h2>
        <p style="color: #bdc3c7;">Prosim izpolnite ta polja s svojimi podatki.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group mb-3">
                <label>Uporabniško ime</label>
                <input type="text" name="username"
                    class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>"
                    value="<?php echo $username; ?>">
                <span class="invalid-feedback">
                    <?php echo $username_err; ?>
                </span>
            </div>

            <div class="form-group mb-3">
                <label>Email</label>
                <input type="text" name="mail"
                    class="form-control <?php echo (!empty($mail_err)) ? 'is-invalid' : ''; ?>"
                    value="<?php echo $mail; ?>">
                <span class="invalid-feedback">
                    <?php echo $mail_err; ?>
                </span>
            </div>

            <div class="form-group mb-3">
                <label>Geslo</label>
                <input type="password" name="password"
                    class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>"
                    value="<?php echo $password; ?>">
                <span class="invalid-feedback">
                    <?php echo $password_err; ?>
                </span>
            </div>

            <div class="form-group mb-3">
                <label>Ponovite geslo</label>
                <input type="password" name="confirm_password"
                    class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>"
                    value="<?php echo $confirm_password; ?>">
                <span class="invalid-feedback">
                    <?php echo $confirm_password_err; ?>
                </span>
            </div>

            <div class="form-group text-center mt-4">
                <button type="submit" class="btn btn-primary">Potrdi</button>
                <button type="reset" class="btn btn-secondary">Izbriši</button>
            </div>
            <br>
            <p class="text-center">Že imate račun? <a href="../login/">Prijavite se tukaj</a>.</p>
        </form>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-OOnqoW8y0Xhly+sOzGLvL1tRd8Vr93xZJQaVbUDUPvZ8xl1QxOxTkwC0BVNR+qdM"
        crossorigin="anonymous"></script>

</body>

</html>