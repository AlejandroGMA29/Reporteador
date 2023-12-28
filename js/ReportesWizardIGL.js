//--------------------------VARIABLES GLOBALES---------------------------------//
let elementosACambiar = [];
let elementosAquitar = [];
let agregar = 0;
let idnueva;
//sigue leer los datos y subirlos
//--------------------------COMIENZA DOCUMENT READY---------------------------------//
$(document).ready(function () {

    $("#seleccionado").checkboxradio(); //crea la opcion para el insert input del select

    $("#tabs").tabs(); //crea las tabs del wizard

    //añade la opcion de ser manejables y acomodables los inputs
    $("#sortable").sortable({
        start: function (event, ui) {
            // Almacenar la posición original antes de que se mueva el elemento
            $(ui.item).data('start-index', ui.item.index());
        },
        update: function (event, ui) {
            // Llamar a la función para actualizar los números de posición después de la clasificación
            actualizarNumerosPosicion();

            // Obtener la posición original
            var startIndex = $(ui.item).data('start-index');
            // Obtener la posición después de la clasificación
            var newIndex = ui.item.index();

            // Enviar información al servidor si la posición ha cambiado
            if (startIndex !== newIndex) {
                var idInput = $(ui.item).data('idInput');
                // Aquí puedes realizar una llamada AJAX al servidor para actualizar la posición
                // Puedes usar idInput para identificar qué elemento estás actualizando
                console.log("Elemento con idInput " + idInput + " movido de posición " + (startIndex + 1) + " a " + (newIndex + 1));
            }
        }
    });

    $("#sortable").disableSelection();

    $("#sortableInputs").sortable({
        update: function (event, ui) {
            // Llamar a la función para actualizar los números de posición después de la clasificación
            actualizarNumerosPosicion();
        }
    });
    $("#sortableInputs").disableSelection();

    //en caso de que solo se cree un reporte, se limpiara la pestaña y ocultara algunas cosas 
    $('#btnAgregarReporte').click(function () {

        $('#lblRepo').hide()
        $('#idReporte').hide()
        $("#tabs").tabs("option", "active", 0);
        $('#DescripcionReporte').val("");
        $('#SPReporte').val("");
        $('#activo').val("");

        agregar = 1;

        $("#sortable").empty();
        $("#sortableInputs").empty();
        if ($('#contenedorTabla').is(':visible')) {
            $('#contenedorTabla').slideUp(); // Otra animación para mostrar el div
        }
        if (!$('#contenedorWizard').is(':visible')) {
            $('#contenedorWizard').slideDown(); // Otra animación para mostrar el div
        }
        if ($('#tab2').is(':visible')) {
            $('#tab2').toggle(); // Otra animación para mostrar el div
        }

        if ($('#btnActualizarReporteBD').is(':visible')) {
            $('#btnActualizarReporteBD').hide() // Otra animación para mostrar el div
        }
        if (!$('#btnAgregarReporteBD').is(':visible')) {
            $('#btnAgregarReporteBD').toggle() // Otra animación para mostrar el div
        }

    });
    //funcion que recoge los datos del reporte y lo agrega a la bd
    $('#btnAgregarReporteBD').click(function () {

        var nombreRepo = $("#DescripcionReporte").val();
        var SPRepor = $("#SPReporte").val();
        var activo = $("#activo").val();
        var pesado = $("#repoPesado").val();
        //crea el reporte en bd
        try {
            var data = $.ajax({
                url: "../php/cWizardIGL.php",
                async: false,
                type: "POST",
                data: {
                    agregarReporte: '1',
                    nombreRepo: nombreRepo,
                    SPRepor: SPRepor,
                    activo: activo,
                    pesado: pesado,
                },
                dataType: "json",
            })

            console.log("id data: ", data.responseJSON[1][0])
            var idReporte = data.responseJSON[1][0]
            alert('Reporte agregado correctamente con la id: ' + idReporte.id_reporte)
            console.log(idReporte.id_reporte)

            //muestra el tab 2 para permitir modificarlo ademas de que carga los inpus disponibles con el reporte creado
            $("#tab2").toggle();
            console.log(agregar)
            idnueva = idReporte.id_reporte;
            reCargarTablaInput(idReporte.id_reporte);

            //vuelve a cargar los reportes incluyendo el actual
            var reportes = $.ajax({
                url: "../php/cReportesIGL.php",
                async: false,
                type: "POST",
                data: {
                    obtenerDatosReportes: '1',
                },
                dataType: "json",
            })
            //limpia el select de reportes
            $("#number").empty();
            //carga los reportes nuevos
            $.each(reportes.responseJSON, function (i, item) {
                console.log(item);
                $('#number').append($('<option>', {
                    value: item.id_reporte,
                    text: item.descripcion
                }));
            });


            //nos deja en el reporte recien creado
            $('#number').val(idReporte.id_reporte);

            // Refresca el menú desplegable
            $("#number").selectmenu("refresh");
            //limpia y vuelve a crear la navbar
            $("#titulo").empty();
            $("#titulo").append('<button class="navbar-toggler me-2" type="button" id="btnMenu" style="display: inline;"></button>')
            $("#btnMenu").append('<span class="navbar-toggler-icon"></span>')
            $("#btnMenu").click(function () {
                var menu = $("#menuReporteador");
                menu.toggle();
                if (menu.is(":visible")) {
                    // Si el menú está visible, ajusta los tamaños a su estado normal
                    $("#contenedorTodo").removeClass("container-fluid").addClass("container-fluid");
                    $("#headerNavbar").removeClass("col-12").addClass("col-9");
                } else {
                    // Si el menú está oculto, ajusta los tamaños para ocupar toda la pantalla
                    $("#headerNavbar").removeClass("col-9").addClass("col-12");
                }

            });
            $("#titulo").append('<span class="navbar-brand mb-0 h1" style="display:inline;" id="headerNavbar">' + nombreRepo + '</span>');


            console.log($("#number"), "prueba")

        } catch (err) {
            alert("ocurrio un error al agregar el reporte")
        }
    })
    //carga el wizard desde la opcion de modificar reporte
    $('#btnModificarReporte').click(function () {

        //oculta informacion y muestra solo la necesaria
        $('#btnActualizarReporteBD').hide();
        $('#btnAgregarReporteBD').hide()
        $('#idReporte').hide()
        $('#lblRepo').hide()

        $('#btnActualizarReporteBD').toggle()
        $('#idReporte').toggle()
        $('#lblRepo').toggle()


        //activa las 2 tabs
        $("#tabs").tabs("option", "active", 0);
        agregar = 0;

        $('#tab2').toggle();
        if (!$('#tab2').is(':visible')) {
            $('#tab2').toggle(); // Otra animación para mostrar el div
        }


        //limpia los inputs 
        $("#sortable").empty();
        $("#sortableInputs").empty();
        if ($('#contenedorTabla').is(':visible')) {
            $('#contenedorTabla').slideUp(); // Otra animación para mostrar el div
        }
        if (!$('#contenedorWizard').is(':visible')) {
            $('#contenedorWizard').slideDown(); // Otra animación para mostrar el div

        }

        console.log(agregar);
        var idReporte = $("#number").val();
        //vuelve a cargar los inputs especificos para el reporte seleccionado
        reCargarTablaInput(idReporte)
    })
    //funcion que actualiza los datos del reporte seleccionado en la bd
    $('#btnActualizarReporteBD').click(function () {
        //
        var idRepo = $("#idReporte").val();
        var nombreRepo = $("#DescripcionReporte").val();
        var SPRepor = $("#SPReporte").val();
        var activo = $("#activo").val();
        var pesado = $("#repoPesado").val();
        var valorSeleccionado = $("#number").val();
        try {
            var data = $.ajax({
                url: "../php/cWizardIGL.php",
                async: false,
                type: "POST",
                data: {
                    modificarReporte: '1',
                    idRepo: idRepo,
                    nombreRepo: nombreRepo,
                    SPRepor: SPRepor,
                    activo: activo,
                    pesado: pesado
                },
                dataType: "json",
            })
            $("#enviarReporteGrupo").hide();
            $("#enviarReportePesado").hide();
            if (pesado != 1) {
                $("#enviarReporteGrupo").toggle();
            } else {
                $("#enviarReportePesado").toggle();
            }


            alert('Reporte actualizado correctamente con la id: ' + idRepo)

            $("#sortable").empty();
            $("#sortableInputs").empty();
            reCargarTablaInput(idRepo);

            var reportes = $.ajax({
                url: "../php/cReportesIGL.php",
                async: false,
                type: "POST",
                data: {
                    obtenerDatosReportes: '1',
                },
                dataType: "json",
            })
            $("#number").empty();

            $.each(reportes.responseJSON, function (i, item) {
                console.log(item);
                $('#number').append($('<option>', {
                    value: item.id_reporte,
                    text: item.descripcion += item.activo == "1" ? "" : " *"
                }));
            });

            $("#number").val(valorSeleccionado);

            // Refresca el menú desplegable
            $("#number").selectmenu("refresh");


        } catch (err) {
            alert("ocurrio un error al agregar el reporte")
        }
    })

    //en caso de estar o no seleccionados, los mueve de un lado para otro
    $("#moverDatos").click(function () {
        // Array para almacenar los elementos a cambiar y quitar
        let elementosACambiarlocal = [];
        let elementosAquitarlocal = [];

        // Iterar sobre los elementos en sortableInputs
        $('#sortableInputs li').each(function (indice, elemento) {
            // Buscar el checkbox dentro del elemento actual
            var checkbox = $(elemento).find('input[type="checkbox"]');

            // Verificar si el checkbox está marcado
            if (checkbox.prop("checked")) {
                // Almacenar el elemento a cambiar en el array
                elementosACambiarlocal.push(elemento);
            }
        });

        // Iterar sobre los elementos en sortable
        $('#sortable li').each(function (indice, elemento) {
            // Buscar el checkbox dentro del elemento actual
            var checkbox = $(elemento).find('input[type="checkbox"]');

            // Verificar si el checkbox está marcado
            if (!checkbox.prop("checked")) {
                // Almacenar el elemento a quitar en el array
                elementosAquitarlocal.push(elemento);
            }
        });

        // Append de los elementos a quitar a sortableInputs
        $('#sortableInputs').append(elementosAquitarlocal);

        // Desmarcar los checkboxes en sortableInputs
        $('#sortable input[type="checkbox"]').prop("checked", true);

        // Append de los elementos a cambiar a sortable
        $('#sortable').append(elementosACambiarlocal);

        // Desmarcar los checkboxes en sortableInputs
        $('#sortableInputs input[type="checkbox"]').prop("checked", false);

        // Eliminar los elementos de sortableInputs
        elementosAquitarlocal.forEach(function (elemento) {
            console.log(elemento);
        });

        //actualiza el numero de los inputs dependiendo su posicion actual
        actualizarNumerosPosicion();

        console.log("Elementos y sus posiciones actualizadas:");
        $('#sortable li').each(function (indice, elemento) {
            var idInput = $(elemento).attr('data-idInput')
            console.log(`Elemento con idInput ${idInput}, posición ${indice + 1}`);
        });
    });
    //actualiza en la bd los inputs actuales
    $("#actualizarInputs").click(function () {

        idReporte = $("#number").val();
        console.log("valor agregar: " + agregar)

        // Arreglos para almacenar datos
        let datosSortable = [];
        let datosSortableInputs = [];

        if (agregar == 1) {
            console.log("global: ", idnueva)
            console.log("anterior", idReporte);
            idReporte = idnueva;
            console.log("nueva", idReporte)
        }

        // Recorrido del primer conjunto de datos
        $('#sortable li').each(function (indice, elemento) {
            console.log('El elemento con el índice ' + indice + ' contiene ' + $(elemento).attr('data-idInput'));
            idInput = $(elemento).attr('data-idInput')
            posicion = indice + 1

            // Almacenar datos en el arreglo
            datosSortable.push({
                idReporte: idReporte,
                idInput: idInput,
                orden: posicion
            });
        });

        // Recorrido del segundo conjunto de datos
        $('#sortableInputs li').each(function (indice, elemento) {
            console.log('El elemento con el índice ' + indice + ' contiene ' + $(elemento).attr('data-idInput'));
            idInput = $(elemento).attr('data-idInput')

            // Almacenar datos en el arreglo
            datosSortableInputs.push({
                idReporte: idReporte,
                idInput: idInput
            });
        });

        // Realizar la petición AJAX para el primer conjunto de datos
        $.ajax({
            url: "../php/cWizardIGL.php",
            type: "POST",
            data: {
                actualizarInformacion: '1',
                datosSortable: JSON.stringify(datosSortable)
            },
            dataType: "json",
            success: function (response) {
                // Manejar la respuesta si es necesario
            }
        });

        // Realizar la petición AJAX para el segundo conjunto de datos
        $.ajax({
            url: "../php/cWizardIGL.php",
            type: "POST",
            data: {
                actualizarInformacionEliminar: '1',
                datosSortableInputs: JSON.stringify(datosSortableInputs)
            },
            dataType: "json",
            success: function (response) {
                // Manejar la respuesta si es necesario
            }
        });

        console.log("nueva fuera if", idReporte)


        $("#sortable").empty();
        $("#sortableInputs").empty();

        alert("Se actualizaron correctamente los inputs");
        cargartInputsMenu(idReporte);
        reCargarTablaInput(idReporte);
    });


    //---------Añadir input wizard----------//

    $("#inpTipo").selectmenu({
        change: function (event, data) {
            var selected = data.item.value;
            mostrarOpciones(selected)


        }
    })
        .selectmenu("menuWidget")
        .addClass("overflow");

    $("#inpTablaAutComplete").autocomplete({
        source: function (request, response) {
            $.ajax({
                url: "../php/cWizardIGL.php",
                dataType: "json",
                type: "POST",
                data: {
                    termn: request.term,
                    autoCompleteTablas: 1,
                },
                success: function (data) {
                    console.log(data)
                    var suggestions = data.map(function (item) {
                        return {
                            label: item.table_name,
                            value: item.table_name
                        };

                    });
                    response(suggestions);
                },
            });
        },
        minLength: 1,
        select: function (event, ui) {
            var selectTable = ui.item.value;
            console.log("Selected Table:", selectTable);

            $("#inpIdAutoComplete").autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: "../php/cWizardIGL.php",
                        dataType: "json",
                        type: "POST",
                        data: {
                            termn: request.term,
                            tabla: selectTable,
                            optionHeaders: 1,
                        },
                        success: function (data) {
                            console.log(data)
                            var suggestions = data.map(function (item) {
                                return {
                                    label: item.COLUMN_NAME,
                                    value: item.COLUMN_NAME
                                };

                            });
                            response(suggestions);
                        },
                    });
                },
                minLength: 1,
                select: function (event, ui) {
                    var selectTable = ui.item.value;
                    console.log("Selected Table:", selectTable);

                }
            })

            $("#inpValorAutoComplete").autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: "../php/cWizardIGL.php",
                        dataType: "json",
                        type: "POST",
                        data: {
                            termn: request.term,
                            tabla: selectTable,
                            optionHeaders: 1,
                        },
                        success: function (data) {
                            console.log(data)
                            var suggestions = data.map(function (item) {
                                return {
                                    label: item.COLUMN_NAME,
                                    value: item.COLUMN_NAME
                                };

                            });
                            response(suggestions);
                        },
                    });
                },
                minLength: 1,
                select: function (event, ui) {
                    var selectTable = ui.item.value;
                    console.log("Selected Table:", selectTable);

                }
            })
        }
    })


    function horarioAutoComplete() {
        // Obtener el input con jQuery
        var $inputHorario = $('#inpHorario');

        // Crear un array con las horas del día
        var horasDelDia = [];
        for (var i = 0; i < 24; i++) {
            var hora = i < 10 ? '0' + i : '' + i;
            horasDelDia.push(hora + ':00:00');
        }

        // Configurar el autocompletado con jQuery UI
        $inputHorario.autocomplete({
            source: horasDelDia
        });
    }

    // Llamar a la función para iniciar el autocompletado

    horarioAutoComplete();


    $("#btnAgregarNuevoInput").click(function () {

        $("#btnAñadirInputMenu").hide();
        $("#btnAñadirInput").hide();
        $("#btnAñadirInputMenu").toggle();
    })

    $("#btnAñadirInputMenu").click(function () {

        var idReporte = $("#number").val();
        var nombre = $("#inpNombreInput").val();
        var tipo = $("#inpTipo").val();
        var valor = $("#inpValorAutoComplete").val();
        var autocomplete = $("#inpTablaAutComplete").val();
        var valorId = $("#inpIdAutoComplete").val();
        var valorSelect = $("#inpValorSelect").val();
        var textoSelect = $("#inpTextoSelect").val();
        var valorSeleccionado = $("#inpSeleccionado").prop('checked')
        var informacionAdicional = $("#inpInformacionAdicional").val();
        var horaSeleccionada = $("#inpHorario").val();

        // Define la expresión regular para el formato de hora
        var formatoHora = /^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/;

        // Verifica si la hora cumple con el formato
        if (formatoHora.test(horaSeleccionada) || horaSeleccionada == " ") {
            console.log("La hora cumple con el formato correcto.");
        } else {
            alert("La hora no es valida");
            return;
           
        }


        var valores = [];
        var textos = [];
        if (tipo == "select") {
            $("#grupoSelect .input-group").each(function () {
                var valor = $(this).find('input[name="inpValorSelect"]').val();
                var texto = $(this).find('input[name="inpTextoSelect"]').val();

                valores.push(valor);
                textos.push(texto);
            });

            // Imprime los valores en la consola (puedes modificar esto según tus necesidades)

        }


        console.log("Valores: " + valores.join(", "));
        console.log("Textos: " + textos.join(", "));


        console.log("info", horaSeleccionada)
        try {
            $.ajax({
                url: "../php/cWizardIGL.php",
                async: false,
                type: "POST",
                data: {
                    insertInputs: '1',
                    nombre: nombre,
                    tipo: tipo,
                    valor: valor,
                    autocomplete: autocomplete,
                    valorId: valorId,
                    valorSelect: valores.join(", "),
                    textoSelect: textos.join(", "),
                    valorSeleccionado: valorSeleccionado,
                    hora: horaSeleccionada,
                    informacionAdicional: informacionAdicional,
                },
                dataType: "json",
            })

            $("#sortable").empty();
            $("#sortableInputs").empty();
            limpiarModal();
            alert("datos agregados correctamente");
            cargarTablaInputModal();
        }
        catch (error) {
            console.log(error)
        }
    })

    $("#añadirInput").click(function () {
        limpiarModal();
        $("#grupoSelect").empty();

        // Construye el HTML de la nueva opción
        var nuevaOpcion = "<div class='input-group'>";
        nuevaOpcion += "<input type='text' class='form-control' name='inpValorSelect' value='' placeholder='valor' style='display: block;'>";
        nuevaOpcion += "<input type='text' class='form-control' name='inpTextoSelect' value='' placeholder='Texto' style='display: block;'>";
        nuevaOpcion += "<button class='btn btn-outline-danger' id='btnEliminarOpcion' type='button'>X</button>"; // Agregamos el botón "Eliminar"
        nuevaOpcion += "</div>";

        // Agrega la nueva opción al final de #grupoSelect
        $("#grupoSelect").append(nuevaOpcion);



        $("#btnAñadirInputMenu").hide();
        $("#inpIDs").hide();
        $("#btnActualizarInputBD").hide();
        $("#btnAñadirInput").hide();
        $("#btnAñadirInput").toggle();

    })


    //añade el input nuevo a la bd dependiendo los datos asignados.
    $("#btnAñadirInput").click(function () {
        var idReporte = $("#number").val();
        var nombre = $("#inpNombreInput").val();
        var tipo = $("#inpTipo").val();
        var valor = $("#inpValorAutoComplete").val();
        var autocomplete = $("#inpTablaAutComplete").val();
        var valorId = $("#inpIdAutoComplete").val();
        var valorSelect = $("#inpValorSelect").val();
        var textoSelect = $("#inpTextoSelect").val();
        var valorSeleccionado = $("#inpSeleccionado").prop('checked')
        var informacionAdicional = $("#inpInformacionAdicional").val();
        var horaSeleccionada = $("#inpHorario").val();

         // Define la expresión regular para el formato de hora
         var formatoHora = /^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/;

         // Verifica si la hora cumple con el formato
         if (formatoHora.test(horaSeleccionada) || horaSeleccionada == " ") {
             console.log("La hora cumple con el formato correcto.");
         } else {
             alert("La hora no es valida");
             return;
            
         }

        var valores = [];
        var textos = [];
        if (tipo == "select") {
            $("#grupoSelect .input-group").each(function () {
                var valor = $(this).find('input[name="inpValorSelect"]').val();
                var texto = $(this).find('input[name="inpTextoSelect"]').val();

                valores.push(valor);
                textos.push(texto);
            });

        }

        console.log("Valores: " + valores.join(", "));
        console.log("Textos: " + textos.join(", "));
        console.log("info", horaSeleccionada)
        try {
            $.ajax({
                url: "../php/cWizardIGL.php",
                async: false,
                type: "POST",
                data: {
                    insertInputs: '1',
                    nombre: nombre,
                    tipo: tipo,
                    valor: valor,
                    autocomplete: autocomplete,
                    valorId: valorId,
                    valorSelect: valores.join(", "),
                    textoSelect: textos.join(", "),
                    valorSeleccionado: valorSeleccionado,
                    hora: horaSeleccionada,
                    informacionAdicional: informacionAdicional,
                },
                dataType: "json",
            })

            $("#sortable").empty();
            $("#sortableInputs").empty();
            reCargarTablaInput(idReporte);
            limpiarModal();
            alert("datos agregados correctamente");

        }
        catch (error) {
            console.log(error)
        }
    })

    //añade nuevas opciones para el select
    $("#btnAñadirOpcion").click(function () {
        // Clona el div con sus elementos
        var nuevaOpcion = "<div class='input-group'>";
        nuevaOpcion += "<input type='text' class='form-control' name='inpValorSelect' value='' placeholder='valor' style='display: block;'>";
        nuevaOpcion += "<input type='text' class='form-control' name='inpTextoSelect' value='' placeholder='Texto' style='display: block;'>";
        nuevaOpcion += "<button class='btn btn-outline-danger' id='btnEliminarOpcion' type='button'>X</button>"; // Agregamos el botón "Eliminar"
        nuevaOpcion += "</div>";

        $("#grupoSelect").append(nuevaOpcion);

        // Desactiva el botón de eliminar si solo queda un elemento
        actualizarBotonEliminar();
    });

    // Manejador de clic en el botón 'Eliminar' para las opciones del select
    $(document).on("click", "#btnEliminarOpcion", function () {
        // Verifica que haya más de un elemento antes de eliminar
        if ($("#grupoSelect .input-group").length > 1) {
            // Elimina el div padre (el contenedor completo)
            $(this).closest(".input-group").remove();
        }

        // Desactiva el botón de eliminar si solo queda un elemento
        actualizarBotonEliminar();
    });

    //busqueda para los diferentes inputs
    $("#busquedaInputsFuera").on("input", function () {
        var term = $(this).val().toUpperCase();

        // Filtra los elementos basados en el término de búsqueda
        $("#sortableInputs > li").each(function () {
            var label = $(this).find("label").text().toUpperCase();

            // Muestra u oculta los elementos según el término de búsqueda
            if (label.indexOf(term) > -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    //muesta los inputs en el modal
    $("#btnCatalogoInputs").click(function () {
        cargarTablaInputModal();
    })

    //da la opcion de editar los inputs
    $('#contenedorInputsTable').on('click', '.editar', function () {
        // Obtiene el ID del input seleccionado
        limpiarModal();
        var idInput = $(this).data('id');
        console.log(idInput)
        // Realiza una nueva solicitud para obtener la información del input seleccionado
        var inputInfo = $.ajax({
            url: "../php/cWizardIGL.php",
            async: false,
            type: "POST",
            data: {
                obtenerInputPorID: "1",
                idInput: idInput,
            },
            dataType: "json",
        }).responseJSON[0];

        console.log(inputInfo)

        // Llena el segundo modal con la información obtenida
        $('#inpId').val(inputInfo.idInput);
        $('#inpNombreInput').val(inputInfo.nombreInput);
        $('#inpTipo').val(inputInfo.tipo);
        $('#inpTipo').selectmenu("refresh");

        var autoComplete = $("#opcionesAutoComplete");
        var select = $("#opcionesSelectComplete");
        var checkbox = $("#opcionesCheckBox");
        var date = $("#opcionesDate");

        autoComplete.hide();
        select.hide();
        checkbox.hide();
        date.hide();

        if (inputInfo.tipo == "autoComplete") {
            autoComplete.toggle();
            $("#inpTablaAutComplete").val(inputInfo.valorAutocomplete)
            $("#inpIdAutoComplete").val(inputInfo.valorId)
            $("#inpValorAutoComplete").val(inputInfo.valor)
        } else if (inputInfo.tipo == "date") {
            date.toggle();
            console.log(inputInfo.hora)
            let horaFormateada = inputInfo.hora.split('.')[0]; // Obtén la parte antes del punto

             $("#inpHorario").val(horaFormateada)
         

           
        } else if (inputInfo.tipo == "checkbox") {
            checkbox.toggle();

            $("#inpSeleccionado").attr("checked", inputInfo.checked == 1 ? true : false)
            
        } else if (inputInfo.tipo == "select") {
            select.toggle();

            var valoresSelect = inputInfo.valoresSelect
            var textoSelect = inputInfo.textoSelect

            // Divide los valores en arrays usando la coma como delimitador
            var valoresArray = valoresSelect.split(', ');
            var textosArray = textoSelect.split(', ');
            $("#grupoSelect").empty();
            // Itera sobre los arrays y agrega nuevas opciones
            for (var i = 0; i < valoresArray.length - 1; i++) {
                console.log("nuevo option ")
                // Construye el HTML de la nueva opción
                var nuevaOpcion = "<div class='input-group'>";
                nuevaOpcion += "<input type='text' class='form-control' name='inpValorSelect' value='" + valoresArray[i] + "' placeholder='valor' style='display: block;'>";
                nuevaOpcion += "<input type='text'  class='form-control' name='inpTextoSelect' value='" + textosArray[i] + "' placeholder='Texto' style='display: block;'>";
                nuevaOpcion += "<button class='btn btn-outline-danger' id='btnEliminarOpcion' type='button'>X</button>"; // Agregamos el botón "Eliminar"
                nuevaOpcion += "</div>";

                // Agrega la nueva opción al final de #grupoSelect
                $("#grupoSelect").append(nuevaOpcion);
            }
            // Desactiva el botón de eliminar si solo queda un elemento
            actualizarBotonEliminar();

        }

        $("#inpInformacionAdicional").val(inputInfo.informacionAdicional)


        //ocultar y mostrar boton para editar
        $("#btnAñadirInputMenu").hide();
        $("#inpIDs").hide();
        $("#btnActualizarInputBD").hide();
        $("#btnAñadirInput").hide();
        $("#inpIDs").toggle();
        $("#btnActualizarInputBD").toggle();

        // Muestra el segundo modal
        $('#modalInputs').modal("hide");
        $('#exampleModal').modal("show");
    });

    //actualiza la informacion de los inputs
    $("#btnActualizarInputBD").click(function () {
        var idInput = $('#inpId').val(); // Obtén el ID del input

        var nombre = $("#inpNombreInput").val();
        var tipo = $("#inpTipo").val();
        var valor = $("#inpValorAutoComplete").val();
        var autocomplete = $("#inpTablaAutComplete").val();
        var valorId = $("#inpIdAutoComplete").val();
        var valorSelect = $("#inpValorSelect").val();
        var textoSelect = $("#inpTextoSelect").val();
        var valorSeleccionado = $("#inpSeleccionado").prop('checked');
        var informacionAdicional = $("#inpInformacionAdicional").val();
        var horaSeleccionada = $("#inpHorario").val();
        // Define la expresión regular para el formato de hora
        var formatoHora = /^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/;

        // Verifica si la hora cumple con el formato
        if (formatoHora.test(horaSeleccionada) || horaSeleccionada == " ") {
            console.log("La hora cumple con el formato correcto.");
        } else {
            alert("La hora no es valida");
            return;
           
        }
        

        var valores = [];
        var textos = [];

        if (tipo == "select") {
            $("#grupoSelect .input-group").each(function () {
                // Verifica si el elemento actual no tiene la clase 'ocultar'
                if (!$(this).hasClass('ocultar')) {
                    var valor = $(this).find('input[name="inpValorSelect"]').val();
                    var texto = $(this).find('input[name="inpTextoSelect"]').val();
                    valores.push(valor);
                    textos.push(texto);
                }
            });
        }

        console.log("Valores: " + valores.join(", "));
        console.log("Textos: " + textos.join(", "));
        console.log("info", horaSeleccionada);

        try {
            $.ajax({
                url: "../php/cWizardIGL.php",
                async: false,
                type: "POST",
                data: {
                    updateInputs: '1', // Cambiado a updateInputs para la operación de actualización
                    idInput: idInput,
                    nombre: nombre,
                    tipo: tipo,
                    valor: valor,
                    autocomplete: autocomplete,
                    valorId: valorId,
                    valorSelect: valores.join(", "),
                    textoSelect: textos.join(", "),
                    valorSeleccionado: valorSeleccionado,
                    hora: horaSeleccionada,
                    informacionAdicional: informacionAdicional,
                },
                dataType: "json",
                success: function (data) {
                    $("#sortable").empty();
                    $("#sortableInputs").empty();
                    limpiarModal();
                    alert("Datos actualizados correctamente");
                    cargarTablaInputModal();
                    $('#modalInputs').modal("show");
                    $('#exampleModal').modal("hide");
                },
                error: function (error) {
                    console.log(error);
                    alert("Error al actualizar los datos");
                }
            });
        } catch (error) {
            console.log(error);
        }

    })

}); //---------TERMINA DOCUMENT READY----------//

//---------------------FUNCIONES---------------------//
//revisa que no se hayan eliminado todas las opciones de un select
function actualizarBotonEliminar() {
    var numElementos = $("#grupoSelect .input-group").length;
    console.log("elementos eliminar restantes", numElementos)
    $("#btnEliminarOpcion").prop("disabled", numElementos === 2);
}

//crea la tabla de inpust para el modal
function cargarTablaInputModal() {
    if ($.fn.DataTable.isDataTable("#inputsTable")) {
        $("#inputsTable").DataTable().destroy();
    }
    var inputs = $.ajax({
        url: "../php/cWizardIGL.php",
        async: false,
        type: "POST",
        data: {
            cargarTodosInputs: '1',
        },
        dataType: "json",
    })


    console.log(inputs.responseJSON)

    $('#inputsTable').DataTable({
        searching: true, // Habilita o deshabilita la búsqueda
        lengthChange: false, // Habilita o deshabilita el cambio de longitud
        pageLength: 5,

        data: inputs.responseJSON,
        columns: [
            { data: "idInput" },
            { data: "nombreInput" },
            { data: "tipo" },
            {
                data: null,
                render: function (data, type, row) {
                    return (
                        '<button class="btn btn-info editar" data-id="' +
                        data.idInput +
                        '">Editar</button>'
                    );
                },
            },
        ],
        columnDefs: [
            { "className": "dt-center", "targets": "_all" },
            { "width": "99%", "targets": "_all" } // Asegúrate de colocar "width" entre comillas.
        ],
        autoWidth: true,
    });
}
//limpia el modal de añadir input
function limpiarModal() {
    // Limpiar el campo de Nombre
    $('#inpNombreInput').val('');

    // Reiniciar el campo de Tipo y ocultar las opciones adicionales
    $('#inpTipo').val('text').change();
    $('#inpHorario').val(' ').change();
    $('#opcionesAutoComplete, #opcionesSelectComplete, #opcionesCheckBox, opcionesDate').hide();

    // Limpiar el campo de Informacion adicional
    $('#inpInformacionAdicional').val('');

    // Limpiar los campos de AutoComplete
    $('#inpTablaAutComplete, #inpIdAutoComplete, #inpValorAutoComplete').val('');

    // Limpiar los campos de SelectComplete
    $('#inpValorSelect, #inpTextoSelect').val('');

    // Desmarcar el checkbox
    $('#seleccionado').prop('checked', false);
}

//actualiza los numeros de los inputs al cambiarlos de posicion
function actualizarNumerosPosicion() {
    $("#sortable li").each(function (index) {
        $(this).attr("data-posicion", index + 1);
    })
}

function reCargarTablaInput(idReporte) {
    try {
        console.log("id reporte dentro de recarga:", idReporte)
        var reportes = $.ajax({
            url: "../php/cWizardIGL.php",
            async: false,
            type: "POST",
            data: {
                datosReporte: '1',
                idReporte: idReporte
            },
            dataType: "json",
        })
        console.log(reportes)


        $("#idReporte").val(idReporte)
        $("#DescripcionReporte").val(reportes.responseJSON[0].descripcion)
        $("#SPReporte").val(reportes.responseJSON[0].sp_reporte)
        $("#activo").val(reportes.responseJSON[0].activo)
        $("#repoPesado").val(reportes.responseJSON[0].ReportePesado)
        console.log("activo", reportes.responseJSON[0].activo)

        idReporte = reportes.responseJSON[0].id_reporte
        console.log("id antes envio: " + idReporte);


        inputsData = $.ajax({
            url: "../php/cWizardIGL.php",
            async: false,
            type: "POST",
            data: {
                cargarInputs: '1',
                idReporte: idReporte
            },
            dataType: "json",
        });
        console.log(idReporte, "id reporte despues de inputs data");
        inputsDataFuera = $.ajax({
            url: "../php/cWizardIGL.php",
            async: false,
            type: "POST",
            data: {
                cargarInputsFuera: '1',
                idReporte: idReporte
            },
            dataType: "json",
        });

        valoresFuera = inputsDataFuera.responseJSON[1];
        valores = inputsData.responseJSON[1]


        valores.forEach(function (valor, index) {
            let liElement;
            if (valor.tipo != "select" && valor.tipo != "autoComplete") {
                $("#sortable").append('<li class="ui-state-default list-group-item"  data-idInput="' + valor.idInput + ' " style="background-color: transparent;><span class="ui-icon ui-icon-arrowthick-2-n-s"></span><input type="checkbox" checked id="checkbox' + (index + 1) + '" name="inputCheckbox" value="' + valor.nombreInput.toUpperCase() + '">' + '<label for="checkbox' + (index + 1) + '">' + valor.nombreInput.toUpperCase() + '</label></li>');
            } else if (valor.tipo == "select") {
                let arreglo = valor.textoSelect.split(", ");
                // Agregar el LI con el checkbox y el label
                let selectLi = $('<li class="ui-state-default list-group-item" data-idInput="' + valor.idInput + '" style="background-color: transparent;><span class="ui-icon ui-icon-arrowthick-2-n-s"></span> <input type="checkbox" checked  id="checkbox' + (index + 1) + '" name="inputCheckbox" value="' + valor.nombreInput.toUpperCase() + '">' + '<label for="checkbox' + (index + 1) + '">' + valor.nombreInput.toUpperCase() + '</label></li>');

                // Agregar el div para las opciones
                let optionsDiv = $('<div class="resizable-options" style="height: 100%; overflow: hidden;"></div>');

                // Agregar las opciones al div
                for (let i = 0; i < arreglo.length - 1; i++) {
                    const option = arreglo[i];
                    optionsDiv.append('<p> - ' + option.toUpperCase() + '</p>');
                }

                // Agregar el div de opciones al LI
                selectLi.append(optionsDiv);

                // Hacer el div redimensionable en altura
                optionsDiv.resizable({
                    handles: "s", // Solo permite redimensionar en la dirección sur (abajo)
                    minHeight: 1, // Altura mínima
                    maxHeight: 100
                });

                // Agregar el LI al sortable
                $("#sortable").append(selectLi);
            } else if (valor.tipo == "autoComplete") {
                let autoCompleteLi = $('<li class="ui-state-default list-group-item"  data-idInput="' + valor.idInput + '" style="background-color: transparent;><span class="ui-icon ui-icon-arrowthick-2-n-s"></span><input type="checkbox" checked id="checkbox' + (index + 1) + '" name="inputCheckbox" value="' + valor.nombreInput.toUpperCase() + '">' + '<label for="checkbox' + (index + 1) + '">' + valor.nombreInput.toUpperCase() + '</label></li>');

                // Crear el div para la opción autoComplete
                let autoCompleteDiv = $('<div class="resizable-autoComplete" style="height: 100%; overflow: hidden;"></div>');

                // Agregar el contenido de autoComplete al div

                autoCompleteDiv.append('<p> - ' + valor.valorAutocomplete.toUpperCase() + '</p>');
                // Hacer el div redimensionable en altura
                autoCompleteDiv.resizable({
                    handles: "s", // Solo permite redimensionar en la dirección sur (abajo)
                    minHeight: 1, // Altura mínima
                });

                // Agregar el div de autoComplete al LI
                autoCompleteLi.append(autoCompleteDiv);

                // Agregar el LI al sortable
                $("#sortable").append(autoCompleteLi);
            }
        });



        valoresFuera.forEach(function (valor, index) {

            if (valor.tipo != "select" && valor.tipo != "autoComplete") {
                $("#sortableInputs").append('<li class="ui-state-default list-group-item" data-idInput="' + valor.idInput + '" style="background-color: transparent;><span class="ui-icon ui-icon-arrowthick-2-n-s"></span><input type="checkbox" id="checkboxF' + (index + 1) + '" name="inputCheckbox" value=" ' + valor.nombreInput.toUpperCase() + '">' + '<label for="checkboxF' + (index + 1) + '">' + valor.nombreInput.toUpperCase() + '</label></li>');
            } else if (valor.tipo == "select") {
                let arreglo = valor.textoSelect.split(", ");
                // Agregar el LI con el checkbox y el label
                let selectLi = $('<li class="ui-state-default list-group-item"  data-idInput="' + valor.idInput + '" style="background-color: transparent;><span class="ui-icon ui-icon-arrowthick-2-n-s"></span> <input type="checkbox" id="checkboxF' + (index + 1) + '" name="inputCheckbox" value=" ' + valor.nombreInput.toUpperCase() + '">' + '<label for="checkboxF' + (index + 1) + '">' + valor.nombreInput.toUpperCase() + '</label></li>');

                // Agregar el div para las opciones
                let optionsDiv = $('<div class="resizable-options" style="height: 100%; overflow: hidden;"></div>');

                // Agregar las opciones al div
                for (let i = 0; i < arreglo.length - 1; i++) {
                    const option = arreglo[i];
                    optionsDiv.append('<p> - ' + option.toUpperCase() + '</p>');
                }

                // Agregar un botón indicador para expandir las opciones
                // Agregar el div de opciones al LI
                selectLi.append(optionsDiv);

                // Hacer el div redimensionable en altura
                optionsDiv.resizable({
                    handles: "s", // Solo permite redimensionar en la dirección sur (abajo)
                    minHeight: 1, // Altura mínima
                });

                // Configurar el botón indicador para mostrar/ocultar las opciones

                // Agregar el LI al sortable
                $("#sortableInputs").append(selectLi);
            } else if (valor.tipo == "autoComplete") {
                let autoCompleteLi = $('<li class="ui-state-default list-group-item" data-idInput="' + valor.idInput + '" style="background-color: transparent;><span class="ui-icon ui-icon-arrowthick-2-n-s"></span><input type="checkbox" id="checkboxF' + (index + 1) + '" name="inputCheckbox" value=" ' + valor.nombreInput.toUpperCase() + '">' + '<label for="checkboxF' + (index + 1) + '">' + valor.nombreInput.toUpperCase() + '</label></li>');

                // Crear el div para la opción autoComplete
                let autoCompleteDiv = $('<div class="resizable-autoComplete"  style="height: 100%; overflow: hidden;" style="background-color: transparent;></div>');

                // Agregar el contenido de autoComplete al div
                autoCompleteDiv.append('<p> - ' + valor.valorAutocomplete.toUpperCase() + '</p>');

                // Hacer el div redimensionable en altura
                autoCompleteDiv.resizable({
                    handles: "s", // Solo permite redimensionar en la dirección sur (abajo)
                    minHeight: 1, // Altura mínima
                });

                // Agregar el div de autoComplete al LI
                autoCompleteLi.append(autoCompleteDiv);

                // Agregar el LI al sortable
                $("#sortableInputs").append(autoCompleteLi);
            }
        });


    } catch (err) {
        console.log(err)
    }


}


//en el modal de input al añadir, dependiendo el tipo mostrara informacion adicional
function mostrarOpciones(selected) {
    // Ocultar todas las opciones
    $('#opcionesAutoComplete, #opcionesSelectComplete, #opcionesCheckBox, #opcionesDate').slideUp();

    // Mostrar la opción seleccionada
    if (selected === "autoComplete") {
        $('#opcionesAutoComplete').slideDown();
    } else if (selected === "select") {
        $('#opcionesSelectComplete').slideDown();
    } else if (selected === "checkbox") {
        $('#opcionesCheckBox').slideDown();
    } else if (selected === "date") {
        $('#opcionesDate').slideDown();
    }
}


function cargartInputsMenu(selectRepo) {
    //cargartInputsMenu(data.item.value)
    $("#titulo").empty();
    $('#contenedorInputs').empty();

    //obtiene la id del reporte para hacer un select especifico de sus inputs

    inputsData = $.ajax({
        url: "../php/cReportesIGL.php",
        async: false,
        type: "POST",
        data: {
            cargarInputs: '1',
            idReporte: selectRepo
        },
        dataType: "json",
    });


    //agrega de nuevo el titulo y el boton del menu para ocultarlo
    $("#titulo").append('<button class="navbar-toggler me-2" type="button" id="btnMenu" style="display: inline;"></button>')
    $("#btnMenu").append('<span class="navbar-toggler-icon"></span>')
    $("#btnMenu").click(function () {
        var menu = $("#menuReporteador");
        menu.toggle();
        if (menu.is(":visible")) {
            // Si el menú está visible, ajusta los tamaños a su estado normal
            $("#contenedorTodo").removeClass("container-fluid").addClass("container-fluid");
            $("#headerNavbar").removeClass("col-12").addClass("col-9");
        } else {
            // Si el menú está oculto, ajusta los tamaños para ocupar toda la pantalla
            $("#headerNavbar").removeClass("col-9").addClass("col-12");
        }

    });
    var textoSeleccionado = $('#number option:selected').text();
    $("#titulo").append('<span class="navbar-brand mb-0 h1" style="display:inline;" id="headerNavbar">' + textoSeleccionado + '</span>');

    //obtiene la informacion en general del reporte seleccionado.
    var reportes = $.ajax({
        url: "../php/cWizardIGL.php",
        async: false,
        type: "POST",
        data: {
            datosReporte: '1',
            idReporte: selectRepo
        },
        dataType: "json",
    }).responseJSON[0]

    //en caso de ser un reporte pesado, el menu mostrara la caracteristica de enviar reporte pesado
    pesado = reportes.ReportePesado

    if (pesado == "1") {
        $("#enviarReportePesado").hide()
        $("#enviarReporteGrupo").hide();
        $("#enviarReportePesado").toggle();
    } else {
        $("#enviarReportePesado").hide()
        $("#enviarReporteGrupo").hide();
        $("#enviarReporteGrupo").toggle();
    }

    //empieza a cargar los inputs en el menu de inputs dependiendo el reporte seleccionado
    valores = inputsData.responseJSON[1]
    valores.forEach(function (valor) {
        //obtiene el nombre del input y lo hace sin espacios, para darle la id a cada input
        var valId = sinEspacios(valor.nombreInput)

        //en caso de ser un input de solo texto, en esta opcion crea un input normal y le asigna el id
        if (valor.tipo != "checkbox" && valor.tipo != "select" && valor.tipo != "date") {
          
             if(valor.valorAutocomplete == "sis_tcusuario"){
                $("#contenedorInputs").append(tipoDatos[valor.tipo]).find('input:last').attr('id', "inp" + valId).tooltip({ 'trigger': 'focus', 'title': '' + valor.informacionAdicional + '' });;
                $("#inpUsuarioSesion").val(usuarioEnSesion)
                $("#inpUsuarioSesion").hide();
            }else{
                $("#contenedorInputs").append('<label style="display:block;" type="text" value="test">' + valor.nombreInput + '</label>');
                $("#contenedorInputs").append(tipoDatos[valor.tipo]).find('input:last').attr('id', "inp" + valId).tooltip({ 'trigger': 'focus', 'title': '' + valor.informacionAdicional + '' });;
            }
        }
        //En esta opcion crea un input de select normal
        if (valor.tipo == "select") {
            $("#contenedorInputs").append('<label style="display:block;" type="text" value="test">' + valor.nombreInput + '</label>');
            $("#contenedorInputs").append(tipoDatos[valor.tipo]).find('select:last').attr('id', "inp" + valId).tooltip({ 'trigger': 'focus', 'title': '' + valor.informacionAdicional + '' });;

        }
        //en esta opcion, crea un select normal
        if (valor.tipo == "checkbox") {
            console.log("valor checked", valor.checked)
            console.log("valor checkeado", valor.checked==1)
            $("#contenedorInputs").append('<label style="display:block;" type="text" for="inp' + valId + '">' + valor.nombreInput + '</label>');
            $("#contenedorInputs").append(tipoDatos[valor.tipo]).find('input:last').attr('id', "inp" + valId).attr('checked', valor.checked == 1).tooltip({ 'trigger': 'focus', 'title': '' + valor.informacionAdicional + '' });;
        }
        //en esta opcion crea un input de text y se le asigna la hora de la bd y lo carga
        if (valor.tipo == "date") {
            var horaSinMilisegundos = valor.hora.split('.')[0]; // Divide la cadena en el punto y toma la primera parte
            $("#contenedorInputs").append('<label style="display:block;" type="text" value="test">' + valor.nombreInput + ' - ' + horaSinMilisegundos + '</label>');
            $("#contenedorInputs").append(tipoDatos[valor.tipo]).find('input:last').attr('id', "inp" + valId).attr('hora', valor.hora).tooltip({ 'trigger': 'focus', 'title': '' + valor.informacionAdicional + '' });;
        }

        
        //en caso de ser Auto complete le da las caracteristicas dependiendo de los valores en la bd.
        if (valor.tipo == "autoComplete") {

            $("#inp" + sinEspacios(valor.nombreInput.trim())).autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: "../php/cReportesIGL.php",
                        dataType: "json",
                        type: "POST",
                        data: {
                            termn: request.term,
                            autoComplete: 1,
                            tabla: valor.valorAutocomplete,
                            id: valor.valorId,
                            valor: valor.valor,
                        },
                        success: function (data) {
                            console.log(data[1])
                            var suggestions = data[1].map(function (item) {
                                return {
                                    label: item.ID + " " + item.valor,
                                    value: item.ID + " - " + item.valor,
                                };
                            });
                            response(suggestions);
                        },
                    });
                },
                minLength: 1,
                select: function (event, ui) {
                    var selectedId = ui.item.value;
                    console.log("Selected ID:", selectedId);
                    console.log("ID devuelva:", getId(selectedId))
                }
            })
        } else if (valor.tipo == "date") { //en esta opcion hace que el text se haga en datepicker y se acomoda al formato de la bd
            $("#inp" + valId).datepicker({
                dateFormat: "yy-mm-dd",
                changeMonth: true,
                changeYear: true
            });
        } else if (valor.tipo == "checkbox") { //esta opcion lo hace checkboxradio 

            $("#inp" + valId).checkboxradio();

            console.log("prueba checkbox: ", valor.checked === 1)
        } else if (valor.tipo == "select") { //esta opcion crea el select, le agrega los valores dependiendo la base de datos y los separa por ,
            $("#inp" + valId).selectmenu().selectmenu("menuWidget").addClass("overflow");
            let texto = valor.textoSelect.split(", ");
            let valores = valor.valoresSelect.split(", ");

            for (let i = 0; i < valores.length - 1; i++) {
                let textItem = texto[i]; // Obtener el texto correspondiente
                let item = valores[i];

                console.log(item, textItem);

                // Añadir la opción al select
                $("#inp" + valId).append($('<option>', {
                    value: item,
                    text: textItem
                }));
            }

        }
    });
    //aqui agregar la opcion de que se despliegue el segundo acordeon dependiendo si es administrador o no
    if (usuarioEnSesion != "1") {
        $('#flush-collapseOne').prev().find('button').click();

        // Simula el clic en el botón del segundo acordeón para abrirlo
        $('#flush-collapseTwo').prev().find('button').click();
    }
}

