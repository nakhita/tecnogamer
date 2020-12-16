$(function () {
  var abrirComentarios = function (comentarios) {
    $("#p-comentarios").text(comentarios);
    $("#modal-comentarios").modal("show");
  };

  var cerrarSolicitud = function (idSolicitud, tipoCerrarSolicitud) {
    $.ajax({
      url: "php/solicitud_cc/cerrar_solicitud_cc.php?idSolicitud=" + idSolicitud + "&tipoCerrarSolicitud=" + tipoCerrarSolicitud,
      type: "POST",
      dataType: "JSON",
      success: function (resultado) {
        if (resultado.correcto) {
          inicializar();
        } else {
          $("#mensaje-error").text(resultado.mensaje);
          $("#alerta-atender-error").show();
        }
      },
    });
  };

  var inicializar = function () {
    $.ajax({
      url: "php/solicitud_cc/obtener_solicitudes_cc.php",
      type: "GET",
      dataType: "JSON",
      success: function (resultado) {
        if (resultado.correcto) {
          $("#tabla-solicitudes").empty();
          $.each(resultado.solicitudes, function (ix, solicitud) {
            var tr = $("<tr></tr>");

            var tdFecha = $("<td></td>").text(solicitud.fecha);

            var aUsuario = $("<a></a>")
              .text(solicitud.usuario)
              .attr("class", "usuario")
              .attr("href", "perfil.html?id=" + solicitud.id_usuario);

            var tdUsuario = $("<td></td>").append(aUsuario);
            var tdExperiencia = "";
            if (solicitud.experiencia == 1) {
              tdExperiencia = $("<td></td>").text("SI");
            } else {
              tdExperiencia = $("<td></td>").text("NO");
            }

            var tdMotivo = $("<td></td>").text(solicitud.motivo);

            var btnAceptar = $("<button></button>")
              .attr("type", "button")
              .attr("class", "boton-accion btn btn-info mr-1")
              .attr("id", "btn-comentarios-" + solicitud.id)
              .append('<i class="fas fa-check"></i>');

            btnAceptar.click(function () {
              cerrarSolicitud(solicitud.id, 1);
            });

            var btnRechazar = $("<button></button>")
              .attr("type", "button")
              .attr("class", "boton-accion btn btn-primary mr-1")
              .attr("id", "btn-atender-" + solicitud.id)
              .append('<i class="fas fa-times"></i>');

            btnRechazar.click(function () {
              cerrarSolicitud(solicitud.id, 2);
            });

            var tdBotones = $("<td></td>");

            var divBotones = $("<div></div>").attr("class", "row");

            divBotones.append(btnAceptar);
            divBotones.append(btnRechazar);

            tdBotones.append(divBotones);

            tr.append(tdFecha);
            tr.append(tdUsuario);
            tr.append(tdExperiencia);
            tr.append(tdMotivo);
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
