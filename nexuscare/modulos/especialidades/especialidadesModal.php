<div class="modal fade" id="modalEspecialidades" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-lg">

        <div class="modal-content">

            <div class="modal-header headerRegister">

                <h5 class="modal-title" id="modalTitleEspecialidad">

                    <i class="bi bi-tags"></i>

                    Nueva Especialidad

                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <form id="formEspecialidades" enctype="multipart/form-data">

                    <input
                        type="hidden"
                        id="idEspecialidad"
                        name="id">

                    <div class="mb-3">

                        <label class="form-label">
                            Nombre de la especialidad
                        </label>

                        <input
                            type="text"
                            id="nombre"
                            name="nombre"
                            class="form-control"
                            required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Descripción
                        </label>

                        <textarea
                            id="descripcion"
                            name="descripcion"
                            class="form-control"
                            rows="4"
                            required></textarea>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Imagen
                        </label>

                        <input
                            type="file"
                            id="imagen"
                            name="imagen"
                            class="form-control"
                            accept=".jpg,.jpeg,.png,.webp,image/*">

                        <small class="text-muted">

                            Formatos permitidos:
                            JPG, JPEG, PNG y WEBP.

                        </small>

                    </div>

                    <div
                        class="mb-3"
                        id="contenedorPreview"
                        style="display:none;">

                        <label class="form-label">

                            Vista previa

                        </label>

                        <br>

                        <img
                            id="previewImagen"
                            src=""
                            alt="Vista previa"
                            style="max-width:180px;
                                   border-radius:8px;
                                   border:1px solid #ddd;
                                   padding:4px;">

                    </div>

                    <div class="mb-3">

                        <label class="form-label">

                            Estado

                        </label>

                        <select
                            id="status"
                            name="status"
                            class="form-control">

                            <option value="1">
                                Activo
                            </option>

                            <option value="2">
                                Inactivo
                            </option>

                        </select>

                    </div>

                    <div class="text-end">

                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">

                            <i class="bi bi-x-circle"></i>

                            Cancelar

                        </button>

                        <button
                            type="submit"
                            class="btn btn-primary"
                            id="btnGuardarEspecialidad">

                            <i class="bi bi-save"></i>

                            Guardar

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>