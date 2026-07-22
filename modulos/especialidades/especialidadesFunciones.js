let modalEspecialidades = null;

/* 
   INICIALIZAR MODAL
 */
document.addEventListener("DOMContentLoaded", function () {

    const modalEl = document.getElementById("modalEspecialidades");

    if (modalEl) {
        modalEspecialidades = new bootstrap.Modal(modalEl);
    }

});


/* 
   PREVIEW DE IMAGEN
 */
$(document).on("change", "#imagen", function () {

    const file = this.files[0];

    if (file) {

        const reader = new FileReader();

        reader.onload = function (e) {

            $("#previewImagen").attr("src", e.target.result);

            $("#contenedorPreview").show();

        };

        reader.readAsDataURL(file);

    } else {

        $("#previewImagen").attr("src", "");

        $("#contenedorPreview").hide();

    }

});


/* 
   NUEVA ESPECIALIDAD
 */
function IncluirEspecialidad() {

    $("#formEspecialidades")[0].reset();

    $("#idEspecialidad").val("");

    $("#modalTitleEspecialidad").html(
        '<i class="bi bi-tags"></i> Nueva Especialidad'
    );

    $("#btnGuardarEspecialidad").html(
        '<i class="bi bi-save"></i> Guardar'
    );

    $("#previewImagen").attr("src","");

    $("#contenedorPreview").hide();

    modalEspecialidades.show();

}


/* 
   MODIFICAR ESPECIALIDAD
 */
function ModificarEspecialidad(id){

    $.ajax({

        url:"/mi_proyecto/modulos/especialidades/especialidadesModelo.php?option=consultar&id="+id,

        type:"GET",

        dataType:"json",

        success:function(r){

            console.log(r);

            if(r.exito==1){

                $("#idEspecialidad").val(r.id);

                $("#nombre").val(r.nombre);

                $("#descripcion").val(r.descripcion);

                $("#status").val(r.status);

                $("#modalTitleEspecialidad").html(
                    '<i class="bi bi-pencil-square"></i> Modificar Especialidad'
                );

                $("#btnGuardarEspecialidad").html(
                    '<i class="bi bi-save"></i> Actualizar'
                );

                if(r.imagen!=""){

                    $("#previewImagen").attr(
                        "src",
                        "/mi_proyecto/uploads/categorias/"+r.imagen
                    );

                    $("#contenedorPreview").show();

                }else{

                    $("#previewImagen").attr("src","");

                    $("#contenedorPreview").hide();

                }

                modalEspecialidades.show();

            }else{

                swal(
                    "Error",
                    r.mensaje,
                    "error"
                );

            }

        },

        error:function(xhr){

            console.log(xhr.responseText);

            swal(
                "Error",
                "No se pudo consultar la especialidad",
                "error"
            );

        }

    });

}
/* 
   GUARDAR ESPECIALIDAD
 */
$(document).off("submit", "#formEspecialidades").on("submit", "#formEspecialidades", function (e) {

    e.preventDefault();

    let id = $("#idEspecialidad").val().trim();

    let option = (id == "") ? "incluir" : "modificar";

    let formData = new FormData(this);

    $.ajax({

        url: "/mi_proyecto/modulos/especialidades/especialidadesModelo.php?option=" + option,

        type: "POST",

        data: formData,

        contentType: false,

        processData: false,

        dataType: "json",

        success: function (r) {

            console.log(r);

            if (r.exito == 1) {

                modalEspecialidades.hide();

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
                "Error del servidor",
                "error"
            );

        }

    });

});


/* 
   ELIMINAR ESPECIALIDAD
 */
function EliminarEspecialidad(id){

    swal({

        title:"¿Eliminar especialidad?",

        text:"La especialidad se marcará como inactiva.",

        icon:"warning",

        buttons:true,

        dangerMode:true

    }).then((ok)=>{

        if(ok){

            $.ajax({

                url:"/mi_proyecto/modulos/especialidades/especialidadesModelo.php?option=eliminar&id="+id,

                type:"POST",

                dataType:"json",

                success:function(r){

                    console.log(r);

                    if(r.exito==1){

                        swal(
                            "Éxito",
                            r.mensaje,
                            "success"
                        ).then(function(){

                            location.reload();

                        });

                    }else{

                        swal(
                            "Error",
                            r.mensaje,
                            "error"
                        );

                    }

                },

                error:function(xhr){

                    console.log(xhr.responseText);

                    swal(
                        "Error",
                        "Error del servidor",
                        "error"
                    );

                }

            });

        }

    });

}