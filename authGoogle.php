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
    $token = $data['token'];
    $google = $data['google'];
    $email = $data['email'];
    $username = $data['username'];

    if (empty($token) || empty($google) || empty($email) || empty($username)) {
        $response = array(
            "status" => "error",
            "message" => "Dati mancanti"
        );
    } else if ($google === true) {
        // Verifica se l'utente è già registrato con Google
        $checkGoogleQuery = "SELECT * FROM utenti WHERE email = '$email' AND auth = 'google'";
        $checkGoogleResult = $connessione->query($checkGoogleQuery);

        if ($checkGoogleResult->num_rows > 0) {
            // L'utente è già registrato con Google
            $userData = $checkGoogleResult->fetch_assoc();
            $response = array(
                "status" => "success",
                "message" => "L'utente è già registrato con Google - Login",
                "data" => $userData
            );
        } else {
            // Verifica se l'utente è già registrato normalmente
            $checkQuery = "SELECT * FROM utenti WHERE email = '$email' AND auth != 'google'";
            $checkResult = $connessione->query($checkQuery);

            if ($checkResult->num_rows > 0) {
                // L'utente è già registrato normalmente
                $response = array(
                    "status" => "error",
                    "message" => "L'utente è già registrato normalmente"
                );
            } else {
                // L'utente non è registrato, esegui l'inserimento nel database
                $insertQuery = "INSERT INTO utenti (email, username, auth) VALUES ('$email', '$username', 'google')";
                if ($connessione->query($insertQuery) === TRUE) {
                    $response = array(
                        "status" => "success",
                        "message" => "Utente GOOGLE registrato con successo"
                    );
                    /* RIFAI LA SELECT */
                    $checkGoogleQuery = "SELECT * FROM utenti WHERE email = '$email' AND auth = 'google'";
                    $checkGoogleResult = $connessione->query($checkGoogleQuery);

                    if ($checkGoogleResult->num_rows > 0) {
                        // L'utente è già registrato con Google
                        $userData = $checkGoogleResult->fetch_assoc();
                        $response = array(
                            "status" => "success",
                            "message" => "Utente registrato + Login Success",
                            "data" => $userData
                        );
                    }
                } else {
                    // Errore durante l'inserimento dell'utente nel database
                    $response = array(
                        "status" => "error",
                        "message" => "Errore durante la registrazione dell'utente GOOGLE"
                    );
                }
            }
        }
    }

    // Restituisci la risposta al frontend come JSON
    header('Content-Type: application/json');
    echo json_encode($response);

    // Chiudi la connessione al database
    $connessione->close();
}
