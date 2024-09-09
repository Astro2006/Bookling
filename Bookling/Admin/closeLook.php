<!DOCTYPE html>
<html lang="de">



<head>
<meta charset="UTF-8">
<?php
session_start();
//alle includes
include "../database.php";
$bookId = 0;

//passe die seite aufgrund der login und isAdmin daten an
if (isset($_SESSION["isAdmin"]) && $_SESSION["isAdmin"]) {
    $isAdmin = true;
} else {
    header("Location: ../index.php");
}
if (isset($_POST["deletePerson"])) {
    deleteKunde($_GET["id"]);
    header("Location: Admin.php");
}
?>

    
    <link rel="icon" type="image/svg" href="Bilder/Shop/Logo.svg">
    <!--Das hier ist das CSS von bootstrap-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
    <link href="https://mdbcdn.b-cdn.net/wp-content/themes/mdbootstrap4/docs-app/css/compiled-4.20.0.min.css" rel="stylesheet" id="bootstrap-css">


    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--Das ist das CSS von der Webseite welches von uns geschrieben wird-->
    <link rel="stylesheet" href="../style.css">
    <title>Bookling</title>
</head>

<body>
    <header>
        <!-- <img src="#" alt="Bookling Logo"> -->
        <div class="inline">
            <h1 id="title" class="inline-child"><img src="../Bilder/Logo.svg" alt="logo von bookling" height="50" width="50">Bookling
            </h1>
        </div>
        <nav>
            <ul>
                <li><a href="../Index.php">Home</a></li>
                <li><a href="../Shop/Shop.php">Unser Angebot</a></li>
                <li><a href="Admin.php" class="active">Admin</a></li>
                <li class="login"><a href="../Login/Logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <?php
    $edit = false;
    if ($_GET["id"] == "new") {
        $result = array("kid" => "new", "vorname" => "", "name" => "", "email" => "", "geschlecht" => "", "kunde_seit" => "", "kontaktpermail" => "", "geburtstag" => "");
        $edit = true;
    }else{
$user = "SELECT * FROM kunden WHERE kid LIKE :kid";
    try {
        $statement = $conn->prepare($user);
        $statement->bindParam(":kid", $_GET["id"]);
        $statement->execute();
        $result = $statement->fetch();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    }
    




    // MYsql benutzer daten ändern
    if (isset($_POST["edit"])&&$_POST["edit"]) {
        $edit = true;
    }
    if (isset($_POST["submit"]) && $_POST["submit"]) {
        if (!isset($_POST["kontaktpermail"])) {
            $_POST["kontaktpermail"] = 0;
        }

        if ($_GET["id"] == "new") {
        $updateUser = "INSERT INTO kunden VALUES (:kid, :geburtstag, :vorname, :name, :geschlecht, :kunde_seit, :email, :kontaktpermail)";
            //finde die höchste ID heraus
            $stmt = $conn->prepare("SELECT MAX(kid) FROM kunden");
            $stmt->execute();
            $result = $stmt->fetch();
            $_GET["id"] = $result[0] + 1;
            $edit = false;
        } else {
            $updateUser = "UPDATE kunden SET vorname = :vorname, name = :name, email = :email, geschlecht = :geschlecht, kunde_seit = :kunde_seit, kontaktpermail = :kontaktpermail, geburtstag = :geburtstag WHERE kid = :kid";
        }
        try {
            //updadte die Daten in der MySQL Datenbank
            $statement = $conn->prepare($updateUser);
            $statement->bindValue(":vorname", htmlspecialchars(trim($_POST["vorname"])));
            $statement->bindValue(":name", htmlspecialchars(trim($_POST["name"])));
            $statement->bindValue(":email", htmlspecialchars(trim($_POST["email"])));
            $statement->bindValue(":geschlecht", htmlspecialchars(trim($_POST["geschlecht"])));
            $statement->bindValue(":kunde_seit", htmlspecialchars(trim($_POST["kunde_seit"])));
            $statement->bindValue(":kontaktpermail", htmlspecialchars(trim($_POST["kontaktpermail"])));
            $statement->bindValue(":geburtstag", htmlspecialchars(trim($_POST["geburtstag"])));
            $statement->bindValue(":kid", htmlspecialchars(trim($_GET["id"])));
            $statement->execute();
            //Für die Anzeige nach der Änderung (Update der Daten)
            $result["kid"] = $_GET["id"];
            $result["vorname"] = $_POST["vorname"];
            $result["name"] = $_POST["name"];
            $result["email"] = $_POST["email"];
            $result["geschlecht"] = $_POST["geschlecht"];
            $result["kunde_seit"] = $_POST["kunde_seit"];
            $result["kontaktpermail"] = $_POST["kontaktpermail"];
            $result["geburtstag"] = $_POST["geburtstag"];
            echo "Daten geändert";

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    

    if ($edit) {

        echo "<form method='post' action='".$_SERVER['PHP_SELF'].'?id='.$result["kid"]. "'>";
        echo '
<div class="main">
        <div class="row">
            <table class="nextto">
                
                <tbody>
                    <tr>
                        <td>Kunden Nummer: </td>
                        <td>';

        echo $result["kid"];
        
        echo "
                        </td>
                    </tr>
                    <tr>
                        <td>Vorname: </td>
                        <td> ";
        //echo $result["vorname"];
        echo '<input type="text" name="vorname" value="'. $result["vorname"] . '" required>';

        echo "
                        </td>
                    </tr>
                    <tr>
                        <td>Name: </td>
                        <td>";
        //echo $result["name"];
        echo '<input type="text" name="name" value="' . $result["name"] . '" required>';

        echo "
                        </td>
                    </tr>
                    <tr>
                        <td>E-Mail: </td>
                        <td>";
        //echo $result["email"];
        echo '<input type="text" name="email" value="' . $result["email"] . '" required>';
        echo "
                        </td>
                    </tr>
                    <tr>
                        <td>Geschlecht: </td>
                        <td>
                            ";

        echo '<input type="radio" name="geschlecht" value="M" ' . ($result["geschlecht"] == "M" ? "checked" : "") . ' required> Männlich';
        echo '<input type="radio" name="geschlecht" value="F" ' . ($result["geschlecht"] == "F" ? "checked" : "") . '> Weiblich';
        
        
        echo "
                            
                        </td>
                    </tr>
                    <tr>
                        <td>Kunde seit: </td>
                        <td>";
        //echo date("d.m.Y", strtotime($result['kunde_seit'])) . "<br>";

        echo '<input type="date" name="kunde_seit" value="' . date("Y-m-d", strtotime($result['kunde_seit'])) . '" required>';

        echo "
                        </td>
                    </tr>
                    <tr>
                        <td>E-Mail erwünscht: </td>
                        <td>";

        echo '<input type="checkbox" name="kontaktpermail" value="1" ' . ($result["kontaktpermail"] == 1 ? "checked" : "") . '>';
        echo "
                        </td>
                    </tr>
                    <tr>
                        <td>Geburtstag: </td>
                        <td>";
        echo '<input type="date" name="geburtstag" value="' . date("Y-m-d", strtotime($result['geburtstag'])) . '" required>';
        echo "
                        </td>
                    </tr>
                    
                </tbody>
            </table>";
        echo "<button type='submit' name='submit' value='true'>Ändern</button>";
        echo " 
        </div>
    </div>";

        echo "</form>";

    
    } else {
        echo '
        <div class="main">
        <div class="row">
            <table class="nextto">
                <tbody>
                    <tr>
                        <td>Kunden Nummer: </td>
                        <td>
                            ';
        echo $result["kid"];
        echo '
                        </td>
                    </tr>
                    <tr>
                        <td>Vorname: </td>
                        <td>';
        echo $result["vorname"];

        echo '
                        </td>
                    </tr>
                    <tr>
                        <td>Name: </td>
                        <td>
                            ';
        echo $result["name"];
        echo '
                        </td>
                    </tr>
                    <tr>
                        <td>E-Mail: </td>
                        <td>
                            ';
        echo $result["email"];
        echo '
                        </td>
                    </tr>
                    <tr>
                        <td>Geschlecht: </td>
                        <td>
                            ';
        if ($result["geschlecht"] == "M") {
            echo "Männlich";
        } else if ($result["geschlecht"] == "F"){
            echo "Weiblich";
        }
        echo '
                        </td>
                    </tr>
                    <tr>
                        <td>Kunde seit: </td>
                        <td>
                            ';
        echo date("d.m.Y", strtotime($result['kunde_seit'])) . "<br>";
        echo '
                        </td>
                    </tr>
                    <tr>
                        <td>E-Mail erwünscht: </td>
                        <td>
                            ';
        echo ($result["kontaktpermail"] == 1 ? "Mail erwünscht" : "Mails nicht erwünscht");
        echo '
                        </td>
                    </tr>
                    <tr>
                        <td>Geburtstag: </td>
                        <td>
                        ';
        echo date("d.m.Y", strtotime($result["geburtstag"])) . "<br>";
        echo '
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    ';
    }

    ?>
    <footer>
        <table>
            <tr>
                <th class="links"> ©2024 Bookling 2024</th>
                <th class="mitte">Impressum</th>
                <th class="rechts">Socials:</th>

            </tr>
            <tr>
                <td></td>
                <td>Kontakt</td>
                <td>
                    <a href="#"><img src="../Bilder/Instagramm.png" alt="Instagramm" height="30" width="30"></a>
                    <a href="#"><img src="../Bilder/Facebook.png" alt="Facebook" height="30" width="30"></a>
                    <a href="#"><img src="../Bilder/Whatsapp.png" alt="Whatsapp" height="30" width="30"></a>
                </td>
            </tr>

        </table>
    </footer>
</body>
</html>