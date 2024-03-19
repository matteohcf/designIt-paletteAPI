<?php
/* TODO */
include_once("config.php");

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Headers: X-Requested-With");

if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    $id = $_GET['id'];

    $connessione = new mysqli($db_host, $db_user, $db_password, $db_name);

    if ($connessione->connect_error) {
        die("Errore di connessione: " . $connessione->connect_error);
    }

    $sql = "DELETE FROM palettes WHERE id_palette = '$id'";

    if ($connessione->query($sql) === TRUE) {
        echo json_encode(array("message" => "Palette eliminata con successo"));
    } else {
        echo json_encode(array("error" => "Errore durante l'eliminazione della palette: " . $connessione->error));
    }

    $connessione->close();
} else {
    http_response_code(405); // Metodo non consentito
    echo json_encode(array("error" => "Metodo non consentito"));
}
?>
