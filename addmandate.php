 <?php

session_start();
include("passwords.php");
check_logged();
check_pravo();
if ($Admin == true) {

$con=mysqli_connect("127.0.0.1","root","root","baza");
mysqli_query($con, "SET NAMES 'utf8'");

//PREUZIMANJE PODATAKA IZ FORME

$funkcija = $_POST["funkcija"];
$ogranak = $_POST["ogranak"];
$organizacija = $_POST["organizacija"];
$datump = $_POST["datump"];
//$datumk = $_POST["datumk"];
$iskaznica = $_POST["id"];

// PROMJENA DATUMA IZ HRVATSKOG U SQL STANDARD
$dateFields = explode(".",$datump);
$datump = $dateFields[2] . "-" . $dateFields[1] . "-" . $dateFields[0];
$datumk = "2999-12-31";

// UPIS ČLANA U BAZU
$sql = "insert into clanfunkcija (rbrFunkcija, rbrOgranak, rbrOrganizacija, rbrClan, datumPocetka, datumKraja) values ('" . $funkcija . "', '" . $ogranak . "', '" . $organizacija . "', '" . $iskaznica. "', '" . $datump . "', '" . $datumk . "')";

if (!mysqli_query($con,$sql)) {
    die('Error: ' . mysqli_error($con));
}
}
header("Location: home.php");
?>
