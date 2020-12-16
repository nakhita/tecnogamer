$(function () {
  var agregarEventos = function () {
    $("#agregar-etiqueta").click(function () {
      limpiarErrores();
      var texto = $("#etiqueta").val();

      // validar
      if (texto == "") {
        imprimirMensajeError($("#etiqueta"), "Debe ingresar una etiqueta");
        return;
      }

      if (!esValidoAlfanumerico(texto)) {
        imprimirMensajeError($("#etiqueta"), "Debe ingresar solo caracteres alfanumericos");
        return;
      }

      agregarEtiquetaInterfaz(texto);
      $("#etiqueta").focus();
    });

    $("#confirmar").click(function () {
      if (validarFormulario()) {
        var solicitud = {};
        solicitud.categoria = $("#categoria").val();
        solicitud.etiquetas = obtenerEtiquetasSeleccionadas();
        solicitud.comentario = $("#comentario").val();

        $.ajax({
          data: solicitud,
          url: "php/solicitud/guardar_solicitud_categoria.php",
          type: "post",
          success: function (response) {
            if (response.correcto) {
              $(".alert").show();
              window.scrollTo(0, 0);
              limpiarFormulario();
            }
          },
          error: function (response) {
            console.error(response);
          },
        });
      }
    });
  };

  var agregarEtiquetaInterfaz = function (texto) {
    if (texto == "") {
      return;
    }

    // Si el elemento existe no se agrega a la lista
    var etiquetaRepetida = false;
    $.each(obtenerEtiquetasSeleccionadas(), function (ix, etiqueta) {
      if (etiqueta.toLowerCase() == texto.toLowerCase()) {
        etiquetaRepetida = true;
      }
    });

    if (etiquetaRepetida) {
      return;
    }

    // Creamos identificador unico para el id de la etiqueta para agregarla a la interfaz
    var idEtiqueta = Date.now();

    var contenedor = $("<div></div>")
      .attr("type", "button")
      .attr("class", "btn btn-secondary etiqueta-individual mr-1")
      .attr("id", "etiqueta-id-" + idEtiqueta);
    var strong = $("<strong></strong>").text(texto);

    contenedor.append(strong);
    contenedor.append("&nbsp;");

    var span = $("<span></span>")
      .attr("type", "button")
      .attr("class", "close")
      .attr("aria-label", "Cerrar")
      .click(function () {
        $("#etiqueta-id-" + idEtiqueta).remove();
      });

    span.append("<span></span>").attr("aria-hidden", "true").html("&times;");

    contenedor.append(span);
    $("#etiqueta-contenedor").append(contenedor);

    $("#etiqueta").val("");
  };

  var limpiarFormulario = function () {
    $("#categoria").val("");
    $("#etiqueta-contenedor").empty();
    $("#etiqueta").val("");
    $("#comentario").val("");
  };

  var obtenerEtiquetasSeleccionadas = function () {
    var etiquetasDIV = $("#etiqueta-contenedor .etiqueta-individual");
    var etiquetas = [];
    $.each(etiquetasDIV, function (ix, el) {
      var etiqueta = $(el).find("strong").text();
      etiquetas.push(etiqueta);
    });
    return etiquetas;
  };

  var validarFormulario = function () {
    limpiarErrores();
    var categoria = $("#categoria").val();
    var etiquetas = obtenerEtiquetasSeleccionadas();
    var comentarios = $("#comentario").val();

    var valido = true;
    if (categoria == "") {
      imprimirMensajeError($("#categoria"), "Debe ingresar una categoria");
      valido = false;
    }
    if (etiquetas.length == 0) {
      imprimirMensajeError($("#etiqueta-conjunto"), "Debe agregar al menos una etiqueta");
      valido = false;
    }
    if (comentarios == "") {
      imprimirMensajeError($("#comentario"), "Debe ingresar un comentario");
      valido = false;
    }

    return valido;
  };

  var esValidoAlfanumerico = function (nombre) {
    let pattern = /^[A-Za-z0-9]+$/g;
    return pattern.test(nombre);
  };

  var limpiarErrores = function () {
    $(".mensaje-error").remove();
    $(".input-error").removeClass("input-error");
  };

  var imprimirMensajeError = function (selector, texto) {
    selector.addClass("input-error");
    selector.after("<div class='mensaje-error'>" + texto + "</div>");
  };

  var inicializar = function () {
    agregarEventos();
  };

  inicializar();
});
