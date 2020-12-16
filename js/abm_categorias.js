$(function () {
  var agregarEventos = function () {
    $("#boton-guardar").click(function () {
      var formulario = {};
      formulario.categoria = $("#categoria").val();

      if (validarCategoria(formulario)) {
        $.ajax({
          url: "php/categoria/guardar_categoria.php",
          type: "POST",
          dataType: "JSON",
          data: formulario,
          success: function (resultado) {
            if (resultado.correcto) {
              imprimirError("Se ha guardado con exito");
              limpiarFormulario();
              cargarCategorias();
            } else {
              imprimirError(resultado.mensajeError);
            }
          },
        });
      }
    });

    $("#boton-actualizar").click(function () {
      var formulario = {};
      formulario.categoria = $("#categoria").val();
      formulario.idCategoria = $("#categoria-id").val();

      if (validarCategoria(formulario)) {
        $.ajax({
          url: "php/categoria/guardar_categoria.php",
          type: "POST",
          dataType: "JSON",
          data: formulario,
          success: function (resultado) {
            if (resultado.correcto) {
              imprimirError("Se ha actualizado con exito");
              limpiarFormulario();
              cargarCategorias();
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
    $("#categoria").val("");
    $("#categoria-id").val("");
    $("#boton-actualizar").hide();
    $("#boton-guardar").show();
    $(".boton-accion").attr("disabled", false);
  };

  var validarCategoria = function (formulario) {
    if (!esValidoAlfanumerico(formulario.categoria)) {
      imprimirError("El nombre de la categoria debe contener caracteres alfanumerico");
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
    $.ajax({
      url: "php/categoria/obtener_categorias.php",
      type: "get",
      success: function (response) {
        if (response) {
          $("#tabla-categorias").empty();
          $.each(response, function (ix, categoria) {
            var fila = $("<tr></tr>");

            fila.append($("<td></td>").text(categoria.nombre));

            var botonEditar = $("<button></button>")
              .attr("type", "button")
              .attr("class", "boton-accion btn btn-primary mr-1")
              .attr("id", categoria.id)
              .text("Editar");

            botonEditar.click(function () {
              mostrarEditar(categoria.id, categoria.nombre);
            });

            var botonEliminar = $("<button></button>").attr("type", "button").attr("class", "boton-accion btn btn-danger").text("Eliminar");

            botonEliminar.click(function () {
              mostrarEliminar(categoria.id, categoria.nombre);
            });

            var contenedorBotones = $("<td></td>");
            contenedorBotones.append(botonEditar);
            contenedorBotones.append(botonEliminar);
            fila.append(contenedorBotones);

            $("#tabla-categorias").append(fila);
          });
        }
      },
      error: function (response) {
        console.error(response);
      },
    });
  };

  var mostrarEditar = function (idCategoria, nombreCategoria) {
    $("#categoria").val(nombreCategoria);
    $("#categoria-id").val(idCategoria);
    $("#boton-actualizar").show();
    $("#boton-guardar").hide();
    $(".boton-accion").attr("disabled", true);
  };

  var mostrarEliminar = function (idCategoria, nombreCategoria) {
    var resultado = confirm("Esta seguro que desea eliminar : " + nombreCategoria);
    if (resultado == true) {
      $.ajax({
        url: "php/categoria/eliminar_categoria.php?idCategoria=" + idCategoria,
        type: "DELETE",
        dataType: "JSON",
        success: function (resultado) {
          if (resultado.correcto) {
            imprimirError("Se ha eliminado con exito");
            limpiarFormulario();
            cargarCategorias();
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
    limpiarFormulario();
  };

  inicializar();
});
