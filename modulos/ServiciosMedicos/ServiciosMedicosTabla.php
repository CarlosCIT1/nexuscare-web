<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/mi_proyecto/tools/mypathdb.php');

$stmt = $conn->query("SELECT sm.*, e.nombre AS especialidad
        FROM servicios_medicos sm
        LEFT JOIN especialidades e
            ON sm.id_especialidad = e.id
        ORDER BY sm.id DESC");

$servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="card-body">

    <div class="table-responsive">

        <table class="table table-bordered table-striped align-middle">

            <thead class="table-light">

                <tr>

                    <th style="width:60px;">#</th>

                    <th>Servicio Médico</th>

                    <th>Área Médica</th>

                    <th>Especialidad</th>

                    <th>Descripción</th>

                    <th>Cupos</th>

                    <th>Estado</th>

                    <th style="width:140px;">Acciones</th>

                </tr>

            </thead>

            <tbody>

            <?php

            $i = 1;

            foreach ($servicios as $row) {

            ?>

                <tr>

                    <td>

                        <?php echo $i++; ?>

                    </td>

                    <td>

                        <strong>

                            <?php echo htmlspecialchars($row['nombre']); ?>

                        </strong>

                    </td>

                    <td>

                        <?php echo htmlspecialchars($row['marca']); ?>

                    </td>

                    <td>

                        <?php echo htmlspecialchars($row['especialidad']); ?>

                    </td>

                    <td>

                        <?php echo htmlspecialchars($row['descripcion']); ?>

                    </td>

                    <td>

                        <?php echo (int)$row['stock']; ?>

                    </td>

                    <td>

                        <?php if($row['status']==1){ ?>

                            <span class="badge bg-success">

                                Activo

                            </span>

                        <?php }else{ ?>

                            <span class="badge bg-danger">

                                Inactivo

                            </span>

                        <?php } ?>

                    </td>

                    <td>

                        <button
                            class="btn btn-primary btn-sm"
                            onclick="ModificarServicio(<?php echo $row['id']; ?>)"
                            data-bs-toggle="modal"
                            data-bs-target="#modalServicio">

                            <i class="bi bi-pencil-square"></i>

                        </button>

                        <button
                            class="btn btn-danger btn-sm"
                            onclick="EliminarServicio(<?php echo $row['id']; ?>)">

                            <i class="bi bi-trash"></i>

                        </button>

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

// El cierre manual no es necesario con PDO.

?>