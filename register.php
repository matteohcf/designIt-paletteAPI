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
    // Recupera i dati inviati dal frontend
    $data = json_decode(file_get_contents("php://input"), true);
    $username = $data['username'];
    $email = $data['email'];
    $password = $data['password'];

    // Verifica che username, email e password siano stati inviati
    if (empty($username) || empty($email) || empty($password)) {
        $response = array(
            "status" => "error",
            "message" => "Username, email e password sono obbligatori"
        );
    } else {
        // Verifica se l'utente esiste già nel database
        $checkQuery = "SELECT * FROM utenti WHERE username = '$username' OR email = '$email'";
        $checkResult = $connessione->query($checkQuery);

        if ($checkResult->num_rows > 0) {
            // L'utente esiste già, restituisci un messaggio di errore
            $response = array(
                "status" => "error",
                "message" => "L'utente esiste già"
            );
        } else {
            // Hash della password
            $hashedPassword = md5($password);

            // Inserisci l'utente nel database
            $insertQuery = "INSERT INTO utenti (email, username, password) VALUES ('$email', '$username', '$hashedPassword')";
            if ($connessione->query($insertQuery) === TRUE) {
                // Successo, restituisci un messaggio di successo
                $response = array(
                    "status" => "success",
                    "message" => "Utente registrato con successo"
                );
            } else {
                // Errore durante l'inserimento dell'utente nel database
                $response = array(
                    "status" => "error",
                    "message" => "Errore durante la registrazione dell'utente"
                );
            }
        }
}

// Restituisci la risposta al frontend come JSON
header('Content-Type: application/json');
echo json_encode($response);

// Chiudi la connessione al database
$connessione->close();
}
?>