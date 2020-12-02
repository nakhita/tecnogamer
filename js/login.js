$(function () {

  var agregarEventos = function () {
    $("#ingresar").click(function () {
      var login = {};
      login.usuario = $("#usuario").val();
      login.password = $("#password").val();
      if (validarUsuario(login)) {
        $.ajax({
          url: "php/log/login.php",
          type: "POST",
          dataType: "JSON",
          data: login,
          success: function (resultado) {
            if (resultado.correcto) {
              setTimeout(function () {
                window.location.href = "ver_publicaciones.html";
              }, 1000);
            } else {
              imprimirError(resultado.mensaje);
            }
          },
        });
      }
    });
  };

  var inicializar = function () {
    agregarEventos();
  };

  var validarUsuario = function (login) {
    if (login.usuario == "") {
      imprimirError("Usuario es requerido");
      return false;
    } else if (login.usuario.length < 5 || login.usuario.length > 20) {
      imprimirError("El usuario debe tener entre 5 y 20 caracteres");
      return false;
    } else if (!esValidoAlfanumerico(login.usuario)) {
      imprimirError("El usuario solo debe contener caracteres alfanumericos sin espacios");
      return false;
    }

    if (login.password == "") {
      imprimirError("Password es requerido");
      return false;
    } else if (login.password.length < 5 || login.password.length > 20) {
      imprimirError("El password debe tener entre 5 y 20 caracteres");
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

  inicializar();
});
