$(function () {
  var agregarEventos = function () {
    $("#registrar-usuario").click(function () {
      var formulario = {};
      formulario.nombre = $("#nombre").val();
      formulario.apellido = $("#apellido").val();
      formulario.sexo = $("#sexo").val();
      formulario.usuario = $("#usuario").val();
      formulario.password = $("#password").val();
      formulario.email = $("#email").val();

      if (validarUsuario(formulario)) {
        subirImagenes(function (rutaImagen) {
          formulario.rutaImagen = rutaImagen;
          $.ajax({
            url: "php/usuario/alta_usuario.php",
            type: "POST",
            dataType: "JSON",
            data: formulario,
            success: function (resultado) {
              if (resultado.correcto) {
                imprimirError("Usuario registrado correctamente, redireccionando a pagina para ingresar");
                setTimeout(function () {
                  window.location.href = "login.html";
                }, 2000);
              } else {
                imprimirError(resultado.mensaje);
              }
            },
          });
        });
      }
    });
  };

  var validarUsuario = function (formulario) {
    if (formulario.nombre == "") {
      imprimirError("Nombre es requerido");
      return false;
    } else if (!esValidoAlfabetico(formulario.nombre)) {
      imprimirError("El nombre debe contener caracteres alfabeticos");
      return false;
    }

    if (formulario.apellido == "") {
      imprimirError("Apellido es requerido");
      return false;
    } else if (!esValidoAlfabetico(formulario.apellido)) {
      imprimirError("El apellido debe contener caracteres alfabeticos");
      return false;
    }

    if (formulario.sexo == "") {
      imprimirError("Debe seleccionar un sexo");
      return false;
    }

    if (formulario.usuario == "") {
      imprimirError("Usuario es requerido");
      return false;
    } else if (formulario.usuario.length < 5 || formulario.usuario.length > 20) {
      imprimirError("El usuario debe tener entre 5 y 20 caracteres");
      return false;
    } else if (!esValidoAlfanumerico(formulario.usuario)) {
      imprimirError("El usuario solo debe contener caracteres alfanumericos sin espacios");
      return false;
    }

    if (formulario.password == "") {
      imprimirError("Password es requerido");
      return false;
    } else if (formulario.password.length < 5 || formulario.password.length > 20) {
      imprimirError("El password debe tener entre 5 y 20 caracteres");
      return false;
    }

    if (formulario.email == "") {
      imprimirError("Email es requerido");
      return false;
    } else if (!esValidoEmail(formulario.email)) {
      imprimirError("Email no es valido");
      return false;
    }

    if ($("#avatar").get(0).files.length === 0) {
      imprimirError("Avatar es requerido");
      return false;
    }

    imprimirError("");
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

  var inicializar = function () {
    agregarEventos();
  };

  inicializar();
});
