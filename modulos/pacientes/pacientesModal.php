<div
    class="modal fade"
    id="modalPaciente"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-lg">

        <div class="modal-content">

            <div class="modal-header headerRegister">

                <h5 class="modal-title">

                    <i class="bi bi-person-vcard"></i>

                    Información del Paciente

                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            Nombre

                        </label>

                        <input
                            type="text"
                            id="ver_nombre"
                            class="form-control"
                            readonly>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            Correo Electrónico

                        </label>

                        <input
                            type="text"
                            id="ver_correo"
                            class="form-control"
                            readonly>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            Teléfono

                        </label>

                        <input
                            type="text"
                            id="ver_telefono"
                            class="form-control"
                            readonly>

                    </div>

                    <div class="col-12 mb-3">

                        <label class="form-label">

                            Dirección

                        </label>

                        <textarea
                            id="ver_direccion"
                            class="form-control"
                            rows="3"
                            readonly></textarea>

                    </div>

                </div>

            </div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">

                    Cerrar

                </button>

            </div>

        </div>

    </div>

</div>