// PERFIL - NEXUS CARE
$(document).ready(function () {

    // VISTA PREVIA DE LA FOTO
    $("#foto").on("change", function () {

        let archivo = this.files[0];

        if (!archivo) {
            return;
        }

        let tiposPermitidos = [
            "image/jpeg",
            "image/jpg",
            "image/png",
            "image/webp"
        ];

        if (!tiposPermitidos.includes(archivo.type)) {

            swal(
                "Error",
                "Solo se permiten imágenes JPG, JPEG, PNG o WEBP.",
                "error"
            );

            $(this).val("");

            return;

        }

        if (archivo.size > (2 * 1024 * 1024)) {

            swal(
                "Error",
                "La imagen no puede ser mayor a 2 MB.",
                "error"
            );

            $(this).val("");

            return;

        }

        let reader = new FileReader();

        reader.onload = function (e) {

            $("#previewFoto").attr(
                "src",
                e.target.result
            );

        };

        reader.readAsDataURL(archivo);

    });

    
    // GUARDAR PERFIL
    $("#formPerfil").on("submit", function (e) {

        e.preventDefault();

        let password = "";
        let confirmar = "";

        if ($("#password").length) {
            password = $("#password").val().trim();
        }

        if ($("#confirmar").length) {
            confirmar = $("#confirmar").val().trim();
        }

        if (password !== "" && password !== confirmar) {

            swal(
                "Error",
                "Las contraseñas no coinciden.",
                "error"
            );

            return;

        }

        let datos = new FormData(this);

        $("#btnGuardarPerfil")
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm"></span> Guardando...'
            );

        $.ajax({

            url: "/mi_proyecto/modulos/perfil/perfilModelo.php",

            type: "POST",

            data: datos,

            processData: false,

            contentType: false,

            cache: false,

            dataType: "json",
                        success: function (r) {

                console.log(r);

                if (r.exito == 1) {

                    swal({

                        title: "Éxito",

                        text: r.mensaje,

                        icon: "success"

                    }).then(function () {

                        location.reload();

                    });

                } else {

                    swal(

                        "Error",

                        r.mensaje,

                        "error"

                    );

                    $("#btnGuardarPerfil")
                        .prop("disabled", false);

                    if ($("#password").length) {

                        $("#btnGuardarPerfil").html(
                            '<i class="bi bi-floppy"></i> Guardar cambios'
                        );

                    } else {

                        $("#btnGuardarPerfil").html(
                            '<i class="bi bi-camera"></i> Actualizar foto'
                        );

                    }

                }

            },

            error: function (xhr) {

                console.log(xhr.responseText);

                swal(

                    "Error",

                    "Ocurrió un error al guardar el perfil.",

                    "error"

                );

                $("#btnGuardarPerfil")
                    .prop("disabled", false);

                if ($("#password").length) {

                    $("#btnGuardarPerfil").html(
                        '<i class="bi bi-floppy"></i> Guardar cambios'
                    );

                } else {

                    $("#btnGuardarPerfil").html(
                        '<i class="bi bi-camera"></i> Actualizar foto'
                    );

                }

            }

        });

    });

});