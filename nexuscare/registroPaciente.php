<?php
require_once("tools/mypathdb.php");

header('Content-Type: application/json; charset=utf-8');

/* VALIDAR CONEXIÓN */
if (!$conn) {
    echo json_encode([
        "error"   => 1,
        "mensaje" => "Error de conexión con la base de datos"
    ]);
    exit;
}

/* DATOS DEL FORMULARIO */
$nombre    = trim($_POST['nombre'] ?? '');
$email     = trim($_POST['email'] ?? '');
$telefono  = trim($_POST['telefono'] ?? '');
$password  = trim($_POST['password'] ?? '');
$confirmar = trim($_POST['confirmar'] ?? '');

/* VALIDAR CAMPOS */
if ($nombre === '' || $email === '' || $telefono === '' || $password === '' || $confirmar === '') {
    echo json_encode([
        "error"   => 1,
        "mensaje" => "Debe completar todos los campos"
    ]);
    exit;
}

/* VALIDAR NOMBRE */
if (!preg_match('/^[a-zA-ZñÑáéíóúÁÉÍÓÚüÜ ]+$/u', $nombre)) {
    echo json_encode([
        "error"   => 1,
        "mensaje" => "El nombre contiene caracteres no válidos"
    ]);
    exit;
}

/* VALIDAR EMAIL */
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        "error"   => 1,
        "mensaje" => "El correo electrónico no es válido"
    ]);
    exit;
}

/* VALIDAR TELÉFONO: solo números, 10 dígitos */
if (!preg_match('/^[0-9]{10}$/', $telefono)) {
    echo json_encode([
        "error"   => 1,
        "mensaje" => "El número de teléfono debe contener exactamente 10 dígitos"
    ]);
    exit;
}

/* VALIDAR CONTRASEÑA */
if ($password !== $confirmar) {
    echo json_encode([
        "error"   => 1,
        "mensaje" => "Las contraseñas no coinciden"
    ]);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode([
        "error"   => 1,
        "mensaje" => "La contraseña debe tener al menos 6 caracteres"
    ]);
    exit;
}

try {
    /* VALIDAR SI YA EXISTE EL CORREO */
    $stmtExiste = $conn->prepare("SELECT id FROM usuarios WHERE email = :email LIMIT 1");
    $stmtExiste->execute([":email" => $email]);

    if ($stmtExiste->fetch(PDO::FETCH_ASSOC)) {
        echo json_encode([
            "error"   => 1,
            "mensaje" => "Ya existe un usuario registrado con ese correo"
        ]);
        exit;
    }

    /* BUSCAR EL ROL PACIENTE */
    $stmtRol = $conn->prepare(
        "SELECT id
         FROM roles
         WHERE LOWER(nombre) = 'paciente'
         LIMIT 1"
    );
    $stmtRol->execute();
    $rolPaciente = $stmtRol->fetch(PDO::FETCH_ASSOC);

    if (!$rolPaciente) {
        echo json_encode([
            "error"   => 1,
            "mensaje" => "No existe el rol Paciente en la base de datos"
        ]);
        exit;
    }

    $rolid = intval($rolPaciente['id']);

    /* ENCRIPTAR CONTRASEÑA */
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    /* VALORES POR DEFECTO PARA PACIENTE */
    $direccion = 'Sin dirección registrada';
    $fecha = date('Y-m-d H:i:s');
    $status = 1;

    /* INSERTAR PACIENTE */
    $stmtInsert = $conn->prepare(
        "INSERT INTO usuarios 
         (nombre, direccion, email, password, rolid, id_especialidad, fecha, status, telefono, especialidad)
         VALUES
         (:nombre, :direccion, :email, :password, :rolid, NULL, :fecha, :status, :telefono, NULL)"
    );

    $stmtInsert->execute([
        ":nombre"    => $nombre,
        ":direccion" => $direccion,
        ":email"     => $email,
        ":password"  => $passwordHash,
        ":rolid"     => $rolid,
        ":fecha"     => $fecha,
        ":status"    => $status,
        ":telefono"  => $telefono
    ]);

    if ($stmtInsert->rowCount() > 0) {
        echo json_encode([
            "exito"   => 1,
            "mensaje" => "Paciente registrado correctamente"
        ]);
    } else {
        echo json_encode([
            "error"   => 1,
            "mensaje" => "No se pudo registrar el paciente"
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        "error"   => 1,
        "mensaje" => "Error al registrar paciente: " . $e->getMessage()
    ]);
}
?>