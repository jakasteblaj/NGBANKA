<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'jsteblaj');
define('DB_PASSWORD', 'HnjJkkEr567');
define('DB_NAME', 'jsteblaj');

$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($link === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>