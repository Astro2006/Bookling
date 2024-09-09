<!DOCTYPE html>
<html lang="de">

<?php
session_start();
include "../database.php";

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == true) {
    $isAdmin = true;
} else {
    $isAdmin = false;
}

if (isset($_POST["edit"])) {
    $edit = true;
} else {
    $edit = false;
}
if (isset($_POST["deleteBuch"]) && $_POST["deleteBuch"] == "true" && $isAdmin) {
    deleteBuch($_GET["id"]);
    header("Location: Shop.php");
    exit();
}


$bookId = 0;
if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
    $bookId = (htmlspecialchars($_GET["id"]));
} else {
    header("Location: Shop.php");
    exit();
}

if (isset($_SESSION["userid"])) {
    echo "<script>document.querySelector('.login').innerHTML = '<a href=\"logout.php\">Logout</a>'</script>";
}

if (isset($_POST["submit"]) && $bookId != 0 && $isAdmin) {
    $updatebuecher = "UPDATE buecher SET kurztitle = :title, autor = :autor, kategorie = :kategorie, zustand = :zustand, title = :beschreibung WHERE id = :bookId";
    // da um den Key herauszufinden
    /* foreach ($_POST as $key => $value) {
         echo "<script>console.log('$key: $value')</script>";
     }*/

    $statement = $conn->prepare($updatebuecher);
    $statement->bindParam(":title", $_POST["title"]);
    $statement->bindParam(":autor", $_POST["autor"]);
    $statement->bindParam(":kategorie", $_POST["Genre"]);
    $statement->bindParam(":zustand", $_POST["zustand"]);
    $statement->bindParam(":beschreibung", $_POST["beschreibung"]);
    $statement->bindParam(":bookId", $bookId);
    $statement->execute();
    header("Location: Shop.php");
    exit();
} else if (isset($_POST["submit"]) && $isAdmin && $bookId == 0) {


    $getNewId = "SELECT MAX(id) FROM buecher";
    $statement = $conn->prepare($getNewId);
    $statement->execute();
    $result = $statement->fetch();
    $newId = $result[0] + 1;


    $addBook = "INSERT INTO buecher (id, kurztitle, autor, kategorie, zustand, title) VALUES (:id, :title, :autor, :kategorie, :zustand, :beschreibung)";
    $statement = $conn->prepare($addBook);
    $statement->bindParam(":id", $newId);
    $statement->bindParam(":title", $_POST["title"]);
    $statement->bindParam(":autor", $_POST["autor"]);
    $statement->bindParam(":kategorie", $_POST["Genre"]);
    $statement->bindParam(":zustand", $_POST["zustand"]);
    $statement->bindParam(":beschreibung", $_POST["beschreibung"]);
    $statement->execute();
    header("Location: Shop.php");
    exit();
}










?>

<head>
    <script>
        function auto_grow(element) {
            element.style.height = "5px";
            element.style.height = (element.scrollHeight) + "px";
        }
    </script>

    <meta charset="UTF-8">
    <link rel="icon" type="image/svg" href="Bilder/Shop/Logo.svg">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
        integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css"
        integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
    <link href="https://mdbcdn.b-cdn.net/wp-content/themes/mdbootstrap4/docs-app/css/compiled-4.20.0.min.css"
        rel="stylesheet" id="bootstrap-css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
    <title>Bookling</title>
</head>

<body>
    <header>
        <div class="inline">
            <h1 id="title" class="inline-child">
                <img src="../Bilder/Logo.svg" alt="logo von bookling" height="50" width="50">Bookling
            </h1>
        </div>
        <nav>
            <ul>
                <li><a href="../Index.php">Home</a></li>
                <li><a href="Shop.php" class="active">Unser Angebot</a></li>
                <?php
                if (isset($isAdmin) && $isAdmin) {
                    echo "<li><a href=\"../Admin/Admin.php\">Admin</a></li>";
                    echo "<li class=\"login\"><a href=\"../Login/Logout.php\">Logout</a></li>";
                } else {
                    echo "<li class=\"login\"><a href=\"../Login/Login.php\">Login</a></li>";
                }
                ?>
            </ul>
        </nav>
    </header>

    <?php
    try {
        $book = "SELECT * FROM buecher b 
                 LEFT JOIN zustaende z ON b.zustand = z.zustand
                 LEFT JOIN kategorien k ON b.kategorie = k.id
                 WHERE b.id = :bookId";

        $statement = $conn->prepare($book);
        $statement->bindParam(":bookId", $bookId);
        $statement->execute();
        $result = $statement->fetch();
        /*foreach ($result as $key => $value) {
            echo $key.":  ".$value . "<br>";
        }*/
    } catch (Exception $e) {
        echo $e->getMessage();
    }

    if (!$result && !$bookId == 0) {
        header("Location: Shop.php");
        exit();
    }


    if ((isset($edit) && $edit) && $isAdmin) {
        // Bearbeiten von schon vorhandenen Daten
        ?>

        <div class="main">
            <div class="container">
                <?php
                echo "<form method='POST' action='" . $_SERVER['PHP_SELF'] . "?id=" . $bookId . "'>";
                echo "<h1><input class='titlebox' maxlength='100' type='text' name='title' value='" . $result['kurztitle'] . "'></h1>";
                ?>
            </div>
            <div class="row">
                <div>
                    <img src="../Bilder/Buch.png" width="500" alt="Buchcover" class="img-fluid">
                </div>
                <div class="nextto">
                    <table>
                        <tbody>
                            <tr>
                                <td>Autor: </td>
                                <td>
                                    <?php
                                    echo "<input type='text' name='autor' value='" . $result["autor"] . "'>";
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Genre: </td>
                                <td>
                                    <select name="Genre" id="a">
                                        <option value="" <?php echo ($result['4'] == "" ? "selected" : ""); ?>>Genre</option>
                                        <option value="1" <?php echo ($result['4'] == "1" ? "selected" : ""); ?>>Alte Drucke
                                        </option>
                                        <option value="2" <?php echo ($result['4'] == "2" ? "selected" : ""); ?>>Geografie und
                                            Reisen</option>
                                        <option value="3" <?php echo ($result['4'] == "3" ? "selected" : ""); ?>>
                                            Geschichtswissenschaften</option>
                                        <option value="4" <?php echo ($result['4'] == "4" ? "selected" : ""); ?>>
                                            Naturwissenschaften</option>
                                        <option value="5" <?php echo ($result['4'] == "5" ? "selected" : ""); ?>>Kinderbücher
                                        </option>
                                        <option value="6" <?php echo ($result['4'] == "6" ? "selected" : ""); ?>>Moderne
                                            Literatur und Kunst</option>
                                        <option value="7" <?php echo ($result['4'] == "7" ? "selected" : ""); ?>>Moderne Kunst
                                            und Künstlergraphik</option>
                                        <option value="8" <?php echo ($result['4'] == "8" ? "selected" : ""); ?>>
                                            Kunstwissenschaften</option>
                                        <option value="9" <?php echo ($result['4'] == "9" ? "selected" : ""); ?>>Architektur
                                        </option>
                                        <option value="10" <?php echo ($result['4'] == "10" ? "selected" : ""); ?>>Technik
                                        </option>
                                        <option value="11" <?php echo ($result['4'] == "11" ? "selected" : ""); ?>>
                                            Naturwissenschaften - Medizin</option>
                                        <option value="12" <?php echo ($result['4'] == "12" ? "selected" : ""); ?>>Ozeanien
                                        </option>
                                        <option value="13" <?php echo ($result['4'] == "13" ? "selected" : ""); ?>>Afrika
                                        </option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Katalog: </td>
                                <td>
                                    <select name="katalog" id="katalog">
                                        <option value="" <?php echo ($result['katalog'] == "%" ? "selected" : ""); ?>>11
                                        </option>
                                        <option value="11" <?php echo ($result['katalog'] == "11" ? "selected" : ""); ?>>12
                                        </option>
                                        <option value="12" <?php echo ($result['katalog'] == "12" ? "selected" : ""); ?>>13
                                        </option>
                                        <option value="13" <?php echo ($result['katalog'] == "13" ? "selected" : ""); ?>>14
                                        </option>
                                        <option value="14" <?php echo ($result['katalog'] == "14" ? "selected" : ""); ?>>15
                                        </option>
                                        <option value="15" <?php echo ($result['katalog'] == "15" ? "selected" : ""); ?>>16
                                        </option>
                                        <option value="16" <?php echo ($result['katalog'] == "16" ? "selected" : ""); ?>>17
                                        </option>
                                        <option value="17" <?php echo ($result['katalog'] == "17" ? "selected" : ""); ?>>17
                                        </option>
                                        <option value="18" <?php echo ($result['katalog'] == "18" ? "selected" : ""); ?>>18
                                        </option>
                                        <option value="19" <?php echo ($result['katalog'] == "19" ? "selected" : ""); ?>>19
                                        </option>

                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Zustand: </td>
                                <td>
                                    <?php
                                    echo '<input type="radio" name="zustand" value="G"' . ($result["zustand"] == "G" ? "checked" : "") . '>  Gut<br>';
                                    echo '<input type="radio" name="zustand" value="M"' . ($result["zustand"] == "M" ? "checked" : "") . '>  Mittel<br>';
                                    echo '<input type="radio" name="zustand" value="S"' . ($result["zustand"] == "S" ? "checked" : "") . '>  Schlecht';
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Beschreibung: </td>
                                <td>
                                    <?php
                                    echo '<textarea onclick="auto_grow(this)" oninput="auto_grow(this)" name="beschreibung" class="text">' . $result["title"] . '</textarea><br>';
                                    echo '<input type="submit" name="submit" value="Ändern">';
                                    ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    </form>
                </div>
            </div>
        </div>

        <?php

    } else if ($_GET['new'] && $isAdmin && $bookId == 0) {
        // Neue Daten erfassen
        ?>

            <div class="main">
                <?php
                echo "<form method='POST' action='" . $_SERVER['PHP_SELF'] . "?id=" . $bookId . "'>";
                ?>
                <div class="container">
                    <?php

                    echo "<h1><input class='titlebox' maxlength='100' type='text' name='title'></h1>";
                    ?>
                </div>
                <div class="row">
                    <div>
                        <img src="../Bilder/Buch.png" width="500" alt="Buchcover" class="img-fluid">
                    </div>
                    <div class="nextto">
                        <table>
                            <tbody>
                                <tr>
                                    <td>Autor: </td>
                                    <td>
                                        <?php
                                        echo " <input type='text' name='autor'>";
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Genre: </td>
                                    <td>
                                        <select name="Genre" id="genre">
                                            <option value="">Genre</option>
                                            <option value="1">Alte Drucke</option>
                                            <option value="2">Geografie und Reisen</option>
                                            <option value="3">Geschichtswissenschaften</option>
                                            <option value="4">Naturwissenschaften</option>
                                            <option value="5">Kinderbücher</option>
                                            <option value="6">Moderne Literatur und Kunst</option>
                                            <option value="7">Moderne Kunst und Künstlergraphik</option>
                                            <option value="8">Kunstwissenschaften</option>
                                            <option value="9">Architektur</option>
                                            <option value="10">Technik</option>
                                            <option value="11">Naturwissenschaften - Medizin</option>
                                            <option value="12">Ozeanien</option>
                                            <option value="13">Afrika</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Katalog: </td>
                                    <td>
                                        <select name="katalog" id="katalog">
                                            <option value="">Katalog</option>
                                            <option value="11">11</option>
                                            <option value="12">12</option>
                                            <option value="13">13</option>
                                            <option value="14">14</option>
                                            <option value="15">15</option>
                                            <option value="16">16</option>
                                            <option value="17">17</option>
                                            <option value="18">18</option>
                                            <option value="19">19</option>

                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Zustand: </td>
                                    <td>
                                        <?php
                                        echo '<input type="radio" name="zustand" value="G">  Gut<br>';
                                        echo '<input type="radio" name="zustand" value="M">  Mittel<br>';
                                        echo '<input type="radio" name="zustand" value="S">  Schlecht';
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Beschreibung: </td>
                                    <td>
                                        <?php
                                        echo '<textarea onclick="auto_grow(this)" oninput="auto_grow(this)" name="beschreibung" class="text"></textarea><br>';
                                        echo '<input type="submit" name="submit" value="Hinzufügen">';
                                        ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                    
                </div></form>
            </div>

        <?php





    } else {
        //anzeige von Daten
        ?>


            <div class="main">
                <div class="container">

                    <?php
                    echo "<h1>" . $result["kurztitle"] . "</h1>";
                    ?>
                </div>
                <div class="row">
                    <div>
                        <img src="../Bilder/Buch.png" width="500" alt="Buchcover" class="img-fluid">
                    </div>

                    <table class="nextto">
                        <tbody>
                            <tr>
                                <td>Author: </td>
                                <td>
                                <?php if (strlen(trim($result["autor"])) == 0) {
                                    echo "Unbekannt";
                                } else {
                                    echo $result["autor"];
                                } ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Genre: </td>
                                <td>
                                <?php echo $result["kategorie"]; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Zustand: </td>
                                <td>
                                <?php echo $result["beschreibung"]; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Beschreibung: </td>
                                <td>
                                <?php echo $result["title"]; ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>


                </div>

        <?php } ?>
        <footer>
            <table>
                <tr>
                    <th class="links">©2024 Bookling 2024</th>
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