<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once($_SERVER['DOCUMENT_ROOT'].'/mi_proyecto/tools/mypathdb.php');

$rolUsuario = strtolower(trim($_SESSION['rol'] ?? ''));
$idSesion   = intval($_SESSION['id'] ?? 0);

$pacientes = null;

try {
    if ($rolUsuario == 'administrador') {
        $stmtPacientes = $conn->prepare(
            "SELECT
                u.id,
                u.nombre
             FROM usuarios u
             INNER JOIN roles r
                ON u.rolid = r.id
             WHERE u.status = 1
             AND LOWER(r.nombre) = 'paciente'
             ORDER BY u.nombre ASC"
        );
        $stmtPacientes->execute();
        $pacientes = $stmtPacientes->fetchAll(PDO::FETCH_ASSOC);
    }

    $stmtEspecialidades = $conn->prepare(
        "SELECT
            id,
            nombre
         FROM especialidades
         WHERE status = 1
         ORDER BY nombre ASC"
    );
    $stmtEspecialidades->execute();
    $especialidades = $stmtEspecialidades->fetchAll(PDO::FETCH_ASSOC);

    $stmtServicios = $conn->prepare(
        "SELECT
            id,
            nombre
         FROM servicios_medicos
         WHERE status = 1
         ORDER BY nombre ASC"
    );
    $stmtServicios->execute();
    $servicios = $stmtServicios->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Error en consulta de datos: ' . $e->getMessage());
}

?>

<?php if ($rolUsuario == "paciente" || $rolUsuario == "administrador") { ?>

<button
    class="btn btn-primary"
    onclick="abrirModalCita()"
    data-bs-toggle="modal"
    data-bs-target="#modalCita">

    <i class="bi bi-plus-circle"></i>

    Nueva Cita

</button>

<?php } ?>

<div
    class="modal fade"
    id="modalCita"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-lg">

        <div class="modal-content">

            <div class="modal-header headerRegister">

                <h5
                    class="modal-title"
                    id="modalTitleCita">

                    <i class="bi bi-calendar2-check"></i>

                    Nueva Cita Médica

                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <form id="formCita">

                    <input
                        type="hidden"
                        id="idCita"
                        name="id">

<?php if ($rolUsuario == "paciente") { ?>

                    <input
                        type="hidden"
                        id="id_paciente"
                        name="id_paciente"
                        value="<?php echo $idSesion; ?>">

<?php } ?>

<?php if ($rolUsuario == "administrador") { ?>

                    <div class="mb-3">

                        <label class="form-label">

                            Paciente

                        </label>

                        <select
                            class="form-select"
                            id="id_paciente"
                            name="id_paciente"
                            required>

                            <option value="">

                                Seleccione un paciente

                            </option>

<?php foreach ($pacientes as $p) { ?>

                            <option
                                value="<?php echo $p["id"]; ?>">

                                <?php echo htmlspecialchars($p["nombre"]); ?>

                            </option>

<?php } ?>

                        </select>

                    </div>

<?php } ?>

                    <div class="mb-3">

                        <label class="form-label">

                            Especialidad médica

                        </label>

                        <select
                            class="form-select"
                            id="id_especialidad"
                            name="id_especialidad"
                            required>

                            <option value="">

                                Seleccione una especialidad

                            </option>

<?php foreach ($especialidades as $e) { ?>

                            <option
                                value="<?php echo $e["id"]; ?>">

                                <?php echo htmlspecialchars($e["nombre"]); ?>

                            </option>

<?php } ?>

                        </select>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">

                            Doctor / Médico

                        </label>

                        <select
                            class="form-select"
                            id="id_medico"
                            name="id_medico"
                            required>

                            <option value="">

                                Primero seleccione una especialidad

                            </option>

                        </select>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">

                            Servicio Médico

                        </label>

                        <select
                            class="form-select"
                            id="id_servicio"
                            name="id_servicio"
                            required>

                            <option value="">

                                Seleccione un servicio

                            </option>

<?php foreach ($servicios as $s) { ?>

                            <option
                                value="<?php echo $s["id"]; ?>">

                                <?php echo htmlspecialchars($s["nombre"]); ?>

                            </option>

<?php } ?>

                        </select>

                    </div>
                                        <div class="mb-3">

                        <label class="form-label">

                            Fecha de la cita

                        </label>

                        <input
                            type="date"
                            class="form-control"
                            id="fecha_cita"
                            name="fecha_cita"
                            min="<?php echo date('Y-m-d'); ?>"
                            required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">

                            Hora

                        </label>

                        <select
                            class="form-select"
                            id="hora_cita"
                            name="hora_cita"
                            required
                            disabled>

                            <option value="">

                                Seleccione primero el médico y la fecha

                            </option>

                        </select>

                        <small class="text-muted">

                            Horario disponible de 06:00 AM a 10:00 PM.

                        </small>

                    </div>

<?php if ($rolUsuario == "administrador") { ?>

                    <div class="mb-3">

                        <label class="form-label">

                            Estado

                        </label>

                        <select
                            class="form-select"
                            id="estado"
                            name="estado">

                            <option value="Pendiente">

                                Pendiente

                            </option>

                            <option value="Confirmada">

                                Confirmada

                            </option>

                            <option value="Atendida">

                                Atendida

                            </option>

                            <option value="Cancelada">

                                Cancelada

                            </option>

                        </select>

                    </div>

<?php } else { ?>

                    <input
                        type="hidden"
                        id="estado"
                        name="estado"
                        value="Pendiente">

<?php } ?>

                    <div class="mb-3">

                        <label class="form-label">

                            Observaciones

                        </label>

                        <textarea
                            class="form-control"
                            id="observaciones"
                            name="observaciones"
                            rows="4"
                            placeholder="Escriba alguna observación"></textarea>

                    </div>

                    <div class="text-end">

                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">

                            Cancelar

                        </button>

                        <button
                            type="submit"
                            class="btn btn-primary"
                            id="btnActionCita">

                            Guardar

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

<?php


?>