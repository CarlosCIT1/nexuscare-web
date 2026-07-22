<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once($_SERVER['DOCUMENT_ROOT'].'/mi_proyecto/tools/mypathdb.php');

$option = $_GET["option"] ?? "";

switch ($option) {

    case "consultar":

        $idPaciente = intval($_GET["id"] ?? 0);

        if ($idPaciente <= 0) {

            echo json_encode([
                "exito"=>0,
                "mensaje"=>"Paciente inválido."
            ]);

            exit;

        }

        try {
            $stmt = $conn->prepare("SELECT
                id,
                nombre,
                direccion,
                email,
                telefono
            FROM usuarios
            WHERE id = :idPaciente
            LIMIT 1");
            $stmt->execute([
                ':idPaciente' => $idPaciente
            ]);

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                echo json_encode([
                    "exito"=>0,
                    "mensaje"=>"Paciente no encontrado."
                ]);
                exit;
            }
        } catch (PDOException $e) {
            echo json_encode([
                "exito"=>0,
                "mensaje"=> $e->getMessage()
            ]);
            exit;
        }

        echo json_encode([

            "exito"=>1,

            "id"=>$row["id"],

            "nombre"=>$row["nombre"],

            "email"=>$row["email"],

            "telefono"=>$row["telefono"],

            "direccion"=>$row["direccion"]

        ]);

    break;

    default:

        echo json_encode([
            "exito"=>0,
            "mensaje"=>"Opción inválida."
        ]);

    break;

}

?>