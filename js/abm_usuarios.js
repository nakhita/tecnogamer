$(function () {
  var agregarEventos = function () {
    $("#boton-guardar").click(function () {
      limpiarErrores();
      var formulario = {};
      formulario.usuario = $("#usuario").val();
      formulario.password = $("#password").val();
      formulario.nombre = $("#nombre").val();
      formulario.apellido = $("#apellido").val();
      formulario.email = $("#email").val();
      formulario.sexo = $("#sexo").val();
      formulario.rol = $("#rol").val();
      formulario.estado = $("#estado").val();

      if (validarUsuario(formulario)) {
        subirImagenes(function (rutaImagen) {
          formulario.rutaImagen = rutaImagen;
          $.ajax({
            url: "php/usuario/registrar_usuario.php",
            type: "POST",
            dataType: "JSON",
            data: formulario,
            success: function (resultado) {
              if (resultado.correcto) {
                limpiarFormulario();
                $("#modal-usuario").modal("hide");
                cargarUsuarios();
              } else {
                console.error(resultado.mensaje);
              }
            },
          });
        });
      }
    });

    $("#boton-actualizar").click(function () {
      limpiarErrores();
      var formulario = {};
      formulario.id = $("#id-usuario").val();
      formulario.usuario = $("#usuario").val();
      if ($("password").val() != "") {
        formulario.password = $("#password").val();
      }
      formulario.nombre = $("#nombre").val();
      formulario.apellido = $("#apellido").val();
      formulario.email = $("#email").val();
      formulario.sexo = $("#sexo").val();
      formulario.rol = $("#rol").val();
      formulario.estado = $("#estado").val();

      if (validarUsuario(formulario)) {
        subirImagenes(function (rutaImagen) {
          if (rutaImagen) {
            formulario.rutaImagen = rutaImagen;
          }

          $.ajax({
            url: "php/usuario/actualizar_usuario.php",
            type: "POST",
            dataType: "JSON",
            data: formulario,
            success: function (resultado) {
              if (resultado.correcto) {
                limpiarFormulario();
                $("#modal-usuario").modal("hide");
                cargarUsuarios();
              } else {
                console.error(resultado.mensaje);
              }
            },
          });
        });
      }
    });

    $("#boton-nuevo-usuario").click(limpiarFormulario);
  };

  var limpiarFormulario = function () {
    limpiarErrores();
    $("#id-usuario").val("");
    $("#usuario").val("");
    $("#nombre").val("");
    $("#apellido").val("");
    $("#email").val("");
    $("#sexo").val("");
    $("#rol").val("");
    $("#estado").val("");
    $("#avatar").val("");
    $("#boton-guardar").show();
    $("#boton-actualizar").hide();
  };

  var validarUsuario = function (formulario) {
    var valido = true;
    if (formulario.usuario == "") {
      imprimirMensajeError($("#usuario"), "El usuario es requerido");
      valido = false;
    } else if (!esValidoAlfanumerico(formulario.usuario)) {
      imprimirMensajeError($("#usuario"), "El usuario debe contener caracteres alfanumerico");
      valido = false;
    }

    if (formulario.nombre == "") {
      imprimirMensajeError($("#nombre"), "El nombre es requerido");
      valido = false;
    } else if (!esValidoAlfabetico(formulario.nombre)) {
      imprimirMensajeError($("#nombre"), "El nombre debe contener caracteres alfabeticos");
      valido = false;
    }

    if (formulario.apellido == "") {
      imprimirMensajeError($("#apellido"), "El apellido es requerido");
      valido = false;
    } else if (!esValidoAlfabetico(formulario.nombre)) {
      imprimirMensajeError($("#apellido"), "El apellido debe contener caracteres alfabeticos");
      valido = false;
    }

    if (formulario.email == "") {
      imprimirMensajeError($("#email"), "El email es requerido");
      valido = false;
    } else if (!esValidoEmail(formulario.email)) {
      imprimirMensajeError($("#email"), "El email no es valido");
      valido = false;
    }

    if (formulario.sexo == "") {
      imprimirMensajeError($("#sexo"), "Debe seleccionar un sexo");
      valido = false;
    }

    if (formulario.rol == "") {
      imprimirMensajeError($("#rol"), "Debe seleccionar un rol");
      valido = false;
    }

    if (formulario.estado == "") {
      imprimirMensajeError($("#estado"), "Debe seleccionar un estado");
      valido = false;
    }

    var esNuevoUsuario = false;
    if ($("#id-usuario").val() == "") {
      esNuevoUsuario = true;
    }

    if (esNuevoUsuario) {
      if (formulario.password == "") {
        imprimirMensajeError($("#password"), "El password es requerido");
        valido = false;
      }

      if ($("#avatar").val() == "") {
        imprimirMensajeError($("#avatar"), "El avatar es requerido");
        valido = false;
      }
    }

    return valido;
  };

  var limpiarErrores = function () {
    $(".mensaje-error").remove();
    $(".input-error").removeClass("input-error");
  };

  var imprimirMensajeError = function (selector, texto) {
    selector.addClass("input-error");
    selector.after("<div class='mensaje-error'>" + texto + "</div>");
  };

  var cargarUsuarios = function () {
    $.ajax({
      url: "php/usuario/obtener_usuarios.php",
      type: "get",
      success: function (respuesta) {
        if (respuesta) {
          $("#tabla-usuarios").empty();
          $.each(respuesta.usuarios, function (ix, usuario) {
            var trFila = $("<tr></tr>");

            var tdUsuario = $("<td></td>").text(usuario.usuario);
            trFila.append(tdUsuario);

            var tdNombre = $("<td></td>").text(usuario.nombre);
            trFila.append(tdNombre);

            var tdApellido = $("<td></td>").text(usuario.apellido);
            trFila.append(tdApellido);

            var tdEmail = $("<td></td>").text(usuario.email);
            trFila.append(tdEmail);

            var tdRol = $("<td></td>").text(usuario.rol_usuario);
            trFila.append(tdRol);

            var estado = "";
            if (usuario.estado == 1) {
              estado = "Activo";
            } else if (usuario.estado == 2) {
              estado = "Baneado";
            }
            var tdEstado = $("<td></td>").text(estado);

            trFila.append(tdEstado);

            var botonEditar = $("<button></button>")
              .attr("type", "button")
              .attr("class", "boton-accion btn btn-primary mr-1")
              .attr("id", "usuario-id-" + usuario.id)
              .text("Editar");

            botonEditar.click(function () {
              mostrarEditar(usuario);
            });

            var contenedorBotones = $("<td></td>");
            contenedorBotones.append(botonEditar);
            trFila.append(contenedorBotones);

            $("#tabla-usuarios").append(trFila);
          });
        }
      },
      error: function (respuesta) {
        console.error(respuesta);
      },
    });
  };

  var cargarRoles = function () {
    $.ajax({
      url: "php/rol/obtener_roles.php",
      type: "get",
      success: function (respuesta) {
        if (respuesta) {
          $("#rol").empty();
          $("#rol").append($("<option></option>").attr("value", "").text("- Seleccione -"));
          $.each(respuesta, function (ix, rol) {
            $("#rol").append($("<option></option>").attr("value", rol.id).text(rol.nombre));
          });
        }
      },
    });
  };

  var mostrarEditar = function (usuario) {
    limpiarErrores();
    $("#id-usuario").val(usuario.id);
    $("#usuario").val(usuario.usuario);
    $("#nombre").val(usuario.nombre);
    $("#apellido").val(usuario.apellido);
    $("#email").val(usuario.email);
    $("#sexo").val(usuario.sexo);
    $("#rol").val(usuario.id_rol);
    $("#estado").val(usuario.estado);

    $("#boton-guardar").hide();
    $("#boton-actualizar").show();
    $("#modal-usuario").modal("show");
  };

  var esValidoAlfanumerico = function (nombre) {
    let pattern = /^[A-Za-z0-9]+$/g;
    return pattern.test(nombre);
  };

  var esValidoAlfabetico = function (nombre) {
    let pattern = /^[A-Za-z ]+$/g;
    return pattern.test(nombre);
  };

  var esValidoEmail = function (email) {
    let pattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
    return pattern.test(email);
  };

  var subirImagenes = function (callback) {
    var file_data = $("#avatar").prop("files")[0];

    if (!file_data) {
      callback();
      return;
    }

    var form_data = new FormData();

    form_data.append("file", file_data);
    $.ajax({
      url: "php/publicacion/subir_imagenes.php",
      dataType: "text",
      cache: false,
      contentType: false,
      processData: false,
      data: form_data,
      type: "post",
      success: function (respuesta) {
        if (respuesta.indexOf("imagenes") == 0 && callback) {
          callback(respuesta);
        }
      },
      error: function (respuesta) {
        console.error(respuesta);
      },
    });
  };

  var inicializar = function () {
    cargarRoles();
    agregarEventos();
    cargarUsuarios();
    limpiarFormulario();
  };

  inicializar();
});
