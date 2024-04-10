<?php

include_once("config.php");

// Abilita CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// CONNESSIONE AL DB
$connessione = new mysqli($db_host, $db_user, $db_password, $db_name);

// Verifica la connessione
if ($connessione->connect_error) {
    die("Errore di connessione: " . $connessione->connect_error);
}

// Ottieni l'id dell'utente dall'input POST
$data = json_decode(file_get_contents("php://input"), true);
$id_utente = $data["id_utente"];

// Sanitizzazione dell'id dell'utente per evitare SQL injection
$id_utente = $connessione->real_escape_string($id_utente);

// Verifica se l'utente ha già salvato qualche palette
$query_check_saved_palette = "SELECT id_palette FROM save_palettes WHERE id_utente = '$id_utente'";
$result_check_saved_palette = $connessione->query($query_check_saved_palette);

// Array per memorizzare gli ID delle palette salvato
$saved_palettes = array();

// Elabora il risultato della query per determinare gli ID delle palette già salvate
if ($result_check_saved_palette->num_rows > 0) {
    while ($row = $result_check_saved_palette->fetch_assoc()) {
        $saved_palettes[] = $row['id_palette'];
    }
}

// Esempio di come ottenere i dati dalla query e preparare una risposta JSON
$response_data = array(
    "saved_palettes" => $saved_palettes
);

// Invia la risposta come JSON
header('Content-Type: application/json');
echo json_encode($response_data);

// Chiudi la connessione
$connessione->close();

?>