<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <!-- allgemeine PHP -->
    <?php
    session_start();
    include "database.php";
    //lese session aus
    if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == true) {
        $isAdmin = $_SESSION["isAdmin"];
    } else {
        $isAdmin = false;
    }
    ?>
    <!-- allgemeine Links -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
    </script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/svg" href="Logo.svg">
    <title>Bookling</title>
</head>

<body>
    <header>
        <!-- <img src="#" alt="Bookling Logo"> -->
        <div class="inline">
            <h1 id="title" class="inline-child"><img src="Bilder/Logo.svg" alt="logo von bookling" height="50" width="50">Bookling
            </h1>
            <div class="inline-child  search">
                <form action="Shop/Shop.php" method="GET">
                    <?php
                    echo '<input class="" type="text" name="search" placeholder="Search">';
                    ?>
                    <button class="btn-rounded " type="submit">Search</button>
                </form>
            </div>
        </div>
        <nav>
            <ul>
                <li><a href="Index.php" class="active">Home</a></li>
                <li><a href="Shop/Shop.php">Unser Angebot</a></li>
                <?php
                if (isset($isAdmin) && $isAdmin) {
                    echo "<li><a href=\"Admin/Admin.php\">Admin</a></li>";
                    echo "<li  class=\"login\"><a href=\"Login/Logout.php\">Logout</a></li>";
                } else {

                    echo "<li  class=\"login\"><a href=\"Login/Login.php\">Login</a></li>";
                }
                ?>
            </ul>
        </nav>
    </header>

    <div class="main">



        <h3><a href="Shop/Shop.php" class="ueberschrift">Unsere besten Angebote</a></h3>
        <div class="book">
            <?php
            //pick 5 random books to display 

            // Count all books
            $sql = "SELECT COUNT(*) FROM buecher";
            $count = $conn->query($sql)->fetchColumn();


            // Generate 5 random book IDs
            for ($i = 0; $i < 5; $i++) {
                $random = rand(1, $count); // Generate a random number within the valid range of book IDs
                $sql = "SELECT b.id AS book_id, b.*, z.*, k.* 
            FROM buecher b
            LEFT JOIN zustaende z ON b.zustand = z.zustand
            LEFT JOIN kategorien k ON b.kategorie = k.id
            WHERE b.id = :random";
                $stmt = $conn->prepare($sql);
                $stmt->execute(['random' => $random]);
                $row = $stmt->fetch();


                if ($row) {
                    echo "<a class=\"CloseLink\" href=\"Shop/closeLook.php?id=" . $row['book_id'] . "\">";
                    echo "<div class=\"angebot\">";
                    echo "<p>" . substr($row['kurztitle'], 0, 20) . "...</p>";
                    echo "<img src=\"Bilder/Buch.png\" alt=\"Buchcover\" class=\"cover\" width=\"150\">";
                    if (strlen(trim($row['autor'])) == 0) {
                        echo "<p>Autor: Unbekannt</p>";
                    } else {
                        echo "<p>Autor: " . $row['autor'] . "</p>";
                    }
                    echo "<p>Genre: " . $row['kategorie'] . "</p>";
                    echo "<p>Zustand: " . $row['beschreibung'] . "</p>";
                    echo "</div>";
                    echo "</a>";
                } else {
                    $i--;
                }
            }

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
                    <a href="#"><img src="Bilder/Instagramm.png" alt="Instagramm" height="30" width="30"></a>
                    <a href="#"><img src="Bilder/Facebook.png" alt="Facebook" height="30" width="30"></a>
                    <a href="#"><img src="Bilder/Whatsapp.png" alt="Whatsapp" height="30" width="30"></a>
                </td>
            </tr>

        </table>
    </footer>
</body>

</html>