<?php
include_once("config.php");

// Abilita CORS
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// CONNESSIONE AL DB
$connessione = new mysqli($db_host, $db_user, $db_password, $db_name);

if ($connessione->connect_error) {
    echo "Errore di connessione: " . $connessione->connect_error;
} else {
    // Ottieni i dati inviati dal frontend
    $data = json_decode(file_get_contents("php://input"), true);
    $email = $data['email'];
    $password = $data['password'];
    $passwordHashed = md5($password);

    // Esegui la query per controllare se l'utente esiste nel database
    $sql = "SELECT * FROM utenti WHERE email = '$email' AND password = '$passwordHashed'";
    $result = $connessione->query($sql);

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
            "message" => "Credenziali non valide",
            "password" => $passwordHashed
        );
    }

    // Restituisci la risposta al frontend come JSON
    header('Content-Type: application/json');
    echo json_encode($response);

    // Chiudi la connessione al database
    $connessione->close();
}
