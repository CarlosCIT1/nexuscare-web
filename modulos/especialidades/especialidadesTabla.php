<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/mi_proyecto/tools/mypathdb.php');

$sql = 'SELECT * FROM especialidades ORDER BY id DESC';
$stmt = $conn->query($sql);
$rows = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
?>

<div class="table-responsive">

    <table class="table table-bordered table-striped align-middle">

        <thead class="table-light">

            <tr>

                <th style="width:70px;">#</th>

                <th>Especialidad</th>

                <th>Descripción</th>

                <th style="width:140px;">Imagen</th>

                <th style="width:150px;">Fecha</th>

                <th style="width:110px;">Estado</th>

                <th style="width:140px;">Acciones</th>

            </tr>

        </thead>

        <tbody>

        <?php if (!empty($rows)): ?>

            <?php foreach ($rows as $row): ?>

                <tr>

                    <td><?php echo $row['id']; ?></td>

                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>

                    <td><?php echo htmlspecialchars($row['descripcion']); ?></td>

                    <td class="text-center">

                        <?php if (!empty($row['imagen'])): ?>

                            <img
                                src="/mi_proyecto/uploads/categorias/<?php echo htmlspecialchars($row['imagen']); ?>"
                                alt="Especialidad"
                                style="width:90px;height:70px;object-fit:cover;border-radius:8px;border:1px solid #ddd;">

                        <?php else: ?>

                            <span class="text-muted">

                                Sin imagen

                            </span>

                        <?php endif; ?>

                    </td>

                    <td>

                        <?php echo htmlspecialchars($row['fecha']); ?>

                    </td>

                    <td>

                        <?php if ($row['status'] == 1): ?>

                            <span class="badge bg-success">

                                Activa

                            </span>

                        <?php else: ?>

                            <span class="badge bg-danger">

                                Inactiva

                            </span>

                        <?php endif; ?>

                    </td>

                    <td>

                        <button
                            class="btn btn-primary btn-sm"
                            type="button"
                            onclick="ModificarEspecialidad(<?php echo $row['id']; ?>)">

                            <i class="bi bi-pencil-square"></i>

                        </button>

                        <button
                            class="btn btn-danger btn-sm"
                            type="button"
                            onclick="EliminarEspecialidad(<?php echo $row['id']; ?>)">

                            <i class="bi bi-trash"></i>

                        </button>

                    </td>

                </tr>

            <?php endforeach; ?>

        <?php else: ?>

            <tr>

                <td colspan="7" class="text-center">

                    No hay especialidades registradas.

                </td>

            </tr>

        <?php endif; ?>

        </tbody>

    </table>

</div>