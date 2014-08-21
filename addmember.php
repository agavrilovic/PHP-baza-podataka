 <?php

session_start();
include("passwords.php");
check_logged();
check_pravo();
if ($Admin == true) {
$con=mysqli_connect("127.0.0.1","root","root","baza");
mysqli_query($con, "SET NAMES 'utf8'");

//PREUZIMANJE PODATAKA IZ FORME

$ime = $_POST["ime"];
$prezime = $_POST["prezime"];
$username = $_POST["username"];
$password = $_POST["password"];
$mail = $_POST["mail"];
$ogranak = $_POST["ogranak"];
$datum = $_POST["datum"];
$datumu = $_POST["datumu"];
$iskaznica = $_POST["iskaznica"];

// PROVJERA USERNAME-A
$result = mysqli_query($con,"SELECT * FROM clan WHERE username = '" . $username . "'");
if ($row = mysqli_fetch_array($result)) {
    if ($username == $row['username']) {
        echo "Postoji netko s tim username-om"; 
        header("Location: member.php?q=1");
    }
}
// PROVJERA ISKAZNICE
$result = mysqli_query($con,"SELECT * FROM clan WHERE rbrClan = '" . $iskaznica . "'");
if ($row = mysqli_fetch_array($result)) {
    if ($iskaznica == $row['rbrClan']) {
        echo "Postoji netko s tim brojem iskaznice."; 
        header("Location: member.php?q=2");
    }
}

// PROMJENA DATUMA IZ HRVATSKOG U SQL STANDARD
$dateFields = explode(".",$datum);
$datum = $dateFields[2] . "-" . $dateFields[1] . "-" . $dateFields[0];

// UPIS ČLANA U BAZU
$sql = "insert into clan (rbrClan, ime, prezime, username, password, rodjen, upisan, mail, rbrOgranak) values ('" . $iskaznica . "', '" . $ime . "', '" . $prezime . "', '" . $username. "', '" . $password . "', '" . $datum . "', '" . $datumu . "', '" . $mail . "', '" . $ogranak . "')";
if (!mysqli_query($con,$sql)) {
    die('Error: ' . mysqli_error($con));
}
}
header("Location: home.php");
?>
