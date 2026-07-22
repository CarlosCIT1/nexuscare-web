<!-- BOTON -->
<button class="btn btn-primary"
    onclick="abrirModalUsuario()"
    data-bs-toggle="modal"
    data-bs-target="#modalUsuarios">
    Nuevo Usuario
</button>

<!-- MODAL -->
<div class="modal fade" id="modalUsuarios" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header headerRegister">
                <h5 class="modal-title" id="modalTitle">Nuevo Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <form id="formUsuarios">

                    <input type="hidden" id="idUsuario" name="id">

                    <!-- NOMBRE -->
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre completo</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>

                    <!-- DIRECCIÓN -->
                    <div class="mb-3">
                        <label for="direccion" class="form-label">Dirección</label>
                        <input type="text" class="form-control" id="direccion" name="direccion" required>
                    </div>

                    <!-- TELÉFONO -->
                    <div class="mb-3">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="telefono" name="telefono" required>
                    </div>

                    <!-- EMAIL -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo electrónico</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <!-- PASSWORD -->
                    <div class="mb-3" id="grupoPassword">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>

                    <?php
                    require_once($_SERVER['DOCUMENT_ROOT'].'/mi_proyecto/tools/mypathdb.php');

                    $rolesStmt = $conn->query('SELECT id, nombre FROM roles WHERE status = 1 ORDER BY nombre ASC');
                    $roles = $rolesStmt ? $rolesStmt->fetchAll(PDO::FETCH_ASSOC) : [];

                    $especialidadesStmt = $conn->query('SELECT id, nombre FROM especialidades WHERE status = 1 ORDER BY nombre ASC');
                    $especialidades = $especialidadesStmt ? $especialidadesStmt->fetchAll(PDO::FETCH_ASSOC) : [];
                    ?>

                    <!-- ROL -->
                    <div class="mb-3">
                        <label for="rolid" class="form-label">Rol</label>
                        <select class="form-select" id="rolid" name="rolid" required onchange="mostrarCamposRol()">
                            <option value="">Seleccione</option>

                            <?php foreach ($roles as $rol): ?>
                                <option value="<?php echo $rol['id']; ?>">
                                    <?php echo htmlspecialchars($rol['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- ESPECIALIDAD -->
                    <div class="mb-3" id="grupoEspecialidad" style="display:none;">
                        <label for="id_especialidad" class="form-label">Especialidad médica</label>
                        <select class="form-select" id="id_especialidad" name="id_especialidad">
                            <option value="">Seleccione una especialidad</option>

                            <?php foreach ($especialidades as $esp): ?>
                                <option value="<?php echo $esp['id']; ?>">
                                    <?php echo htmlspecialchars($esp['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- STATUS -->
                    <div class="mb-3">
                        <label for="status" class="form-label">Estatus</label>
                        <select class="form-select" id="status" name="status">
                            <option value="1">Activo</option>
                            <option value="2">Inactivo</option>
                        </select>
                    </div>

                    <!-- BOTONES -->
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Cerrar
                        </button>

                        <button type="submit" class="btn btn-primary">
                            Guardar
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

<script>
function mostrarCamposRol() {
    const selectRol = document.getElementById("rolid");
    const textoRol = selectRol.options[selectRol.selectedIndex]?.text.toLowerCase().trim();
    const grupoEspecialidad = document.getElementById("grupoEspecialidad");
    const campoEspecialidad = document.getElementById("id_especialidad");

    if (textoRol === "medico" || textoRol === "médico") {
        grupoEspecialidad.style.display = "block";
    } else {
        grupoEspecialidad.style.display = "none";
        campoEspecialidad.value = "";
    }
}
</script>