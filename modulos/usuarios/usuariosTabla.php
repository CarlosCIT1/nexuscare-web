<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/mi_proyecto/tools/mypathdb.php');

$sql = 'SELECT
            u.*,
            r.nombre AS rol,
            e.nombre AS especialidad_nombre
        FROM usuarios u
        LEFT JOIN roles r
            ON u.rolid = r.id
        LEFT JOIN especialidades e
            ON u.id_especialidad = e.id
        ORDER BY u.id DESC';

$stmt = $conn->query($sql);
if (!$stmt) {
    die('Error SQL al obtener usuarios.');
}
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<style>
.avatar-iniciales{
    width:40px;
    height:40px;
    background:#0d6efd;
    color:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    border-radius:50%;
    font-weight:bold;
    font-size:14px;
}
</style>

<div class="card-body">

<table class="table table-bordered table-striped align-middle">

    <thead>

        <tr>
            <th style="width:10px">#</th>
            <th>Usuario</th>
            <th>Teléfono</th>
            <th>Rol</th>
            <th>Especialidad</th>
            <th>Fecha</th>
            <th style="width:120px">Estatus</th>
            <th style="width:120px">Acciones</th>
        </tr>

    </thead>

    <tbody>

<?php

if (!empty($rows)) {

    $contador = 1;

    foreach ($rows as $row) {

        $nombre = $row['nombre'] ?? '';

        $palabras = explode(" ", trim($nombre));

        $iniciales = "";

        foreach($palabras as $p){

            if($p != ""){
                $iniciales .= strtoupper(substr($p,0,1));
            }

            if(strlen($iniciales) >= 2){
                break;
            }

        }

        $rol = strtolower(trim($row['rol'] ?? ''));

        if($rol == "medico" || $rol == "médico"){
            $especialidad = !empty($row['especialidad_nombre'])
                ? $row['especialidad_nombre']
                : "Sin especialidad";
        }elseif($rol == "administrador"){
            $especialidad = "Administración";
        }else{
            $especialidad = "No aplica";
        }

        $telefono = !empty($row['telefono'])
            ? $row['telefono']
            : "Sin teléfono";

        $fecha = $row['fecha'] ?? '';

        $estatus = $row['status'] ?? 0;

?>

<tr>

    <td><?= $contador++; ?></td>

    <td>

        <div class="d-flex align-items-center">

            <div class="avatar-iniciales">
                <?= htmlspecialchars($iniciales); ?>
            </div>

            <div class="ms-2">

                <strong>
                    <?= htmlspecialchars($nombre); ?>
                </strong>

                <br>

                <small class="text-muted">
                    <?= htmlspecialchars($row['email'] ?? ''); ?>
                </small>

            </div>

        </div>

    </td>

    <td>
        <?= htmlspecialchars($telefono); ?>
    </td>

    <td>

        <span class="badge text-bg-info">
            <?= htmlspecialchars($row['rol'] ?? ''); ?>
        </span>

    </td>

    <td>
        <?= htmlspecialchars($especialidad); ?>
    </td>

    <td>
        <?= htmlspecialchars($fecha); ?>
    </td>

    <td>

        <?php if($estatus == 1){ ?>

            <span class="badge text-bg-success">
                Activo
            </span>

        <?php }else{ ?>

            <span class="badge text-bg-danger">
                Inactivo
            </span>

        <?php } ?>

    </td>

    <td class="text-center">

        <button
            class="btn btn-primary btn-sm me-1"
            onclick="Modificar('<?= $row['id']; ?>')"
            data-bs-toggle="modal"
            data-bs-target="#modalUsuarios">

            <i class="bi bi-pencil-square"></i>

        </button>

        <button
            class="btn btn-danger btn-sm"
            onclick="Eliminar('<?= $row['id']; ?>')">

            <i class="bi bi-trash"></i>

        </button>

    </td>

</tr>

<?php

    }

}else{

?>

<tr>

    <td colspan="8" class="text-center text-muted">
        No hay usuarios registrados.
    </td>

</tr>

<?php

}

?>

    </tbody>

</table>

</div>

<?php
// PDO no requiere cierre explícito de conexión.
?>