<!DOCTYPE html>
<html lang="hr">
 <head>
	<meta http-equiv="Content-type" content="text/html; charset="utf-8">
        <meta name="viewport"
              content="width=device-width">
        <meta name="keywords" content="SDP" >
        <meta name="author" content="Aleksandar Gavrilovic" >
        <meta name="description" content="Baza Gradske organizacije SDP Zagreb" >
	<link rel="stylesheet" href="css/style.css" />
    <title>Baza Gradske organizacije SDP Zagreb</title>
    <script>
    function validateForm() {
     var x=document.forms["new"]["datum"].value;
     var y=document.forms["new"]["iskaznica"].value;
     var z=document.forms["new"]["datumu"].value;
     if (x.match(/^(0[1-9]|[1-2][0-9]|3[0-1])\.(0[1-9]|1[0-2])\.[0-9]{4}\.?$/)==null) {
      alert("Datum mora biti u obliku poput ovog: 01.02.2013.");
      return false;
     }
     var dateFields = x.split(".");
     var date = new Date(dateFields[2],dateFields[1]-1,dateFields[0]);
     if (date>Date.now()) {
        alert("Datum ne smije biti u budućnosti.");
        return false;
     }
     if (isNaN(y)) {
        alert("Broj iskaznice mora biti broj.");
        return false;
     }
    
    var dateFields = z.split(".");
    var date = new Date(dateFields[2],dateFields[1]-1,dateFields[0]);
    if (z.match(/^(0[1-9]|[1-2][0-9]|3[0-1])\.(0[1-9]|1[0-2])\.[0-9]{4}\.?$/)==null) {
      alert("Datum upisa mora biti u obliku poput ovog: 01.02.2013.");
      return false;
    }
    if (date>Date.now()) {
       alert("Datum ne smije biti u budućnosti.");
       return false;
    }
   }
    
    (function (exports) {
    function valOrFunction(val, ctx, args) {
        if (typeof val == "function") {
            return val.apply(ctx, args);
        } else {
            return val;
        }
    }
    
    function InvalidInputHelper(input, options) {
        input.setCustomValidity(valOrFunction(options.defaultText, window, [input]));

        function changeOrInput() {
            if (input.value == "") {
                input.setCustomValidity(valOrFunction(options.emptyText, window, [input]));
            } else {
                input.setCustomValidity("");
            }
        }

        function invalid() {
            if (input.value == "") {
                input.setCustomValidity(valOrFunction(options.emptyText, window, [input]));
            } else {
               console.log("INVALID!"); input.setCustomValidity(valOrFunction(options.invalidText, window, [input]));
            }
        }

        input.addEventListener("change", changeOrInput);
        input.addEventListener("input", changeOrInput);
        input.addEventListener("invalid", invalid);
    }
    exports.InvalidInputHelper = InvalidInputHelper;
})(window);
    </script>
 </head>
<body><div id="login-box"><div id="box1"><div id="crveni-box">
  
   <h1>Baza Gradske organizacije SDP Zagreb</h1>
  <div id="box2" />
  <?php
    session_start();
    include("passwords.php");
    check_logged();
    echo "<div id='korisnik'>Korisnik: " . $_SESSION["logged"] . "</div>";
    check_pravo();
    if ($_GET["q"]==1) {
        echo "<b>Korisnik već postoji.</b>";
    }
    if ($_GET["q"]==2) {
        echo "<b>Broj iskaznice već postoji.</b>";
    }
  ?>

  <form name="new" action="addmember.php" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
  <label for="ime">Ime:</label>
  <input type="hidden" name="ac" value="member" />
  <input type="text" name="ime" style="width:275px;" id="ime" placeholder="Josip" required /><br />
  <br />
  <label for="ime">Prezime:</label>
  <input type="text" name="prezime" style="width:247px;" id="prezime" placeholder="Broz" required /><br />
  <br />
  <label for="ime">Username:</label>
  <input type="text" name="username" style="width:235px;" id="username" placeholder="jbroz" required /><br />
  <br />
  <label for="ime">Password:</label>
  <input type="password" name="password" style="width:238px;" id="password" placeholder="" required /><br />
  <br />
  <label for="mail">Mail:</label>
  <input type="text" name="mail" style="width:268px;" id="mail" placeholder="oeskic@me.com" required /><br />
  <br />
  <label for="datum">Datum rođenja:</label>
  <input type="date" name="datum" id="datum" style="width:204px;" placeholder="07.05.1892." required /><br />
  <br />
  <label for="datum">Datum upisa:</label>
  <input type="date" name="datumu" id="datumu" style="width:220px;" placeholder="07.05.1892." required /><br />
  <br />
  <label for="ogranak">Ogranak:</label>
  <select name="ogranak" id="ogranak" style="width:252px" placeholder="Nova ves" required /><br />
    <?php
    // PRETRAGA ZA OGRANCIMA
        $con=mysqli_connect("127.0.0.1","root","root","baza");
    mysqli_query($con, "SET NAMES 'utf8'");
    $result = mysqli_query($con,"SELECT * FROM ogranak");
    while ($row = mysqli_fetch_array($result)) {
        echo '<option value=' . $row['rbrOgranak'] . '>';
        echo $row['ime'];
        echo '</option>';
        }
    ?>
  </select>
  <br /><br />
  <label for="iskaznica">Broj iskaznice:</label>
  <input type="number" name="iskaznica" id="iskaznica" style="width:208px;" placeholder="10000" required /><br />
  <br /><br />
  <input type="submit" value="Dodaj člana" class="btn btn-large btn-block btn-danger" style="width:320px;"/>
  </form>
  <br />
  <footer>
   <a href="home.php" class="btn btn-large btn-block btn-danger">Natrag</a> 
   <a href="logout.php" class="btn btn-large btn-block btn-danger">Izlaz iz programa</a><br /><br /><br />
  </footer>
 </body>
 <script>
 InvalidInputHelper(document.getElementById("ime"), {
    defaultText: "Upiši ime.",
    emptyText: "Upiši ime.",
    invalidText: function (input) {
        return 'Ne možeš staviti "' + input.value + '" kao ime.';
    }
});
 InvalidInputHelper(document.getElementById("prezime"), {
    defaultText: "Upiši prezime.",
    emptyText: "Upiši prezime.",
    invalidText: function (input) {
        return 'Ne možeš staviti "' + input.value + '" kao prezime.';
    }
});
InvalidInputHelper(document.getElementById("username"), {
    defaultText: "Upiši username.",
    emptyText: "Upiši username.",
    invalidText: function (input) {
        return 'Ne možeš staviti "' + input.value + '" kao username!';
    }
});
InvalidInputHelper(document.getElementById("password"), {
    defaultText: "Upiši password.",
    emptyText: "Upiši password.",
    invalidText: function (input) {
        return 'Ne možeš staviti "' + input.value + '" kao password!';
    }
});
InvalidInputHelper(document.getElementById("datum"), {
    defaultText: "Upiši datum.",
    emptyText: "Upiši datum.",
    invalidText: function (input) {
        return 'Ne možeš staviti "' + input.value + '" kao datum!';
    }
});
InvalidInputHelper(document.getElementById("datumu"), {
    defaultText: "Upiši datum.",
    emptyText: "Upiši datum.",
    invalidText: function (input) {
        return 'Ne možeš staviti "' + input.value + '" kao datum!';
    }
});
InvalidInputHelper(document.getElementById("mail"), {
    defaultText: "Upiši mail.",
    emptyText: "Upiši mail.",
    invalidText: function (input) {
        return 'Ne možeš staviti "' + input.value + '" kao mail!';
    }
});
InvalidInputHelper(document.getElementById("iskaznica"), {
    defaultText: "Upiši broj iskaznice.",
    emptyText: "Upiši broj iskaznice.",
    invalidText: function (input) {
        return 'Ne možeš staviti "' + input.value + '" kao broj iskaznice!';
    }
});
 </script>
</html>