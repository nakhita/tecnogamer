$(function () {

  var cancelarSus = function (idSus) {
    var respuesta = confirm("Esta seguro de cancelar su suscricion?");
    if (respuesta == true) {
      $.ajax({
        url: "php/listasuscriciones/cancelar_suscricion.php?idSus=" + idSus,
        type: "DELETE",
        dataType: "JSON",
        success: function (resultado) {
          if (!resultado.error) {
            if (resultado.correcto) {
              imprimirMensaje(resultado.correcto, resultado.mensaje);
              inicializar();
            } else {
              imprimirMensaje(resultado.correcto, resultado.mensaje)
            }
          } else {
            imprimirMensaje(false, resultado.mensaje);
          }
        },
      });
    }
  };

  var imprimirMensaje = function (error, mensaje) {
    $("#mensaje").text(mensaje);
    if (error) {
      $("#alerta").removeClass("alert-danger");
      $("#alerta").addClass("alert-success");
    } else {
      $("#alerta").addClass("alert-danger");
      $("#alerta").removeClass("alert-success");
    }
    $(".alert").show();
    window.scrollTo(0, 0);
  };

  var inicializar = function () {
    $.ajax({
      url: "php/listasuscriciones/obtener_lista_suscriciones.php",
      type: "GET",
      dataType: "JSON",
      success: function (resultado) {
        if (resultado) {
          $("#lista-suscriciones").empty();
          $.each(resultado, function (ix, suscricion) {
            var tr = $("<tr></tr>");
            var a = $("<a></a>");
            var img = $("<img>");
            var boton = $("<button></button>");
            var i = $("<i></i>");
            var avatar = img
              .attr("class", "mini-avatar")
              .attr("src", suscricion.ruta);
            var tdAvatar = $("<td></td>").append(avatar);
            var userName = a.text(suscricion.autor)
              .attr("class", "user-name")
              .attr("href", "perfil.html?id=" + suscricion.idAutor);
            var tdUser = $("<td></td>").append(userName);
            var icono = i.attr("class", "fas fa-times");
            var btn = boton.append(icono)
              .attr("class", "btn boton-celeste boton-card")
              .attr("type", "button")
              .attr("id", "cancel-sus" + suscricion.idSus);
            var tdBtn = $("<td></td>").append(btn);
            tr.append(tdAvatar);
            tr.append(tdUser);
            tr.append(tdBtn);

            btn.click(function () {
              cancelarSus(suscricion.idSus);
            });
            $("#lista-suscriciones").append(tr);
          });
        } else {
          alert("No existe suscriciones");
        }
      }
    });
  };
  inicializar();
});
