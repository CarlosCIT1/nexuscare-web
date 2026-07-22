<?php

/* CONFIGURACIÓN BASE DE DATOS */

$host = "localhost";
$port = "5432";
$dbname = "nexuscare";
$username = "postgres";
$password = "1234";

/* CONEXIÓN */

try {
    /** @var PDO $conn */
    $conn = new PDO(
        "pgsql:host=$host;port=$port;dbname=$dbname",
        $username,
        $password
    );

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $conn->exec("SET NAMES 'UTF8'");
} catch (PDOException $e) {
    die("Error de conexión a PostgreSQL: " . $e->getMessage());
}

?>