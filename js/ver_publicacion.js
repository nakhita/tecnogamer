$(function () {
  var id_publicacion = 0;

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
                alert("No usuario");
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

  var inicializar = function () {
    cargarPublicacion();
    agregarEventos();
  };

  inicializar();
});
