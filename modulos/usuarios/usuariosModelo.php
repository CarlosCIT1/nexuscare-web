<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once($_SERVER['DOCUMENT_ROOT'].'/mi_proyecto/tools/mypathdb.php');

$option = $_GET['option'] ?? '';


function esRolMedico(PDO $conn, int $rolid): bool {
    $stmt = $conn->prepare('SELECT nombre FROM roles WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $rolid]);
    $rol = $stmt->fetchColumn();

    if ($rol === false) {
        return false;
    }

    $rol = mb_strtolower(trim($rol), 'UTF-8');
    return ($rol === 'medico' || $rol === 'médico');
}



if ($option == "incluir") {

    $nombre          = trim($_POST['nombre'] ?? '');
    $direccion       = trim($_POST['direccion'] ?? '');
    $telefono        = trim($_POST['telefono'] ?? '');
    $email           = trim($_POST['email'] ?? '');
    $password        = trim($_POST['password'] ?? '');
    $rolid           = intval($_POST['rolid'] ?? 0);
    $id_especialidad = intval($_POST['id_especialidad'] ?? 0);
    $status          = intval($_POST['status'] ?? 1);

    if (!preg_match('/^[a-zA-ZñÑáéíóúü ]+$/', $nombre)) {

        echo json_encode([
            "error"=>"3"
        ]);

        exit;

    }

    if(
        $nombre=="" ||
        $direccion=="" ||
        $telefono=="" ||
        $email=="" ||
        $password=="" ||
        $rolid==0
    ){

        echo json_encode([
            "error"=>"3"
        ]);

        exit;

    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['error' => '3']);
        exit;
    }

    $stmt = $conn->prepare('SELECT id FROM usuarios WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch()) {
        echo json_encode(['error' => '1']);
        exit;
    }

    $esMedico = esRolMedico($conn, $rolid);

    if ($esMedico) {

        if ($id_especialidad <= 0) {

            echo json_encode([
                'error' => '5'
            ]);

            exit;

        }

        $valorEspecialidad = $id_especialidad;

    } else {

        $valorEspecialidad = null;

    }

    $password_encriptado = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $conn->prepare('INSERT INTO usuarios (
                nombre,
                direccion,
                telefono,
                email,
                password,
                rolid,
                id_especialidad,
                fecha,
                status
            ) VALUES (
                :nombre,
                :direccion,
                :telefono,
                :email,
                :password,
                :rolid,
                :id_especialidad,
                NOW(),
                :status
            )');

        $stmt->execute([
            'nombre'          => $nombre,
            'direccion'       => $direccion,
            'telefono'        => $telefono,
            'email'           => $email,
            'password'        => $password_encriptado,
            'rolid'           => $rolid,
            'id_especialidad' => $valorEspecialidad,
            'status'          => $status,
        ]);

        echo json_encode(['exito' => '1']);
    } catch (PDOException $e) {
        echo json_encode(['error' => '4', 'mensaje' => $e->getMessage()]);
    }

    exit;

}

if ($option === 'consultar') {

    $clave = intval($_GET['id'] ?? 0);

    $stmt = $conn->prepare('SELECT * FROM usuarios WHERE id = :id');
    $stmt->execute(['id' => $clave]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        echo json_encode([
            'error' => '2'
        ]);
        exit;
    }

    if (($data['status'] ?? 0) == 2) {
        echo json_encode([
            'error' => '1'
        ]);
        exit;
    }

    echo json_encode([
        'exito' => '1',
        'id' => $data['id'],
        'nombre' => $data['nombre'],
        'direccion' => $data['direccion'],
        'telefono' => $data['telefono'],
        'email' => $data['email'],
        'rolid' => $data['rolid'],
        'id_especialidad' => $data['id_especialidad'],
        'fecha' => $data['fecha'],
        'status' => $data['status'],
    ]);

    exit;

}




if ($option === 'modificarConsultar') {

    $clave = intval($_GET['id'] ?? 0);

    $stmt = $conn->prepare('SELECT * FROM usuarios WHERE id = :id');
    $stmt->execute(['id' => $clave]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        echo json_encode([
            'error' => '2'
        ]);
        exit;
    }

    echo json_encode([
        'exito' => '1',
        'id' => $data['id'],
        'nombre' => $data['nombre'],
        'direccion' => $data['direccion'],
        'telefono' => $data['telefono'],
        'email' => $data['email'],
        'rolid' => $data['rolid'],
        'id_especialidad' => $data['id_especialidad'],
        'fecha' => $data['fecha'],
        'status' => $data['status'],
    ]);

    exit;

}

if ($option == "modificar") {

    $clave           = intval($_POST['id'] ?? 0);
    $nombre          = trim($_POST['nombre'] ?? '');
    $direccion       = trim($_POST['direccion'] ?? '');
    $telefono        = trim($_POST['telefono'] ?? '');
    $email           = trim($_POST['email'] ?? '');
    $rolid           = intval($_POST['rolid'] ?? 0);
    $id_especialidad = intval($_POST['id_especialidad'] ?? 0);
    $status          = intval($_POST['status'] ?? 1);

    /* VALIDAR NOMBRE */
    if (!preg_match('/^[a-zA-ZñÑáéíóúü ]+$/', $nombre)) {

        echo json_encode([
            "error" => "3"
        ]);

        exit;

    }

    /* VALIDAR CAMPOS */
    if (
        $clave <= 0 ||
        $nombre == "" ||
        $direccion == "" ||
        $telefono == "" ||
        $email == "" ||
        $rolid == 0
    ) {

        echo json_encode([
            "error" => "3"
        ]);

        exit;

    }

    /* VALIDAR EMAIL */
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        echo json_encode([
            "error" => "3"
        ]);

        exit;

    }

    /* VERIFICAR USUARIO */
    $stmt = $conn->prepare('SELECT id FROM usuarios WHERE id = :id');
    $stmt->execute(['id' => $clave]);

    if (!$stmt->fetch()) {
        echo json_encode([
            'error' => '2'
        ]);
        exit;
    }

    /* VERIFICAR CORREO DUPLICADO */
    $stmt = $conn->prepare('SELECT id FROM usuarios WHERE email = :email AND id <> :id');
    $stmt->execute([
        'email' => $email,
        'id' => $clave,
    ]);

    if ($stmt->fetch()) {
        echo json_encode([
            'error' => '1'
        ]);
        exit;
    }

    /* VALIDAR ESPECIALIDAD SI ES MÉDICO */
    $esMedico = esRolMedico($conn, $rolid);

    if ($esMedico) {

        if ($id_especialidad <= 0) {

            echo json_encode([
                "error" => "5"
            ]);

            exit;

        }

        $valorEspecialidad = $id_especialidad;

    } else {

        $valorEspecialidad = null;

    }

    /* UPDATE */
    try {
        $stmt = $conn->prepare('UPDATE usuarios SET
                nombre = :nombre,
                direccion = :direccion,
                telefono = :telefono,
                email = :email,
                rolid = :rolid,
                id_especialidad = :id_especialidad,
                status = :status
            WHERE id = :id');
        $stmt->execute([
            'nombre' => $nombre,
            'direccion' => $direccion,
            'telefono' => $telefono,
            'email' => $email,
            'rolid' => $rolid,
            'id_especialidad' => $valorEspecialidad,
            'status' => $status,
            'id' => $clave
        ]);

        if ($clave == intval($_SESSION['id'] ?? 0)) {
            session_unset();
            session_destroy();
            echo json_encode([
                'exito' => 1,
                'cerrarSesion' => 1,
                'mensaje' => 'Tu rol fue actualizado correctamente. Algunos permisos cambiaron, por lo que es necesario iniciar sesión nuevamente para continuar.'
            ]);
        } else {
            echo json_encode([
                'exito' => 1
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'error' => 4,
            'mensaje' => $e->getMessage()
        ]);
    }

    exit;

}

if ($option === 'eliminar') {

    $clave = intval($_GET['id'] ?? 0);

    $stmt = $conn->prepare('SELECT id FROM usuarios WHERE id = :id');
    $stmt->execute(['id' => $clave]);

    if (!$stmt->fetch()) {
        echo json_encode([
            'error' => '1'
        ]);
        exit;
    }

    try {
        $stmt = $conn->prepare('UPDATE usuarios SET status = 2 WHERE id = :id');
        $stmt->execute(['id' => $clave]);
        echo json_encode(['exito' => '1']);
    } catch (PDOException $e) {
        echo json_encode(['error' => '4', 'mensaje' => $e->getMessage()]);
    }

    exit;
}


echo json_encode([
    'error' => '0',
    'mensaje' => 'Opción no válida.'
]);

?>