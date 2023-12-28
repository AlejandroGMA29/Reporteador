<?php



// Verifica si la solicitud es para obtener datos de reportes
if ($_REQUEST['obtenerDatosReportes'] == "1") {
    // Inicia la sesión
    session_start();

    // Conexión a la base de datos usando SQLExecute (asumo que ya está definido)
    $db_conn = new SQLExecute('IGLSQL');

    // Consulta SQL para obtener todos los reportes
    $cmdsqlReporte = "SELECT * FROM catalogoReportes";

    // Si el usuario no es admin, filtra solo los reportes activos
    $concatenacion = $_SESSION["Usuario"] == 1 ? "" : " WHERE activo = 1";

    // Agrega la condición al comando SQL
    $cmdsqlReporte = $cmdsqlReporte . $concatenacion . " ORDER BY descripcion ASC";

    // Ejecuta la consulta SQL
    $datosReporte = $db_conn->Execsql("SELECT", $cmdsqlReporte);

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

    // Itera sobre cada valor en el array
    foreach ($valoresArray as $valores) {
        // Construye la ruta completa del archivo a eliminar
        $ruta_completa = $ruta_archivo . $valores;

        // Intenta eliminar el archivo
        if (unlink($ruta_completa)) {
            $resp = [
                "mensaje" => 'Se eliminó el archivo correctamente',
                "datos" => $ruta_archivo,
                "Nombre" => $valores,
            ];
        } else {
            $resp = [
                "mensaje" => 'No se eliminó el archivo',
                "datos" => $ruta_archivo,
                "Nombre" => $valores,
            ];
        }
    }
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

        // Inicia la conexión a la base de datos utilizando SQLExecute
        $db_conn = new SQLExecute('IGLSQL');

        // Consulta SQL para obtener información del reporte
        $cmdsqlReporte = "SELECT * FROM catalogoReportes WHERE id_reporte = $id_reporte";
        $datosReporte = $db_conn->Execsql("SELECT", $cmdsqlReporte);

        // Extrae la información necesaria del catálogo de reportes
        $sp_reporte = $datosReporte[0]["sp_reporte"];
        $nombre = $datosReporte[0]["descripcion"];

        // Construye el comando SQL para ejecutar el stored procedure del reporte
        $cmdsql = "EXEC $sp_reporte ";

        // Agrega placeholders para los parámetros del stored procedure
        foreach ($valoresArray as &$valor) {
            $cmdsql .= ' ?,';
        }

        // Elimina la coma extra al final del comando SQL
        $resultcmdsql = substr($cmdsql, 0, -1);

        // Parámetros para el stored procedure
        $params = [...$valoresArray];

        // Ejecuta el stored procedure y obtiene los datos del reporte
        $datos = $db_conn->Execsql('SP-PARAM', $resultcmdsql, $params);

        // Crea el archivo Excel y obtiene detalles sobre el proceso
        $resp = crearExcel($nombre, $datos);

        // Nombre del archivo Excel generado
        $nombreExcel = $resp['Nombre'];

        // Respuesta final que se enviará como JSON
        $respuesta = [
            "datos" => $datos,
            "resp" => $resp,
            "sp" => $cmdsql,
            "error" => 0
        ];

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



?>