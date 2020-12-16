$(function () {
  var nombre = "";
  var apellido = "";
  var sexo = "";
  var usuario = "";
  var password = "";
  var email = "";

  var agregarEventos = function () {
    $("#actualizar-usuario").click(function () {
      var formulario = {};
      formulario.nombre = compararFormulario(nombre, $("#nombre").val());
      formulario.apellido = compararFormulario(apellido, $("#apellido").val());
      formulario.sexo = compararFormulario(sexo, $("#sexo").val());
      formulario.usuario = compararFormulario(usuario, $("#usuario").val());
      formulario.password = compararFormulario(password, $("#password").val());
      formulario.email = compararFormulario(email, $("#email").val());

      if (validarUsuario(formulario)) {
        $.ajax({
          url: "php/obtener_usuario_echo.php",
          type: "GET",
          dataType: "JSON",
          success: function (id) {
            if (id > 0) {
              formulario.id = id;
              subirImagenes(function (rutaImagen) {
                if (rutaImagen) {
                  formulario.rutaImagen = rutaImagen;
                } else {
                  formulario.rutaImagen = "";
                }
                $.ajax({
                  url: "php/perfil/editar_perfil.php",
                  type: "POST",
                  dataType: "JSON",
                  data: formulario,
                  success: function (resultado) {
                    if (resultado.correcto) {
                      imprimirError("Usuario actualizado correctamente, redireccionando a pagina para ingresar");
                      setTimeout(function () {
                        window.location.href = "perfil.html?id=" + resultado.id;
                      }, 2000);
                    } else {
                      imprimirError(resultado.mensaje);
                    }
                  }
                });
              });
            } else {
              imprimirError(id.mensaje);
            }
          }
        });

      }
    });
  };

  var compararFormulario = function (antiguo, nuevo) {
    if (antiguo === nuevo) {
      return "";
    }
    return nuevo;
  };

  var validarUsuario = function (formulario) {
    if (formulario.nombre != "") {
      if (!esValidoAlfabetico(formulario.nombre)) {
        imprimirError("El nombre debe contener caracteres alfabeticos");
        return false;
      }
    }
    if (formulario.apellido != "") {
      if (!esValidoAlfabetico(formulario.apellido)) {
        imprimirError("El apellido debe contener caracteres alfabeticos");
        return false;
      }
    }

    if (formulario.usuario != "") {
      if (formulario.usuario.length < 5 || formulario.usuario.length > 20) {
        imprimirError("El usuario debe tener entre 5 y 20 caracteres");
        return false;
      } else if (!esValidoAlfanumerico(formulario.usuario)) {
        imprimirError("El usuario solo debe contener caracteres alfanumericos sin espacios");
        return false;
      }
    }

    if (formulario.password != "") {
      if (formulario.password.length < 5 || formulario.password.length > 20) {
        imprimirError("El password debe tener entre 5 y 20 caracteres");
        return false;
      }
    }
    if (formulario.email != "") {
      if (!esValidoEmail(formulario.email)) {
        imprimirError("Email no es valido");
        return false;
      }
    }
    return true;
  };

  var imprimirError = function (texto) {
    $("#resultado").text(texto);
  };

  var esValidoAlfabetico = function (nombre) {
    let pattern = /^[A-Za-z ]+$/g;
    return pattern.test(nombre);
  };

  var esValidoAlfanumerico = function (nombre) {
    let pattern = /^[A-Za-z0-9]+$/g;
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
      success: function (response) {
        if (response.indexOf("imagenes") == 0 && callback) {
          callback(response);
        }
      },
      error: function (response) {
        console.error(response);
      },
    });
  };

  var cargarEditar = function () {
    var queryString = window.location.search;
    var urlParams = new URLSearchParams(queryString);
    var idPerfil = urlParams.get("id");
    if (idPerfil) {
      $("#perfil-id").val(idPerfil);
      $("#titulo").text("Editar Perfil");
      $.ajax({
        url: "php/perfil/obtener_datos_perfil.php?id=" + idPerfil,
        type: "GET",
        dataType: "JSON",
        success: function (respuesta) {
          if (respuesta && respuesta.length > 0) {
            var perfil = respuesta[0];
            nombre = perfil.nombre;
            apellido = perfil.apellido;
            sexo = perfil.sexo;
            usuario = perfil.usuario;
            email = perfil.email;
            $("#nombre").val(nombre);
            $("#apellido").val(apellido);
            $("#sexo option[value=" + sexo + "]").attr("selected", true);
            $("#usuario").val(usuario);
            $("#email").val(email);
          }
        },
        error: function (respuesta) {
          console.error(respuesta);
        },
      });
    }
  };
  var inicializar = function () {
    agregarEventos();
    cargarEditar();
  };

  inicializar();
});
