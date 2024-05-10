<?php
/* Only for mobile APP */
include_once("config.php");

// Connessione al database
$connessione = new mysqli($db_host, $db_user, $db_password, $db_name);

if ($connessione->connect_error) {
    die("Errore di connessione: " . $connessione->connect_error);
}

// Ottieni i dati
$data = json_decode(file_get_contents("php://input"), true);
$id_utente = $data["id_utente"];

// Per evitare SQL injection
$id_utente = $connessione->real_escape_string($id_utente);

// Verifica se l'utente ha già salvato qualche palette
$query_check_saved_palette = "SELECT id_palette FROM save_palettes WHERE id_utente = '$id_utente'";
$result_check_saved_palette = $connessione->query($query_check_saved_palette);

$saved_palettes = array();

if ($result_check_saved_palette->num_rows > 0) {
    while ($row = $result_check_saved_palette->fetch_assoc()) {
        $saved_palettes[] = $row['id_palette'];
    }
}

$response_data = array(
    "saved_palettes" => $saved_palettes
);

// Restituisci i dati come JSON
header('Content-Type: application/json');
echo json_encode($response_data);

// Chiudi la connessione
$connessione->close();

?>
