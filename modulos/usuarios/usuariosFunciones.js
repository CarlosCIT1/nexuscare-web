// ABRIR MODAL NUEVO USUARIO
function abrirModalUsuario() {

    $("#modalTitle").text("Nuevo Usuario");

    $("#formUsuarios")[0].reset();

    $("#idUsuario").val("");

    // Password obligatoria cuando es nuevo usuario
    $("#password").prop("required", true);

    $("#grupoPassword").show();

    // Ocultar especialidad por defecto
    $("#grupoEspecialidad").hide();

    $("#id_especialidad").val("");

}

// MOSTRAR / OCULTAR ESPECIALIDAD
function mostrarCamposRol() {

    let textoRol = $("#rolid option:selected")
        .text()
        .toLowerCase()
        .trim();

    if (textoRol === "medico" || textoRol === "médico") {

        $("#grupoEspecialidad").show();

    } else {

        $("#grupoEspecialidad").hide();

        $("#id_especialidad").val("");

    }

}

// GUARDAR (INCLUIR / MODIFICAR)
$(document).ready(function () {

    $("#rolid").on("change", function () {

        mostrarCamposRol();

    });

    $("#formUsuarios").off("submit").on("submit", function (e) {

        e.preventDefault();

        let id = $("#idUsuario").val();

        let opcion = (id == "")
            ? "incluir"
            : "modificar";

        let formData = new FormData(this);

        $.ajax({

            url: "/mi_proyecto/modulos/usuarios/usuariosModelo.php?option=" + opcion,

            type: "POST",

            data: formData,

            contentType: false,

            processData: false,

            dataType: "json",

            success: function (r) {

                console.log("RESPUESTA:", r);

                // OPERACIÓN EXITOSA
                if (r.exito == 1) {

                    /*
                     * Si el usuario modificó
                     * su propio rol
                     */
                    if (r.cerrarSesion == 1) {

                        swal({

                            title: "Sesión finalizada",

                            text: "Tu rol fue actualizado correctamente.\n\nPara aplicar los nuevos permisos es necesario iniciar sesión nuevamente.",

                            icon: "success",

                            button: "Aceptar"

                        }).then(function () {

                            window.location.href = "/mi_proyecto/logout.php";

                        });

                        return;

                    }

                    let mensaje = (opcion == "incluir")

                        ? "Usuario registrado correctamente."

                        : "Usuario actualizado correctamente.";

                    swal(

                        "Éxito",

                        mensaje,

                        "success"

                    ).then(function () {

                        location.reload();

                    });

                }

                // CORREO DUPLICADO
                else if (r.error == 1) {

                    swal(

                        "Error",

                        "El correo electrónico ya está registrado.",

                        "error"

                    );

                }

                // USUARIO NO EXISTE
                else if (r.error == 2) {

                    swal(

                        "Error",

                        "El usuario no existe.",

                        "error"

                    );

                }

                // DATOS INCOMPLETOS
                else if (r.error == 3) {

                    swal(

                        "Error",

                        "Debe completar correctamente todos los campos.",

                        "error"

                    );

                }

                // MÉDICO SIN ESPECIALIDAD
                else if (r.error == 5) {

                    swal(

                        "Error",

                        "Debe seleccionar una especialidad para el médico.",

                        "error"

                    );

                }

                // ==========================
                // OTRO ERROR
                // ==========================
                else {

                    swal(

                        "Error",

                        r.mensaje || "Ocurrió un error.",

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
// MODIFICAR USUARIO
function Modificar(id) {

    $("#modalTitle").text("Modificar Usuario");

    // En edición la contraseña no es obligatoria
    $("#grupoPassword").hide();

    $("#password").prop("required", false);

    $.ajax({

        url: "/mi_proyecto/modulos/usuarios/usuariosModelo.php?option=modificarConsultar&id=" + id,

        type: "GET",

        dataType: "json",

        success: function (r) {

            console.log("DATOS USUARIO:", r);

            if (r.exito == 1) {

                $("#idUsuario").val(r.id);

                $("#nombre").val(r.nombre);

                $("#direccion").val(r.direccion);

                $("#telefono").val(r.telefono);

                $("#email").val(r.email);

                $("#rolid").val(r.rolid);

                $("#status").val(r.status);

                // Mostrar u ocultar especialidad
                mostrarCamposRol();

                if (r.id_especialidad != null && r.id_especialidad != "") {

                    $("#id_especialidad").val(r.id_especialidad);

                } else {

                    $("#id_especialidad").val("");

                }

                $("#modalUsuarios").modal("show");

            } else {

                swal(
                    "Error",
                    "No se encontró el usuario.",
                    "error"
                );

            }

        },

        error: function (xhr) {

            console.log(xhr.responseText);

            swal(
                "Error",
                "No fue posible consultar el usuario.",
                "error"
            );

        }

    });

}
// ELIMINAR USUARIO
function Eliminar(id) {

    swal({

        title: "¿Eliminar usuario?",

        text: "El usuario será marcado como inactivo.",

        icon: "warning",

        buttons: true,

        dangerMode: true

    }).then((ok) => {

        if (ok) {

            $.ajax({

                url: "/mi_proyecto/modulos/usuarios/usuariosModelo.php?option=eliminar&id=" + id,

                type: "POST",

                dataType: "json",

                success: function (r) {

                    console.log("RESPUESTA:", r);

                    if (r.exito == 1) {

                        swal(

                            "Éxito",

                            "Usuario eliminado correctamente.",

                            "success"

                        ).then(function () {

                            location.reload();

                        });

                    } else {

                        swal(

                            "Error",

                            r.mensaje || "No fue posible eliminar el usuario.",

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