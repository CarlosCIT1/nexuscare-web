<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/mi_proyecto/tools/mypathdb.php');
header('Content-Type: application/json; charset=utf-8');

$option = $_GET['option'] ?? '';

/* INCLUIR ROL*/
if ($option === 'incluir') {
    $nombre      = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $accesos     = trim($_POST['accesos'] ?? '');
    $status      = intval($_POST['status'] ?? 1);

    if ($nombre === '' || $descripcion === '' || $accesos === '') {
        echo json_encode(['error' => 3]);
        exit;
    }

    if (!preg_match('/^[a-zA-ZñÑáéíóúü ]+$/u', $nombre)) {
        echo json_encode(['error' => 3]);
        exit;
    }

    try {
        $stmt = $conn->prepare('SELECT id FROM roles WHERE nombre = :nombre LIMIT 1');
        $stmt->execute(['nombre' => $nombre]);
        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            echo json_encode(['error' => 1]);
            exit;
        }

        $stmt = $conn->prepare('INSERT INTO roles (nombre, descripcion, accesos, fecha, status)
            VALUES (:nombre, :descripcion, :accesos, NOW(), :status)');

        $stmt->execute([
            'nombre'      => $nombre,
            'descripcion' => $descripcion,
            'accesos'     => $accesos,
            'status'      => $status,
        ]);

        echo json_encode(['exito' => 1]);
    } catch (PDOException $e) {
        echo json_encode(['error' => 4, 'mensaje' => $e->getMessage()]);
    }

    exit;
}

/*CONSULTAR ROL*/
if ($option === 'consultar') {
    $id = intval($_GET['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['error' => 2]);
        exit;
    }

    try {
        $stmt = $conn->prepare('SELECT * FROM roles WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            echo json_encode(['error' => 2]);
            exit;
        }

        echo json_encode([
            'exito'       => 1,
            'id'          => $data['id'],
            'nombre'      => $data['nombre'],
            'descripcion' => $data['descripcion'],
            'accesos'     => $data['accesos'],
            'status'      => $data['status']
        ]);
    } catch (PDOException $e) {
        echo json_encode(['error' => 4, 'mensaje' => $e->getMessage()]);
    }

    exit;
}

/*MODIFICAR ROL */
if ($option === 'modificar') {
    $id          = intval($_POST['id'] ?? 0);
    $nombre      = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $accesos     = trim($_POST['accesos'] ?? '');
    $status      = intval($_POST['status'] ?? 1);

    if ($id <= 0 || $nombre === '' || $descripcion === '' || $accesos === '') {
        echo json_encode(['error' => 3]);
        exit;
    }

    if (!preg_match('/^[a-zA-ZñÑáéíóúü ]+$/u', $nombre)) {
        echo json_encode(['error' => 3]);
        exit;
    }

    try {
        $stmt = $conn->prepare('SELECT id FROM roles WHERE id = :id');
        $stmt->execute(['id' => $id]);
        if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
            echo json_encode(['error' => 2]);
            exit;
        }

        $stmt = $conn->prepare('SELECT id FROM roles WHERE nombre = :nombre AND id <> :id LIMIT 1');
        $stmt->execute(['nombre' => $nombre, 'id' => $id]);
        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            echo json_encode(['error' => 1]);
            exit;
        }

        $stmt = $conn->prepare('UPDATE roles SET nombre = :nombre, descripcion = :descripcion, accesos = :accesos, status = :status WHERE id = :id');
        $stmt->execute([
            'nombre'      => $nombre,
            'descripcion' => $descripcion,
            'accesos'     => $accesos,
            'status'      => $status,
            'id'          => $id,
        ]);

        echo json_encode(['exito' => 1]);
    } catch (PDOException $e) {
        echo json_encode(['error' => 4, 'mensaje' => $e->getMessage()]);
    }

    exit;
}

/*ELIMINAR ROL */
if ($option === 'eliminar') {
    $id = intval($_GET['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['error' => 2]);
        exit;
    }

    try {
        $stmt = $conn->prepare('SELECT id FROM roles WHERE id = :id');
        $stmt->execute(['id' => $id]);
        if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
            echo json_encode(['error' => 2]);
            exit;
        }

        $stmt = $conn->prepare('UPDATE roles SET status = 2 WHERE id = :id');
        $stmt->execute(['id' => $id]);

        echo json_encode(['exito' => 1]);
    } catch (PDOException $e) {
        echo json_encode(['error' => 4, 'mensaje' => $e->getMessage()]);
    }

    exit;
}

echo json_encode([
    'error' => 1,
    'mensaje' => 'Opción no válida.'
]);
?>