$(function () {
  var agregarEventos = function () {
    $("#categoriaSelect").change(function () {
      var id = $("#categoriaSelect").val();
      obtenerEtiquetas(id, function (etiquetas) {
        $("#etiquetaSelect").empty();
        $.each(etiquetas, function () {
          $("#etiquetaSelect").append($("<option></option>").attr("value", this.id).text(this.nombre));
        });
      });
    });

    $("#agregar-etiqueta").click(function () {
      var id = $("#etiquetaSelect").val();
      var texto = $("#etiquetaSelect option:selected").text();
      agregarEtiquetaInterfaz(id, texto);
    });

    $("#confirmar").click(function () {
      if (validarFormulario()) {
        subirImagenes(function (rutaImagen) {
          var publicacion = {};
          publicacion.nombre = $("#nombre").val();
          publicacion.descripcion = $("#descripcion").val();
          publicacion.etiquetas = obtenerEtiquetasSeleccionadas();

          if (rutaImagen) {
            publicacion.rutaImagen = rutaImagen;
          }

          if ($("#publicacion-id").val() == "") {
            guardarPublicacion(publicacion, function () {
              $(".alert").show();
              window.scrollTo(0, 0);
              limpiarFormulario();
            });
          } else {
            publicacion.idPublicacion = $("#publicacion-id").val();
            actualizarPublicacion(publicacion, function () {
              $(".alert").show();
              window.scrollTo(0, 0);
              limpiarFormulario();
            });
          }
        });
      }
    });
  };

  var agregarEtiquetaInterfaz = function (id, texto) {
    // Si el elemento existe no se agrega a la lista
    if ($("#etiqueta-id-" + id).length) {
      return;
    }

    var contenedor = $("<div></div>")
      .attr("type", "button")
      .attr("class", "btn btn-secondary etiqueta-individual")
      .attr("id", "etiqueta-id-" + id);
    var strong = $("<strong></strong>").text(texto);

    contenedor.append(strong);
    contenedor.append("&nbsp;");

    var span = $("<span></span>")
      .attr("type", "button")
      .attr("class", "close")
      .attr("aria-label", "Cerrar")
      .click(function () {
        $("#etiqueta-id-" + id).remove();
      });

    span.append("<span></span>").attr("aria-hidden", "true").html("&times;");

    contenedor.append(span);
    $("#etiqueta-contenedor").append(contenedor);
  };

  var cargarCategorias = function () {
    obtenerCategorias(function (categorias) {
      $.each(categorias, function () {
        $("#categoriaSelect").append($("<option></option>").attr("value", this.id).text(this.nombre));
      });
      obtenerEtiquetas(categorias[0].id, function (etiquetas) {
        $("#etiquetaSelect").empty();
        $.each(etiquetas, function () {
          $("#etiquetaSelect").append($("<option></option>").attr("value", this.id).text(this.nombre));
        });
      });
    });
  };

  var limpiarFormulario = function () {
    $("#nombre").val("");
    $("#etiqueta-contenedor").empty();
    $("#descripcion").val("");
    $(".fileinput").fileinput("clear");
  };

  var obtenerEtiquetasSeleccionadas = function () {
    var etiquetasDIV = $("#etiqueta-contenedor .etiqueta-individual");
    var etiquetas = [];
    $.each(etiquetasDIV, function (ix, el) {
      var prefijoIndex = "etiqueta-id-".length;
      var etiqueta = el.id.substring(prefijoIndex);
      etiquetas.push(etiqueta);
    });
    return etiquetas;
  };

  var validarFormulario = function () {
    var nombre = $("#nombre").val();
    var etiquetas = obtenerEtiquetasSeleccionadas();
    var descripcion = $("#descripcion").val();

    if (nombre == "") {
      imprimirError("El nombre es requerido");
      return false;
    }
    if (etiquetas.length == 0) {
      imprimirError("Debe seleccionar al menos una etiqueta");
      return false;
    }
    if (descripcion == "") {
      imprimirError("La descripcion es requerida");
      return false;
    }

    // Solo si es nueva publicacion se valida que haya una imagen en el campo de tipo archivo
    if ($("#publicacion-id").val() == "" && $("#publicacion_imagen").get(0).files.length === 0) {
      imprimirError("La imagen es requerida");
      return false;
    }

    return true;
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

  var subirImagenes = function (callback) {
    var file_data = $("#publicacion_imagen").prop("files")[0];

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

  var guardarPublicacion = function (publicacion, callback) {
    $.ajax({
      data: publicacion,
      url: "php/publicacion/guardar_publicacion.php",
      type: "post",
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

  var actualizarPublicacion = function (publicacion, callback) {
    $.ajax({
      data: publicacion,
      url: "php/publicacion/actualizar_publicacion.php",
      type: "post",
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

  var imprimirError = function (texto) {
    $("#resultado").text(texto);
  };

  var cargarEditar = function () {
    var queryString = window.location.search;
    var urlParams = new URLSearchParams(queryString);
    var idPublicacion = urlParams.get("id");

    if (idPublicacion) {
      $("#publicacion-id").val(idPublicacion);
      $("#titulo-h3").text("Modificar publicación");
      $.ajax({
        url: "php/publicacion/obtener_publicacion.php?id=" + idPublicacion,
        type: "GET",
        dataType: "JSON",
        success: function (respuesta) {
          if (respuesta && respuesta.length > 0) {
            var publicacion = respuesta[0];
            $("#nombre").val(publicacion.titulo);
            $("#descripcion").text(publicacion.descripcion);
            $.each(publicacion.etiquetas, function (ix, etiqueta) {
              agregarEtiquetaInterfaz(etiqueta.id, etiqueta.nombre);
            });
            $("#publicacion-imagen-preview").append($("<img></img>").attr("src", publicacion.ruta_imagen));
          }
        },
        error: function (respuesta) {
          console.error(respuesta);
        },
      });
    } else {
      $("#publicacion-id").val("");
      $("#titulo-h3").text("Alta publicación");
    }
  };

  var inicializar = function () {
    agregarEventos();
    cargarCategorias();
    cargarEditar();
  };

  inicializar();
});
