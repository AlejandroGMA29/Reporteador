<?php

require_once('/var/www/html/proyecto/public_html/CexecuteSQL.php');

//agrega un reporte nuevo a la bd
if ($_REQUEST['agregarReporte'] == "1") {
    try {
        // Obtener datos del formulario
        $nombreRepo = $_REQUEST['nombreRepo'];
        $SPRepor = $_REQUEST['SPRepor'];
        $activo = $_REQUEST['activo'];
        $pesado = $_REQUEST['pesado'];

        // Crear una instancia de SQLExecute con la conexión 'IGLSQL'
        $db_conn = new SQLExecute('IGLSQL');

        // Definir el comando SQL para insertar un nuevo reporte
        $cmdsqlReporte = "exec insertCatalogoReporte ?, ?, ?, ?";

        // Preparar los parámetros para el comando SQL
        $params = array($nombreRepo, $SPRepor, $activo, $pesado);

        // Ejecutar el comando SQL utilizando el método 'Execsql' con el modo 'SP-PARAM'
        $datosInputs = $db_conn->Execsql("SP-PARAM", $cmdsqlReporte, $params);

        // Devolver la respuesta en formato JSON
        echo json_encode($datosInputs);
    } catch (Exception $e) {
        // Manejar excepciones y devolver un mensaje de error en formato JSON
        echo json_encode(array("error" => $e->getMessage()));
    }
}


//actualiza un reporte de la bd
if ($_REQUEST['modificarReporte'] == "1") {
    try {
        // Obtener datos del formulario
        $idRepo = $_REQUEST['idRepo'];
        $nombreRepo = $_REQUEST['nombreRepo'];
        $SPRepor = $_REQUEST['SPRepor'];
        $activo = $_REQUEST['activo'];
        $pesado = $_REQUEST['pesado'];

        // Crear una instancia de SQLExecute con la conexión 'IGLSQL'
        $db_conn = new SQLExecute('IGLSQL');

        // Definir el comando SQL para actualizar un reporte en la base de datos
        $cmdsqlReporte = "exec updateCatalogoReporte ?, ?, ?, ?, ?";

        // Preparar los parámetros para el comando SQL
        $params = array($idRepo, $nombreRepo, $SPRepor, $activo, $pesado);

        // Ejecutar el comando SQL utilizando el método 'Execsql' con el modo 'SP-PARAM'
        $datosInputs = $db_conn->Execsql("SP-PARAM", $cmdsqlReporte, $params);

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

        // Crear una instancia de SQLExecute con la conexión 'IGLSQL'
        $db_conn = new SQLExecute('IGLSQL');

        // Definir el comando SQL para seleccionar los inputs con el ID especificado
        $cmdsqlReporte = "SELECT * FROM Inputs WHERE idInput = {$idInput}";

        // Ejecutar el comando SQL utilizando el método 'Execsql' con el modo 'SELECT'
        $datosReporte = $db_conn->Execsql("SELECT", $cmdsqlReporte);

        // Devolver la respuesta en formato JSON
        echo json_encode($datosReporte); // Asumiendo que deseas imprimir los resultados como JSON
    } catch (Exception $e) {
        // Manejar excepciones y devolver un mensaje de error en formato JSON
        echo json_encode(array("error" => $e->getMessage()));
    }
}


// Verifica si la solicitud contiene el parámetro 'datosReporte' con el valor "1"
if ($_REQUEST['datosReporte'] == "1") {
    try {
        // Obtener el ID del reporte desde la solicitud
        $idReporte = $_REQUEST['idReporte'];

        // Crear una instancia de SQLExecute con la conexión 'IGLSQL'
        $db_conn = new SQLExecute('IGLSQL');

        // Definir el comando SQL para seleccionar los datos específicos del reporte con el ID especificado
        $cmdsqlReporte = "SELECT * FROM catalogoReportes WHERE id_reporte = {$idReporte}";

        // Ejecutar el comando SQL utilizando el método 'Execsql' con el modo 'SELECT'
        $datosReporte = $db_conn->Execsql("SELECT", $cmdsqlReporte);

        // Devolver la respuesta en formato JSON
        echo json_encode($datosReporte); // Asumiendo que deseas imprimir los resultados como JSON
    } catch (Exception $e) {
        // Manejar excepciones y devolver un mensaje de error en formato JSON
        echo json_encode(array("error" => $e->getMessage()));
    }
}


//carga input dependiendo el reporte
if ($_REQUEST["cargarInputs"] == "1") {
    try {
        // Obtener el ID del reporte desde la solicitud
        $id_reporte = $_REQUEST["idReporte"];

        // Crear una instancia de SQLExecute con la conexión 'IGLSQL'
        $db_conn = new SQLExecute('IGLSQL');

        // Definir el comando SQL almacenado para seleccionar inputs de un reporte
        $cmdsqlReporte = "EXEC selectInputReportes ?";

        // Preparar los parámetros para el comando SQL almacenado
        $params = array($id_reporte);

        // Ejecutar el comando SQL utilizando el método 'Execsql' con el modo 'SP-PARAM'
        $datosInputs = $db_conn->Execsql("SP-PARAM", $cmdsqlReporte, $params);

        // Devolver la respuesta en formato JSON
        echo json_encode($datosInputs);
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

        // Crear una instancia de SQLExecute con la conexión 'IGLSQL'
        $db_conn = new SQLExecute('IGLSQL');

        // Definir el comando SQL almacenado para seleccionar inputs fuera de un reporte
        $cmdsqlReporte = "EXEC selectInputsFueraReporte ?";

        // Preparar los parámetros para el comando SQL almacenado
        $params = array($id_reporte);

        // Ejecutar el comando SQL utilizando el método 'Execsql' con el modo 'SP-PARAM'
        $datosInputs = $db_conn->Execsql("SP-PARAM", $cmdsqlReporte, $params);

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
        $db_conn = new SQLExecute('IGLSQL');

        $cmdsqlReporte = "SELECT * FROM inputs";

        $datosInputs = $db_conn->Execsql("SELECT", $cmdsqlReporte);

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

        // Crear una instancia de SQLExecute con la conexión 'IGLSQL'
        $db_conn = new SQLExecute('IGLSQL');

        // Itera sobre cada elemento en los datos decodificados
        foreach ($data as $item) {
            // Extrae la información de cada elemento
            $idInput = $item['idInput'];
            $id_reporte = $item['idReporte'];
            $orden = $item['orden'];

            // Define el comando SQL almacenado para actualizar la posición del input en el reporte
            $cmdsqlReporte = "EXEC unirReporteInputPosicion ?, ?, ?";
            $params = array($idInput, $id_reporte, $orden);

            // Ejecuta el comando SQL utilizando el método 'Execsql' con el modo 'SP-PARAM'
            $datosInputs = $db_conn->Execsql("SP-PARAM", $cmdsqlReporte, $params);
        }

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

        // Crear una instancia de SQLExecute con la conexión 'IGLSQL'
        $db_conn = new SQLExecute('IGLSQL');

        // Itera sobre cada elemento en los datos decodificados
        foreach ($data as $item) {
            // Extrae la información de cada elemento
            $idInput = $item['idInput'];
            $id_reporte = $item['idReporte'];

            // Define el comando SQL almacenado para eliminar la vinculación entre el input y el reporte
            $cmdsqlReporte = "EXEC eliminarReporteInputPosicion ?, ?";
            $params = array($idInput, $id_reporte);

            // Ejecuta el comando SQL utilizando el método 'Execsql' con el modo 'SP-PARAM'
            $datosInputs = $db_conn->Execsql("SP-PARAM", $cmdsqlReporte, $params);
        }

        // Devuelve la respuesta en formato JSON
        echo json_encode($datosInputs);
    } catch (Exception $e) {
        // Manejar excepciones y devolver un mensaje de error en formato JSON
        echo json_encode(array("error" => $e->getMessage()));
    }
}


//autocomplete del wizard de agregar autoCompletes
if ($_REQUEST['autoCompleteTablas'] == '1') {
    try {
        // Obtiene el término de búsqueda de la solicitud
        $busqueda = $_REQUEST['termn'];

        // Crea una instancia de SQLExecute con la conexión 'IGLSQL'
        $db_conn = new SQLExecute('IGLSQL');

        // Define el comando SQL almacenado para realizar la búsqueda de autocompletar en tablas
        $cmdsqlReporte = "EXEC seletecTablaAutoComplete '$busqueda'";

        // Ejecuta el comando SQL utilizando el método 'Execsql' con el modo 'SP'
        $resp = $db_conn->Execsql("SP", $cmdsqlReporte);

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

        // Crea una instancia de SQLExecute con la conexión 'IGLSQL'
        $db_conn = new SQLExecute('IGLSQL');

        // Define el comando SQL almacenado para obtener las columnas de una tabla específica
        $cmdsqlReporte = "EXEC obtenerColumnas '$nombreTabla', '$busqueda'";

        // Ejecuta el comando SQL utilizando el método 'Execsql' con el modo 'SP'
        $resp = $db_conn->Execsql("SP", $cmdsqlReporte);

        // Devuelve la respuesta en formato JSON
        echo json_encode($resp);
    } catch (Exception $e) {
        // Maneja excepciones y devuelve un mensaje de error en formato JSON
        echo json_encode(['error' => $e->getMessage()]);
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

        // Crea una instancia de SQLExecute con la conexión 'IGLSQL'
        $db_conn = new SQLExecute('IGLSQL');

        // Define el comando SQL almacenado para insertar un nuevo input en la base de datos
        $cmdsqlReporte = "EXEC createInput ?,?,?,?,?,?,?,?,?,?";

        // Define los parámetros del procedimiento almacenado dependiendo del tipo de input
        $params;
        if ($tipo == 'autoComplete') {
            $params = array($nombre, $tipo, $valor, $valorAutocomplete, $valorId, null, null, null, $informacionAdicional, null);
        } else if ($tipo == 'select') {
            $valorAutocomplete = null;
            $valorId = null;
            $params = array($nombre, $tipo, $valor, $valorAutocomplete, $valorId, $valorSelect, $textoSelect, null, $informacionAdicional, null);
        } else if ($tipo == 'checkbox') {
            $valorAutocomplete = null;
            $valorId = null;
            $valor = null;
            $valorSeleccionado = $valorSeleccionado == "true" ? 1 : 0;
            $params = array($nombre, $tipo, $valor, $valorAutocomplete, $valorId, null, null, $valorSeleccionado, $informacionAdicional, null);
        } else if ($tipo == 'date') {
            $valorAutocomplete = null;
            $valorId = null;
            $valor = null;
            $params = array($nombre, $tipo, $valor, $valorAutocomplete, $valorId, null, null, null, $informacionAdicional, $hora);
        } else {
            $valorAutocomplete = null;
            $valorId = null;
            $valor = null;
            $params = array($nombre, $tipo, $valor, $valorAutocomplete, $valorId, null, null, null, $informacionAdicional, null);
        }

        // Ejecuta el comando SQL utilizando el método 'Execsql' con el modo 'SP-PARAM'
        $datosInputs = $db_conn->Execsql("SP-PARAM", $cmdsqlReporte, $params);

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

        // Crea una instancia de SQLExecute con la conexión 'IGLSQL'
        $db_conn = new SQLExecute('IGLSQL');

        // Define el comando SQL para actualizar el input en la base de datos
        $cmdsqlUpdate = "EXEC actualizarInputsReportes ?,?,?,?,?,?,?,?,?,?,?";
        $params;

        // Determina el tipo de input y asigna los parámetros correspondientes
        if ($tipo == 'autoComplete') {
            $params = array(
                $idInput,
                $nombre,
                $tipo,
                $valor,
                $valorAutocomplete,
                $valorId,
                null,
                null,
                null,
                $informacionAdicional,
                null,
            );
        } else if ($tipo == 'select') {
            $valorAutocomplete = null;
            $valorId = null;
            $params = array(
                $idInput,
                $nombre,
                $tipo,
                $valor,
                $valorAutocomplete,
                $valorId,
                $valorSelect,
                $textoSelect,
                null,
                $informacionAdicional,
                null,
            );
        } else if ($tipo == 'checkbox') {
            $valorAutocomplete = null;
            $valorId = null;
            $valor = null;
            $valorSeleccionado = $valorSeleccionado == "true" ? 1 : 0;
            $params = array(
                $idInput,
                $nombre,
                $tipo,
                $valor,
                $valorAutocomplete,
                $valorId,
                null,
                null,
                $valorSeleccionado,
                $informacionAdicional,
                null,
            );
        } else if ($tipo == 'date') {
            $valorAutocomplete = null;
            $valorId = null;
            $valor = null;
            $params = array(
                $idInput,
                $nombre,
                $tipo,
                $valor,
                $valorAutocomplete,
                $valorId,
                null,
                null,
                null,
                $informacionAdicional,
                $hora,
            );
        } else {
            $valorAutocomplete = null;
            $valorId = null;
            $valor = null;
            $params = array(
                $idInput,
                $nombre,
                $tipo,
                $valor,
                $valorAutocomplete,
                $valorId,
                null,
                null,
                null,
                $informacionAdicional,
                null,
            );
        }

        // Ejecuta el comando SQL utilizando el método 'Execsql' con el modo 'SP-PARAM'
        $datosInputs = $db_conn->Execsql("SP-PARAM", $cmdsqlUpdate, $params);

        // Devuelve el resultado de la actualización en formato JSON
        echo json_encode($valorSeleccionado);
    } catch (Exception $e) {
        // Maneja excepciones y devuelve un mensaje de error en formato JSON
        echo json_encode(array("error" => $e->getMessage()));
    }
}


//obtiene todos los inputs
if ($_REQUEST['obtenerInputs'] == "1") {
    try {
        // Crea una instancia de SQLExecute con la conexión 'IGLSQL'
        $db_conn = new SQLExecute('IGLSQL');

        // Define el comando SQL para seleccionar todos los inputs de la tabla 'INPUTS'
        $cmdsqlReporte = "SELECT * FROM INPUTS";

        // Ejecuta el comando SQL utilizando el método 'Execsql' con el modo 'SELECT'
        $datosInputs = $db_conn->Execsql("SELECT", $cmdsqlReporte);

        // Devuelve los datos de los inputs en formato JSON
        echo json_encode($datosInputs);
    } catch (Exception $e) {
        // Maneja excepciones y devuelve un mensaje de error en formato JSON
        echo json_encode(array("error" => $e->getMessage()));
    }
}


?>