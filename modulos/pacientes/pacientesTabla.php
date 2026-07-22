<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once($_SERVER['DOCUMENT_ROOT'].'/mi_proyecto/tools/mypathdb.php');

$rolUsuario = strtolower(trim($_SESSION['rol'] ?? ''));
$idSesion   = intval($_SESSION['id'] ?? 0);

if ($rolUsuario == "administrador") {

    $sql = "
        SELECT
            u.id,
            u.nombre,
            u.email,
            u.telefono
        FROM usuarios u
        INNER JOIN roles r
            ON u.rolid = r.id
        WHERE LOWER(r.nombre)='paciente'
        AND u.status=1
        ORDER BY u.nombre
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

} else {

    $sql = "
        SELECT DISTINCT
            u.id,
            u.nombre,
            u.email,
            u.telefono
        FROM citas c
        INNER JOIN usuarios u
            ON c.id_paciente=u.id
        WHERE c.id_medico = :idSesion
        AND c.status=1
        ORDER BY u.nombre
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':idSesion' => $idSesion
    ]);

}

$pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="table-responsive">

<table class="table table-bordered table-striped align-middle">

<thead>

<tr>

<th>#</th>
<th>Nombre</th>
<th>Correo</th>
<th>Teléfono</th>
<th width="120">Acciones</th>

</tr>

</thead>

<tbody>

<?php

$i = 1;

foreach ($pacientes as $row) {

?>

<tr>

<td><?php echo $i++; ?></td>

<td>

<?php echo htmlspecialchars($row["nombre"]); ?>

</td>

<td>

<?php echo htmlspecialchars($row["email"]); ?>

</td>

<td>

<?php echo htmlspecialchars($row["telefono"]); ?>

</td>

<td class="text-center">

<button

class="btn btn-info btn-sm"

onclick="VerPaciente(<?php echo $row['id']; ?>)">

<i class="bi bi-eye"></i>

</button>

</td>

</tr>
<?php
}
?>
</tbody>
</table>
</div>