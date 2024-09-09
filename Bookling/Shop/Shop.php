<!DOCTYPE html>
<html lang="de">


<head>
    <meta charset="UTF-8">
    <?php
    session_start();
    //alle includes
    include '../database.php';


    //lese alles wichtige aus der session aus
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
        $isAdmin = $_SESSION['isAdmin'];
    } else {
        $isAdmin = false;
    }
    // Elemente aus dem $get nehmen und speichern
    $getParams = http_build_query($_GET);
    //lese alle wichtigen daten aus GET aus
    //lese alle filter für die anzeige aus falls vorhanden
    $filter = [
        'search' => $_GET['search'] ?? '',
        'Katalog' => $_GET['Katalog'] ?? '%',
        'Zustand' => $_GET['Zustand'] ?? '%',
        'Genre' => $_GET['Genre']?? '%',
        'Sortieren' => $_GET['Sortieren'] ?? 'b.id'
    ];
    // Die aktuelle Seite ermitteln 
    $AktuelleSeite = $_GET["seite"] ?? 1;

    //passe login oder logout button an
    if (isset($_SESSION['userid'])) {
        echo "<script>document.querySelector('.login').innerHTML = '<a href=\"logout.php\">Logout</a>'</script>";
    }



    //handeing des seiten managements

    //hier werden die Daten abgefragt und gezählt um die Anzahl der Seiten zu ermitteln
    $sqlCount = "SELECT COUNT(*) FROM buecher b 
             WHERE (b.kurztitle LIKE '%" . $filter['search'] . "%' 
                    OR b.autor LIKE '%" . $filter['search'] . "%' 
                    OR b.title LIKE '%" . $filter['search'] . "%') 
                   AND b.katalog LIKE '%" . $filter['Katalog'] . "%' 
                   AND b.zustand LIKE '%" . $filter['Zustand'] . "%' 
                   AND b.kategorie LIKE '%" . $filter['Genre'] . "%'";

    //um die anzahl der datensätze zu ermitteln für die seitenanzahl und die anzeige der Suchergebnisse
    //hier werden die Statement vorbereitet und ausgeführt
    $stmt = $conn->prepare($sqlCount);
    $stmt->execute();
    $AnzahlDatensaetze = $stmt->fetchColumn();

    // Anzeige der Datensätze pro Seite 
    $DatensaetzeSeite = 18;

    // Die Anzahl der Seiten ermitteln 
    $AnzahlSeiten = ceil($AnzahlDatensaetze / $DatensaetzeSeite);

    // Den Wert überprüfen und ggf. ändern 
    $AktuelleSeite = ctype_digit((string) $AktuelleSeite) ? $AktuelleSeite : 1;
    $AktuelleSeite = $AktuelleSeite < 1 || $AktuelleSeite > $AnzahlSeiten ? 1 : $AktuelleSeite;

    // Den Versatz ermitteln 
    $Versatz = $AktuelleSeite * $DatensaetzeSeite - $DatensaetzeSeite;
    ?>


    <?php 

#einfügen eines Neuen Buches

    



    ?>







    <script>
        // Attach event listener to the edit button
        document.querySelector('.editButton').addEventListener('click', function() {
            // Submit form to set edit mode to true
            this.closest('form').submit();
        });
        // JavaScript zum überschreiben der normalen Enter-Funktion beim Input-Feld
        document.getElementById("pageForm").addEventListener("submit", function(event) {
            // Verhindern des Standardformular-Submit-Verhaltens
            event.preventDefault();

            // Den Wert des Eingabefelds für die Seitenzahl erhalten
            var pageInputValue = document.getElementById("pageInput").value;

            // Die GET-Parameter extrahieren
            var getParams = "<?php echo $getParams; ?>";

            // Bestimmen der neuen URL
            var newUrl = currentPagePath + "?" + getParams + "&seite=" + pageInputValue;

            // Weiterleitung zur neuen URL
            window.location.href = newUrl;
        })
    </script>



    <!--Das ist das Logo der Webseite-->
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
            <span class="inline-child  search">

                <?php
                echo '<input class="" type="text" name="search" value="' . $filter['search'] . '" placeholder="Search" form="filterForm">';
                ?>
                <button form="filterForm" class="btn-rounded " type="submit">Search</button>
            </span>
        </div>
        <nav>
            <ul>
                <li><a href="../Index.php">Home</a></li>
                <li><a href="Shop.php" class="active">Unser Angebot</a></li>
                <?php
                if (isset($isAdmin) && $isAdmin) {
                    echo "<li><a href=\"../Admin/Admin.php\">Admin</a></li>";
                    echo "<li  class=\"login\"><a href=\"../Login/Logout.php\">Logout</a></li>";
                } else {
                    echo "<li  class=\"login\"><a href=\"../Login/Login.php\">Login</a></li>";
                }
                ?>
            </ul>
        </nav>
    </header>

    <div class="main">
        <!--Hier ist das Form welches zum Sortieren und Filtern des Angebotes Dient-->
        <div>
            <form action="Shop.php" method="get" id="filterForm" class="filter">
                <!--Hier ist das Dropdown Menu welches dafür zuständig ist den Richtigen Katalog auszuwählen-->
                <select class="sort" name="Katalog" id="g" onchange="this.form.submit()">
                    <option value="%" <?php echo ($filter['Katalog'] == "%" ? "selected" : ""); ?>>Katalog</option>
                    <option value="11" <?php echo ($filter['Katalog'] == "11" ? "selected" : ""); ?>>11</option>
                    <option value="12" <?php echo ($filter['Katalog'] == "12" ? "selected" : ""); ?>>12</option>
                    <option value="13" <?php echo ($filter['Katalog'] == "13" ? "selected" : ""); ?>>13</option>
                    <option value="14" <?php echo ($filter['Katalog'] == "14" ? "selected" : ""); ?>>14</option>
                    <option value="15" <?php echo ($filter['Katalog'] == "15" ? "selected" : ""); ?>>15</option>
                    <option value="16" <?php echo ($filter['Katalog'] == "16" ? "selected" : ""); ?>>16</option>
                    <option value="17" <?php echo ($filter['Katalog'] == "17" ? "selected" : ""); ?>>17</option>
                    <option value="18" <?php echo ($filter['Katalog'] == "18" ? "selected" : ""); ?>>18</option>
                    <option value="19" <?php echo ($filter['Katalog'] == "19" ? "selected" : ""); ?>>19</option>
                </select>
                <!--Hier ist die Auswahl wo man den Zustand Filtern kann-->
                <select class="sort" name="Zustand" id="z" onchange="this.form.submit()">
                    <option value="%" <?php echo ($filter['Zustand'] == "%" ? "selected" : ""); ?>>Zustand</option>
                    <option value="G" <?php echo ($filter['Zustand'] == "G" ? "selected" : ""); ?>>Gut</option>
                    <option value="M" <?php echo ($filter['Zustand'] == "M" ? "selected" : ""); ?>>Mittel</option>
                    <option value="S" <?php echo ($filter['Zustand'] == "S" ? "selected" : ""); ?>>Schlecht</option>
                </select>
                <!--Hier ist ein Dropdown welches für die Auswahl des Genres ist-->
                <select class="sort" name="Genre" id="a" onchange="this.form.submit()">
                    <option value="%" <?php echo ($filter['Genre'] == "%" ? "selected" : ""); ?>>Genre</option>
                    <option value="1" <?php echo ($filter['Genre'] == "1" ? "selected" : ""); ?>>Alte Drucke</option>
                    <option value="2" <?php echo ($filter['Genre'] == "2" ? "selected" : ""); ?>>Geografie und Reisen
                    </option>
                    <option value="3" <?php echo ($filter['Genre'] == "3" ? "selected" : ""); ?>> Geschichtswissenschaften
                    </option>
                    <option value="4" <?php echo ($filter['Genre'] == "4" ? "selected" : ""); ?>>Naturwissenschaften
                    </option>
                    <option value="5" <?php echo ($filter['Genre'] == "5" ? "selected" : ""); ?>>Kinderbücher</option>
                    <option value="6" <?php echo ($filter['Genre'] == "6" ? "selected" : ""); ?>>Moderne Literatur und
                        Kunst</option>
                    <option value="7" <?php echo ($filter['Genre'] == "7" ? "selected" : ""); ?>>Moderne Kunst und
                        Künstlergraphik</option>
                    <option value="8" <?php echo ($filter['Genre'] == "8" ? "selected" : ""); ?>>Kunstwissenschaften
                    </option>
                    <option value="9" <?php echo ($filter['Genre'] == "9" ? "selected" : ""); ?>>Architektur</option>
                    <option value="10" <?php echo ($filter['Genre'] == "10" ? "selected" : ""); ?>>Technik</option>
                    <option value="11" <?php echo ($filter['Genre'] == "11" ? "selected" : ""); ?>>Naturwissenschaften -
                        Medizin</option>
                    <option value="12" <?php echo ($filter['Genre'] == "12" ? "selected" : ""); ?>>Ozeanien</option>
                    <option value="13" <?php echo ($filter['Genre'] == "13" ? "selected" : ""); ?>>Afrika</option>
                </select>
                <!--Das ist das Dropdown Menu welches für das Sortieren zuständig ist-->
                <select class="sort" name="Sortieren" id="sort" onchange="this.form.submit()">
                    <option value="b.id" <?php echo ($filter['Sortieren'] == "b.id" ? "selected" : ""); ?>>Sortieren
                    </option>
                    <option value="b.autor asc" <?php echo ($filter['Sortieren'] == "b.autor asc" ? "selected" : ""); ?>>
                        Autor A-Z</option>
                    <option value="b.autor desc" <?php echo ($filter['Sortieren'] == "b.autor desc" ? "selected" : ""); ?>>Autor Z-A</option>
                    <option value="b.nummer asc" <?php echo ($filter['Sortieren'] == "b.nummer asc" ? "selected" : ""); ?>>Buchnummer auf</option>
                    <option value="b.nummer desc" <?php echo ($filter['Sortieren'] == "b.nummer desc" ? "selected" : ""); ?>>Buchnummer ab</option>
                    <option value="b.id asc" <?php echo ($filter['Sortieren'] == "b.id asc" ? "selected" : "") ?>>ID auf
                    </option>
                    <option value="b.id desc" <?php echo ($filter['Sortieren'] == "b.id desc" ? "selected" : ""); ?>>
                        Buch ID ab</option>
                </select>
                <?php if ($isAdmin == true) {
                 echo '<input class="addNew" type="button" value="+" name="new" onclick="window.location.replace(\'closeLook.php?id=0&new=true\')">';
                } ?>
            </form>
        </div>

        <div class="book">
            <?php
            //an extremely complicated sql quary. once humam kind will understand this but for now eveolution has not reached that plane yet.

            $sql = "SELECT b.id AS book_id, b.*, z.*, k.* 
        FROM buecher b
        LEFT JOIN zustaende z ON b.zustand = z.zustand
        LEFT JOIN kategorien k ON b.kategorie = k.id
        WHERE (b.kurztitle LIKE :search 
               OR b.autor LIKE :search 
               OR b.title LIKE :search)
               AND b.katalog LIKE :Katalog
               AND b.zustand LIKE :Zustand
               AND b.kategorie LIKE :Genre
        ORDER BY " . $filter['Sortieren'] . "
        LIMIT 18
        OFFSET :offset";



            //Hier werden BindValues erstellt welche dann für SQL abfragen genuzt werden.
            try {
                $stmt = $conn->prepare($sql);
                $stmt->bindValue(':search', '%' . $filter['search'] . '%', PDO::PARAM_STR);
                $stmt->bindValue(':Katalog', $filter['Katalog'], PDO::PARAM_STR);
                $stmt->bindValue(':Zustand', $filter['Zustand'], PDO::PARAM_STR);
                $stmt->bindValue(':Genre', $filter['Genre'], PDO::PARAM_STR);
                $stmt->bindValue(':direction', $filter['Sortieren'], PDO::PARAM_STR);
                $stmt->bindValue(':offset', ($AktuelleSeite - 1) * 18, PDO::PARAM_INT);
                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($results as $row) {

                    echo "<a class=\"CloseLink\" href=\"closeLook.php?id=" . $row['book_id'] . "\">";
                    //   echo $row['id'];
                    echo "<div class=\"angebot\">";
                    echo "<p>" . substr($row['kurztitle'], 0, 20) . "...</p>";
                    echo "<img src=\"../Bilder/Buch.png\" alt=\"Buchcover\" class=\"cover\" width=\"150\">";
                    if (strlen(trim($row['autor'])) == 0) {
                        echo "<p>Autor: Unbekannt</p>";
                    } else {
                        echo "<p>Autor: " . $row['autor'] . "</p>";
                    }
                    echo "<p>Genre: " . $row['kategorie'] . " </p>";
                    echo "<p>Zustand: " . $row['beschreibung'] . "</p>";
                    if ($isAdmin == true) {
                        echo "<form method='post' action='closeLook.php?id=" . $row['book_id'] . "'>";
                        echo '<button type="submit" class="editButton invert" name="edit" value="true">&#9998</button>';
                        //Delete button
                        echo '<button type="submit" class="deleteButton invert" name="deleteBuch" value="true" >&#128465;</button>';
                        echo "</form>";
                    }
                    echo "</div>";
                    echo "</a>";
                }
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
            ?>
        </div>

        <?php


        // Formular für die Seitennavigation ausgeben
        echo '<form method="GET" autocomplete="off" id="pageForm">';
        // Verstecktes Eingabefeld hinzufügen, um vorhandene GET-Parameter beizubehalten
        echo
        // Link für vorherige Seite oder Pfeilsymbol, falls keine vorherige Seite vorhanden ist
        (($AktuelleSeite - 1) > 0 ?
            '<a href="?' . $getParams . '&seite=' . ($AktuelleSeite - 1) . '">&#9668;</a>' :
            ' &#9668;') .
            // Label und Eingabefeld für die Seitenauswahl
            ' <label>Seite <input type="text" value="' . $AktuelleSeite . '" name="seite" size="3" 
    title="Seitenzahl eingeben und Eingabetaste betätigen" id="pageInput"> von ' . $AnzahlSeiten . '</label>' .
            // Link für nächste Seite oder Pfeilsymbol, falls keine nächste Seite vorhanden ist
            (($AktuelleSeite + 1) <= $AnzahlSeiten ?
                ' <a href="?' . $getParams . '&seite=' . ($AktuelleSeite + 1) . '">&#9658;</a>' :
                ' &#9658;') .
            '</form>'; ?>
    </div>
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