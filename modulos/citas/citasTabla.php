<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once($_SERVER['DOCUMENT_ROOT'].'/mi_proyecto/tools/mypathdb.php');

$rolUsuario = strtolower(trim($_SESSION['rol'] ?? ''));
$idSesion   = intval($_SESSION['id'] ?? 0);

$where = " WHERE c.status IN (1,2) ";

if ($rolUsuario == "paciente") {

    $where .= " AND c.id_paciente=$idSesion ";

} elseif ($rolUsuario == "medico" || $rolUsuario == "médico") {

    $where .= " AND c.id_medico=$idSesion ";

}

try {
    $stmt = $conn->prepare(
        "SELECT
            c.*,
            p.nombre paciente,
            p.id idPaciente,
            m.nombre medico,
            esp.nombre especialidad,
            sm.nombre servicio
         FROM citas c
         LEFT JOIN usuarios p ON c.id_paciente = p.id
         LEFT JOIN usuarios m ON c.id_medico = m.id
         LEFT JOIN especialidades esp ON c.id_especialidad = esp.id
         LEFT JOIN servicios_medicos sm ON c.id_servicio = sm.id
         $where
         ORDER BY c.id DESC"
    );
    $stmt->execute();
    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Error en consulta de citas: ' . $e->getMessage());
}

?>

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered table-striped align-middle">

<thead>

<tr>

<th>#</th>

<th>Paciente</th>

<th>Médico</th>

<th>Especialidad</th>

<th>Servicio</th>

<th>Fecha</th>

<th>Hora</th>

<th>Estado</th>

<th>Observaciones</th>

<th width="220">

Acciones

</th>

</tr>

</thead>

<tbody>

<?php

$contador = 1;

foreach ($resultado as $row) {
    $estadoLower = strtolower(trim($row["estado"]));

?>
<tr>

    <td><?php echo $contador++; ?></td>

    <td><?php echo htmlspecialchars($row["paciente"] ?? "Sin paciente"); ?></td>

    <td><?php echo htmlspecialchars($row["medico"] ?? "Sin médico"); ?></td>

    <td><?php echo htmlspecialchars($row["especialidad"] ?? "Sin especialidad"); ?></td>

    <td><?php echo htmlspecialchars($row["servicio"] ?? "Sin servicio"); ?></td>

    <td><?php echo htmlspecialchars($row["fecha_cita"]); ?></td>

    <td><?php echo substr($row["hora_cita"],0,5); ?></td>

    <td>

<?php if($estadoLower=="pendiente"){ ?>

        <span class="badge text-bg-warning">

            Pendiente

        </span>

<?php }elseif($estadoLower=="confirmada"){ ?>

        <span class="badge text-bg-primary">

            Confirmada

        </span>

<?php }elseif($estadoLower=="atendida"){ ?>

        <span class="badge text-bg-success">

            Atendida

        </span>

<?php }elseif($estadoLower=="cancelada"){ ?>

        <span class="badge text-bg-danger">

            Cancelada

        </span>

<?php }else{ ?>

        <span class="badge text-bg-secondary">

            <?php echo htmlspecialchars($row["estado"]); ?>

        </span>

<?php } ?>

    </td>

    <td>

        <?php

        if($row["observaciones"]!=""){

            echo htmlspecialchars($row["observaciones"]);

        }else{

            echo "Sin observaciones";

        }

        ?>

    </td>

    <td class="text-center">

<?php

if($rolUsuario=="paciente"){

    if($estadoLower!="atendida" && $estadoLower!="cancelada"){

?>

        <button
            class="btn btn-primary btn-sm"
            onclick="ModificarCita('<?php echo $row['id']; ?>')"
            data-bs-toggle="modal"
            data-bs-target="#modalCita">

            <i class="bi bi-pencil-square"></i>

        </button>

        <button
            class="btn btn-danger btn-sm"
            onclick="EliminarCita('<?php echo $row['id']; ?>')">

            <i class="bi bi-x-circle"></i>

        </button>

<?php

    }

}elseif($rolUsuario=="medico" || $rolUsuario=="médico"){

?>

        <button
            class="btn btn-info btn-sm"
            onclick="VerPaciente('<?php echo $row['idPaciente']; ?>')"
            title="Ver información del paciente">

            <i class="bi bi-person-vcard"></i>

        </button>

<?php

    if($estadoLower=="pendiente" || $estadoLower=="confirmada"){

?>

        <button
            class="btn btn-success btn-sm"
            onclick="MarcarAtendida('<?php echo $row['id']; ?>')">

            <i class="bi bi-check-circle"></i>

        </button>

<?php

    }elseif($estadoLower=="atendida"){

?>

        <span class="badge text-bg-success">

            Atendida

        </span>

<?php

    }

}elseif($rolUsuario=="administrador"){

?>

        <button
            class="btn btn-primary btn-sm"
            onclick="ModificarCita('<?php echo $row['id']; ?>')"
            data-bs-toggle="modal"
            data-bs-target="#modalCita">

            <i class="bi bi-pencil-square"></i>

        </button>

        <button
            class="btn btn-success btn-sm"
            onclick="MarcarAtendida('<?php echo $row['id']; ?>')">

            <i class="bi bi-check-circle"></i>

        </button>

        <button
            class="btn btn-danger btn-sm"
            onclick="EliminarCita('<?php echo $row['id']; ?>')">

            <i class="bi bi-x-circle"></i>

        </button>

<?php

}

?>

    </td>

</tr>
<?php

}

if (empty($resultado)) {

?>

<tr>

    <td colspan="10" class="text-center text-muted">

        No hay citas registradas.

    </td>

</tr>

<?php

}

?>

            </tbody>

        </table>

    </div>

</div>

<?php
?>