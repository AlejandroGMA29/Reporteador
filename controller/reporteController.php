<?php
  session_start();
  require_once("/aleLaps/Reporteador/model/mReportesIGL.php");
  $Reporte = new mReportesIGL();
  
// Verifica si la solicitud es para obtener datos de reportes
if ($_REQUEST['obtenerDatosReportes'] == "1") {
    // Inicia la sesión
    $datosReporte = $Reporte->obtenerDatosReportes();
    // Devuelve los datos de los reportes en formato JSON
    echo json_encode($datosReporte);
}


// Verifica si la solicitud es para eliminar el archivo Excel enviado
if ($_REQUEST["salida"] == "1") {
    // Obtiene los valores JSON de los elementos a eliminar
    $valoresJson = $_REQUEST["elementos"];
    // Decodifica los valores JSON a un array de PHP
    $valoresArray = json_decode($valoresJson);

    // Ruta donde se encuentran los archivos a eliminar
    //$ruta_archivo = "/var/www/html/proyecto/public_html/SistemaN/Modulos/Reporteador/Reportes/";
    $ruta_archivo = "/var/www/html/aleLaps/Reporteador/Reportes/";
    
    $resp = $Reporte->eliminarArchivosExcel($valoresArray,$ruta_archivo);
}


// Verifica si la solicitud es para obtener la información del reporte y generar un Excel para cada respuesta
if ($_REQUEST["tabla"] == "1") {
    try {
        // Obtiene los valores JSON y los decodifica a arrays de PHP
        $valoresJson = $_REQUEST["ValoresArray"];
        $valoresArray = json_decode($valoresJson);
        $titulosJSON = $_REQUEST["titulosArray"];
        $titulosArray = json_decode($titulosJSON);

        // Obtiene el ID del reporte
        $id_reporte = $_REQUEST['id_reporte'];

        $respuesta = $Reporte->cargarReporte($valoresArray,$titulosArray,$id_reporte);
        // Devuelve la respuesta en formato JSON
        echo json_encode($respuesta);
    } catch (Exception $e) {
        // En caso de error, devuelve un mensaje de error en formato JSON
        echo json_encode(['error' => $e->getMessage(), "info" => $datos]);
    }
}


//devuelve el usuario en sesion 
if ($_REQUEST["sesion"] == "1") {
    session_start();
    $usuario = $_SESSION["Usuario"];
    echo json_encode($usuario);
}

//agrega un reporte nuevo a la bd
if ($_REQUEST['agregarReporte'] == "1") {
    try {
        // Obtener datos del formulario
        $nombreRepo = $_REQUEST['nombreRepo'];
        $SPRepor = $_REQUEST['SPRepor'];
        $activo = $_REQUEST['activo'];
        $pesado = $_REQUEST['pesado'];

        $datosInputs = $Reporte->agregarReporte($nombreRepo,$SPRepor,$activo,$pesado);

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

        $datosInputs = $Reporte->modificarReporte($idRepo,$nombreRepo,$SPRepor,$activo,$pesado);

        // Devolver la respuesta en formato JSON
        echo json_encode($datosInputs);
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
        $datosReporte = $Reporte->datosReporte($idReporte);
        
        // Devolver la respuesta en formato JSON
        echo json_encode($datosReporte); // Asumiendo que deseas imprimir los resultados como JSON
    } catch (Exception $e) {
        // Manejar excepciones y devolver un mensaje de error en formato JSON
        echo json_encode(array("error" => $e->getMessage()));
    }
}


?>