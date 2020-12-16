$(function () {
  var id_publicacion = 0;
  var TIENE_PERMISO_CALIFICAR = false;

  var agregarEventos = function () {
    $("#me-gusta-boton").click(function () {
      if ($("#me-gusta-boton").hasClass("boton-celeste")) {
        guardarMegusta();
      } else {
        borrarMegusta();
      }
    });
    $("#boton-editar").click(editarPublicacion);
    $("#boton-eliminar").click(eliminarPublicacion);

    $("#confirmar-reporte").click(confirmarReporte);

    $("#boton-reportar").click(function () {
      limpiarErrores();
      $("#comentario-reporte").val("");
    });

    $(".radio-estrellas").click(function (e) {
      var estrellas = $("input[name='radio-estrellas']:checked").val();
      var formulario = {};
      formulario.estrellas = estrellas;
      formulario.id_publicacion = id_publicacion;
      $.ajax({
        url: "php/calificacion/calificar_publicacion.php",
        type: "POST",
        data: formulario,
        dataType: "JSON",
        success: function (resultado) {
          if (resultado.correcto) {
            cargarCalificacion();
          } else {
            console.error("Error al guardar calificacion");
          }
        },
      });
    });
  };

  var cargarCalificacion = function () {
    $.ajax({
      url: "php/calificacion/obtener_calificacion.php?id=" + id_publicacion,
      type: "GET",
      dataType: "JSON",
      success: function (resultado) {
        if (resultado.correcto) {
          $("#calificacion-estrellas").text(resultado.puntaje);
          if (TIENE_PERMISO_CALIFICAR) {
            if (resultado.tieneCalificacion) {
              $("#radio-group-estrellas").hide();
            } else {
              $("#radio-group-estrellas").show();
            }
          }
        } else {
          console.error("Error al cargar calificacion");
        }
      },
    });
  };

  var editarPublicacion = function () {
    window.location.href = "alta_publicacion.html?id=" + id_publicacion;
  };

  var eliminarPublicacion = function () {
    var respuesta = confirm("Esta seguro de eliminar la publicacion?");
    if (respuesta == true) {
      $.ajax({
        url: "php/publicacion/eliminar_publicacion.php?id=" + id_publicacion,
        type: "DELETE",
        dataType: "JSON",
        success: function (resultado) {
          if (resultado.correcto) {
            imprimirError("Se ha eliminado con exito");
            window.location.href = "ver_publicaciones.html";
          } else {
            imprimirError(resultado.mensajeError);
          }
        },
      });
    }
  };

  var cargarPublicacion = function () {
    var queryString = window.location.search;
    var urlParams = new URLSearchParams(queryString);
    var idPublicacion = urlParams.get("id");

    id_publicacion = idPublicacion;

    $.ajax({
      url: "php/publicacion/obtener_publicacion.php?id=" + idPublicacion,
      type: "GET",
      dataType: "JSON",
      success: function (resultado) {
        if (resultado && resultado.length > 0) {
          var publicacion = resultado[0];
          $("#titulo").text(publicacion.titulo);
          $("#imagen").attr("src", publicacion.ruta_imagen);
          $("#descripcion").text(publicacion.descripcion);
          var etiquetas = [];
          $.each(publicacion.etiquetas, function (ix, etiqueta) {
            etiquetas.push(etiqueta.nombre);
          });
          $("#etiquetas").text("Etiquetas: " + etiquetas.join(", "));
          $("#autor").text(publicacion.usuario);
          $("#autor").attr("href", "perfil.html?id=" + publicacion.idAutor);
          $("#idautor").text(publicacion.idAutor);
          $.ajax({
            url: "php/obtener_usuario_echo.php",
            type: "GET",
            dataType: "JSON",
            success: function (respuesta) {
              if (respuesta > 0) {
                $("#iduser").text(respuesta);
                mostrarBotonesAccion();
                $("#me-gusta-contador").text(publicacion.megusta);
                cargarMegusta(respuesta, id_publicacion);
                if (respuesta != publicacion.idAutor) {
                  $.getScript("js/boton_suscribirse.js");
                }
              } else {
                $("#iduser").text("");
              }
            },
          });
          $("#idautor").hide();
          $("#iduser").hide();
        } else {
          alert("No existe una publicacion con id: " + idPublicacion);
        }
      },
    });
  };

  var guardarMegusta = function () {
    var datos = {};
    datos.idUser = $("#iduser").text();
    datos.idPublicacion = id_publicacion;
    $.ajax({
      url: "php/megusta/agregar_megusta.php",
      type: "POST",
      data: datos,
      dataType: "JSON",
      success: function (resultado) {
        $("#me-gusta-boton").removeClass("boton-celeste");
        $("#me-gusta-boton").addClass("btn-success");

        var megusta = parseInt($("#me-gusta-contador").text());
        $("#me-gusta-contador").text(megusta + 1);
      },
    });
  };

  var borrarMegusta = function () {
    var datos = {};
    datos.idUser = $("#iduser").text();
    datos.idPublicacion = id_publicacion;
    $.ajax({
      url: "php/megusta/remover_megusta.php",
      type: "POST",
      data: datos,
      dataType: "JSON",
      success: function (resultado) {
        $("#me-gusta-boton").removeClass("btn-success");
        $("#me-gusta-boton").addClass("boton-celeste");

        var megusta = parseInt($("#me-gusta-contador").text());
        $("#me-gusta-contador").text(megusta - 1);
      },
    });
  };

  var cargarMegusta = function (idUser, idPublicacion) {
    $.ajax({
      url: "php/megusta/obtener_megusta.php?idUser=" + idUser + "&idPublicacion=" + idPublicacion,
      type: "GET",
      dataType: "JSON",
      success: function (resultado) {
        if (resultado.correcto) {
          if (resultado.megusta) {
            $("#me-gusta-boton").removeClass("boton-celeste");
            $("#me-gusta-boton").addClass("btn-success");
          } else {
            $("#me-gusta-boton").removeClass("btn-success");
            $("#me-gusta-boton").addClass("boton-celeste");
          }
        }
      },
    });
  };

  var confirmarReporte = function () {
    // validar comentario requerido
    var selectorComentario = $("#comentario-reporte");
    var comentario = selectorComentario.val();

    var esValido = true;
    limpiarErrores();
    if (comentario == "") {
      imprimirMensajeError(selectorComentario, "El comentario es requerido");
      esValido = false;
    }

    if (esValido) {
      guardarReporte();
    }
  };

  var limpiarErrores = function () {
    $(".mensaje-error").remove();
    $(".input-error").removeClass("input-error");
  };

  var imprimirMensajeError = function (selector, texto) {
    selector.addClass("input-error");
    selector.after("<div class='mensaje-error'>" + texto + "</div>");
  };

  var guardarReporte = function () {
    var datos = {};
    datos.idPublicacion = id_publicacion;
    datos.comentario = $("#comentario-reporte").val();
    $.ajax({
      url: "php/reporte/guardar_reporte.php",
      type: "POST",
      data: datos,
      dataType: "JSON",
      success: function (resultado) {
        $(".modal").modal("hide");
        $("html, body").animate({ scrollTop: 0 }, 500);
        if (resultado.correcto) {
          obtenerTieneReporte();
          $("#alerta").show();
        } else {
          $("#alerta-error").show();
        }
      },
    });
  };

  var obtenerTieneReporte = function () {
    $.ajax({
      url: "php/reporte/obtener_tiene_reporte.php?idPublicacion=" + id_publicacion,
      type: "GET",
      dataType: "JSON",
      success: function (resultado) {
        if (resultado.tieneReporte) {
          $("#boton-reportar").prop("disabled", true);
        } else {
          $("#boton-reportar").prop("disabled", false);
        }
      },
    });
  };

  var mostrarBotonesAccion = function () {
    if ($("#idautor").text() == $("#iduser").text()) {
      $("#botones-accion").show();
    } else {
      $("#botones-accion").hide();
    }
  };

  var imprimirError = function (texto) {
    $("#resultado").text(texto);
  };

  var cargarPermisos = function () {
    $.ajax({
      url: "php/permisos/obtener_permisos.php",
      type: "GET",
      dataType: "JSON",
      success: function (permisos) {
        TIENE_PERMISO_CALIFICAR = mostrarElemento(permisos, "#radio-group-estrellas", "CALIFICAR_PUBLICACION");
        mostrarElemento(permisos, "#me-gusta-boton", "CALIFICAR_PUBLICACION");
        mostrarElemento(permisos, "#boton-reportar", "REPORTAR_PUBLICACION");
        mostrarElemento(permisos, "#botones-accion", "ABM_PUBLICACION");

        cargarCalificacion();
      },
    });
  };

  var mostrarElemento = function (permisos, elemento, nombre) {
    var tienePermiso = false;
    $(elemento).css("display", "none");
    $.each(permisos, function (ix, permiso) {
      if (permiso.nombre == nombre) {
        $(elemento).css("display", "inline-block");
        tienePermiso = true;
      }
    });
    return tienePermiso;
  };

  var inicializar = function () {
    cargarPermisos();
    cargarPublicacion();
    obtenerTieneReporte();
    agregarEventos();
  };

  inicializar();
});
