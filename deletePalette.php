<?php
/* TODO */
include_once("config.php");

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");

// CONNESSIONE AL DB
$connessione = new mysqli($db_host, $db_user, $db_password, $db_name);

if ($connessione->connect_error) {
    echo "Errore di connessione: " . $connessione->connect_error;
} else {
    $id = $_GET['id'];
}
    $sql = "DELETE FROM palettes WHERE id_palette = '$id'";

    if ($connessione->query($sql) === TRUE) {
        echo json_encode(array("message" => "Palette eliminata con successo con ID: " . $id));
    } else {
        echo json_encode(array("error" => "Errore durante l'eliminazione della palette: " . $connessione->error));
    }

$connessione->close();

?>
