$(function () {
  var idPerfil = 0;

  var cargarUsuario = function (usuario) {
    $("#user-name").attr("href", "ver_publicaciones.html?autor=" + usuario);
  }
  var cargarPerfil = function () {
    var queryString = window.location.search;
    var urlParams = new URLSearchParams(queryString);
    var idPerfil = urlParams.get("id");

    idPerfil = idPerfil;

    $.ajax({
      url: "php/perfil/obtener_perfil.php?id=" + idPerfil,
      type: "GET",
      dataType: "JSON",
      success: function (resultado) {
        if (resultado && resultado.length > 0) {
          var perfil = resultado[0];
          $("#nombre").text(perfil.nombre);
          $("#apellido").text(perfil.apellido);
          $("#avatar").attr("src", perfil.avatar);
          $("#user-name").text(perfil.usuario);
          $("#idautor").text(perfil.id);
          cargarUsuario(perfil.usuario);
          $.ajax({
            url: "php/obtener_usuario_echo.php",
            type: "GET",
            dataType: "JSON",
            success: function (respuesta) {
              if (!respuesta.error) {
                if (respuesta.perfil > 0) {
                  $("#iduser").text(respuesta);
                  if (respuesta.perfil != perfil.id) {
                    $.getScript("js/boton_suscribirse.js");
                  }
                }
              } else {
                alert("Error al obtener usuario");
              }
            },
          });
          $("#idautor").hide();
          $("#iduser").hide();
        } else {
          alert("No existe");
        }
      },
    });
  };

  var inicializar = function () {
    cargarPerfil();
  };

  inicializar();
});
