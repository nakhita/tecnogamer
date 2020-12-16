$(function () {
  var paginasRestringidas = ["perfil.html", "alta_publicacion.html", "abm_etiquetas.html", "lista_suscriciones.html", "abm_categorias.html"];

  var agregarEventos = function () {
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
    $("#logout").click(function (e) {
      $("#menu-login-registrar").css("width", "250px");
      $("#menu-login-registrar").css("display", "block");
    });
    $("#btn-menu-cerrar-login-registrar").click(function (e) {
      $("#menu-login-registrar").css("width", "0");
    });
    $("#btn-menu").click(function (e) {
      $("#menu-login").css("width", "250px");
      $("#menu-login").css("display", "block");
    });
    $("#btn-menu-cerrar").click(function (e) {
      $("#menu-login").css("width", "0");
    });
    $("#btn-notificacion").click(function (e) {
      if ($("#lista-notificaciones").hasClass("nover")) {
        $("#lista-notificaciones").removeClass("nover").addClass("ver");
      } else {
        $("#lista-notificaciones").removeClass("ver").addClass("nover");
      }
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

  var cargarPermisos = function () {
    $.ajax({
      url: "php/permisos/obtener_permisos.php",
      type: "GET",
      dataType: "JSON",
      success: function (permisos) {
        mostrarMenu(permisos, "#miperfil");
        mostrarMenu(permisos, "#crear-publicacion");
        mostrarMenu(permisos, "#abm-etiquetas");
        mostrarMenu(permisos, "#abm-categorias");
        mostrarMenu(permisos, "#suscriciones");
        mostrarMenu(permisos, "#solicitud-categoria");
        mostrarMenu(permisos, "#abm-usuarios");
        mostrarMenu(permisos, "#ver-solicitudes-categoria");
        mostrarMenu(permisos, "#ver-reportes");
        mostrarMenu(permisos, "#solicitud-cc");
        mostrarMenu(permisos, "#ver-solicitudes-cc");
        mostrarMenu(permisos, "#editar-perfil-menu-link");

        validarPermisos(permisos);
      },
    });
  };

  var mostrarMenu = function (permisos, menu) {
    $(menu).css("display", "none");
    $.each(permisos, function (ix, permiso) {
      if (permiso.menu_id == menu) {
        $(menu).css("display", "block");
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

  var actualizarNotificacion = function (notificacion) {
    $.ajax({
      url: "php/notificacion/actualizar_notificacion.php?id=" + notificacion.id + "&visto=1",
      type: "PUT",
      dataType: "JSON",
      success: function (respuesta) {
        if (respuesta.correcto) {
          window.location.href = "ver_publicacion.html?id=" + notificacion.id_publicacion;
        }
      },
    });
  };

  var obtenerNotificaciones = function () {
    $.ajax({
      url: "php/notificacion/obtener_notificaciones.php",
      type: "GET",
      dataType: "JSON",
      success: function (respuesta) {
        if (respuesta.correcto && respuesta.notificaciones && respuesta.notificaciones.length > 0) {
          var notificaciones = respuesta.notificaciones;
          $("#canti-notificaciones").text(notificaciones.length);

          $("#lista-notificaciones").empty();
          $.each(notificaciones, function (ix, notificacion) {
            var aNotificacion = $("<a></a>").attr("class", "noti noti-novista").attr("href", "#");

            aNotificacion.click(function () {
              actualizarNotificacion(notificacion);
            });

            var div = $("<div></div>");

            var spanTitulo = $("<span></span>")
              .attr("id", "titulo-noti-" + notificacion.id)
              .attr("class", "titulo-noti")
              .text(notificacion.titulo);

            var spanAutor = $("<span></span>").text(notificacion.usuario);
            var pAutor = $("<p></p>").attr("class", "cuerpo-noti").text("Por : ").append(spanAutor);

            div.append(spanTitulo);
            div.append(pAutor);

            aNotificacion.append(div);

            $("#lista-notificaciones").append(aNotificacion);
          });
        }
      },
    });
  };

  var inicializar = function () {
    $("#menu-contenedor").load("./menu.html", function () {
      $.ajax({
        url: "php/obtener_usuario_echo.php",
        type: "GET",
        dataType: "JSON",
        success: function (respuesta) {
          if (respuesta > 0) {
            $("#btn-menu").css("display", "inline-block");
            $("#registrar-link").css("display", "none");
            $("#loguear-link").css("display", "none");
            $("#logout").css("display", "none");
            $("#notificacion").css("display", "inline-block");
            $("#id-user").css("display", "none");
            $("#id-user").text(respuesta);
          } else {
            $("#btn-menu").css("display", "none");
            $("#id-user").css("display", "none");
            $("#id-user").text(respuesta);
            $("#notificacion").css("display", "none");
            $("#registrar-link").css("display", "inline-block");
            $("#loguear-link").css("display", "inline-block");
            $("#logout").css("display", "inline-block");
          }

          cargarPermisos();
        },
      });
      agregarEventos();
      cargarPerfil();
      obtenerNotificaciones();
    });
  };

  inicializar();
});
