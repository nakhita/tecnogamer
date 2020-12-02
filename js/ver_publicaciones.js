$(function () {
  var agregarEventos = function () {
    $("#buscar-publicacion").click(click);
    $("#categoria-select").change(function () {
      var id = $("#categoria-select").val();
      obtenerEtiquetas(id, function (etiquetas) {
        $("#etiqueta-select").empty();
        $("#etiqueta-select").append($("<option></option>").attr("value", "").text("- Seleccione -"));
        $.each(etiquetas, function () {
          $("#etiqueta-select").append($("<option></option>").attr("value", this.id).text(this.nombre));
        });
      });
    });
  };

  var click = function () {
    var formulario = {};
    formulario.titulo = $("#titulo").val();
    formulario.autor = $("#autor").val();
    formulario.categoria = $("#categoria-select").val();
    formulario.etiqueta = $("#etiqueta-select").val();

    if (validarFormulario(formulario)) {
      $.ajax({
        url: "php/publicacion/buscar_publicaciones.php",
        type: "POST",
        dataType: "JSON",
        data: formulario,
        success: function (resultado) {
          cargarPublicaciones(resultado);
        },
      });
    }
  };

  var cargarPublicaciones = function (publicaciones) {
    $("#publicaciones-contenedor").empty();
    $.each(publicaciones, function (ix, publicacion) {
      var cardContenedor = $("<div></div>").attr("class", "col-md-4");
      var card = $("<div></div>").attr("class", "card card-individual");

      var img = $("<img>").attr("class", "card-img-top").attr("src", publicacion.ruta_imagen);

      card.append(img);

      var cardBody = $("<div></div>").attr("class", "card-body");

      cardBody.append($("<h4></h4>").attr("class", "card-title").text(publicacion.titulo));

      var etiquetas = publicacion.etiquetas.join(", ");
      cardBody.append(
        $("<p></p>")
        .attr("class", "card-text")
        .text("Etiquetas: " + etiquetas)
      );

      var autor = publicacion.usuario;

      cardBody.append(
        $("<p></p>")
        .attr("class", "card-text")
        .text("Autor: " + autor)
      );

      cardBody.append(
        $('<a></a>')
        .attr("href", "ver_publicacion.html?id=" + publicacion.id)
        .attr("class", "centrar btn boton-celeste boton-card")
        .text("Ver publicacion")
      );

      card.append(cardBody);

      cardContenedor.append(card);
      $("#publicaciones-contenedor").append(cardContenedor);
    });
  };

  var cargarCategorias = function () {
    $("#categoria-select").empty();
    $("#categoria-select").append($("<option></option>").attr("value", "").text("- Seleccione -"));
    obtenerCategorias(function (categorias) {
      $.each(categorias, function () {
        $("#categoria-select").append($("<option></option>").attr("value", this.id).text(this.nombre));
      });
      obtenerEtiquetas(categorias[0].id, function (etiquetas) {
        $("#etiqueta-select").empty();
        $("#etiqueta-select").append($("<option></option>").attr("value", "").text("- Seleccione -"));
        $.each(etiquetas, function () {
          $("#etiqueta-select").append($("<option></option>").attr("value", this.id).text(this.nombre));
        });
      });
    });
  };

  var cargarParametros = function () {
    var queryString = window.location.search;
    var urlParams = new URLSearchParams(queryString);
    var autor = urlParams.get("autor");

    if (autor) {
      $("#autor").val(autor);
    }
  };

  var obtenerCategorias = function (callback) {
    $.ajax({
      url: "php/publicacion/obtener_categorias.php",
      type: "get",
      success: function (response) {
        if (callback) {
          callback(response);
        }
      },
      error: function (response) {
        console.error(response);
      },
    });
  };

  var obtenerEtiquetas = function (id, callback) {
    $.ajax({
      url: "php/publicacion/obtener_etiquetas_por_categoria.php?id=" + id,
      type: "get",
      success: function (response) {
        if (callback) {
          callback(response);
        }
      },
      error: function (response) {
        console.error(response);
      },
    });
  };

  var validarFormulario = function (formulario) {
    return true;
  };

  var inicializar = function () {
    cargarParametros();
    agregarEventos();
    cargarCategorias();
    click();
  };

  inicializar();
});
