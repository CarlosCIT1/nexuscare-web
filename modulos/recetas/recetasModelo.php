<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'].'/mi_proyecto/tools/mypathdb.php');

$option = $_GET['option'] ?? '';

$rolSesion = strtolower(trim($_SESSION['rol'] ?? ''));
$idSesion  = intval($_SESSION['id'] ?? 0);

/*INCLUIR RECETA*/
if ($option === 'incluir') {

    if ($rolSesion != 'medico' && $rolSesion != 'médico') {
        echo json_encode(['error' => 4, 'mensaje' => 'Solo los médicos pueden crear recetas']);
        exit;
    }

    $id_paciente        = intval($_POST['id_paciente'] ?? 0);
    $diagnostico        = trim($_POST['diagnostico'] ?? '');
    $medicamentos       = trim($_POST['medicamentos'] ?? '');
    $indicaciones       = trim($_POST['indicaciones'] ?? '');
    $cedula_profesional = trim($_POST['cedula_profesional'] ?? '');

    if ($id_paciente <= 0 || $diagnostico === '' || $medicamentos === '' || $indicaciones === '' || $cedula_profesional === '') {
        echo json_encode(['error' => 3]);
        exit;
    }

    try {
        $stmt = $conn->prepare('INSERT INTO recetas
            (id_paciente, id_medico, diagnostico, medicamentos, indicaciones, cedula_profesional, fecha_receta, status)
            VALUES
            (:id_paciente, :id_medico, :diagnostico, :medicamentos, :indicaciones, :cedula_profesional, NOW(), 1)');

        $stmt->execute([
            'id_paciente'        => $id_paciente,
            'id_medico'          => $idSesion,
            'diagnostico'        => $diagnostico,
            'medicamentos'       => $medicamentos,
            'indicaciones'       => $indicaciones,
            'cedula_profesional' => $cedula_profesional,
        ]);

        echo json_encode(['exito' => 1]);
    } catch (PDOException $e) {
        echo json_encode(['error' => 4, 'mensaje' => $e->getMessage()]);
    }

    exit;
}

/*CONSULTAR RECETA*/
if ($option === 'consultar') {

    $id = intval($_GET['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['error' => 2]);
        exit;
    }

    $whereExtra = '';
    $params = ['id' => $id];

    if ($rolSesion == 'paciente') {
        $whereExtra = ' AND id_paciente = :idSesion';
        $params['idSesion'] = $idSesion;
    }

    if ($rolSesion == 'medico' || $rolSesion == 'médico') {
        $whereExtra = ' AND id_medico = :idSesion';
        $params['idSesion'] = $idSesion;
    }

    try {
        $stmt = $conn->prepare('SELECT * FROM recetas WHERE id = :id AND status = 1 ' . $whereExtra . ' LIMIT 1');
        $stmt->execute($params);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            echo json_encode(['error' => 2]);
            exit;
        }

        echo json_encode([
            'exito'              => 1,
            'id'                 => $data['id'],
            'id_paciente'        => $data['id_paciente'],
            'diagnostico'        => $data['diagnostico'],
            'medicamentos'       => $data['medicamentos'],
            'indicaciones'       => $data['indicaciones'],
            'cedula_profesional' => $data['cedula_profesional']
        ]);
    } catch (PDOException $e) {
        echo json_encode(['error' => 2, 'mensaje' => $e->getMessage()]);
    }

    exit;
}

/* MODIFICAR RECETA */
if ($option === 'modificar') {

    if ($rolSesion != 'medico' && $rolSesion != 'médico') {
        echo json_encode(['error' => 4, 'mensaje' => 'Solo los médicos pueden modificar recetas']);
        exit;
    }

    $id                   = intval($_POST['id'] ?? 0);
    $id_paciente          = intval($_POST['id_paciente'] ?? 0);
    $diagnostico          = trim($_POST['diagnostico'] ?? '');
    $medicamentos         = trim($_POST['medicamentos'] ?? '');
    $indicaciones         = trim($_POST['indicaciones'] ?? '');
    $cedula_profesional   = trim($_POST['cedula_profesional'] ?? '');

    if ($id <= 0 || $id_paciente <= 0 || $diagnostico === '' || $medicamentos === '' || $indicaciones === '' || $cedula_profesional === '') {
        echo json_encode(['error' => 3]);
        exit;
    }

    try {
        $stmt = $conn->prepare('SELECT * FROM recetas WHERE id = :id AND id_medico = :id_medico AND status = 1 LIMIT 1');
        $stmt->execute(['id' => $id, 'id_medico' => $idSesion]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            echo json_encode(['error' => 2]);
            exit;
        }

        $stmt = $conn->prepare('UPDATE recetas SET
                id_paciente = :id_paciente,
                diagnostico = :diagnostico,
                medicamentos = :medicamentos,
                indicaciones = :indicaciones,
                cedula_profesional = :cedula_profesional
            WHERE id = :id');

        $stmt->execute([
            'id_paciente'          => $id_paciente,
            'diagnostico'          => $diagnostico,
            'medicamentos'         => $medicamentos,
            'indicaciones'         => $indicaciones,
            'cedula_profesional'   => $cedula_profesional,
            'id'                   => $id,
        ]);

        echo json_encode(['exito' => 1]);
    } catch (PDOException $e) {
        echo json_encode(['error' => 4, 'mensaje' => $e->getMessage()]);
    }

    exit;
}

/* ELIMINAR RECETA */
if ($option === 'eliminar') {

    if ($rolSesion != 'medico' && $rolSesion != 'médico') {
        echo json_encode(['error' => 4, 'mensaje' => 'Solo los médicos pueden eliminar recetas']);
        exit;
    }

    $id = intval($_GET['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['error' => 2]);
        exit;
    }

    try {
        $stmt = $conn->prepare('SELECT * FROM recetas WHERE id = :id AND id_medico = :id_medico AND status = 1 LIMIT 1');
        $stmt->execute(['id' => $id, 'id_medico' => $idSesion]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            echo json_encode(['error' => 2]);
            exit;
        }

        $stmt = $conn->prepare('UPDATE recetas SET status = 2 WHERE id = :id');
        $stmt->execute(['id' => $id]);

        echo json_encode(['exito' => 1]);
    } catch (PDOException $e) {
        echo json_encode(['error' => 4, 'mensaje' => $e->getMessage()]);
    }

    exit;
}

// Cerrar conexión no es necesario en PDO.
?>