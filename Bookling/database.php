<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "books";
// Kopiert von https://www.w3schools.com/php/php_mysql_connect.asp
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo"Connection failed: " . $e->getMessage();
}

function deleteKunde($id){
    global $conn;
    $stmt = $conn->prepare("DELETE FROM kunden WHERE kid=:id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    return "Kunde gelöscht";
}
function deleteBuch($id){
    global $conn;
    $stmt = $conn->prepare("DELETE FROM buecher WHERE id=:id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
}


