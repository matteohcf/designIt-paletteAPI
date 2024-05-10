<?php
include_once("config.php");

// Connessione al database
$connessione = new mysqli($db_host, $db_user, $db_password, $db_name);

if ($connessione->connect_error) {
    echo "Errore di connessione: " . $connessione->connect_error;
} else {
    // Ottieni i dati dall'URL della GET
    $id_utente = isset($_GET["creating_user_id"]) ? $connessione->real_escape_string($_GET["creating_user_id"]) : null;

    if ($id_utente !== null) {
        // Query per ottenere i dati delle palette salvate e a cui ha messo like l'utente
        $sql = "SELECT p.id_palette, p.color1, p.color2, p.color3, p.color4, p.likes, p.creating_user_id,
                CASE WHEN sp.id_utente IS NOT NULL THEN true ELSE false END AS isSaved,
                CASE WHEN l.id_utente IS NOT NULL THEN true ELSE false END AS isLiked
                FROM palettes p 
                LEFT JOIN save_palettes sp ON p.id_palette = sp.id_palette AND sp.id_utente = ?
                LEFT JOIN likes l ON p.id_palette = l.id_palette AND l.id_utente = ?
                WHERE sp.id_utente IS NOT NULL OR l.id_utente IS NOT NULL
                ORDER BY p.likes DESC";

        $stmt = $connessione->prepare($sql);

        if ($stmt) {
            // Associa i parametri e esegui la query
            $stmt->bind_param("ss", $id_utente, $id_utente);
            $stmt->execute();
            $result = $stmt->get_result();

            $paletteData = array();

            while ($row = $result->fetch_assoc()) {
                // Converti i valori di isSaved e isLiked in boolean
                $row['isSaved'] = (bool)$row['isSaved'];
                $row['isLiked'] = (bool)$row['isLiked'];
                $paletteData[] = $row;
            }

            // Restituisci i dati come JSON
            header('Content-Type: application/json');
            echo json_encode($paletteData);

            $stmt->close();
        } else {
            echo "Errore nella preparazione della query: " . $connessione->error;
        }
    } else {
        echo "ID utente non fornito.";
    }
}

// Chiudi la connessione al database
$connessione->close();
