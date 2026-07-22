<!-- BOTÓN -->
<button class="btn btn-success"
    onclick="abrirModalServicio()"
    data-bs-toggle="modal"
    data-bs-target="#modalServicio">

    <i class="bi bi-plus-circle"></i>
    Agregar Servicio Médico
</button>

<!-- MODAL -->
<div class="modal fade"
    id="modalServicio"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-lg">

        <div class="modal-content">

            <div class="modal-header headerRegister">

                <h5 class="modal-title" id="modalTitleServicio">

                    Nuevo Servicio Médico

                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <form
                    id="formServicio"
                    enctype="multipart/form-data">

                    <input
                        type="hidden"
                        id="idServicio"
                        name="id">

                    <!-- NOMBRE -->
                    <div class="mb-3">

                        <label class="form-label">

                            Nombre del servicio

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="nombreServicio"
                            name="nombre"
                            required>

                    </div>

                    <!-- ÁREA MÉDICA -->
                    <div class="mb-3">

                        <label class="form-label">

                            Área médica

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="marcaServicio"
                            name="marca"
                            required>

                    </div>

                    <!-- ESPECIALIDAD -->
                    <div class="mb-3">

                        <label class="form-label">

                            Especialidad

                        </label>

                        <select
                            class="form-control"
                            id="idEspecialidadServicio"
                            name="id_especialidad"
                            required>

                            <option value="">
                                Seleccione una especialidad
                            </option>

                            <?php

                            require_once($_SERVER['DOCUMENT_ROOT'].'/mi_proyecto/tools/mypathdb.php');

                            $stmt = $conn->prepare("SELECT id, nombre
                                    FROM especialidades
                                    WHERE status = 1
                                    ORDER BY nombre ASC");
                            $stmt->execute();

                            while ($esp = $stmt->fetch(PDO::FETCH_ASSOC)) {

                            ?>

                                <option value="<?= $esp['id']; ?>">

                                    <?= htmlspecialchars($esp['nombre']); ?>

                                </option>

                            <?php

                            }

                            ?>

                        </select>

                    </div>

                    <!-- DESCRIPCIÓN -->
                    <div class="mb-3">

                        <label class="form-label">

                            Descripción

                        </label>

                        <textarea
                            class="form-control"
                            id="descripcionServicio"
                            name="descripcion"
                            rows="4"
                            required></textarea>

                    </div>

                    <!-- CUPOS -->
                    <div class="mb-3">

                        <label class="form-label">

                            Cupos disponibles

                        </label>

                        <input
                            type="number"
                            class="form-control"
                            id="stockServicio"
                            name="stock"
                            min="0"
                            required>

                    </div>

                    <!-- ESTADO -->
                    <div class="mb-3">

                        <label class="form-label">

                            Estado

                        </label>

                        <select
                            class="form-control"
                            id="statusServicio"
                            name="status">

                            <option value="1">

                                Activo

                            </option>

                            <option value="2">

                                Inactivo

                            </option>

                        </select>

                    </div>

                    <!-- BOTONES -->
                    <div class="text-end">

                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">

                            Cancelar

                        </button>

                        <button
                            type="submit"
                            class="btn btn-success"
                            id="btnActionServicio">

                            Guardar

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>
