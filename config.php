<?php
// Configurazione del database 
$db_host = "localhost";
$db_user = "matteohcf";
$db_password = "matteo2004";
$db_name = "design_palette";


if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *");
    header("Access-Control-Allow-Methods: *");
    http_response_code(200);
    exit;
}

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");

?>