// ======================================================
// LIMPIAR CHECKBOXES
// ======================================================
function limpiarAccesosRol() {

    $("#txtAccesoDashboard").prop("checked", false);
    $("#txtAccesoUsuarios").prop("checked", false);
    $("#txtAccesoRoles").prop("checked", false);
    $("#txtAccesoCategorias").prop("checked", false);
    $("#txtAccesoProductos").prop("checked", false);
    $("#txtAccesoCarrito").prop("checked", false);
    $("#txtAccesoCitas").prop("checked", false);
    $("#txtAccesoPacientes").prop("checked", false);
    $("#txtAccesoReportes").prop("checked", false);

}

// ======================================================
// OBTENER ACCESOS MARCADOS
// ======================================================
function obtenerAccesosRol() {

    let accesos = [];

    if ($("#txtAccesoDashboard").is(":checked")) accesos.push("dashboard");
    if ($("#txtAccesoUsuarios").is(":checked")) accesos.push("usuarios");
    if ($("#txtAccesoRoles").is(":checked")) accesos.push("roles");
    if ($("#txtAccesoCategorias").is(":checked")) accesos.push("categorias");
    if ($("#txtAccesoProductos").is(":checked")) accesos.push("productos");
    if ($("#txtAccesoCarrito").is(":checked")) accesos.push("carrito");
    if ($("#txtAccesoCitas").is(":checked")) accesos.push("citas");
    if ($("#txtAccesoPacientes").is(":checked")) accesos.push("pacientes");
    if ($("#txtAccesoReportes").is(":checked")) accesos.push("reportes");

    return accesos;

}

// ======================================================
// ABRIR MODAL NUEVO ROL
// ======================================================
function Incluir() {

    $("#modalTitleRol").html(
        '<i class="bi bi-shield-lock"></i> Nuevo Rol'
    );

    $("#formRoles")[0].reset();

    $("#idRol").val("");

    $("#btnGuardarRol").text("Guardar");

    limpiarAccesosRol();

}

// ======================================================
// GUARDAR / MODIFICAR
// ======================================================
$(document).ready(function () {

    $("#formRoles").off("submit").on("submit", function (e) {

        e.preventDefault();

        let id = $("#idRol").val();

        let opcion = (id == "") ? "incluir" : "modificar";

        let accesos = obtenerAccesosRol();
        console.log("ACCESOS:", accesos);
        if (accesos.length == 0) {

            swal(
                "Error",
                "Debe seleccionar al menos un acceso.",
                "error"
            );

            return;

        }

        let formData = new FormData();

        formData.append("id", $("#idRol").val());
        formData.append("nombre", $("#nombre").val());
        formData.append("descripcion", $("#descripcion").val());
        formData.append("accesos", accesos.join(","));
        formData.append("status", $("#status").val());

        $.ajax({

            url: "/mi_proyecto/modulos/roles/rolesModelo.php?option=" + opcion,

            type: "POST",

            data: formData,

            processData: false,

            contentType: false,

            dataType: "json",
            success: function (r) {

                console.log("RESPUESTA ROL:", r);

                if (r.exito == 1) {

                    let mensaje = (opcion == "incluir")
                        ? "Rol registrado correctamente."
                        : "Rol modificado correctamente.";

                    swal(
                        "Éxito",
                        mensaje,
                        "success"
                    ).then(function () {

                        location.reload();

                    });

                } else if (r.error == 1) {

                    swal(
                        "Error",
                        "Ya existe un rol con ese nombre.",
                        "error"
                    );

                } else if (r.error == 2) {

                    swal(
                        "Error",
                        "El rol no existe.",
                        "error"
                    );

                } else if (r.error == 3) {

                    swal(
                        "Error",
                        "Debe completar correctamente los datos.",
                        "error"
                    );

                } else {

                    swal(
                        "Error",
                        r.mensaje || "No fue posible guardar el rol.",
                        "error"
                    );

                }

            },

            error: function (xhr) {

                console.log(xhr.responseText);

                swal(
                    "Error",
                    "Error en el servidor.",
                    "error"
                );

            }

        });

    });

});


// ======================================================
// MODIFICAR ROL
// ======================================================
function Modificar(id) {

    $.ajax({

        url: "/mi_proyecto/modulos/roles/rolesModelo.php?option=consultar&id=" + id,

        type: "GET",

        dataType: "json",

        success: function (r) {

            console.log("ROL CONSULTADO:", r);

            if (r.exito == 1) {

                $("#modalTitleRol").html(
                    '<i class="bi bi-pencil-square"></i> Modificar Rol'
                );

                $("#btnGuardarRol").text("Actualizar");

                $("#idRol").val(r.id);
                $("#nombre").val(r.nombre);
                $("#descripcion").val(r.descripcion);
                $("#status").val(r.status);

                limpiarAccesosRol();

                let accesos = [];

                if (r.accesos) {

                    accesos = r.accesos
                        .split(/[;,]/)
                        .map(function (item) {
                            return item.trim().toLowerCase();
                        })
                        .filter(function (item) {
                            return item != "";
                        });

                }

                $("#txtAccesoDashboard").prop("checked", accesos.includes("dashboard"));
                $("#txtAccesoUsuarios").prop("checked", accesos.includes("usuarios"));
                $("#txtAccesoRoles").prop("checked", accesos.includes("roles"));
                $("#txtAccesoCategorias").prop("checked", accesos.includes("categorias"));
                $("#txtAccesoProductos").prop("checked", accesos.includes("productos"));
                $("#txtAccesoCarrito").prop("checked", accesos.includes("carrito"));
                $("#txtAccesoCitas").prop("checked", accesos.includes("citas"));
                $("#txtAccesoPacientes").prop("checked", accesos.includes("pacientes"));
                $("#txtAccesoReportes").prop("checked", accesos.includes("reportes"));

                $("#modalRoles").modal("show");

            } else {

                swal(
                    "Error",
                    "Rol no encontrado.",
                    "error"
                );

            }

        },
        error: function (xhr) {

            console.log(xhr.responseText);

            swal(
                "Error",
                "No fue posible consultar el rol.",
                "error"
            );

        }

    });

}


// ======================================================
// ELIMINAR ROL
// ======================================================
function Eliminar(id) {

    swal({

        title: "¿Eliminar rol?",

        text: "El rol será marcado como inactivo.",

        icon: "warning",

        buttons: true,

        dangerMode: true

    }).then(function (ok) {

        if (!ok) return;

        $.ajax({

            url: "/mi_proyecto/modulos/roles/rolesModelo.php?option=eliminar&id=" + id,

            type: "POST",

            dataType: "json",

            success: function (r) {

                if (r.exito == 1) {

                    swal(
                        "Éxito",
                        "Rol eliminado correctamente.",
                        "success"
                    ).then(function () {

                        location.reload();

                    });

                } else {

                    swal(
                        "Error",
                        r.mensaje || "No fue posible eliminar el rol.",
                        "error"
                    );

                }

            },

            error: function (xhr) {

                console.log(xhr.responseText);

                swal(
                    "Error",
                    "Error en el servidor al eliminar.",
                    "error"
                );

            }

        });

    });

}