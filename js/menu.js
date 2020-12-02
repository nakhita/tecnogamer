$(function () {
  var paginasRestringidas = [
    "ver_publicacion.html",
    "ver_publicaciones.html",
    "perfil.html",
    "alta_publicacion.html",
    "abm_etiquetas.html",
    "lista_suscriciones.html",
  ];

  var agregarEventos = function () {
    $("#registrar-link").click(function (e) {
      window.location.href = "registrar.html";
    });
    $("#loguear-link").click(function (e) {
      window.location.href = "login.html";
    });
    $("#logout-link").click(function (e) {
      $.ajax({
        url: "php/obtener_usuario_echo.php",
        type: "GET",
        dataType: "JSON",
        success: function (resultado) {
          if (resultado > 0) {
            $.ajax({
              url: "php/log/logout.php",
              type: "GET",
              dataType: "JSON",
              success: function (respuesta) {
                if (respuesta.correcto) {
                  setTimeout(function () {
                    window.location.href = "index.html";
                  }, 1000);
                }
              },
            });
          }
        },
      });
    });
  };

  var cargarPerfil = function () {
    $.ajax({
      url: "php/obtener_usuario_echo.php",
      type: "GET",
      dataType: "JSON",
      success: function (resultado) {
        if (resultado > 0) {
          $("#miperfil").attr("href", "perfil.html?id=" + resultado);
        }
      },
    });
  };

  var imprimirError = function (mensaje) {
    alert("Error no tiene permisos");
  };

  var cargarPermisos = function () {
    $.ajax({
      url: "php/permisos/obtener_permisos.php",
      type: "GET",
      dataType: "JSON",
      success: function (respuesta) {
        if (!respuesta.error) {
          validarPermisos(respuesta.permisos);
          mostrarMenu(respuesta.permisos, "#miperfil");
          mostrarMenu(respuesta.permisos, "#publicaciones");
          mostrarMenu(respuesta.permisos, "#crear-publicacion");
          mostrarMenu(respuesta.permisos, "#abm-etiquetas");
          mostrarMenu(respuesta.permisos, "#suscriciones");
        } else {
          imprimirError(respuesta.mensaje);
        }
      },
    });
  };

  var mostrarMenu = function (permisos, menu) {
    $(menu).css("display", "none");
    $.each(permisos, function (ix, permiso) {
      if (permiso.menu_id == menu) {
        $(menu).css("display", "inline-block");
      }
    });
  };

  var validarPermisos = function (permisos) {
    var url = window.location.href.split("?")[0];
    url = url.substring(url.lastIndexOf("/") + 1);
    if (paginasRestringidas.indexOf(url) >= 0) {
      var permitido = false;
      $.each(permisos, function (ix, permiso) {
        if (permiso.url == url) {
          permitido = true;
        }
      });
      if (!permitido) {
        window.location.href = "index.html";
      }
    }
  };

  var inicializar = function () {
    $("#menu-contenedor").load("./menu.html", function () {
      $.ajax({
        url: "php/obtener_usuario_echo.php",
        type: "GET",
        dataType: "JSON",
        success: function (respuesta) {
          if (respuesta > 0) {
            $("#inicio").css("display", "inline-block");
            $("#logout-link").css("display", "inline-block");
            $("#registrar-link").css("display", "none");
            $("#loguear-link").css("display", "none");
            $("#id-user").css("display", "none");
            $("#id-user").text(respuesta);
          } else {
            $("#inicio").css("display", "none");
            $("#logout-link").css("display", "none");
            $("#id-user").css("display", "none");
            $("#id-user").text(respuesta);
            $("#registrar-link").css("display", "inline-block");
            $("#loguear-link").css("display", "inline-block");
          }

          cargarPermisos();
        },
      });
      agregarEventos();
      cargarPerfil();
    });
  };

  inicializar();
});
