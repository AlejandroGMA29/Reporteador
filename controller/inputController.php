<?php

session_start();
require_once("/aleLaps/Reporteador/model/mReportesIGL.php");
$Input = new mInpt();

//obtiene todos los inputs
if ($_REQUEST['obtenerInputs'] == "1") {
    try {
        // Devuelve los datos de los inputs en formato JSON
        echo json_encode($datosInputs);
    } catch (Exception $e) {
        // Maneja excepciones y devuelve un mensaje de error en formato JSON
        echo json_encode(array("error" => $e->getMessage()));
    }
}


//carga input dependiendo el reporte
if ($_REQUEST["cargarInputs"] == "1") {
    try {
        // Obtener el ID del reporte desde la solicitud
        $id_reporte = $_REQUEST["idReporte"];

        // Devolver la respuesta en formato JSON
        echo json_encode($datosInputs);
    } catch (Exception $e) {
        // Manejar excepciones y devolver un mensaje de error en formato JSON
        echo json_encode(array("error" => $e->getMessage()));
    }
}


//devuelve los inputs dependiendo del id insertado
if ($_REQUEST['obtenerInputPorID'] == "1") {
    try {
        // Obtener el ID del input desde la solicitud
        $idInput = $_REQUEST['idInput'];


        // Devolver la respuesta en formato JSON
        echo json_encode($datosReporte); // Asumiendo que deseas imprimir los resultados como JSON
    } catch (Exception $e) {
        // Manejar excepciones y devolver un mensaje de error en formato JSON
        echo json_encode(array("error" => $e->getMessage()));
    }
}



//carga los inputs que no estan unidos a un reporte especifico
if ($_REQUEST["cargarInputsFuera"] == "1") {
    try {
        // Obtener el ID del reporte desde la solicitud
        $id_reporte = $_REQUEST["idReporte"];


        // Devolver la respuesta en formato JSON
        echo json_encode($datosInputs);
    } catch (Exception $e) {
        // Manejar excepciones y devolver un mensaje de error en formato JSON
        echo json_encode(array("error" => $e->getMessage()));
    }
}


//carga todos los inputs
if ($_REQUEST["cargarTodosInputs"] == "1") {
    try {


        echo json_encode($datosInputs);
    } catch (Exception $e) {
        echo json_encode(array("error" => $e->getMessage()));
    }
}


// Verifica si la solicitud contiene el parámetro 'actualizarInformacion' con el valor "1"
if ($_REQUEST["actualizarInformacion"] == "1") {
    try {
        // Decodifica los datos JSON enviados en la solicitud
        $data = json_decode($_REQUEST["datosSortable"], true);

        // Devuelve la respuesta en formato JSON
        echo json_encode($datosInputs);
    } catch (Exception $e) {
        // Manejar excepciones y devolver un mensaje de error en formato JSON
        echo json_encode(array("error" => $e->getMessage()));
    }
}


// Verifica si la solicitud contiene el parámetro 'actualizarInformacionEliminar' con el valor "1"
if ($_REQUEST["actualizarInformacionEliminar"] == "1") {
    try {
        // Decodifica los datos JSON enviados en la solicitud
        $data = json_decode($_REQUEST["datosSortableInputs"], true);


        // Devuelve la respuesta en formato JSON
        echo json_encode($datosInputs);
    } catch (Exception $e) {
        // Manejar excepciones y devolver un mensaje de error en formato JSON
        echo json_encode(array("error" => $e->getMessage()));
    }
}

//inserta un nuevo input dependiendo del tipo toma diferentes valores
if ($_REQUEST['insertInputs'] == '1') {
    try {
        // Obtiene los datos de la solicitud para la inserción del nuevo input
        $nombre = $_REQUEST['nombre'];
        $tipo = $_REQUEST['tipo'];
        $valor = $_REQUEST['valor'];
        $valorAutocomplete = $_REQUEST['autocomplete'];
        $valorId = $_REQUEST['valorId'];
        $valorSelect = $_REQUEST['valorSelect'];
        $textoSelect = $_REQUEST['textoSelect'];
        $valorSeleccionado = $_REQUEST['valorSeleccionado'];
        $informacionAdicional = $_REQUEST['informacionAdicional'];
        $hora = $_REQUEST['hora'];

       
        // Devuelve la respuesta en formato JSON
        echo json_encode($datosInputs);
    } catch (Exception $e) {
        // Maneja excepciones y devuelve un mensaje de error en formato JSON
        echo json_encode(array("error" => $e->getMessage()));
    }
}


//actualiza un input dependiendo del tipo toma diferentes valores
if ($_REQUEST['updateInputs'] == '1') {
    try {
        // Obtiene los valores de la solicitud
        $idInput = $_REQUEST['idInput'];
        $nombre = $_REQUEST['nombre'];
        $tipo = $_REQUEST['tipo'];
        $valor = $_REQUEST['valor'];
        $valorAutocomplete = $_REQUEST['autocomplete'];
        $valorId = $_REQUEST['valorId'];
        $valorSelect = $_REQUEST['valorSelect'];
        $textoSelect = $_REQUEST['textoSelect'];
        $valorSeleccionado = $_REQUEST['valorSeleccionado'];
        $informacionAdicional = $_REQUEST['informacionAdicional'];
        $hora = $_REQUEST['hora'];



        // Devuelve el resultado de la actualización en formato JSON
        echo json_encode($valorSeleccionado);
    } catch (Exception $e) {
        // Maneja excepciones y devuelve un mensaje de error en formato JSON
        echo json_encode(array("error" => $e->getMessage()));
    }
}

//autocomplete del wizard de agregar autoComplete
if ($_REQUEST['autoCompleteTablas'] == '1') {
    try {
        // Obtiene el término de búsqueda de la solicitud
        $busqueda = $_REQUEST['termn'];



        // Devuelve la respuesta en formato JSON
        echo json_encode($resp);
    } catch (Exception $e) {
        // Maneja excepciones y devuelve un mensaje de error en formato JSON
        echo json_encode(['error' => $e->getMessage()]);
    }
}

//nos devuelve las columnas de una tabla en especifico
if ($_REQUEST['optionHeaders'] == '1') {
    try {
        // Obtiene el término de búsqueda y el nombre de la tabla de la solicitud
        $busqueda = $_REQUEST['termn'];
        $nombreTabla = $_REQUEST['tabla'];

    
        // Devuelve la respuesta en formato JSON
        echo json_encode($resp);
    } catch (Exception $e) {
        // Maneja excepciones y devuelve un mensaje de error en formato JSON
        echo json_encode(['error' => $e->getMessage()]);
    }
}





?>