<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'].'/mi_proyecto/tools/mypathdb.php');

header('Content-Type: application/json; charset=utf-8');
$option = $_GET['option'] ?? '';

/* INCLUIR REPORTE */
if ($option === 'incluir') {
    $id_usuario  = $_SESSION['id'];
    $titulo      = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');

    if ($titulo === '' || $descripcion === '') {
        echo json_encode(['error' => 3]);
        exit;
    }

    try {
        $stmt = $conn->prepare('INSERT INTO reportes
            (id_usuario, titulo, descripcion, estado, fecha_reporte, status)
            VALUES
            (:id_usuario, :titulo, :descripcion, :estado, NOW(), 1)');

        $stmt->execute([
            'id_usuario'  => $id_usuario,
            'titulo'      => $titulo,
            'descripcion' => $descripcion,
            'estado'      => 'Pendiente',
        ]);

        echo json_encode(['exito' => 1]);
    } catch (PDOException $e) {
        echo json_encode(['error' => 4, 'mensaje' => $e->getMessage()]);
    }

    exit;
}

/*CONSULTAR REPORTE */
if ($option === 'consultar') {
    $id = intval($_GET['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['error' => 2]);
        exit;
    }

    try {
        $stmt = $conn->prepare('SELECT * FROM reportes WHERE id = :id AND status = 1');
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            echo json_encode(['error' => 2]);
            exit;
        }

        if ($_SESSION['rolid'] != 1 && $data['id_usuario'] != $_SESSION['id']) {
            echo json_encode(['error' => 2]);
            exit;
        }

        echo json_encode([
            'exito'       => 1,
            'id'          => $data['id'],
            'titulo'      => $data['titulo'],
            'descripcion' => $data['descripcion'],
            'estado'      => $data['estado']
        ]);
    } catch (PDOException $e) {
        echo json_encode(['error' => 4, 'mensaje' => $e->getMessage()]);
    }

    exit;
}

/*MODIFICAR REPORTE*/
if ($option === 'modificar') {
    $id          = intval($_POST['id'] ?? 0);
    $titulo      = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $estado      = trim($_POST['estado'] ?? 'Pendiente');

    if ($id <= 0 || $titulo === '' || $descripcion === '') {
        echo json_encode(['error' => 3]);
        exit;
    }

    try {
        $stmt = $conn->prepare('SELECT * FROM reportes WHERE id = :id AND status = 1');
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            echo json_encode(['error' => 2]);
            exit;
        }

        if ($_SESSION['rolid'] != 1) {
            if ($data['id_usuario'] != $_SESSION['id'] || $data['estado'] != 'Pendiente') {
                echo json_encode(['error' => 2]);
                exit;
            }
            $estado = $data['estado'];
        }

        $params = [
            'titulo'      => $titulo,
            'descripcion' => $descripcion,
            'estado'      => $estado,
            'id'          => $id,
        ];

        if ($estado === 'Completado' && empty($data['fecha_solucion'])) {
            $sql = 'UPDATE reportes SET
                    titulo = :titulo,
                    descripcion = :descripcion,
                    estado = :estado,
                    fecha_solucion = NOW()
                WHERE id = :id';
        } else {
            $sql = 'UPDATE reportes SET
                    titulo = :titulo,
                    descripcion = :descripcion,
                    estado = :estado
                WHERE id = :id';
        }

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        echo json_encode(['exito' => 1]);
    } catch (PDOException $e) {
        echo json_encode(['error' => 4, 'mensaje' => $e->getMessage()]);
    }

    exit;
}

/* ADMIN: COMPLETAR REPORTE*/
if ($option === 'completar') {
    if ($_SESSION['rolid'] != 1) {
        echo json_encode(['error' => 2]);
        exit;
    }

    $id = intval($_GET['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['error' => 2]);
        exit;
    }

    try {
        $stmt = $conn->prepare('SELECT id FROM reportes WHERE id = :id AND status = 1');
        $stmt->execute(['id' => $id]);

        if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
            echo json_encode(['error' => 2]);
            exit;
        }

        $stmt = $conn->prepare('UPDATE reportes
            SET estado = :estado,
                fecha_solucion = NOW()
            WHERE id = :id');
        $stmt->execute(['estado' => 'Completado', 'id' => $id]);

        echo json_encode(['exito' => 1]);
    } catch (PDOException $e) {
        echo json_encode(['error' => 4, 'mensaje' => $e->getMessage()]);
    }

    exit;
}

/* PDF DE REPORTE*/
if ($option === 'pdf') {
    if ($_SESSION['rolid'] != 1) {
        exit('Acceso denegado');
    }

    $id = intval($_GET['id'] ?? 0);

    try {
        $stmt = $conn->prepare('SELECT r.*, u.nombre AS usuario, ro.nombre AS rol
            FROM reportes r
            INNER JOIN usuarios u ON r.id_usuario = u.id
            INNER JOIN roles ro ON u.rolid = ro.id
            WHERE r.id = :id
            LIMIT 1');
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            exit('Reporte no encontrado');
        }

        header('Content-Type: text/html; charset=utf-8');
        ?>
        <html>
        <head>
            <title>Reporte #<?php echo $data['id']; ?></title>
            <style>
                body{ font-family: Arial, sans-serif; padding:20px; }
                h2{ margin-bottom:20px; }
                .campo{ margin-bottom:10px; }
                .titulo{ font-weight:bold; }
                .box{
                    border:1px solid #ccc;
                    padding:15px;
                    border-radius:8px;
                }
            </style>
        </head>
        <body onload="window.print()">

            <h2>Reporte del sistema - Nexus Care</h2>

            <div class="box">
                <div class="campo"><span class="titulo">ID:</span> <?php echo $data['id']; ?></div>
                <div class="campo"><span class="titulo">Usuario:</span> <?php echo htmlspecialchars($data['usuario']); ?></div>
                <div class="campo"><span class="titulo">Rol:</span> <?php echo htmlspecialchars($data['rol']); ?></div>
                <div class="campo"><span class="titulo">Título:</span> <?php echo htmlspecialchars($data['titulo']); ?></div>
                <div class="campo"><span class="titulo">Descripción:</span> <?php echo nl2br(htmlspecialchars($data['descripcion'])); ?></div>
                <div class="campo"><span class="titulo">Estado:</span> <?php echo htmlspecialchars($data['estado']); ?></div>
                <div class="campo"><span class="titulo">Fecha reporte:</span> <?php echo htmlspecialchars($data['fecha_reporte']); ?></div>
                <div class="campo"><span class="titulo">Fecha solución:</span> <?php echo !empty($data['fecha_solucion']) ? htmlspecialchars($data['fecha_solucion']) : 'Sin resolver'; ?></div>
            </div>

        </body>
        </html>
        <?php
        exit;
    } catch (PDOException $e) {
        exit('Reporte no encontrado');
    }
}

// El cierre de conexión manual no es necesario con PDO.
?>
