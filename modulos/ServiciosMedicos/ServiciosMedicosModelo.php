<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once($_SERVER['DOCUMENT_ROOT'].'/mi_proyecto/tools/mypathdb.php');

header('Content-Type: application/json; charset=utf-8');

$option = $_GET['option'] ?? '';

if ($option === 'incluir') {
    $nombre         = trim($_POST['nombre'] ?? '');
    $marca          = trim($_POST['marca'] ?? '');
    $descripcion    = trim($_POST['descripcion'] ?? '');
    $stock          = intval($_POST['stock'] ?? 0);
    $idEspecialidad = intval($_POST['id_especialidad'] ?? 0);
    $status         = intval($_POST['status'] ?? 1);

    if ($nombre === '' || $marca === '' || $descripcion === '' || $stock < 0 || $idEspecialidad <= 0) {
        echo json_encode([
            'error' => 3,
            'mensaje' => 'Debe completar todos los campos.'
        ]);
        exit;
    }

    try {
        $stmt = $conn->prepare('INSERT INTO servicios_medicos (
                nombre,
                marca,
                descripcion,
                stock,
                id_especialidad,
                fecha,
                status
            ) VALUES (
                :nombre,
                :marca,
                :descripcion,
                :stock,
                :id_especialidad,
                NOW(),
                :status
            )');

        $stmt->execute([
            'nombre'         => $nombre,
            'marca'          => $marca,
            'descripcion'    => $descripcion,
            'stock'          => $stock,
            'id_especialidad'=> $idEspecialidad,
            'status'         => $status,
        ]);

        echo json_encode([
            'exito' => 1,
            'mensaje' => 'Servicio médico registrado correctamente.'
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'error' => 4,
            'mensaje' => $e->getMessage()
        ]);
    }

    exit;
}

if ($option === 'modificarConsultar') {
    $id = intval($_GET['id'] ?? 0);

    try {
        $stmt = $conn->prepare('SELECT * FROM servicios_medicos WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            echo json_encode([
                'error' => 2,
                'mensaje' => 'Servicio médico no encontrado.'
            ]);
            exit;
        }

        echo json_encode([
            'exito' => 1,
            'id' => $data['id'],
            'nombre' => $data['nombre'],
            'marca' => $data['marca'],
            'descripcion' => $data['descripcion'],
            'stock' => $data['stock'],
            'id_especialidad' => $data['id_especialidad'],
            'status' => $data['status']
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'error' => 4,
            'mensaje' => $e->getMessage()
        ]);
    }

    exit;
}

if ($option === 'modificar') {
    $id             = intval($_POST['id'] ?? 0);
    $nombre         = trim($_POST['nombre'] ?? '');
    $marca          = trim($_POST['marca'] ?? '');
    $descripcion    = trim($_POST['descripcion'] ?? '');
    $stock          = intval($_POST['stock'] ?? 0);
    $idEspecialidad = intval($_POST['id_especialidad'] ?? 0);
    $status         = intval($_POST['status'] ?? 1);

    if ($id <= 0 || $nombre === '' || $marca === '' || $descripcion === '' || $stock < 0 || $idEspecialidad <= 0) {
        echo json_encode([
            'error' => 3,
            'mensaje' => 'Debe completar todos los campos.'
        ]);
        exit;
    }

    try {
        $stmt = $conn->prepare('SELECT id FROM servicios_medicos WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        if (!$stmt->fetch()) {
            echo json_encode([
                'error' => 2,
                'mensaje' => 'El servicio médico no existe.'
            ]);
            exit;
        }

        $stmt = $conn->prepare('UPDATE servicios_medicos SET
                nombre = :nombre,
                marca = :marca,
                descripcion = :descripcion,
                stock = :stock,
                id_especialidad = :id_especialidad,
                status = :status
            WHERE id = :id');

        $stmt->execute([
            'nombre'         => $nombre,
            'marca'          => $marca,
            'descripcion'    => $descripcion,
            'stock'          => $stock,
            'id_especialidad'=> $idEspecialidad,
            'status'         => $status,
            'id'             => $id,
        ]);

        echo json_encode([
            'exito' => 1,
            'mensaje' => 'Servicio médico actualizado correctamente.'
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'error' => 4,
            'mensaje' => $e->getMessage()
        ]);
    }

    exit;
}

if ($option === 'eliminar') {
    $id = intval($_GET['id'] ?? 0);

    try {
        $stmt = $conn->prepare('SELECT id FROM servicios_medicos WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        if (!$stmt->fetch()) {
            echo json_encode([
                'error' => 2,
                'mensaje' => 'Servicio médico no encontrado.'
            ]);
            exit;
        }

        $stmt = $conn->prepare('UPDATE servicios_medicos SET status = 2 WHERE id = :id');
        $stmt->execute(['id' => $id]);

        echo json_encode([
            'exito' => 1,
            'mensaje' => 'Servicio médico eliminado correctamente.'
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'error' => 4,
            'mensaje' => $e->getMessage()
        ]);
    }

    exit;
}

echo json_encode([
    'error' => 1,
    'mensaje' => 'Opción no válida.'
]);

?>