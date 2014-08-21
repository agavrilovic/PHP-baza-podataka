<?php
ini_set('max_execution_time', 3000);
session_start();
include("passwords.php");
check_logged();
check_pravo();
$row = 1;
$con=mysqli_connect("127.0.0.1","root","root","baza");
mysqli_query($con, "SET NAMES 'utf8'");
if (($handle = fopen("clanstvo.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, "$")) !== FALSE) {
        $num = count($data);
        $row++;
        if ($row>3) {
        echo $row . " ";
        for ($c=0; $c < $num; $c++) {
        //echo $data[$c] . " ";
        $podatak = str_replace("®","Ž",$data[$c]);
        $podatak = str_replace("ľ","ž",$podatak);
        $podatak = str_replace("©","Š",$podatak);
        $podatak = str_replace("ą","š",$podatak);
            if ($c == 0) {
                $iskaznica = $podatak;
            }
            if ($c == 3) {
                $organizacija = $podatak;
            }
            if ($c == 4) {
                $ogranakIme = $podatak;
            }
            if ($c == 5) {
                $ime = $podatak;
            }
            if ($c == 6) {
                $prezime = $podatak;
            }
            if ($c == 9) {
                $datum = $podatak;
            }
            if ($c == 17) {
                $mail = $data[$c];
            }
            if ($c == 21) {
                $datumu = $data[$c];
                // PROMJENA DATUMA IZ HRVATSKOG U SQL STANDARD
                //$dateFields = explode(".",$datumu);
                //$datumu = $dateFields[2] . "-" . $dateFields[1] . "-" . $dateFields[0];

            }
        }
        $username = $iskaznica;
        $password = hash('ripemd160', $username);
        $sql = "select rbrOgranak from ogranak where ime = '" . $ogranakIme . "'";
        $result = mysqli_query($con,$sql);
        if ($row = mysqli_fetch_array($result)) {
            $ogranak = $row["rbrOgranak"];
        }
        $sql = "insert into clan (rbrClan, ime, prezime, username, password, rodjen, upisan, mail, rbrOgranak) values ('" . $iskaznica . "', '" . $ime . "', '" . $prezime . "', '" . $username . "', '" . $password . "', '" . $datum . "', '" . $datumu . "', '" . $mail . "', '" . $ogranak . "')";
            if (!mysqli_query($con,$sql)) {
                echo mysqli_error($con);
            }
        $sql = "select rbrOrganizacija from ogranak where rbrOgranak = " . $ogranak;
        $result = mysqli_query($con,$sql);
        if ($row = mysqli_fetch_array($result)) {
            $organizacija = $row["rbrOrganizacija"];
        }
        $sql = "insert into clanfunkcija (rbrFunkcija, rbrOgranak, rbrOrganizacija, rbrClan, datumPocetka, datumKraja) values (1, '" . $ogranak . "', '" . $organizacija . "', '" . $iskaznica . "', '" . "2014-01-01" . "', '" . "2999-12-31" . "')";
            if (!mysqli_query($con,$sql)) {
                echo mysqli_error($con);
            }
        
        }
    }
    fclose($handle);
}
ini_set('max_execution_time', 30);
echo '<a href="home.php" class="btn btn-large btn-block btn-danger">Natrag</a>';
?>