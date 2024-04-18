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
    // Ottieni i dati inviati dal frontend
    $data = json_decode(file_get_contents("php://input"), true);
    $email = isset($data['email']) ? $connessione->real_escape_string($data['email']) : null;
    $password = isset($data['password']) ? $connessione->real_escape_string($data['password']) : null;
    $passwordHashed = md5($password);

    if ($email !== null && $password !== null) {
        // Esegui la query preparata per controllare se l'utente esiste nel database
        $sql = "SELECT * FROM utenti WHERE email = ? AND password = ? AND auth = 'normal'";
        $stmt = $connessione->prepare($sql);

        if ($stmt) {
            // Associa i parametri e esegui la query
            $stmt->bind_param("ss", $email, $passwordHashed);
            $stmt->execute();
            $result = $stmt->get_result();

            // Controlla se la query ha prodotto risultati
            if ($result->num_rows > 0) {
                // Utente trovato, restituisci un messaggio di successo
                $userData = $result->fetch_assoc();
                $response = array(
                    "status" => "success",
                    "data" => $userData
                );
            } else {
                // Utente non trovato, restituisci un messaggio di errore
                $response = array(
                    "status" => "error",
                    "message" => "Credenziali non valide"
                );
            }

            // Chiudi lo statement
            $stmt->close();
        } else {
            // Errore nella preparazione della query
            echo "Errore nella preparazione della query: " . $connessione->error;
        }
    } else {
        echo "Email o password non fornite.";
    }

    // Restituisci la risposta al frontend come JSON
    header('Content-Type: application/json');
    echo json_encode($response);

    // Chiudi la connessione al database
    $connessione->close();
}
