$(document).ready(function () {

  var cargarBoton = function (callback) {
    $("#boton-sus-contenedor").load('./boton_suscribirse.html', callback);
  };

  var ponerSus = function () {
    $("#boton-sus").prop('disabled', true);
    suscribir(function () {
      $("#boton-sus").prop("disabled", false);
      suscritoVerde();
    });
  };

  var suscribir = function (callback) {
    var suscricion = {};
    var idAutor = $("#idautor").text();
    var idUser = $("#iduser").text();
    suscricion.idAutor = idAutor;
    suscricion.idUser = idUser;

    $.ajax({
      url: "php/suscripciones/agregar_suscripcion.php",
      type: "POST",
      dataType: "JSON",
      data: suscricion,
      success: function (respuesta) {
        if (respuesta.correcto) {
          callback();
        } else {
          alert(respuesta.mensaje);
        }
      },
      error: function (err) {
        console.log(err);
      },
    });
  };

  var quitarSuscribir = function (callback) {
    var suscricion = {};
    var idAutor = $("#idautor").text();
    var idUser = $("#iduser").text();
    suscricion.idAutor = idAutor;
    suscricion.idUser = idUser;

    $.ajax({
      url: "php/suscripciones/remover_suscripcion.php",
      type: "POST",
      dataType: "JSON",
      data: suscricion,
      success: function (respuesta) {
        if (respuesta.correcto) {
          callback();
        } else {
          alert(respuesta.mensaje);
        }
      },
      error: function (err) {
        console.log(err);
      },
    });
  };

  var cargarSuscrito = function (callback) {
    var suscricion = {};
    var idAutor = $("#idautor").text();
    var idUser = $("#iduser").text();
    suscricion.idAutor = idAutor;
    suscricion.idUser = idUser;

    $.ajax({
      url: "php/suscripciones/cargar_suscrito.php",
      type: "GET",
      dataType: "JSON",
      data: suscricion,
      success: function (respuesta) {
        if (respuesta.correcto) {
          callback(respuesta.suscrito);
        } else {
          alert(respuesta.mensaje);
        }
      },
      error: function (err) {
        console.log(err);
      },
    });
  };

  var quitarSus = function () {
    $("#boton-sus").prop("disabled", true);
    quitarSuscribir(function () {
      $("#boton-sus").prop("disabled", false);
      suscritoAzul();
    });
  };

  var suscritoAzul = function () {
    $("#boton-sus").text("SUSCRIBIRSE");
    $("#boton-sus").append(' <i class="fas fa-bell" id="icono"></i>');
    $("#boton-sus").removeClass("btn-success");
    $("#boton-sus").removeClass("btn-danger");
    $("#boton-sus").addClass("boton-celeste");
  };

  var suscritoVerde = function () {
    $("#boton-sus").removeClass("boton-celeste");
    $("#boton-sus").removeClass("btn-success");
    $("#boton-sus").addClass("btn-danger");
    $("#icono").removeClass("fa-bell");
    $("#icono").addClass("fa-times");
  };

  var esSus = function () {
    if ($("#boton-sus").hasClass("boton-celeste")) {
      ponerSus();
    } else {
      quitarSus();
    }
  };

  var suscritoRojo = function () {
    $("#boton-sus").removeClass("boton-celeste");
    $("#boton-sus").removeClass("btn-danger");
    $("#boton-sus").addClass("btn-success");
    $("#icono").removeClass("fa-times");
    $("#icono").addClass("fa-check");
  };

  var agregarEventos = function () {
    $("#boton-sus").click(esSus);
    $("#boton-sus").hover(function () {
      if (!$("#boton-sus").hasClass("boton-celeste")) {
        suscritoVerde();
      }
    }, function () {
      if (!$("#boton-sus").hasClass("boton-celeste")) {
        suscritoRojo();
      }
    });
  };

  var inicializar = function () {
    cargarBoton(function () {
      agregarEventos();
      $("#boton-sus").prop("disabled", true);
      var queryString = window.location.search;
      var urlParams = new URLSearchParams(queryString);
      var idPublicacion = urlParams.get("id");
      cargarSuscrito(function (suscrito) {
        $("#boton-sus").prop("disabled", false);
        if (suscrito) {
          suscritoVerde();
        } else {
          suscritoAzul();
        }
      });
    });
  };

  inicializar();
});
