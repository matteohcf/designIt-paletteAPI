<?php

/* Abilita CORS se è una richiesta OPTIONS */
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *");
    header("Access-Control-Allow-Methods: *");
    http_response_code(200);
    exit;
}

include_once("config.php");

// Abilita CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");

// CONNESSIONE AL DATABASE
$connessione = new mysqli($db_host, $db_user, $db_password, $db_name);

// Verifica la connessione
if ($connessione->connect_error) {
    die("Errore di connessione al database: " . $connessione->connect_error);
}

// Ottieni l'id della palette e l'id dell'utente dall'input POST
$data = json_decode(file_get_contents("php://input"), true);
$id_palette = $data["id_palette"];
$id_utente = $data["id_utente"];

// Sanitizzazione dell'id della palette per evitare SQL injection
$id_palette = $connessione->real_escape_string($id_palette);

// Verifica se l'utente ha già salvato questa palette
$query_check_save = "SELECT * FROM save_palettes WHERE id_utente = '$id_utente' AND id_palette = '$id_palette'";
$result_check_save = $connessione->query($query_check_save);

if ($result_check_save->num_rows == 0) { // L'utente non ha ancora salvato questa palette

    // Salva la palette per l'utente
    $sql_insert = "INSERT INTO save_palettes (id_utente, id_palette) VALUES ('$id_utente', '$id_palette')";

    if ($connessione->query($sql_insert) === TRUE) {
        echo json_encode(array("id_palette" => $id_palette, "id_utente" => $id_utente, "isSaved" => true));
    } else {
        echo "Errore durante il salvataggio della palette: " . $connessione->error;
    }
} else {
    // Rimuovi la palette salvata per l'utente
    $sql_delete = "DELETE FROM save_palettes WHERE id_utente = '$id_utente' AND id_palette = '$id_palette'";

    if ($connessione->query($sql_delete) === TRUE) {
        echo json_encode(array("id_palette" => $id_palette, "id_utente" => $id_utente, "isSaved" => false));
    } else {
        echo "Errore durante la rimozione della palette salvata: " . $connessione->error;
    }
}

// Chiudi la connessione al database
$connessione->close();
