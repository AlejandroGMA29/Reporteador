//--------------------------VARIABLES GLOBALES---------------------------------//
const tipoDatos = {
    "text": '<input type="text" value="" style="display:block;" class="form-control">',
    "select": '<select>',
    "checkbox": '<input type="checkbox" style="display:block;">',
    "autoComplete": '<input type="text" value="" style="display:block;" class="form-control">',
    "date": '<input type="text" value="" style="display:block;" class="form-control">',
    "password": '<input type="password" value="" style="display:block;" class="form-control">',
    "server": '<input type="password" value="" style="display:none;" class="form-control">',
};


var nombreExcel; //excel actual, se utiliza para utilizarlo tanto en correos como en descargas
var rutaExcel; // ruta donde se almacena el excel en el servidor
var excelSesion = [] //exceles creados anteriormente, usados para que se eliminen en caso de cerrar la pagina
var correoSesion; //correo precreado creo que ya no se uiliza
var usuarioEnSesion = null; //usuario que verificara si es admin o no


//--------------------------COMIENZA DOCUMENT READY---------------------------------//
$(document).ready(function () {
    //obtiene los reportes ya creados
    var reportes = $.ajax({
        url: "../php/cReportesIGL.php",
        async: false,
        type: "POST",
        data: {
            obtenerDatosReportes: '1',
        },
        dataType: "json",
    })

    //añade al select los reportes, en caso de ser inactivo los cargara con * al final.
    $.each(reportes.responseJSON, function (i, item) {
        console.log(item.activo)
        $('#number').append($('<option>', {
            value: item.id_reporte,
            text: item.descripcion += item.activo == "1" ? "" : " *",
            pesado: item.ReportePesado  // Aquí asignamos la propiedad "pesado"
        }));
    });

    //obtiene el usuario en sesion en el servidor
    var session = $.ajax({
        url: "../php/cReportesIGL.php",
        async: false,
        type: "POST",
        data: {
            sesion: '1',
        },
        dataType: "json",
    })


    //almacena el usuario en sesion y verifica si es administrador para permitir ciertas funciones
    usuarioEnSesion = session.responseJSON
    cargarAccionesAdmin(usuarioEnSesion)


    //añade la funcion del menu para ocutarlo o mostrarlo
    $("#btnMenu").click(function () {
        var menu = $("#menuReporteador");
        menu.toggle();

        // Ajusta el tamaño de los elementos al ocultar/mostrar el menú
        if (menu.is(":visible")) {
            // Si el menú está visible, ajusta los tamaños a su estado normal
            $("#contenedorTodo").removeClass("container-fluid").addClass("container-fluid");
            $("#headerNavbar").removeClass("col-12").addClass("col-9");
        } else {
            // Si el menú está oculto, ajusta los tamaños para ocupar toda la pantalla
            $("#headerNavbar").removeClass("col-9").addClass("col-12");
        }
    });


    //carga en el input de horario, las horas de 00:00:00 a las 23:00:00
    $("#añadirInput").click(function () {
        // limpiarModal();
        for (let index = 0; index < 24; index++) {
            if (index < 10) {
                $("#inpHorario").append('<option value="0' + index + ':00:00">0' + index + ':00:00</option>');
            } else {
                $("#inpHorario").append('<option value="' + index + ':00:00">' + index + ':00:00</option>');
            }
        }
    })

    var inputsData

    //el select se convierte en selectmenu de jquery y se le agrega la funcion para cargar los inputs 
    console.log(reportes.responseJSON)
    $("#number").selectmenu(
        {
            change: function (event, data) {
                //limpia la tabla y la pagina en general cada que se selecciona un reporte, ademas limpia tambien los botones en general.
                if ($.fn.DataTable.isDataTable("#contenidoReporte")) {

                    $("#contenidoReporte").DataTable().destroy();
                    $('#contenidoReporte thead').empty();
                    $('#contenidoReporte tbody').empty();
                }
                if ($('#contenedorWizard').is(':visible')) {
                    $('#contenedorWizard').slideUp();
                }

                $("#btnExcel").prop('disabled', true);
                $("#btnAbrirCorreos").prop('disabled', true);

               
                cargartInputsMenu(data.item.value)
            }
        }
    )
        .selectmenu("menuWidget")
        .addClass("overflow");


    //funcion para crear el reporte en pantalla y en el servidor
    $("#btnBuscarReporte").click(function () {
        //apaga un momento el boton en caso de que sea muy tardado la busqueda del servidor
        $(this).prop('disabled', true);
        $(this).find('.spinner-border').show();


        //en caso de estar creada una tabla anteriormente, la elimina para empezar de nuevo
        if ($.fn.DataTable.isDataTable("#contenidoReporte")) {

            $("#contenidoReporte").DataTable().destroy();
            $('#contenidoReporte thead').empty();
            $('#contenidoReporte tbody').empty();
        }
        //revisa si la tabla esta visible, en caso de que no la muestra y oculta lo que se este mostrando
        if (!$('#contenedorTabla').is(':visible')) {
            $('#contenedorTabla').slideDown();
            $('#contenidoReporte').hide();
        }
        //en caso de que el contenedor del wizard este mostrado, lo oculta
        if ($('#contenedorWizard').is(':visible')) {
            $('#contenedorWizard').slideUp();
        }

        $("#texto").empty();
        console.log("boton")

        var valoresArray = []
        var titulosArray = []

        //almacena los datos ingresados en los inputs para subirlos, tambien el titulo para usarlos despues
        valores.forEach(function (valor) {
            // Obtener el valor del input y agregarlo al arreglo

            var inputValue = $("#inp" + sinEspacios(valor.nombreInput)).val();
            if (valor.tipo == 'autoComplete') {
                //en caso de ser autocomplete, guarda solo el id
                valoresArray.push(getId(inputValue));
            } else if (valor.tipo == "checkbox") {
                //en caso de ser checkbox toma 1 o 0
                valoresArray.push($("#inp" + sinEspacios(valor.nombreInput)).prop('checked') == true ? 1 : 0);
            } else if (valor.tipo == "date") {
                //en fecha, toma el valor de la hora y envia ambas cosas
                var horaCompleta = $("#inp" + sinEspacios(valor.nombreInput)).attr('hora');
                var hora = horaCompleta.substring(0, 8);
                valoresArray.push(inputValue + " " + hora);
            }
            else {
                //en otros casos, simplemente sube el valor
                valoresArray.push(inputValue)
            }
            //sube le titulo del input
            titulosArray.push(valor.nombreInput);

        });


        //guarda el id del reporte actual
        idReporte = $('#number').val();

        //elimina el excel del reporte anterior, ademas de que sube los datos para crear un excel nuevo y que devuelva los datos del reporte en caso de que exista
        $.ajax({
            type: "POST",
            url: "../php/cReportesIGL.php",
            data: {
                id_reporte: idReporte,
                salida: '1',
                elementos: JSON.stringify(excelSesion),
                tabla: '1',
                ValoresArray: JSON.stringify(valoresArray),
                titulosArray: JSON.stringify(titulosArray)
            },
            dataType: "json",
            success: function (data) {
                //en caso de darnos una respuesta valida, se activara el boton excel para descargarlo, el de abrir correos para enviar correos y empezara a cargar la tabla
                //tambien volvera a activar el boton de buscar
                if (data.error == 0) {
                    if (data.datos.length === 2 && Array.isArray(data.datos[1]) && data.datos[1].length > 0) {
                        // El segundo elemento del array no está vacío
                        $('#btnExcel').prop('disabled', false);
                        $('#btnAbrirCorreos').prop('disabled', false);

                        correoSesion = data.mail
                        console.log(data.datos[1]);
                        nombreExcel = data.resp.Nombre;
                        rutaExcel = data.resp.datos;
                        excelSesion.push(nombreExcel)
                        $("#contenidoReporte").slideDown();
                        cargarTabla(data.datos[1]);
                        $("#btnBuscarReporte").prop('disabled', false);
                        $("#btnBuscarReporte").find('.spinner-border').hide();

                    }
                } else {
                    //en caso de no encontrar informacion, nos manda a este error, por lo general es por que no encuentra y no puede crear el excel
                    //elimina la tabla si se creao y vuelve a activar el boton
                    console.log("entra a error por falta de info")
                    // El segundo elemento del array está vacío o no es un array
                    alert("¡Inserto datos no validos!")
                    console.log("La respuesta contiene datos vacíos o no válidos.");
                    if ($.fn.DataTable.isDataTable("#contenidoReporte")) {
                        $("#contenidoReporte").DataTable().destroy();
                        $('#contenidoReporte thead').empty();
                        $('#contenidoReporte tbody').empty();
                        $('#btnExcel').prop('disabled', true);
                        $('#btnAbrirCorreos').prop('disabled', true);

                    }
                    $("#btnBuscarReporte").prop('disabled', false);
                    $("#btnBuscarReporte").find('.spinner-border').hide();
                }
            },
            error: function (xhr, status, error) {
                //en caso de devolver algun otro error, envia un mensaje en consola y activa el boton
                console.error("Error en la petición:", status, error);
                $("#btnBuscarReporte").prop('disabled', false);
                $("#btnBuscarReporte").find('.spinner-border').hide(); s
            }
        });
    });

    //descarga el excel previamnete generado, da el enlace de descarga
    $("#btnExcel").click(function () {
        // Obtén la ruta completa utilizando el objeto window.location
        var nuevaRuta = window.location.origin + '/aleLaps/Reporteador/Reportes/' + nombreExcel;

        // Imprime la ruta completa en la consola
        console.log("Ruta completa a la que estás enviando:", nuevaRuta);

        // Abre la nueva ventana con la ruta
        window.open(nuevaRuta, '_blank');
        console.log("excel");
        console.log(rutaExcel, nombreExcel);
    });

    //acciones que se realizaran al abrir el modal de los correos existentes
    $("#btnAbrirCorreos").click(function () {
        //ocultara los botones en enviar correos, y mostrara el corresponeidnete
        $("#btnEnviarCorreos").hide();
        $("#btnEnviarCorreosPesados").hide();
        $("#btnEnviarCorreos").toggle();

        //oculta y muestra el div con el cliente asignado

        $('#contenedorCliente').hide();
        $('#contenedorCliente').toggle();


        var clienteExiste = false;


        //revisa si existe algun input de cliente especifico, y devuelve algun valor
        $('#inpClientesCorreos').val("");
        $("#contenedorInputs input").each(function () {
            // Verificar si la input tiene el id "inpCliente"
            if ($(this).attr("id") === "inpCliente") {
                clienteExiste = true;
            }
        });


        //en caso de que tenga un input de cliente, lo toma y lo utiliza para cargar los correos corresponeidnete
        if (clienteExiste) {
            console.log("encontro cliente");
            $('#ulCorreos').empty();
            $('#inpClientesCorreos').val($("#inpCliente").val())
            $("#inpClientesCorreos").prop('disabled', true);
            $("#btnAsignarClientesMostrar").prop('disabled', true);
            var idCliente
            valores.forEach(function (valor) {
                // Obtener el valor del input y agregarlo al arreglo
                var inputValue = $("#inp" + sinEspacios(valor.nombreInput)).val();
                if (valor.tipo == 'autoComplete' && valor.valorAutocomplete.toUpperCase() == 'FCLIENTE') {
                    idCliente = getId(inputValue);
                }
            });
            var idReporte = $('#number').val();
            console.log("id cliente y id reporte", idCliente, idReporte)
            cargarCorreos(idCliente, idReporte)
        } else {
            //en caso de que no, mostrarala pantalla vacia y permitira buscar clientes especificos desde el modal
            console.log("no encontro cliente");
            $('#ulCorreos').empty();
            $('#inpClientesCorreos').val("");
            $("#inpClientesCorreos").prop('disabled', false);
            $("#btnAsignarClientesMostrar").prop('disabled', false);
        }
        console.log($("#chbInterno").prop("checked"))

        //revisa si interno esta seleccionado, en caso de que si, encontes no deja elegir cliente especifico aun este en los inputs
        //en caso de que sea asi, mostrara los correos del cliente 9999 y lo ocultara.
        if ($("#chbInterno").prop("checked")) {

            $('#contenedorCliente').hide();
            console.log("encontro cliente");
            $('#ulCorreos').empty();
            $('#inpClientesCorreos').val("9999")
            $("#inpClientesCorreos").prop('disabled', true);
            $("#btnAsignarClientesMostrar").prop('disabled', true);
            var idCliente
            valores.forEach(function (valor) {
                // Obtener el valor del input y agregarlo al arreglo
                var inputValue = $("#inp" + sinEspacios(valor.nombreInput)).val();
                if (valor.tipo == 'autoComplete' && valor.valorAutocomplete.toUpperCase() == 'FCLIENTE') {
                    idCliente = getId(inputValue);
                }
            });
            var idReporte = $('#number').val();
            console.log("id cliente y id reporte", idCliente, idReporte)
            cargarCorreos(9999, idReporte)
        }
    });
    //acciones que se realizaran al abrir el modal de los correos existentes
    $("#btnAbrirCorreoPesado").click(function () {
        //cambia el boton permitido, aqui se ocultaran ambos botones pero se mostrara el de enviarCorreosPesados
        $("#btnEnviarCorreos").hide();
        $("#btnEnviarCorreosPesados").hide();
        $("#btnEnviarCorreosPesados").toggle();

        //oculta y muestra el div con el cliente asignado
        $('#contenedorCliente').hide();
        $('#contenedorCliente').toggle();
        var clienteExiste = false;
        $('#inpClientesCorreos').val("");
        $("#contenedorInputs input").each(function () {
            // Verificar si la input tiene el id "inpCliente"
            if ($(this).attr("id") === "inpCliente") {
                clienteExiste = true;
            }
        });
        //en caso de que tenga un input de cliente, lo toma y lo utiliza para cargar los correos corresponeidnete
        if (clienteExiste) {
            $('#ulCorreos').empty();
            $('#inpClientesCorreos').val($("#inpCliente").val())
            $("#inpClientesCorreos").prop('disabled', true);
            $("#btnAsignarClientesMostrar").prop('disabled', true);
            var idCliente
            valores.forEach(function (valor) {
                // Obtener el valor del input y agregarlo al arreglo
                var inputValue = $("#inp" + sinEspacios(valor.nombreInput)).val();
                if (valor.tipo == 'autoComplete') {
                    idCliente = getId(inputValue);
                }
            });
            var idReporte = $('#number').val();
            cargarCorreos(idCliente, idReporte)
        } else {
            //en caso de que no, mostrarala pantalla vacia y permitira buscar clientes especificos desde el modal
            $('#ulCorreos').empty();
            $('#inpClientesCorreos').val("");
            $("#inpClientesCorreos").prop('disabled', false);
            $("#btnAsignarClientesMostrar").prop('disabled', false);
        }

        //revisa si interno esta seleccionado, en caso de que si, encontes no deja elegir cliente especifico aun este en los inputs
        //en caso de que sea asi, mostrara los correos del cliente 9999 y lo ocultara.
        if ($("#chbInterno").prop("checked")) {
            $('#contenedorCliente').hide();
            console.log("encontro cliente");
            $('#ulCorreos').empty();
            $('#inpClientesCorreos').val("9999")
            $("#inpClientesCorreos").prop('disabled', true);
            $("#btnAsignarClientesMostrar").prop('disabled', true);
            var idCliente
            valores.forEach(function (valor) {
                // Obtener el valor del input y agregarlo al arreglo
                var inputValue = $("#inp" + sinEspacios(valor.nombreInput)).val();
                if (valor.tipo == 'autoComplete' && valor.valorAutocomplete.toUpperCase() == 'FCLIENTE') {
                    idCliente = getId(inputValue);
                }
            });
            var idReporte = $('#number').val();
            console.log("id cliente y id reporte", idCliente, idReporte)
            cargarCorreos(9999, idReporte)
        }
    })

    //boton asignar clientes, en caso de insertar un cliente valido, muestra los remitentes asignados para ese reporte y ese cliente
    $("#btnAsignarClientesMostrar").click(function () {
        $('#ulCorreos').empty();
        var idCliente
        var cliente = $('#inpClientesCorreos').val();

        idCliente = getId(cliente);
        var idReporte = $('#number').val();

        cargarCorreos(idCliente, idReporte)
        alert(`¡Correos para el id: ${idReporte} cargados correctamente!`);

    })

    //la opcion de agregar correos al cliente y reporte, verifica la valides y los sube.
    $("#btnAgregarCorreos").click(function () {
        var idCliente = getId($("#inpClientesCorreos").val());

        var idReporte = $('#number').val();
        var correoNuevo = $("#inpCorreo").val();
        if (!esCorreoValido(correoNuevo)) {
            alert("Por favor, ingrese un correo electrónico válido.");
            return; // Detener la ejecución si el correo no es válido
        }
        try {
            $.ajax({
                url: "../php/cReportesIGL.php",
                async: false,
                type: "POST",
                data: {
                    select: '0',
                    Excel: '0',
                    Correos: '0',
                    insertCorreo: '1',
                    Correo: correoNuevo,
                    IDCliente: idCliente,
                    idReporte: idReporte
                },
                dataType: "json",
            })
            $('#ulCorreos').empty();
            cargarCorreos(idCliente, idReporte)

            $("#inpCorreo").val(' ');
            alert("¡Correo agregado satisfactoriamente!")

        } catch {
            alert("Ocurrio un error al agregar el correo")
        }

    })

    //la opcion de eliminar el remitente del reporte, dejara de mostrarlo.
    $('#ulCorreos').on('click', '.eliminar', function () {
        var mail = $(this).data('correo');
        var idCorreo = $(this).data('id');
        var idCliente = getId($("#inpClientesCorreos").val());
        var idReporte = $('#number').val();


        console.log(idCorreo)
        console.log(mail)

        // eliminar objeto
        console.log(idCliente, idCorreo)

        $.ajax({
            url: '../php/cReportesIGL.php',
            method: 'POST',
            data: {
                select: '0',
                Excel: '0',
                Correos: '0',
                eliminarCorreo: '1',
                IdCliente: idCliente,
                Correo: idCorreo,
                idReporte: idReporte
            },
            success: function (response) {
                alert("correo: " + mail + " eliminado correctamente"); // Mostrar el mensaje de éxito o error

                $('#ulCorreos').empty();
                var dataMails = $.ajax({
                    url: "../php/cReportesIGL.php",
                    async: false,
                    type: "POST",
                    data: {
                        select: '0',
                        Excel: '0',
                        Correos: '1',
                        IDCliente: idCliente,
                    },
                    dataType: "json",
                });
                console.log(dataMails.responseJSON[1]);
                console.log(nombreExcel)
                cargarCorreos(idCliente, idReporte)

            },
            error: function (xhr, status, error) {
                alert("Error al eliminar el correo. Detalles: " + xhr.responseText);
            }
        });
    });

    //envia los emails a la lista de correos del cliente asignado.
    $("#btnEnviarCorreos").click(function () {
        // Deshabilitar el botón y mostrar el spinner
        $(this).prop('disabled', true);
        $(this).find('.spinner-border').css('visibility', 'visible');
        $(this).find('.visually-hidden').css('visibility', 'visible');

        var valoresArray = []
        var titulosArray = []
        //toma los valores de los inputs para almacenarlo
        valores.forEach(function (valor) {
            // Obtener el valor del input y agregarlo al arreglo
            var inputValue = $("#inp" + sinEspacios(valor.nombreInput)).val();
            if (valor.tipo == 'autoComplete') {
                valoresArray.push(getId(inputValue));
            } else if (valor.tipo == "checkbox") {
                valoresArray.push($("#inp" + sinEspacios(valor.nombreInput)).prop('checked') == true ? 1 : 0);
            } else if (valor.tipo == "date") {
                var horaCompleta = $("#inp" + sinEspacios(valor.nombreInput)).attr('hora');
                var hora = horaCompleta.substring(0, 8);
                valoresArray.push(inputValue + " " + hora);
            }
            else {
                valoresArray.push(inputValue)
            }
            titulosArray.push(valor.nombreInput);

        });


        var idCliente = getId($("#inpClientesCorreos").val());
        var datoscliente = $("#inpClientesCorreos").val()
        var idReporte = $('#number').val();
        console.log(correoSesion);
        try {
            //toma el nombre del excel actual para utilizarlo en el correo
            //ademas sube los datos del cliente y los del reporte para realizar la plantilla del correo
            $.ajax({
                type: "POST",
                url: "../php/cReportesIGL.php",
                data: {
                    EnviarCorreos: '1',
                    excel: nombreExcel,
                    IDCliente: idCliente,
                    datosCliente: datoscliente,
                    idReporte: idReporte,
                    mail: correoSesion,
                    ValoresArray: JSON.stringify(valoresArray),
                    titulosArray: JSON.stringify(titulosArray)
                },
                dataType: "json",
                success: function (data) {
                    //en caso de funcionar, activa los botones y cierra el modal, ademas avisa si fue correcto
                    var nombreReporte = data.data;
                    console.log(nombreReporte);

                    // Realizar otras acciones según la respuesta

                    // Habilitar nuevamente el botón y ocultar el spinner
                    $("#btnEnviarCorreos").prop('disabled', false);
                    $("#btnEnviarCorreos").find('.spinner-border').css('visibility', 'hidden');
                    $("#btnEnviarCorreos").find('.visually-hidden').css('visibility', 'hidden');
                    $(function () {
                        $('#modalCorreos').modal('toggle');
                    });
                    alert("correo enviado correctamente")
                    console.log(data)
                },
                error: function () {
                    // Manejar el error
                    alert("Ocurrió un error al enviar el correo.");

                    // Habilitar nuevamente el botón y ocultar el spinner en caso de error
                    $("#btnEnviarCorreos").prop('disabled', false);
                    $("#btnEnviarCorreos").find('.spinner-border').css('visibility', 'hidden');
                    $("#btnEnviarCorreos").find('.visually-hidden').css('visibility', 'hidden');
                }
            });

        } catch (err) {
            // Manejar el error
            alert("Ocurrió un error al enviar el correo.");
            // Habilitar nuevamente el botón y ocultar el spinner en caso de error
            $("#btnEnviarCorreos").prop('disabled', false);
            $("#btnEnviarCorreos").find('.spinner-border').css('visibility', 'hidden');
            $("#btnEnviarCorreos").find('.visually-hidden').css('visibility', 'hidden');
        }
    });


    //esta opcion, creara y enviara un enlace para el excel, no esperara una respuesta
    $("#btnEnviarCorreosPesados").click(function () {
        // Deshabilitar el botón y mostrar el spinner
        $(this).prop('disabled', true);
        $(this).find('.spinner-border').css('visibility', 'visible');
        $(this).find('.visually-hidden').css('visibility', 'visible');

        var valoresArray = []
        var titulosArray = []

        //obtiene los valores de los inputs
        valores.forEach(function (valor) {
            // Obtener el valor del input y agregarlo al arreglo

            var inputValue = $("#inp" + sinEspacios(valor.nombreInput)).val();
            if (valor.tipo == 'autoComplete') {
                valoresArray.push(getId(inputValue));
            } else if (valor.tipo == "checkbox") {

                valoresArray.push($("#inp" + sinEspacios(valor.nombreInput)).prop('checked') == true ? 1 : 0);
            } else if (valor.tipo == "date") {
                var horaCompleta = $("#inp" + sinEspacios(valor.nombreInput)).attr('hora');
                var hora = horaCompleta.substring(0, 8);
                valoresArray.push(inputValue + " " + hora);
            }
            else {
                valoresArray.push(inputValue)
            }
            titulosArray.push(valor.nombreInput);

        });
        //obtiene los datos de los correos y del id del reporte
        var idCliente = getId($("#inpClientesCorreos").val());
        var datoscliente = $("#inpClientesCorreos").val()
        var idReporte = $('#number').val();

        try {
            //envia los datos del cliente, el id del reporte y los valores para crear el correo y el excel
            $.ajax({
                type: "POST",
                url: "../php/cReportesIGL.php",
                data: {
                    enviarCorreoPesado: '1',
                    IDCliente: idCliente,
                    datosCliente: datoscliente,
                    idReporte: idReporte,
                    ValoresArray: JSON.stringify(valoresArray),
                    titulosArray: JSON.stringify(titulosArray)
                },
                dataType: "json"
            })
            // Habilitar nuevamente el botón y ocultar el spinner
            $("#btnEnviarCorreosPesados").prop('disabled', false);
            $("#btnEnviarCorreosPesados").find('.spinner-border').css('visibility', 'hidden');
            $("#btnEnviarCorreosPesados").find('.visually-hidden').css('visibility', 'hidden');
            $(function () {
                $('#modalCorreos').modal('toggle');
            });
            //avisa que el correo fue enviado
            alert("El correo se esta enviando, dependiendo del tamaño del reporte es posible que tarde mas o menos.")
        } catch (err) {
            // Manejar el error
            alert("Ocurrió un error al enviar el correo.");
            // Habilitar nuevamente el botón y ocultar el spinner en caso de error
            $("#btnEnviarCorreos").prop('disabled', false);
            $("#btnEnviarCorreos").find('.spinner-border').css('visibility', 'hidden');
            $("#btnEnviarCorreos").find('.visually-hidden').css('visibility', 'hidden');

        }
    });


    $("#inpClientesCorreos").autocomplete({
        //autocomplete de los clientes disponibles para asignar correos.
        source: function (request, response) {
            $.ajax({
                url: "../php/cReportesIGL.php",
                dataType: "json",
                type: "POST",
                data: {
                    termn: request.term,
                    autoCompleteCliente: 1,
                    id: $("#number").val(),
                },
                success: function (data) {
                    console.log(data)
                    var suggestions = data.map(function (item) {
                        return {
                            label: item.ID_CLIENTE + " " + item.RAZON_SOCIAL + " - " + item.razon_social_abreviada,
                            value: item.ID_CLIENTE + " - " + item.RAZON_SOCIAL,
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



    window.addEventListener('unload', sendData, false);



}); //---------TERMINA DOCUMENT READY----------//

//---------------------FUNCIONES---------------------//
//funcion en caso de que se cierre, envia un beacon que elimina el ultimo reporte.
function sendData() {
    // Crear los datos a enviar
    var data = new FormData();
    data.append('salida', '1');
    data.append('elementos', JSON.stringify(excelSesion));

    // Enviar los datos mediante sendBeacon
    navigator.sendBeacon('../php/cReportesIGL.php', data);
}

//revisa si el correo es valido
function esCorreoValido(correo) {
    var regexCorreo = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    return regexCorreo.test(correo);
}

//limpia el modal de añadir input
function limpiarModal() {
    // Limpiar el campo de Nombre
    $("#grupoSelect").empty();
    $('#inpNombreInput').val('');

    // Reiniciar el campo de Tipo y ocultar las opciones adicionales
    $('#inpTipo').val('text').change();
    $('#inpHorario').val('').change();

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

//quita espacios a cadena
function sinEspacios(text) {
    return text.replace(/ /g, "");
}

//obtiene la primer parte de una cadena dividida por - en este caso para obtener id de autocomplete
function getId(text) {
    if (text.indexOf(' -') !== -1 || text.indexOf(' ') !== -1 || text.indexOf('') !== -1) {
        // Divide la cadena en número y nombre usando el guion como separador
        var partes = text.split('-');
        var posibleNumero = partes[0].trim();
        return posibleNumero;
    }
}

//hace un select de los correos del cliente y los carga en el modal, dando la opcion de elimianrlos
function cargarCorreos(idCliente, idReporte) {
    var dataMails = $.ajax({
        url: "../php/cReportesIGL.php",
        async: false,
        type: "POST",
        data: {
            select: '0',
            Excel: '0',
            Correos: '1',
            IDCliente: idCliente,
            idReporte: idReporte
        },
        dataType: "json",
    });
    console.log(dataMails.responseJSON[1]);
    console.log(nombreExcel)
    dataMails.responseJSON[1].forEach(element => {
        $('#ulCorreos').append('<li class="list-group-item d-flex justify-content-between align-items-center">' +
            element.correo +
            '<button type="button" class="btn btn-outline-danger btn-sm eliminar" data-id="' + element.id_destinatario + '" data-correo="' + element.correo + '">X</button>' +
            '</li>');
    });
}


// Función para cargar y dibujar una tabla de reporte en el elemento con el ID "contenidoReporte".
function cargarTabla(data) {

    // Verificar si la DataTable ya está inicializada en el elemento "#contenidoReporte".
    if ($.fn.DataTable.isDataTable("#contenidoReporte")) {
        // Destruir la DataTable existente para evitar conflictos al volver a cargar la tabla.
        $("#contenidoReporte").DataTable().destroy();
        // Limpiar el encabezado y cuerpo de la tabla.
        $('#contenidoReporte thead').empty();
        $('#contenidoReporte tbody').empty();
    }

    // Obtener las columnas de los datos y mapearlas a objetos con la propiedad "title".
    var columnas = Object.keys(data[0]).map(function (key) {
        return { title: key };
    });

    // Agregar filas de encabezado a la tabla.
    $('#contenidoReporte thead').append('<tr>');
    columnas.forEach(function (columna) {
        $('#contenidoReporte thead tr').append('<th>' + columna.title + '</th>');
    });
    $('#contenidoReporte thead').append('</tr>');

    // Cargar los datos en la tabla.
    data.forEach(function (fila) {
        $('#contenidoReporte tbody').append('<tr>');
        columnas.forEach(function (columna) {
            $('#contenidoReporte tbody tr:last-child').append('<td>' + fila[columna.title] + '</td>');
        });
        $('#contenidoReporte tbody tr:last-child').append('</tr>'); // Cerrar la fila después de agregar todas las celdas.
    });

    // Inicializar la DataTable con opciones de configuración.
    $('#contenidoReporte').DataTable({
        scrollX: true,
        searching: false, // Habilita o deshabilita la búsqueda.
        lengthChange: false, // Habilita o deshabilita el cambio de longitud.
        pageLength: 10,
        nowrap: true,
        columnDefs: [
            { "className": "dt-center", "targets": "_all" },
            { "width": "99%", "targets": "_all" } // Asegúrate de colocar "width" entre comillas.
        ],
        autoWidth: true,
    });
}

//carga el contenedor con las acciones que puede realizar el administrador.
function cargarAccionesAdmin(sesion) {
    var btnModificarReporte = $("#btnModificarReporte")
    var btnAgregarReporte = $("#btnAgregarReporte")
    var catalogoInputs = $("#btnCatalogoInputs")
    var contenedor = $("#contenedorAdmin")
    contenedor.hide();

    if (sesion == 1) {
        contenedor.toggle();

    }
}