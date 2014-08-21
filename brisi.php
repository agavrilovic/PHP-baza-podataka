<?php
session_start();
include("passwords.php");
check_logged();
$con=mysqli_connect("127.0.0.1","root","root","baza");
echo "<div id='korisnik'>Korisnik: " . $_SESSION["logged"] . "</div>";
if ($_SESSION["logged"] == "admin" && $_GET['id'] != 0) {
$sql = "DELETE FROM clan WHERE rbrClan='" . $_GET['id'] . "'";
if (!mysqli_query($con,$sql)) {
    die('Error: ' . mysqli_error($con));
}
echo $sql;
}
header("Location: members.php");
?>