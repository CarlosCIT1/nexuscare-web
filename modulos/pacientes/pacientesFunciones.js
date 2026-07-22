// =====================================================
// VER INFORMACIÓN DEL PACIENTE
// =====================================================
function VerPaciente(id) {

    $.ajax({

        url: "/mi_proyecto/modulos/pacientes/pacientesModelo.php",

        type: "GET",

        data: {

            option: "consultar",

            id: id

        },

        dataType: "json",

        success: function (r) {

            console.log("Respuesta del servidor:", r);

            if (r.exito != 1) {

                swal(
                    "Error",
                    r.mensaje,
                    "error"
                );

                return;

            }

            $("#ver_nombre").val(r.nombre);

            $("#ver_correo").val(r.email);

            $("#ver_telefono").val(r.telefono);

            $("#ver_direccion").val(r.direccion);

            const modal = bootstrap.Modal.getOrCreateInstance(
                document.getElementById("modalPaciente")
            );

            modal.show();

        },

        error: function (xhr) {

            console.log("ERROR AJAX");
            console.log(xhr.responseText);

            swal(
                "Error",
                "No fue posible consultar la información del paciente.",
                "error"
            );

        }

    });

}