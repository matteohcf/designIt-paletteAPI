<?php
include_once("config.php");

// Abilita CORS
header("Access-Control-Allow-Origin: https://palette.matteocarrara.it");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// CONNESSIONE AL DB
$connessione = new mysqli($db_host, $db_user, $db_password, $db_name);

if ($connessione->connect_error) {
    echo "Errore di connessione: " . $connessione->connect_error;
} else {
    // Query per ottenere i dati della palette (esempio)
$sql = "SELECT id_palette, color1, color2, color3, color4, likes, creating_user_id FROM palettes ORDER BY likes DESC";
$result = $connessione->query($sql);

if ($result->num_rows > 0) {
    // Array per salvare i dati della palette
    $paletteData = array();

    // Iterazione sui risultati della query
    while($row = $result->fetch_assoc()) {
        // Aggiungi i dati della palette all'array
        $paletteData[] = $row;
    }

    // Restituisci i dati della palette come risposta JSON
    header('Content-Type: application/json');
    echo json_encode($paletteData);
} else {
    // Nessun risultato trovato
    echo "Nessun dato della palette trovato.";
}

}
// Chiudi la connessione al database
$connessione->close();
?>