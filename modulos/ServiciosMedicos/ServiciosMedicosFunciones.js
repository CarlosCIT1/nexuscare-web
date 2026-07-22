function abrirModalServicio() {

    $("#modalTitleServicio").text("Nuevo Servicio Médico");

    $("#btnActionServicio").text("Guardar");

    $("#formServicio")[0].reset();

    $("#idServicio").val("");

}

$(document).ready(function () {

    $("#formServicio").off("submit").on("submit", function (e) {

        e.preventDefault();

        let id = $("#idServicio").val();

        let opcion = (id == "") ? "incluir" : "modificar";

        let formData = new FormData(this);

        $.ajax({

            url: "/mi_proyecto/modulos/serviciosMedicos/serviciosMedicosModelo.php?option=" + opcion,

            type: "POST",

            data: formData,

            contentType: false,

            processData: false,

            dataType: "json",

            success: function (r) {

                console.log(r);

                if (r.exito == 1) {

                    let mensaje = (opcion == "incluir")
                        ? "Servicio médico registrado correctamente."
                        : "Servicio médico actualizado correctamente.";

                    swal(
                        "Éxito",
                        mensaje,
                        "success"
                    ).then(function () {

                        window.location.href = "/mi_proyecto/?page=serviciosMedicos";

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
                    "Error del servidor.",
                    "error"
                );

            }

        });

    });

});


function ModificarServicio(id) {

    $("#modalTitleServicio").text("Modificar Servicio Médico");

    $("#btnActionServicio").text("Actualizar");

    $.ajax({

        url: "/mi_proyecto/modulos/serviciosMedicos/serviciosMedicosModelo.php?option=modificarConsultar&id=" + id,

        type: "GET",

        dataType: "json",

        success: function (r) {

            console.log(r);

            if (r.exito == 1) {

                $("#idServicio").val(r.id);

                $("#nombreServicio").val(r.nombre);

                $("#marcaServicio").val(r.marca);

                $("#descripcionServicio").val(r.descripcion);

                $("#stockServicio").val(r.stock);

                $("#idEspecialidadServicio").val(r.id_especialidad);

                $("#statusServicio").val(r.status);

                $("#modalServicio").modal("show");

            } else {

                swal(
                    "Error",
                    "No se encontró el servicio.",
                    "error"
                );

            }

        },

        error: function (xhr) {

            console.log(xhr.responseText);

            swal(
                "Error",
                "No se pudo consultar el servicio.",
                "error"
            );

        }

    });

}


function EliminarServicio(id) {

    swal({

        title: "¿Eliminar servicio médico?",

        text: "El servicio será marcado como inactivo.",

        icon: "warning",

        buttons: true,

        dangerMode: true

    }).then((ok) => {

        if (ok) {

            $.ajax({

                url: "/mi_proyecto/modulos/serviciosMedicos/serviciosMedicosModelo.php?option=eliminar&id=" + id,

                type: "POST",

                dataType: "json",

                success: function (r) {

                    console.log(r);

                    if (r.exito == 1) {

                        swal(
                            "Éxito",
                            "Servicio eliminado correctamente.",
                            "success"
                        ).then(function () {

                            window.location.href = "/mi_proyecto/?page=serviciosMedicos";

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
                        "Error del servidor.",
                        "error"
                    );

                }

            });

        }

    });

}