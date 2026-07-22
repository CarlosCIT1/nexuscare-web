<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/mi_proyecto/tools/mypathdb.php');

header('Content-Type: application/json; charset=utf-8');

$option = $_GET['option'] ?? '';
$uploadDir = $_SERVER['DOCUMENT_ROOT'].'/mi_proyecto/uploads/categorias/';

if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

function existeColumna(PDO $conn, string $tabla, string $columna): bool {
    $sql = "SELECT 1 FROM information_schema.columns
            WHERE table_schema = current_schema()
              AND table_name = :tabla
              AND column_name = :columna
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        'tabla' => $tabla,
        'columna' => $columna,
    ]);

    return (bool) $stmt->fetchColumn();
}

$tieneImagen = existeColumna($conn, 'especialidades', 'imagen');
$tieneFecha  = existeColumna($conn, 'especialidades', 'fecha');
$tieneStatus = existeColumna($conn, 'especialidades', 'status');

if ($option === 'incluir') {
    $nombre      = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $status      = intval($_POST['status'] ?? 1);

    if ($nombre === '' || $descripcion === '') {
        echo json_encode([
            'error' => 1,
            'mensaje' => 'Debe completar nombre y descripción'
        ]);
        exit;
    }

    $stmt = $conn->prepare('SELECT id FROM especialidades WHERE nombre = :nombre LIMIT 1');
    $stmt->execute(['nombre' => $nombre]);
    if ($stmt->fetch()) {
        echo json_encode([
            'error' => 1,
            'mensaje' => 'La especialidad ya existe'
        ]);
        exit;
    }

    $nombreImagen = '';
    if ($tieneImagen && isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
        $tmpName = $_FILES['imagen']['tmp_name'];
        $originalName = $_FILES['imagen']['name'];
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $permitidas = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($ext, $permitidas, true)) {
            echo json_encode([
                'error' => 1,
                'mensaje' => 'Formato de imagen no permitido'
            ]);
            exit;
        }

        $nombreImagen = time() . '_' . preg_replace('/[^A-Za-z0-9_\.-]/', '_', $originalName);

        if (!move_uploaded_file($tmpName, $uploadDir . $nombreImagen)) {
            echo json_encode([
                'error' => 1,
                'mensaje' => 'No se pudo guardar la imagen'
            ]);
            exit;
        }
    }

    $fields = ['nombre', 'descripcion'];
    $placeholders = [':nombre', ':descripcion'];
    $params = [
        'nombre' => $nombre,
        'descripcion' => $descripcion,
    ];

    if ($tieneImagen) {
        $fields[] = 'imagen';
        $placeholders[] = ':imagen';
        $params['imagen'] = $nombreImagen;
    }

    if ($tieneFecha) {
        $fields[] = 'fecha';
        $placeholders[] = 'NOW()';
    }

    if ($tieneStatus) {
        $fields[] = 'status';
        $placeholders[] = ':status';
        $params['status'] = $status;
    }

    $sql = 'INSERT INTO especialidades (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $placeholders) . ')';
    $stmt = $conn->prepare($sql);
    $success = $stmt->execute($params);

    if ($success) {
        echo json_encode([
            'exito' => 1,
            'mensaje' => 'Especialidad registrada correctamente'
        ]);
    } else {
        echo json_encode([
            'error' => 1,
            'mensaje' => 'No se pudo registrar la especialidad'
        ]);
    }

    exit;
}

if ($option === 'consultar') {
    $id = intval($_GET['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode([
            'error' => 1,
            'mensaje' => 'ID inválido'
        ]);
        exit;
    }

    $stmt = $conn->prepare('SELECT * FROM especialidades WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        echo json_encode([
            'error' => 1,
            'mensaje' => 'La especialidad no existe'
        ]);
        exit;
    }

    echo json_encode([
        'exito'       => 1,
        'id'          => $data['id'],
        'nombre'      => $data['nombre'] ?? '',
        'descripcion' => $data['descripcion'] ?? '',
        'imagen'      => ($tieneImagen ? ($data['imagen'] ?? '') : ''),
        'status'      => ($tieneStatus ? ($data['status'] ?? 1) : 1),
    ]);

    exit;
}

if ($option === 'modificar') {
    $id          = intval($_POST['id'] ?? 0);
    $nombre      = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $status      = intval($_POST['status'] ?? 1);

    if ($id <= 0 || $nombre === '' || $descripcion === '') {
        echo json_encode([
            'error' => 1,
            'mensaje' => 'Debe completar nombre y descripción'
        ]);
        exit;
    }

    $stmt = $conn->prepare('SELECT * FROM especialidades WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $id]);
    $actual = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$actual) {
        echo json_encode([
            'error' => 1,
            'mensaje' => 'La especialidad no existe'
        ]);
        exit;
    }

    $nombreImagen = $tieneImagen ? ($actual['imagen'] ?? '') : '';

    $stmt = $conn->prepare('SELECT id FROM especialidades WHERE nombre = :nombre AND id != :id LIMIT 1');
    $stmt->execute([
        'nombre' => $nombre,
        'id' => $id,
    ]);

    if ($stmt->fetch()) {
        echo json_encode([
            'error' => 1,
            'mensaje' => 'Ya existe otra especialidad con ese nombre'
        ]);
        exit;
    }

    if ($tieneImagen && isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
        $tmpName = $_FILES['imagen']['tmp_name'];
        $originalName = $_FILES['imagen']['name'];
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $permitidas = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($ext, $permitidas, true)) {
            echo json_encode([
                'error' => 1,
                'mensaje' => 'Formato de imagen no permitido'
            ]);
            exit;
        }

        $nuevoNombreImagen = time() . '_' . preg_replace('/[^A-Za-z0-9_\.-]/', '_', $originalName);

        if (!move_uploaded_file($tmpName, $uploadDir . $nuevoNombreImagen)) {
            echo json_encode([
                'error' => 1,
                'mensaje' => 'No se pudo guardar la nueva imagen'
            ]);
            exit;
        }

        if (!empty($nombreImagen) && file_exists($uploadDir . $nombreImagen)) {
            @unlink($uploadDir . $nombreImagen);
        }

        $nombreImagen = $nuevoNombreImagen;
    }

    $sets = [
        'nombre = :nombre',
        'descripcion = :descripcion',
    ];
    $params = [
        'nombre'      => $nombre,
        'descripcion' => $descripcion,
        'id'          => $id,
    ];

    if ($tieneImagen) {
        $sets[] = 'imagen = :imagen';
        $params['imagen'] = $nombreImagen;
    }

    if ($tieneStatus) {
        $sets[] = 'status = :status';
        $params['status'] = $status;
    }

    $sql = 'UPDATE especialidades SET ' . implode(', ', $sets) . ' WHERE id = :id';
    $stmt = $conn->prepare($sql);
    $success = $stmt->execute($params);

    if ($success) {
        echo json_encode([
            'exito' => 1,
            'mensaje' => 'Especialidad actualizada correctamente'
        ]);
    } else {
        echo json_encode([
            'error' => 1,
            'mensaje' => 'No se pudo actualizar la especialidad'
        ]);
    }

    exit;
}

if ($option === 'eliminar') {
    $id = intval($_GET['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode([
            'error' => 1,
            'mensaje' => 'ID inválido'
        ]);
        exit;
    }

    if ($tieneStatus) {
        $stmt = $conn->prepare('UPDATE especialidades SET status = 2 WHERE id = :id');
        $success = $stmt->execute(['id' => $id]);
    } else {
        $stmt = $conn->prepare('DELETE FROM especialidades WHERE id = :id');
        $success = $stmt->execute(['id' => $id]);
    }

    if ($success) {
        echo json_encode([
            'exito' => 1,
            'mensaje' => 'Especialidad eliminada correctamente'
        ]);
    } else {
        echo json_encode([
            'error' => 1,
            'mensaje' => 'No se pudo eliminar la especialidad'
        ]);
    }

    exit;
}

echo json_encode([
    'error' => 1,
    'mensaje' => 'Opción no válida'
]);
?>