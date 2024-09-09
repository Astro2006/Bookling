<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <?php
    session_start();
    include "../database.php";
    //read SESSION
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
        header('Location: ../Index.php');
    }
    $failed = false;
    //read username and password from $_post
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = htmlspecialchars(trim($_POST['username']));
        $password = htmlspecialchars(trim($_POST['password']));
        if (strlen($username) < 1 || strlen($username) > 45 || strlen($password) < 8) {
            echo "<script>alert('Unzulässige logindaten')</script>";
            $failed = true;
            exit();
        }



        //check if username and password are correct
        $sql = "SELECT * FROM benutzer  WHERE benutzername = '$username'";
        //echo $password;
        foreach ($conn->query($sql) as $row) {
            if (password_verify($password, $row['passwort'])) {
                $_SESSION['loggedin'] = true;
                $_SESSION['username'] = $username;
                $_SESSION['isAdmin'] = true;
                echo "<script>alert('Login erfolgreich')</script>";
                header("Location: ../index.php");
            } else {
                echo "<script>alert('Passwort oder Username Falsch')</script>";
                $failed = true;
            }
            
        }
        #header("Location: ../index.php");
    }

    ?>

    <!-- Link fuer header -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
    <title>Bookling</title>
</head>

<body>
    <header>
        <div class="inline">
            <h1 id="title"><img src="../Bilder/Logo.svg" alt="logo von bookling" height="50" width="50">Bookling
            </h1>
        </div>
        <nav>
            <ul>
                <li><a href="../Index.php">Home</a></li>
                <li><a href="../Shop/Shop.php" class="active"> Unser Angebot</a></li>
            </ul>
        </nav>
    </header>

    <div class="main">
        <div class="loginField">
            <?php
            if ($failed == true) {
               // echo "<p>Username oder Passwort falsch</p>";
            }
            ?>
            <form action="login.php" method="POST">

                <label for="username">Username:</label><br>
                <?php //if username is set, echo it in the input field
                if (isset($_POST['username'])) {
                    echo "<input type='text' id='username' name='username' value='" . $_POST['username'] . "' required><br>";
                } else {
                    echo "<input type='text' id='username' name='username' value='' required minlength='1' maxlength='45'> <br>";
                }
                ?>

                <label for="password">Password:</label><br>
                <input type="password" id="password" name="password" required minlength="8" ><br>
                <input type="submit" value="Login">


            </form>



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
                <td class="links"></td>
                <td class="mitte">Kontakt</td>
                <td class="rechts">
                    <a href="#"><img src="../Bilder/Instagramm.png" alt="Instagramm" height="30" width="30"></a>
                    <a href="#"><img src="../Bilder/Facebook.png" alt="Facebook" height="30" width="30"></a>
                    <a href="#"><img src="../Bilder/Whatsapp.png" alt="Whatsapp" height="30" width="30"></a>
                </td>
            </tr>
        </table>
    </footer>
</body>

</html>




