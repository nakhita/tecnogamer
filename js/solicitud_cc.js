$(function () {
  var agregarEventos = function () {
    $("#solicitar").click(function (e) {
      limpiarErrores();
      var solicitud = {};
      solicitud.experiencia = $("input[name='experiencia']:checked").val();
      solicitud.motivo = $("#motivo").val();
      solicitud.termino = $("#termino-check").is(":checked");
      if (validarSolitud(solicitud)) {
        $.ajax({
          url: "php/cc/solicitud_cc.php",
          type: "POST",
          dataType: "JSON",
          data: solicitud,
          success: function (resultado) {
            if (resultado.correcto) {
              inicializar();
            } else {
              alert(resultado.mensajeError);
            }
          },
        });
      }
    });
  };
  var limpiarErrores = function () {
    $(".mensaje-error").remove();
    $(".input-error").removeClass("input-error");
  };
  var imprimirMensajeError = function (selector, texto) {
    selector.addClass("input-error");
    selector.after("<div class='mensaje-error'>" + texto + "</div>");
  };

  var validarSolitud = function (solicitud) {
    var valido = true;
    if (solicitud.motivo == "") {
      imprimirMensajeError($("#motivo"), "*Es obligatorio dejar su motivo");
      valido = false;
    }
    if (!solicitud.termino) {
      imprimirMensajeError($("#termino"), "*Es obligatorio estar de acuerdo con los terminos y condiciones");
      valido = false;
    }
    return valido;
  };


  var inicializar = function () {
    $.ajax({
      url: "php/obtener_usuario_echo.php",
      type: "GET",
      dataType: "JSON",
      success: function (id) {
        if (id > 0) {
          $.ajax({
            url: "php/cc/estado_solicitud_cc.php",
            type: "GET",
            dataType: "JSON",
            success: function (resultado) {
              var solicitud = resultado[0];
              if (solicitud) {
                $("#formulario").css("display", "none");
                $("#estado-solicitud").css("display", "block");
                $("#fecha").text(solicitud.fecha);
                if (solicitud.experiencia) {
                  $("#res-experiencia").text(" " + "SI");
                } else {
                  $("#res-experiencia").text(" " + "NO");
                }
                $("#res-motivo").text(" " + solicitud.motivo);
              } else {

                $("#estado-solicitud").css("display", "none");
                agregarEventos();
              }
            },
          });
        } else {
          alert("DEBE INICIAR SESIÓN");
        }
      },
    });


  };

  inicializar();
});
