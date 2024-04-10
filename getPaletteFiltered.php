<?php
include_once("config.php");

// Abilita CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// CONNESSIONE AL DB
$connessione = new mysqli($db_host, $db_user, $db_password, $db_name);

if ($connessione->connect_error) {
    echo "Errore di connessione: " . $connessione->connect_error;
} else {
    // Ottieni l'id dell'utente dall'input POST
    $data = json_decode(file_get_contents("php://input"), true);
    $id_utente = $data["creating_user_id"];

    // Query per ottenere i dati delle palette salvate e a cui ha messo like l'utente
    $sql = "SELECT p.id_palette, p.color1, p.color2, p.color3, p.color4, p.likes, p.creating_user_id 
            FROM palettes p 
            LEFT JOIN save_palettes sp ON p.id_palette = sp.id_palette AND sp.id_utente = '$id_utente'
            LEFT JOIN likes l ON p.id_palette = l.id_palette AND l.id_utente = '$id_utente'
            WHERE sp.id_utente IS NOT NULL OR l.id_utente IS NOT NULL
            ORDER BY p.likes DESC";

    $result = $connessione->query($sql);

    // Array per salvare i dati delle palette salvate e a cui ha messo like l'utente
    $paletteData = array();

    // Iterazione sui risultati della query
    while ($row = $result->fetch_assoc()) {
        $paletteData[] = $row;
    }

    // Restituisci i dati delle palette come risposta JSON
    header('Content-Type: application/json');
    echo json_encode($paletteData);
}

// Chiudi la connessione al database
$connessione->close();
?>
