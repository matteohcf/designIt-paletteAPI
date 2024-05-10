<?php

include_once("config.php");
include_once("verifyTokenJWT.php"); // Includi il file con la funzione verifyToken

// Connessione al database
$connessione = new mysqli($db_host, $db_user, $db_password, $db_name);

if ($connessione->connect_error) {
    die("Errore di connessione: " . $connessione->connect_error);
}

/* Verifica token */
$headers = getallheaders();
$token = "null";
foreach ($headers as $name => $value) {
    if ($name === 'Authorization') {
        // Dividi il valore dell'header per ottenere solo il token
        $token = trim(str_replace('Bearer', '', $value));
        break;
    }
}
/* echo $token; */

// Verify the token using the function
$decodedToken = verifyToken($token);

// Handle invalid token
if ($decodedToken === false) {
  http_response_code(401);
  echo json_encode(array("message" => "Invalid Token"));
  exit;
}

// Ottieni i dati dal post
$data = json_decode(file_get_contents("php://input"), true);
$id_palette = isset($data["id_palette"]) ? $connessione->real_escape_string($data["id_palette"]) : null;
$id_utente = isset($data["id_utente"]) ? $connessione->real_escape_string($data["id_utente"]) : null;

if ($id_palette && $id_utente) {
    // Verifica se l'utente ha già messo like per questa palette
    $stmt_check_like = $connessione->prepare("SELECT * FROM likes WHERE id_utente = ? AND id_palette = ?");
    $stmt_check_like->bind_param("ss", $id_utente, $id_palette);
    $stmt_check_like->execute();
    $result_check_like = $stmt_check_like->get_result();

    if ($result_check_like->num_rows == 0) { // L'utente non ha ancora messo like per questa palette

        // Aggiorna il numero di likes nella tabella 'palettes'
        $stmt_update_likes = $connessione->prepare("UPDATE palettes SET likes = likes + 1 WHERE id_palette = ?");
        $stmt_update_likes->bind_param("s", $id_palette);
        $stmt_insert_like = $connessione->prepare("INSERT INTO likes (id_utente, id_palette) VALUES (?, ?)");
        $stmt_insert_like->bind_param("ss", $id_utente, $id_palette);

        if ($stmt_update_likes->execute() && $stmt_insert_like->execute()) {
            // Ottieni il nuovo numero di likes
            $stmt_get_likes = $connessione->prepare("SELECT likes FROM palettes WHERE id_palette = ?");
            $stmt_get_likes->bind_param("s", $id_palette);
            $stmt_get_likes->execute();
            $result_likes = $stmt_get_likes->get_result();

            if ($result_likes) {
                $row = $result_likes->fetch_assoc();
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
        // Ha già messo like
        $stmt_undo_like = $connessione->prepare("UPDATE palettes SET likes = likes - 1 WHERE id_palette = ?");
        $stmt_undo_like->bind_param("s", $id_palette);
        $stmt_delete_like = $connessione->prepare("DELETE FROM likes WHERE id_utente = ? AND id_palette = ?");
        $stmt_delete_like->bind_param("ss", $id_utente, $id_palette);

        if ($stmt_undo_like->execute() && $stmt_delete_like->execute()) {
            // Ottieni il nuovo numero di likes
            $stmt_get_likes = $connessione->prepare("SELECT likes FROM palettes WHERE id_palette = ?");
            $stmt_get_likes->bind_param("s", $id_palette);
            $stmt_get_likes->execute();
            $result_likes = $stmt_get_likes->get_result();

            if ($result_likes) {
                $row = $result_likes->fetch_assoc();
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
} else {
    echo "Dati mancanti.";
}

// Chiudi la connessione
$connessione->close();

?>
