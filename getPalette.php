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
    $creating_user_id = $data["creating_user_id"];

    // Query per ottenere i dati della palette
    if ($creating_user_id != '/') {
        // Se l'ID dell'utente è fornito, includi isLiked e isSaved
        $sql = "SELECT id_palette, color1, color2, color3, color4, likes, creating_user_id FROM palettes ORDER BY likes DESC";
        $result = $connessione->query($sql);

        if ($result->num_rows > 0) {
            // Array per salvare i dati della palette
            $paletteData = array();

            // Iterazione sui risultati della query
            while($row = $result->fetch_assoc()) {
                // Aggiungi i dati della palette all'array
                $paletteId = $row['id_palette'];
                $row['isLiked'] = false;
                $row['isSaved'] = false;

                // Controlla se l'utente ha messo like a questa palette
                $query_check_like = "SELECT * FROM likes WHERE id_palette = '$paletteId' AND id_utente = '$creating_user_id'";
                $result_check_like = $connessione->query($query_check_like);
                if ($result_check_like->num_rows > 0) {
                    $row['isLiked'] = true;
                }

                // Controlla se l'utente ha salvato questa palette
                $query_check_saved = "SELECT * FROM save_palettes WHERE id_palette = '$paletteId' AND id_utente = '$creating_user_id'";
                $result_check_saved = $connessione->query($query_check_saved);
                if ($result_check_saved->num_rows > 0) {
                    $row['isSaved'] = true;
                }

                $paletteData[] = $row;
            }

            // Restituisci i dati della palette come risposta JSON
            header('Content-Type: application/json');
            echo json_encode($paletteData);
        } else {
            // Nessun risultato trovato
            echo "Nessun dato della palette trovato.";
        }
    } else if ($creating_user_id == '/') {
        // Se l'ID dell'utente non è fornito, restituisci solo i dati base della palette
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
}

// Chiudi la connessione al database
$connessione->close();
?>
