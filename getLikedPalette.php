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

$id_utente = $connessione->real_escape_string($id_utente);

// Verifica se l'utente ha già messo like per qualche palette
$query_check_liked_palette = "SELECT id_palette FROM likes WHERE id_utente = '$id_utente'";
$result_check_liked_palette = $connessione->query($query_check_liked_palette);

// Array per memorizzare gli ID delle palette già liked
$liked_palettes = array();

// Elabora il risultato della query per determinare gli ID delle palette già piaciute
if ($result_check_liked_palette->num_rows > 0) {
    while ($row = $result_check_liked_palette->fetch_assoc()) {
        $liked_palettes[] = $row['id_palette'];
    }
}

// Prepara la response
$response_data = array(
    "liked_palettes" => $liked_palettes
);

// Invia la response come JSON
header('Content-Type: application/json');
echo json_encode($response_data);

// Chiudi la connessione al database
$connessione->close();

?>
