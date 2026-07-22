function cargarMedicosPorEspecialidad(idEspecialidad, medicoSeleccionado = "") {

    $("#id_medico").prop("disabled", true);

    $("#hora_cita").prop("disabled", true);

    $("#id_medico").html(
        '<option value="">Cargando médicos...</option>'
    );

    $("#hora_cita").html(
        '<option value="">Seleccione primero el médico y la fecha</option>'
    );

    if (idEspecialidad == "") {

        $("#id_medico").html(
            '<option value="">Seleccione una especialidad</option>'
        );

        return;

    }

    $.ajax({

        url: "/mi_proyecto/modulos/citas/citasModelo.php",

        type: "GET",

        dataType: "json",

        data: {

            option: "obtenerMedicos",

            id_especialidad: idEspecialidad

        },

        success: function (respuesta) {

            let opciones =
                '<option value="">Seleccione un médico</option>';

            if (Array.isArray(respuesta)) {

                respuesta.forEach(function (medico) {

                    let selected = "";

                    if (String(medico.id) == String(medicoSeleccionado)) {

                        selected = "selected";

                    }

                    opciones +=
                        '<option value="' +
                        medico.id +
                        '" ' +
                        selected +
                        '>' +
                        medico.nombre +
                        '</option>';

                });

            }

            $("#id_medico")
                .html(opciones)
                .prop("disabled", false);

            if (medicoSeleccionado != "") {

                cargarHorariosDisponibles();

            }

        },

        error: function () {

            swal(
                "Error",
                "No fue posible cargar los médicos.",
                "error"
            );

        }

    });

}

function cargarHorariosDisponibles(horaSeleccionada = "") {

    let medico = $("#id_medico").val();

    let fecha = $("#fecha_cita").val();

    if (medico == "" || fecha == "") {

        $("#hora_cita").prop("disabled", true);

        $("#hora_cita").html(
            '<option value="">Seleccione primero el médico y la fecha</option>'
        );

        return;

    }

    $("#hora_cita").prop("disabled", true);

    $("#hora_cita").html(
        '<option value="">Consultando horarios...</option>'
    );

    $.ajax({

        url: "/mi_proyecto/modulos/citas/citasModelo.php",

        type: "GET",

        dataType: "json",

        data: {

            option: "obtenerHorarios",

            id_medico: medico,

            fecha: fecha

        },
                success: function (respuesta) {

            let opciones =
                '<option value="">Seleccione una hora</option>';

            if (Array.isArray(respuesta) && respuesta.length > 0) {

                respuesta.forEach(function (hora) {

                    let selected = "";

                    if (horaSeleccionada == hora) {

                        selected = "selected";

                    }

                    opciones +=
                        '<option value="' +
                        hora +
                        '" ' +
                        selected +
                        '>' +
                        hora +
                        '</option>';

                });

                $("#hora_cita")
                    .html(opciones)
                    .prop("disabled", false);

            } else {

                $("#hora_cita")
                    .html(
                        '<option value="">No hay horarios disponibles</option>'
                    )
                    .prop("disabled", true);

            }

        },

        error: function (xhr) {

            console.log(xhr.responseText);

            $("#hora_cita")
                .html(
                    '<option value="">Error al consultar horarios</option>'
                )
                .prop("disabled", true);

        }

    });

}

function abrirModalCita() {

    $("#formCita")[0].reset();

    $("#idCita").val("");

    $("#modalTitleCita").html(
        '<i class="bi bi-calendar2-check"></i> Nueva Cita Médica'
    );

    $("#btnActionCita").html("Guardar");

    $("#id_medico")
        .html(
            '<option value="">Seleccione una especialidad</option>'
        )
        .prop("disabled", true);

    $("#hora_cita")
        .html(
            '<option value="">Seleccione primero el médico y la fecha</option>'
        )
        .prop("disabled", true);

}
$(document).on("change", "#id_especialidad", function () {

    cargarMedicosPorEspecialidad($(this).val());

});

$(document).on("change", "#id_medico", function () {

    cargarHorariosDisponibles();

});

$(document).on("change", "#fecha_cita", function () {

    cargarHorariosDisponibles();

});

$(document).ready(function () {

    $("#formCita").off("submit").on("submit", function (e) {

        e.preventDefault();

        let opcion = $("#idCita").val() == ""
            ? "incluir"
            : "modificar";

        $.ajax({

            url: "/mi_proyecto/modulos/citas/citasModelo.php?option=" + opcion,

            type: "POST",

            data: new FormData(this),

            processData: false,

            contentType: false,

            dataType: "json",

            beforeSend: function () {

                $("#btnActionCita")
                    .prop("disabled", true)
                    .html("Guardando...");

            },

            success: function (r) {

                $("#btnActionCita")
                    .prop("disabled", false)
                    .html(
                        $("#idCita").val() == ""
                            ? "Guardar"
                            : "Actualizar"
                    );

                if (r.exito == 1) {

                    swal(
                        "Éxito",
                        r.mensaje,
                        "success"
                    ).then(function () {

                        location.reload();

                    });

                    return;

                }

                swal(
                    "Error",
                    r.mensaje,
                    "error"
                );

            },

            error: function (xhr) {

                $("#btnActionCita")
                    .prop("disabled", false)
                    .html(
                        $("#idCita").val() == ""
                            ? "Guardar"
                            : "Actualizar"
                    );

                console.log(xhr.responseText);

                swal(
                    "Error",
                    "Ocurrió un error al guardar la cita.",
                    "error"
                );

            }

        });

    });

});
function ModificarCita(id) {

    $("#modalTitleCita").html(
        '<i class="bi bi-pencil-square"></i> Modificar Cita'
    );

    $("#btnActionCita").html("Actualizar");

    $.ajax({

        url: "/mi_proyecto/modulos/citas/citasModelo.php",

        type: "GET",

        dataType: "json",

        data: {

            option: "modificarConsultar",

            id: id

        },

        success: function (r) {

            if (r.exito != 1) {

                swal(
                    "Error",
                    "No se encontró la cita.",
                    "error"
                );

                return;

            }

            $("#idCita").val(r.id);

            $("#id_paciente").val(r.id_paciente);

            $("#id_especialidad").val(r.id_especialidad);

            $("#id_servicio").val(r.id_servicio);

            $("#fecha_cita").val(r.fecha_cita);

            $("#estado").val(r.estado);

            $("#observaciones").val(r.observaciones);

            cargarMedicosPorEspecialidad(
                r.id_especialidad,
                r.id_medico
            );

            let intervalo = setInterval(function () {

                if ($("#id_medico option").length > 1) {

                    $("#id_medico").val(r.id_medico);

                    clearInterval(intervalo);

                    cargarHorariosDisponibles(r.hora_cita);

                }

            },100);

            bootstrap.Modal
                .getOrCreateInstance(
                    document.getElementById("modalCita")
                )
                .show();

        },

        error: function (xhr) {

            console.log(xhr.responseText);

            swal(
                "Error",
                "No fue posible consultar la cita.",
                "error"
            );

        }

    });

}
function EliminarCita(id) {

    swal({

        title: "¿Cancelar la cita?",

        text: "La cita será cancelada.",

        icon: "warning",

        buttons: true,

        dangerMode: true

    }).then(function (ok) {

        if (!ok) {

            return;

        }

        $.ajax({

            url: "/mi_proyecto/modulos/citas/citasModelo.php",

            type: "GET",

            dataType: "json",

            data: {

                option: "eliminar",

                id: id

            },

            success: function (r) {

                if (r.exito == 1) {

                    swal(
                        "Éxito",
                        r.mensaje,
                        "success"
                    ).then(function () {

                        location.reload();

                    });

                } else {

                    swal(
                        "Error",
                        r.mensaje,
                        "error"
                    );

                }

            },

            error: function (xhr) {

                console.log(xhr.responseText);

                swal(
                    "Error",
                    "No fue posible cancelar la cita.",
                    "error"
                );

            }

        });

    });

}

function MarcarAtendida(id) {

    swal({

        title: "¿Marcar como atendida?",

        text: "La cita cambiará al estado Atendida.",

        icon: "info",

        buttons: true

    }).then(function (ok) {

        if (!ok) {

            return;

        }

        $.ajax({

            url: "/mi_proyecto/modulos/citas/citasModelo.php",

            type: "GET",

            dataType: "json",

            data: {

                option: "marcarAtendida",

                id: id

            },

            success: function (r) {

                if (r.exito == 1) {

                    swal(
                        "Éxito",
                        r.mensaje,
                        "success"
                    ).then(function () {

                        location.reload();

                    });

                } else {

                    swal(
                        "Error",
                        r.mensaje,
                        "error"
                    );

                }

            },

            error: function (xhr) {

                console.log(xhr.responseText);

                swal(
                    "Error",
                    "No fue posible actualizar la cita.",
                    "error"
                );

            }

        });

    });

}