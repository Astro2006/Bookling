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
        header('Location: ../Index.php');
    }

    //lese alle wichtigen daten aus GET aus
    //lese alle filter für die anzeige aus falls vorhanden
    $filteradmin = [
        'search' => $_POST['search'] ?? '%',
        'geschlecht' => $_POST['geschlecht'] ?? '_',
        'mail' => $_POST['mail'] ?? '',
        'year' => $_POST['year'] ?? '%'
    ];
    // Die aktuelle Seite ermitteln 
    $AktuelleSeite = $_GET["seite"] ?? 1;


    //hier werden die Statement vorbereitet und ausgeführt
    ?>
    <script>
        function confirmAction(kid) {
            var result = confirm("Willst du den Kunden wirklich löschen?");
            if (result) {
                window.location.href = "./delete.php?id=" + kid;
            } else {
                // If user clicks Cancel in the confirm dialog
                alert("You clicked Cancel!");
                // You can handle cancel action here if needed
            }
        }
        
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
            <form class="inline-child  search" method="GET" action="../Shop/Shop.php">

                <?php
                echo '<input class="" type="text" name="search" placeholder="Search">';
                ?>
                <button class="btn-rounded " type="submit">Search</button>
            </form>
        </div>
        <nav>
            <ul>
                <li><a href="../Index.php">Home</a></li>
                <li><a href="../Shop/Shop.php">Unser Angebot</a></li>

                <li><a href="Admin.php#adminpass" class="active">Admin</a></li>
                <li class="login"><a href="../Login/Logout.php">Logout</a></li>

            </ul>
        </nav>
    </header>

    <div class="main">
        <!--Hier ist das Form welches zum Sortieren und Filtern des Angebotes Dient-->
        <div>
            <form action="Admin.php" method="post" id="adminform" class="filter">
                <span class="kundensuche">
                    <?php
                    echo '<input class="ksearch" type="text" name="search" placeholder="Search" value="' . ($filteradmin['search']== "%" ? "" : "") . '">';
                    ?>
                    <button class="btn-rounded addNew" type="submit">Search</button>
                </span>
                <select class="sort" name="mail" id="mail" onchange="this.form.submit()">
                    <option value="" <?php echo ($filteradmin['mail'] == "" ? "selected" : ""); ?>>Kontakt per Mail erwünscht</option>
                    <option value="1" <?php echo ($filteradmin['mail'] == "1" ? "selected" : ""); ?>>ja</option>
                    <option value="0" <?php echo ($filteradmin['mail'] == "0" ? "selected" : ""); ?>>Nein</option>

                </select>
                <!--Hier ist ein Dropdown welches für die Auswahl des Genres ist-->
                <select class="sort" name="geschlecht" id="geschlecht" onchange="this.form.submit()">
                    <option value="%" <?php echo ($filteradmin['geschlecht'] == "" ? "selected" : ""); ?>>Geschlecht</option>
                    <option value="M" <?php echo ($filteradmin['geschlecht'] == "M" ? "selected" : ""); ?>>Männlich</option>
                    <option value="F" <?php echo ($filteradmin['geschlecht'] == "F" ? "selected" : ""); ?>>Weiblich</option>
                </select>
                <!-- Hier kann man nach den Jahren sortieren -->
                <select class="sort" name="year" id="year" onchange="this.form.submit()">
                    <option value="%" <?php echo ($filteradmin['year'] == "%" ? "selected" : ""); ?>>Jahr</option>
                    <?php
                    $sql = "SELECT DISTINCT YEAR(kunde_seit) as jahr FROM kunden ORDER BY jahr DESC";
                    $stmt = $conn->query($sql);
                    foreach ($stmt as $row) {
                        echo '<option value="' . $row['jahr'] . '" ' . ($filteradmin['year'] == $row['jahr'] ? "selected" : "") . '>' . $row['jahr'] . '</option>';
                    }
                    ?>
                </select>
                <?php if ($isAdmin == true) {

                    echo '<input class="addNew" type="button" value="+" name="new" onclick="window.location.replace(\'closeLook.php?id=new\')">';
                } ?>
            </form>
        </div>

        <div class="book">
            <?php
            try {
                $adminsql = "SELECT * FROM kunden k
            Where (k.vorname LIKE :search
            or k.name LIKE :search)
            and k.geschlecht LIKE :geschlecht
                " . ($filteradmin['mail'] != '' ? ' and k.kontaktpermail = :mail' : '') . "
            and YEAR(k.kunde_seit) LIKE :year
            ORDER BY k.kid ASC";

                $stmt = $conn->prepare($adminsql);
                $stmt->bindValue(':geschlecht', $filteradmin['geschlecht'], PDO::PARAM_STR_CHAR);
                $stmt->bindValue(':mail', $filteradmin['mail'], PDO::PARAM_INT);
                $stmt->bindValue(':year', $filteradmin['year'], PDO::PARAM_STR_CHAR);
                $stmt->bindValue(':search', '%' . $filteradmin['search'] . '%', PDO::PARAM_STR);
                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($results as $row) {

                    echo "<a class=\"CloseLink\" href=\"closeLook.php?id=" . $row['kid'] . "\">";
                    echo "<div class=\"Kangebot\">";
                    echo "Vorname: " . $row['vorname'] . "<br>";
                    echo "Nachname: " . $row['name'] . "<br>";
                    echo "E-Mail: " . $row['email'] . "<br>";
                    //Strtotime wandelt das Datum in einen Unix Timestamp um, date formatiert diesen dann in ein lesbares Datum
                    echo " Datum: " . date("d.m.Y", strtotime($row['kunde_seit'])) . "<br>";
                    //Edit button
                    echo "<form method='post' action='closeLook.php?id=" . $row['kid'] . "'>";
                    echo '<button type="submit" class="editButton invert" name="edit" value="true">&#9998;</button>';
                    //Delete button
                    echo '<button type="submit" class="deleteButton invert" name="deletePerson" value="true" >&#128465;</button>';
                    echo "</form>";
                    echo "</div>";
                    echo "</a>";
                }
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            } ?>
        </div>

        <div id="adminpass">
            <h1 id="passlink">Passwort des Admins Ändern</h1>
            <form action="Admin.php" method="post">
                <input type="hidden" name="action" value="changePassword">
                <label for="oldpassword">Altes Passwort:</label><br>
                <input type="password" id="oldPassword" name="oldPassword" required><br>
                <label for="newpassword">Neues Passwort:</label><br>
                <input type="password" id="newPassword" name="newPassword" required><br>
                <label for="newpassword2">Neues Passwort wiederholen:</label><br>
                <input type="password" id="newPassword2" name="newPassword2" required><br>
                <button type="submit">Passwort ändern</button>
            </form>


            <?php
            if (isset($_POST['action']) && $_POST['action'] == "changePassword"){
                $oldPassword = $_POST['oldPassword'];
                $newPassword = $_POST['newPassword'];
                $newPassword2 = $_POST['newPassword2'];
                $username = $_SESSION['username'];

                //überprüfen des inputs
                if ($oldPassword == "" || $newPassword == "" || $newPassword2 == "") {
                    echo "<script>alert('Bitte füllen Sie alle Felder aus');</script>";
                } else {
                if (strlen($newPassword) < 8) {
                    echo "<script>alert('Das Passwort muss mindestens 8 Zeichen lang sein');</script>";
                }else{
                //check if old passwort was correct
                $sql = "SELECT * FROM benutzer  WHERE benutzername = '$username'";
                foreach ($conn->query($sql) as $row) {
                    if (password_verify($oldPassword, $row['passwort'])) {
                        if ($newPassword == $newPassword2) {
                            $hash = password_hash($newPassword, PASSWORD_DEFAULT);
                            $sql = "UPDATE benutzer SET passwort = '$hash' WHERE benutzername = '$username'";
                            $conn->query($sql);
                            echo "<script>alert('Passwort erfolgreich geändert');</script>";
                        } else {
                            echo "<script>alert('Die neuen Passwörter stimmen nicht überein');</script>";
                        }
                    } else {
                        echo "<script>alert('Das alte Passwort ist falsch');</script>";
                    }
                }
            }}}
            ?>
        </div>
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