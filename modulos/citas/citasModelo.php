<?php

session_start();

require_once($_SERVER['DOCUMENT_ROOT'].'/mi_proyecto/tools/mypathdb.php');

$option = $_GET['option'] ?? '';

function horarioValido($hora)
{
    return ($hora >= "06:00" && $hora <= "22:00");
}

function fechaValida($fecha)
{
    return ($fecha >= date("Y-m-d"));
}

function medicoDisponible($conn, $idMedico, $fecha, $hora, $idExcluir = 0)
{
    $sql = "SELECT id
            FROM citas
            WHERE id_medico = :id_medico
            AND fecha_cita = :fecha_cita
            AND hora_cita = :hora_cita
            AND status = 1";

    $params = [
        ":id_medico" => $idMedico,
        ":fecha_cita" => $fecha,
        ":hora_cita" => $hora
    ];

    if ($idExcluir > 0) {
        $sql .= " AND id <> :id_excluir";
        $params[":id_excluir"] = $idExcluir;
    }

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetch(PDO::FETCH_ASSOC) === false;
}

if ($option == "obtenerMedicos") {

    $id_especialidad = intval($_GET['id_especialidad'] ?? 0);

    if ($id_especialidad <= 0) {
        echo json_encode([]);
        exit;
    }

    $sql = "SELECT
                u.id,
                u.nombre
            FROM usuarios u
            INNER JOIN roles r
                ON u.rolid = r.id
            WHERE u.status = 1
            AND (LOWER(r.nombre)='medico' OR LOWER(r.nombre)='médico')
            AND u.id_especialidad = :id_especialidad
            ORDER BY u.nombre";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ":id_especialidad" => $id_especialidad
    ]);

    $medicos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($medicos);

    exit;

}

if ($option == "obtenerHorarios") {

    $id_medico = intval($_GET["id_medico"] ?? 0);

    $fecha = trim($_GET["fecha"] ?? "");

    if ($id_medico <= 0 || $fecha == "") {

        echo json_encode([]);

        exit;

    }

    $ocupadas = [];

    $sql = "SELECT hora_cita
            FROM citas
            WHERE id_medico = :id_medico
            AND fecha_cita = :fecha_cita
            AND status = 1";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ":id_medico" => $id_medico,
        ":fecha_cita" => $fecha
    ]);

    $ocupadas = [];

    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {

        $ocupadas[] = substr($row["hora_cita"], 0, 5);

    }

    $horarios = [];

    for ($i = 6; $i <= 22; $i++) {

        $hora = sprintf("%02d:00",$i);

        if (!in_array($hora,$ocupadas)) {

            $horarios[] = $hora;

        }

    }

    echo json_encode($horarios);

    exit;

}
if ($option == "incluir") {

    $rolSesion = strtolower(trim($_SESSION['rol'] ?? ''));

    $idSesion = intval($_SESSION['id'] ?? 0);

    $id_paciente = intval($_POST['id_paciente'] ?? 0);

    $id_especialidad = intval($_POST['id_especialidad'] ?? 0);

    $id_medico = intval($_POST['id_medico'] ?? 0);

    $id_servicio = intval($_POST['id_servicio'] ?? 0);

    $fecha_cita = trim($_POST['fecha_cita'] ?? '');

    $hora_cita = trim($_POST['hora_cita'] ?? '');

    $estado = trim($_POST['estado'] ?? 'Pendiente');

    $observaciones = trim($_POST['observaciones'] ?? '');

    if ($rolSesion == "paciente") {

        $id_paciente = $idSesion;

    }

    if (
        $id_paciente <= 0 ||
        $id_especialidad <= 0 ||
        $id_medico <= 0 ||
        $id_servicio <= 0 ||
        $fecha_cita == "" ||
        $hora_cita == ""
    ) {

        echo json_encode([
            "error" => 3,
            "mensaje" => "Debe completar toda la información."
        ]);

        exit;

    }

    if (!fechaValida($fecha_cita)) {

        echo json_encode([
            "error" => 5,
            "mensaje" => "No es posible registrar citas en fechas pasadas."
        ]);

        exit;

    }

    $hora_cita = substr($hora_cita,0,5);

    if (!horarioValido($hora_cita)) {

        echo json_encode([
            "error" => 6,
            "mensaje" => "Las citas únicamente pueden agendarse entre las 06:00 y las 22:00 horas."
        ]);

        exit;

    }

    if (!medicoDisponible(
        $conn,
        $id_medico,
        $fecha_cita,
        $hora_cita
    )) {

        echo json_encode([
            "error" => 7,
            "mensaje" => "El médico ya tiene una cita programada para esa fecha y hora."
        ]);

        exit;

    }

    // Cambiado
    $observaciones = trim($observaciones);

    try {
        $stmt = $conn->prepare("CALL sp_registrar_cita(:p_id_paciente, :p_id_especialidad, :p_id_medico, :p_id_servicio, :p_fecha_cita, :p_hora_cita, :p_observaciones)");
        $stmt->execute([
            ":p_id_paciente" => $id_paciente,
            ":p_id_especialidad" => $id_especialidad,
            ":p_id_medico" => $id_medico,
            ":p_id_servicio" => $id_servicio,
            ":p_fecha_cita" => $fecha_cita,
            ":p_hora_cita" => $hora_cita,
            ":p_observaciones" => $observaciones
        ]);

        echo json_encode([
            "exito" => 1,
            "mensaje" => "La cita fue registrada correctamente."
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            "error" => 4,
            "mensaje" => $e->getMessage()
        ]);
    }

    exit;

}
if ($option == "modificarConsultar") {

    $id = intval($_GET['id'] ?? 0);

    if ($id <= 0) {

        echo json_encode([
            "error" => 2
        ]);

        exit;

    }

    $rolSesion = strtolower(trim($_SESSION['rol'] ?? ''));

    $idSesion = intval($_SESSION['id'] ?? 0);

    $whereExtra = "";

    if ($rolSesion == "paciente") {

        $whereExtra = " AND id_paciente = $idSesion";

    }

    if ($rolSesion == "medico" || $rolSesion == "médico") {

        $whereExtra = " AND id_medico = $idSesion";

    }

    try {
        $sql = "SELECT *
                FROM citas
                WHERE id = :id
                $whereExtra
                LIMIT 1";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ":id" => $id
        ]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            echo json_encode([
                "error" => 2
            ]);
            exit;
        }

        echo json_encode([
            "exito" => 1,
            "id" => $data["id"],
            "id_paciente" => $data["id_paciente"],
            "id_especialidad" => $data["id_especialidad"],
            "id_medico" => $data["id_medico"],
            "id_servicio" => $data["id_servicio"],
            "fecha_cita" => $data["fecha_cita"],
            "hora_cita" => substr($data["hora_cita"], 0, 5),
            "observaciones" => $data["observaciones"],
            "estado" => $data["estado"]
        ]);

        exit;
    } catch (PDOException $e) {
        echo json_encode([
            "error" => 2,
            "mensaje" => "Error al consultar la cita: " . $e->getMessage()
        ]);
        exit;
    }
}
if ($option == "modificar") {

    $rolSesion = strtolower(trim($_SESSION['rol'] ?? ''));

    $idSesion = intval($_SESSION['id'] ?? 0);

    $id = intval($_POST['id'] ?? 0);

    $id_paciente = intval($_POST['id_paciente'] ?? 0);

    $id_especialidad = intval($_POST['id_especialidad'] ?? 0);

    $id_medico = intval($_POST['id_medico'] ?? 0);

    $id_servicio = intval($_POST['id_servicio'] ?? 0);

    $fecha_cita = trim($_POST['fecha_cita'] ?? '');

    $hora_cita = trim($_POST['hora_cita'] ?? '');

    $estado = trim($_POST['estado'] ?? 'Pendiente');

    $observaciones = trim($_POST['observaciones'] ?? '');

    if (
        $id <= 0 ||
        $id_paciente <= 0 ||
        $id_especialidad <= 0 ||
        $id_medico <= 0 ||
        $id_servicio <= 0 ||
        $fecha_cita == "" ||
        $hora_cita == ""
    ) {

        echo json_encode([
            "error" => 3,
            "mensaje" => "Debe completar toda la información."
        ]);

        exit;

    }

    if ($rolSesion == "paciente") {

        $id_paciente = $idSesion;

    }

    try {
        $stmtVerificar = $conn->prepare("SELECT * FROM citas WHERE id = :id LIMIT 1");
        $stmtVerificar->execute([
            ":id" => $id
        ]);

        $cita = $stmtVerificar->fetch(PDO::FETCH_ASSOC);

        if (!$cita) {
            echo json_encode([
                "error" => 2,
                "mensaje" => "La cita no existe."
            ]);
            exit;
        }
    } catch (PDOException $e) {
        echo json_encode([
            "error" => 2,
            "mensaje" => "Error al verificar la cita: " . $e->getMessage()
        ]);
        exit;
    }

    if ($rolSesion == "paciente") {

        if (intval($cita["id_paciente"]) != $idSesion) {

            echo json_encode([
                "error" => 4,
                "mensaje" => "No puedes modificar esta cita."
            ]);

            exit;

        }

        $estadoActual = strtolower(trim($cita["estado"]));

        if (
            $estadoActual == "atendida" ||
            $estadoActual == "cancelada"
        ) {

            echo json_encode([
                "error" => 4,
                "mensaje" => "No es posible modificar una cita atendida o cancelada."
            ]);

            exit;

        }

        $estado = $cita["estado"];

    }

    if ($rolSesion == "medico" || $rolSesion == "médico") {

        if (intval($cita["id_medico"]) != $idSesion) {

            echo json_encode([
                "error" => 4,
                "mensaje" => "No puedes modificar esta cita."
            ]);

            exit;

        }

    }

    if (!fechaValida($fecha_cita)) {

        echo json_encode([
            "error" => 5,
            "mensaje" => "No puedes programar una cita en una fecha pasada."
        ]);

        exit;

    }

    $hora_cita = substr($hora_cita,0,5);

    if (!horarioValido($hora_cita)) {

        echo json_encode([
            "error" => 6,
            "mensaje" => "El horario permitido es de 06:00 a 22:00."
        ]);

        exit;

    }

    if (
        !medicoDisponible(
            $conn,
            $id_medico,
            $fecha_cita,
            $hora_cita,
            $id
        )
    ) {

        echo json_encode([
            "error" => 7,
            "mensaje" => "El médico ya tiene una cita registrada en ese horario."
        ]);

        exit;

    }

    try {
        $stmtUpdate = $conn->prepare(
            "UPDATE citas SET
                id_paciente = :id_paciente,
                id_especialidad = :id_especialidad,
                id_medico = :id_medico,
                id_servicio = :id_servicio,
                fecha_cita = :fecha_cita,
                hora_cita = :hora_cita,
                observaciones = :observaciones,
                estado = :estado
            WHERE id = :id"
        );

        $stmtUpdate->execute([
            ":id_paciente" => $id_paciente,
            ":id_especialidad" => $id_especialidad,
            ":id_medico" => $id_medico,
            ":id_servicio" => $id_servicio,
            ":fecha_cita" => $fecha_cita,
            ":hora_cita" => $hora_cita,
            ":observaciones" => $observaciones,
            ":estado" => $estado,
            ":id" => $id
        ]);

        echo json_encode([
            "exito" => 1,
            "mensaje" => "La cita fue actualizada correctamente."
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            "error" => 4,
            "mensaje" => "Error al actualizar la cita: " . $e->getMessage()
        ]);
    }

    exit;

}
if ($option == "eliminar") {

    $id = intval($_GET["id"] ?? 0);

    if ($id <= 0) {

        echo json_encode([
            "error" => 1,
            "mensaje" => "La cita no existe."
        ]);

        exit;

    }

    $rolSesion = strtolower(trim($_SESSION["rol"] ?? ""));

    $idSesion = intval($_SESSION["id"] ?? 0);

    try {
        $stmt = $conn->prepare(
            "SELECT *
             FROM citas
             WHERE id = :id
             LIMIT 1"
        );
        $stmt->execute([
            ":id" => $id
        ]);
        $cita = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cita) {
            echo json_encode([
                "error" => 1,
                "mensaje" => "La cita no fue encontrada."
            ]);
            exit;
        }
    } catch (PDOException $e) {
        echo json_encode([
            "error" => 1,
            "mensaje" => "Error al consultar la cita: " . $e->getMessage()
        ]);
        exit;
    }

    if ($rolSesion == "paciente") {

        if (intval($cita["id_paciente"]) != $idSesion) {

            echo json_encode([
                "error" => 1,
                "mensaje" => "No puedes cancelar esta cita."
            ]);

            exit;

        }

        if (
            strtolower($cita["estado"]) == "atendida" ||
            strtolower($cita["estado"]) == "cancelada"
        ) {

            echo json_encode([
                "error" => 1,
                "mensaje" => "La cita ya no puede cancelarse."
            ]);

            exit;

        }

    }

    if ($rolSesion == "medico" || $rolSesion == "médico") {

        echo json_encode([
            "error" => 1,
            "mensaje" => "El médico no puede cancelar citas."
        ]);

        exit;

    }

    try {
        $stmtCancel = $conn->prepare(
            "UPDATE citas
             SET estado = 'Cancelada',
                 status = 2
             WHERE id = :id"
        );
        $stmtCancel->execute([
            ":id" => $id
        ]);

        echo json_encode([
            "exito" => 1,
            "mensaje" => "La cita fue cancelada correctamente."
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            "error" => 1,
            "mensaje" => "Error al cancelar la cita: " . $e->getMessage()
        ]);
    }

    exit;

}
if ($option == "marcarAtendida") {

    $id = intval($_GET["id"] ?? 0);

    if ($id <= 0) {

        echo json_encode([
            "error" => 1,
            "mensaje" => "La cita no existe."
        ]);

        exit;

    }

    $rolSesion = strtolower(trim($_SESSION["rol"] ?? ""));

    $idSesion = intval($_SESSION["id"] ?? 0);

    if ($rolSesion != "administrador" && $rolSesion != "medico" && $rolSesion != "médico") {

        echo json_encode([
            "error" => 1,
            "mensaje" => "No tiene permisos para realizar esta acción."
        ]);
        exit;
    }
    try {
        $stmt = $conn->prepare(
            "SELECT *
             FROM citas
             WHERE id = :id
             LIMIT 1"
        );
        $stmt->execute([
            ":id" => $id
        ]);
        $cita = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cita) {
            echo json_encode([
                "error" => 1,
                "mensaje" => "La cita no fue encontrada."
            ]);
            exit;
        }
    } catch (PDOException $e) {
        echo json_encode([
            "error" => 1,
            "mensaje" => "Error al consultar la cita: " . $e->getMessage()
        ]);
        exit;
    }
    if ($rolSesion == "medico" || $rolSesion == "médico") {
        if (intval($cita["id_medico"]) != $idSesion) {
            echo json_encode([
                "error" => 1,
                "mensaje" => "No puedes modificar una cita que no te pertenece."
            ]);
            exit;
        }
    }
    if (strtolower($cita["estado"]) == "cancelada") {

        echo json_encode([
            "error" => 1,
            "mensaje" => "La cita fue cancelada y no puede marcarse como atendida."
        ]);
        exit;
    }
    if (strtolower($cita["estado"]) == "atendida") {
        echo json_encode([
            "error" => 1,
            "mensaje" => "La cita ya fue atendida."
        ]);

        exit;
    }
    try {
        $stmtAtendida = $conn->prepare(
            "UPDATE citas
             SET estado = 'Atendida'
             WHERE id = :id"
        );
        $stmtAtendida->execute([
            ":id" => $id
        ]);

        echo json_encode([
            "exito" => 1,
            "mensaje" => "La cita fue marcada como atendida."
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            "error" => 1,
            "mensaje" => "Error al marcar la cita como atendida: " . $e->getMessage()
        ]);
    }
    exit;
}