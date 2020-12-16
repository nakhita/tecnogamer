$(function () {
  var abrirComentarios = function (comentarios) {
    $("#p-comentarios").text(comentarios);
    $("#modal-comentarios").modal("show");
  };

  var abrirAtender = function (idSolicitud, categoria, etiquetas) {
    $("#alerta-atender-error").hide();
    $("#categoria-nombre-modal").text(categoria);
    $("#etiqueta-nombre-modal").text(etiquetas);
    $("#modal-atender").modal("show");
    $("#boton-aprobar-solicitud")
      .off("click")
      .on("click", function () {
        cerrarSolicitud(idSolicitud, 1);
      });
    $("#boton-cerrar-solicitud")
      .off("click")
      .on("click", function () {
        cerrarSolicitud(idSolicitud, 2);
      });
  };

  var cerrarSolicitud = function (idSolicitud, tipoCerrarSolicitud) {
    $.ajax({
      url: "php/solicitud/cerrar_solicitud_categoria.php?idSolicitud=" + idSolicitud + "&tipoCerrarSolicitud=" + tipoCerrarSolicitud,
      type: "POST",
      dataType: "JSON",
      success: function (resultado) {
        if (resultado.correcto) {
          $("#modal-atender").modal("hide");
          inicializar();
        } else {
          $("#mensaje-error").text(resultado.mensaje);
          $("#alerta-atender-error").show();
        }
      },
    });
  };

  var inicializar = function () {
    $(".alert").on("close.bs.alert", function () {
      $(this).hide();
      return false;
    });

    $.ajax({
      url: "php/solicitud/obtener_solicitudes_categoria.php",
      type: "GET",
      dataType: "JSON",
      success: function (resultado) {
        if (resultado.solicitudes) {
          $("#tabla-solicitudes").empty();
          $.each(resultado.solicitudes, function (ix, solicitud) {
            var tr = $("<tr></tr>");

            var tdFecha = $("<td></td>").text(solicitud.fecha);

            var aUsuario = $("<a></a>")
              .text(solicitud.usuario)
              .attr("class", "usuario")
              .attr("href", "perfil.html?id=" + solicitud.id_usuario);

            var tdUsuario = $("<td></td>").append(aUsuario);

            var tdCategoria = $("<td></td>").text(solicitud.categoria);

            var arrayEtiquetas = [];
            $.each(solicitud.etiquetas, function (ix, elemento) {
              arrayEtiquetas.push(elemento.etiqueta);
            });
            var etiquetas = arrayEtiquetas.join(", ");

            var tdEtiquetas = $("<td></td>").text(etiquetas);

            var btnComentarios = $("<button></button>")
              .attr("type", "button")
              .attr("class", "boton-accion btn btn-info mr-1")
              .attr("id", "btn-comentarios-" + solicitud.id)
              .text("Ver Comentarios");

            btnComentarios.click(function () {
              abrirComentarios(solicitud.comentario);
            });

            var btnAtender = $("<button></button>")
              .attr("type", "button")
              .attr("class", "boton-accion btn btn-primary mr-1")
              .attr("id", "btn-atender-" + solicitud.id)
              .text("Atender");

            btnAtender.click(function () {
              abrirAtender(solicitud.id, solicitud.categoria, etiquetas);
            });

            var tdBotones = $("<td></td>");

            var divBotones = $("<div></div>").attr("class", "row");

            divBotones.append(btnComentarios);
            divBotones.append(btnAtender);

            tdBotones.append(divBotones);

            tr.append(tdFecha);
            tr.append(tdUsuario);
            tr.append(tdCategoria);
            tr.append(tdEtiquetas);
            tr.append(tdBotones);
            $("#tabla-solicitudes").append(tr);
          });
        } else {
          console.log("No existe solicitudes");
        }
      },
    });
  };

  inicializar();
});
