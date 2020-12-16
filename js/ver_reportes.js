$(function () {
  var abrirReportes = function (idPublicacion) {
    $.ajax({
      url: "php/reporte/obtener_reportes_por_publicacion.php?idPublicacion=" + idPublicacion,
      type: "GET",
      dataType: "JSON",
      success: function (resultado) {
        if (resultado.correcto) {
          $("#tabla-comentarios").empty();

          $.each(resultado.reportes, function (ix, reporte) {
            var tr = $("<tr></tr>");

            var tdFecha = $("<td></td>").text(reporte.fecha);

            var tdReportador = $("<td></td>").text(reporte.usuario);

            var tdComentario = $("<td></td>").text(reporte.comentario);

            tr.append(tdFecha);
            tr.append(tdReportador);
            tr.append(tdComentario);

            $("#tabla-comentarios").append(tr);
          });

          $("#modal-comentarios").modal("show");
        }
      },
    });
  };

  var abrirAtender = function (idPublicacion) {
    $("#modal-atender").modal("show");
    $("#boton-borrar-reporte")
      .off("click")
      .on("click", function () {
        cerrarReportes(idPublicacion, 1);
      });
    $("#boton-eliminar")
      .off("click")
      .on("click", function () {
        cerrarReportes(idPublicacion, 2);
      });
    $("#boton-eliminar-banear")
      .off("click")
      .on("click", function () {
        cerrarReportes(idPublicacion, 3);
      });
  };

  var cerrarReportes = function (idPublicacion, tipoCerrarReporte) {
    $.ajax({
      url: "php/reporte/cerrar_reportes.php?idPublicacion=" + idPublicacion + "&tipoCerrarReporte=" + tipoCerrarReporte,
      type: "DELETE",
      dataType: "JSON",
      success: function (resultado) {
        if (resultado) {
          $("#modal-atender").modal("hide");
          inicializar();
        }
      },
    });
  };

  var inicializar = function () {
    $.ajax({
      url: "php/reporte/obtener_reportes.php",
      type: "GET",
      dataType: "JSON",
      success: function (resultado) {
        if (resultado.reportes) {
          $("#tabla-reportes").empty();
          $.each(resultado.reportes, function (ix, reporte) {
            var tr = $("<tr></tr>");

            var aTituloPublicacion = $("<a></a>")
              .attr("class", "usuario")
              .attr("href", "ver_publicacion.html?id=" + reporte.id_publicacion)
              .text(reporte.titulo);

            var tdFecha = $("<td></td>").text(reporte.fecha);

            var tdTituloPublicacion = $("<td></td>").append(aTituloPublicacion);

            var aCreador = $("<a></a>")
              .text(reporte.usuario)
              .attr("class", "usuario")
              .attr("href", "perfil.html?id=" + reporte.id_autor);

            var tdCreador = $("<td></td>").append(aCreador);

            var btnComentarios = $("<button></button>")
              .attr("type", "button")
              .attr("class", "boton-accion btn btn-info mr-1")
              .attr("id", reporte.id)
              .text("Reportes");

            btnComentarios.click(function () {
              abrirReportes(reporte.id_publicacion);
            });

            var btnAtender = $("<button></button>")
              .attr("type", "button")
              .attr("class", "boton-accion btn btn-primary mr-1")
              .attr("id", reporte.id)
              .text("Atender");

            btnAtender.click(function () {
              abrirAtender(reporte.id_publicacion);
            });

            var tdBotones = $("<td></td>");
            tdBotones.append(btnComentarios);
            tdBotones.append(btnAtender);

            tr.append(tdFecha);
            tr.append(tdTituloPublicacion);
            tr.append(tdCreador);
            tr.append(tdBotones);
            $("#tabla-reportes").append(tr);
          });
        } else {
          alert("No existe reportes");
        }
      },
    });
  };
  inicializar();
});
