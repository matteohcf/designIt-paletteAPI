<?php

/* Abilita cors se è una riciesta option + https response code */
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("Access-Control-Allow-Origin: https://palette.matteocarrara.it");
    header("Access-Control-Allow-Headers: *");
    header("Access-Control-Allow-Methods: *");
    http_response_code(200);
    exit;
}

include_once("config.php");

// Abilita CORS
header("Access-Control-Allow-Origin: https://palette.matteocarrara.it");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");

// CONNESSIONE AL DB
$connessione = new mysqli($db_host, $db_user, $db_password, $db_name);

// Verifica la connessione
if ($connessione->connect_error) {
    die("Errore di connessione: " . $connessione->connect_error);
}

// Ottieni l'id della palette e l'id dell'utente dall'input POST
$data = json_decode(file_get_contents("php://input"), true);
$id_palette = $data["id_palette"];
$id_utente = $data["id_utente"];

// Sanitizzazione dell'id della palette per evitare SQL injection
$id_palette = $connessione->real_escape_string($id_palette);

// Verifica se l'utente ha già messo like per questa palette
$query_check_like = "SELECT * FROM likes WHERE id_utente = '$id_utente' AND id_palette = '$id_palette'";
$result_check_like = $connessione->query($query_check_like);

if ($result_check_like->num_rows == 0) { // L'utente non ha ancora messo like per questa palette

    // Aggiorna il numero di likes nella tabella 'palettes'
    $sql = "UPDATE palettes SET likes = likes + 1 WHERE id_palette = '$id_palette'";
    $sql1 = "INSERT INTO likes (id_utente, id_palette) VALUES ('$id_utente', '$id_palette')";

    if ($connessione->query($sql) === TRUE && $connessione->query($sql1) === TRUE) {
        // Se l'aggiornamento è avvenuto con successo, ottieni il nuovo numero di likes
        $result = $connessione->query("SELECT likes FROM palettes WHERE id_palette = '$id_palette'");
        if ($result) {
            $row = $result->fetch_assoc();
            $likes = $row['likes'];
            // Restituisci il nuovo numero di likes come JSON
            echo json_encode(array("likes" => $likes, "id_palette" => $id_palette, "id_utente" => $id_utente, "isLiked" => true));
        } else {
            echo "Errore nell'ottenere i likes aggiornati.";
        }
    } else {
        echo "Errore nell'aggiornamento dei likes: " . $connessione->error;
    }
} else {
    /* Ha già messo like */
    $sqlInverso = "UPDATE palettes SET likes = likes - 1 WHERE id_palette = '$id_palette'";
    $sqlInverso1 = "DELETE FROM likes WHERE id_utente = '$id_utente' AND id_palette = '$id_palette'";

    if ($connessione->query($sqlInverso) === TRUE && $connessione->query($sqlInverso1) === TRUE) {
        // Se l'aggiornamento è avvenuto con successo, ottieni il nuovo numero di likes
        $result = $connessione->query("SELECT likes FROM palettes WHERE id_palette = '$id_palette'");
        if ($result) {
            $row = $result->fetch_assoc();
            $likes = $row['likes'];
            // Restituisci il nuovo numero di likes come JSON
            echo json_encode(array("likes" => $likes, "id_palette" => $id_palette, "id_utente" => $id_utente, "isLiked" => false));
        } else {
            echo "Errore nell'ottenere i likes aggiornati.";
        }
    } else {
        echo "Errore nell'aggiornamento dei likes: " . $connessione->error;
    }
}

// Chiudi la connessione
$connessione->close();

?>
