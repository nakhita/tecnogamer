$(function () {
  var agregarEventos = function () {
    $("#boton-guardar").click(function () {
      var formulario = {};
      formulario.idCategoria = $("#categoria-select").val();
      formulario.etiqueta = $("#etiqueta").val();

      if (validarEtiqueta(formulario)) {
        $.ajax({
          url: "php/etiqueta/guardar_etiqueta.php",
          type: "POST",
          dataType: "JSON",
          data: formulario,
          success: function (resultado) {
            if (resultado.correcto) {
              imprimirError("Se ha guardado con exito");
              limpiarFormulario();
              cargarEtiquetas();
            } else {
              imprimirError(resultado.mensajeError);
            }
          },
        });
      }
    });

    $("#boton-actualizar").click(function () {
      var formulario = {};
      formulario.idCategoria = $("#categoria-select").val();
      formulario.etiqueta = $("#etiqueta").val();
      formulario.idEtiqueta = $("#etiqueta-id").val();

      if (validarEtiqueta(formulario)) {
        $.ajax({
          url: "php/etiqueta/guardar_etiqueta.php",
          type: "POST",
          dataType: "JSON",
          data: formulario,
          success: function (resultado) {
            if (resultado.correcto) {
              imprimirError("Se ha actualizado con exito");
              limpiarFormulario();
              cargarEtiquetas();
            } else {
              imprimirError(resultado.mensajeError);
            }
          },
        });
      }
    });

    $("#boton-limpiar").click(limpiarFormulario);
  };

  var limpiarFormulario = function () {
    $("#etiqueta").val("");
    $("#categoria-select").prop("selectedIndex", 0);
    $("#etiqueta-id").val("");
    $("#boton-actualizar").hide();
    $("#boton-guardar").show();
    $(".boton-accion").attr("disabled", false);
  };

  var validarEtiqueta = function (formulario) {
    if (formulario.etiqueta == "") {
      imprimirError("Etiqueta es requerido");
      return false;
    } else if (!esValidoAlfanumerico(formulario.etiqueta)) {
      imprimirError("El nombre de la etiqueta contener caracteres alfanumerico");
      return false;
    }

    imprimirError("");
    return true;
  };

  var imprimirError = function (texto) {
    $("#resultado").text(texto);
  };

  var esValidoAlfanumerico = function (nombre) {
    let pattern = /^[A-Za-z0-9]+$/g;
    return pattern.test(nombre);
  };

  var cargarCategorias = function () {
    $("#categoria-select").empty();
    obtenerCategorias(function (categorias) {
      $.each(categorias, function () {
        $("#categoria-select").append($("<option></option>").attr("value", this.id).text(this.nombre));
      });
    });
  };

  var obtenerCategorias = function (callback) {
    $.ajax({
      url: "php/publicacion/obtener_categorias.php",
      type: "get",
      success: function (response) {
        if (callback) {
          callback(response);
        }
      },
      error: function (response) {
        console.error(response);
      },
    });
  };

  var cargarEtiquetas = function (callback) {
    $.ajax({
      url: "php/etiqueta/obtener_etiquetas.php",
      type: "get",
      success: function (response) {
        if (response) {
          $("#tabla-etiquetas").empty();
          $.each(response, function (ix, etiqueta) {
            var fila = $("<tr></tr>");

            fila.append($("<td></td>").text(etiqueta.nombreCategoria));
            fila.append($("<td></td>").text(etiqueta.nombre));

            var botonEditar = $("<button></button>")
              .attr("type", "button")
              .attr("class", "boton-accion btn btn-primary mr-1")
              .attr("id", etiqueta.id)
              .text("Editar");

            botonEditar.click(function () {
              mostrarEditar(etiqueta.id, etiqueta.nombre, etiqueta.idCategoria);
            });

            var botonEliminar = $("<button></button>").attr("type", "button").attr("class", "boton-accion btn btn-danger").text("Eliminar");

            botonEliminar.click(function () {
              mostrarEliminar(etiqueta.id);
            });

            var contenedorBotones = $("<td></td>");
            contenedorBotones.append(botonEditar);
            contenedorBotones.append(botonEliminar);
            fila.append(contenedorBotones);

            $("#tabla-etiquetas").append(fila);
          });
        }
      },
      error: function (response) {
        console.error(response);
      },
    });
  };

  var mostrarEditar = function (idEtiqueta, nombreEtiqueta, idCategoria) {
    $("#etiqueta").val(nombreEtiqueta);
    $("#categoria-select").val(idCategoria);
    $("#etiqueta-id").val(idEtiqueta);
    $("#boton-actualizar").show();
    $("#boton-guardar").hide();
    $(".boton-accion").attr("disabled", true);
  };

  var mostrarEliminar = function (idEtiqueta) {
    var resultado = confirm("Esta seguro que desea eliminar : " + idEtiqueta);
    if (resultado == true) {
      $.ajax({
        url: "php/etiqueta/eliminar_etiqueta.php?idEtiqueta=" + idEtiqueta,
        type: "DELETE",
        dataType: "JSON",
        success: function (resultado) {
          if (resultado.correcto) {
            imprimirError("Se ha eliminado con exito");
            limpiarFormulario();
            cargarEtiquetas();
          } else {
            imprimirError(resultado.mensajeError);
          }
        },
      });
    }
  };

  var inicializar = function () {
    agregarEventos();
    cargarCategorias();
    cargarEtiquetas();
    limpiarFormulario();
  };

  inicializar();
});
