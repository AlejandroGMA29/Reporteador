<?php
// Verifica si la solicitud es para crear un Excel "pesado" y enviarlo como enlace por correo
if ($_REQUEST["enviarCorreoPesado"] == "1") {
    try {
        // Obtención de parámetros de la solicitud
        $valoresJson = $_REQUEST["ValoresArray"];
        $valoresArray = json_decode($valoresJson);

        $titulosJSON = $_REQUEST["titulosArray"];
        $titulosArray = json_decode($titulosJSON);

        $id_reporte = $_REQUEST['id_reporte'];
        $idCliente = $_REQUEST['IDCliente'];
        $datosCLiente = $_REQUEST['datosCliente'];
        $id_reporte = $_REQUEST['idReporte'];

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

        // Crea el archivo Excel "pesado" y obtiene detalles sobre el proceso
        $resp = crearExcel($nombre, $datos);
        $nombreExcel = $resp["Nombre"];

        // Consulta SQL para obtener los correos asociados al cliente y al reporte
        $cmdsql = "EXEC selectCorreosCliente '$idCliente', $id_reporte;";
        $datosMails = $db_conn->Execsql('SP', $cmdsql);

        // Construye la ruta de descarga del archivo Excel
        $rutaExcelDescarga = "http://192.168.80.220:8080/proyecto/aleLaps/Reporteador/Reportes/{$nombreExcel}";

        // Crea el objeto de correo electrónico
        $mail = crearCorreo($nombre, $valoresArray, $titulosArray, $rutaExcelDescarga);

        // Agrega destinatarios al correo
        foreach ($datosMails as $key => $value) {
            $mail->addAddress($value['correo']);
        }

        // Envía el correo electrónico
        $mail->send();

        // Respuesta final que se enviará como JSON
        $respuesta = [
            'data' => $nombreExcel,
            'prueba' => $mail,
            'message' => 'Message has been sent'
        ];

    } catch (Exception $e) {
        // En caso de error, devuelve un mensaje de error en formato JSON
        echo json_encode(['error' => $e->getMessage(), "info" => $datos,]);
    }
}

// Verifica si la solicitud es para obtener los correos dependiendo del cliente y reporte especificado
if ($_REQUEST["Correos"] == "1") {
    // Obtiene los parámetros de la solicitud
    $IdCliente = $_REQUEST['IDCliente'];
    $id_reporte = $_REQUEST['idReporte'];

    // Inicia la conexión a la base de datos utilizando SQLExecute
    $db_conn = new SQLExecute('IGLSQL');

    // Comando SQL almacenado para seleccionar correos según cliente y reporte
    $cmdsql = "EXEC selectCorreosCliente ?,?;";

    // Parámetros para el comando SQL almacenado
    $params = array($IdCliente, $id_reporte);

    // Ejecuta el comando SQL almacenado con los parámetros
    $datos = $db_conn->Execsql('SP-PARAM', $cmdsql, $params);

    // Devuelve los datos de los correos en formato JSON
    echo json_encode($datos);
}

// Verifica si la solicitud es para enviar un correo normal con un archivo adjunto (Excel)
if ($_REQUEST["EnviarCorreos"] == "1") {
    try {
        // Obtención de parámetros de la solicitud
        $idCliente = $_REQUEST['IDCliente'];
        $datosCLiente = $_REQUEST['datosCliente'];
        $id_reporte = $_REQUEST['idReporte'];
        $nombreExcel = $_REQUEST['excel'];

        // Decodificación de datos JSON
        $valoresJson = $_REQUEST["ValoresArray"];
        $valoresArray = json_decode($valoresJson);
        $titulosJSON = $_REQUEST["titulosArray"];
        $titulosArray = json_decode($titulosJSON);

        // Inicia la conexión a la base de datos utilizando SQLExecute
        $db_conn = new SQLExecute('IGLSQL');

        // Consulta SQL para obtener correos asociados al cliente y al reporte
        $cmdsql = "EXEC selectCorreosCliente '$idCliente', $id_reporte;";
        $datosMails = $db_conn->Execsql('SP', $cmdsql);

        // Consulta SQL para obtener información del reporte
        $cmdsqlReporte = "SELECT * FROM catalogoReportes WHERE id_reporte = $id_reporte";
        $datosReporte = $db_conn->Execsql("SELECT", $cmdsqlReporte);
        $nombre = $datosReporte[0]["descripcion"];

        // Ruta del archivo Excel a adjuntar
        $rutaExcel = "/var/www/html/aleLaps/Reporteador/Reportes/{$nombreExcel}";

        // Crea el objeto de correo electrónico
        $mail = crearCorreo($nombre, $valoresArray, $titulosArray, "");

        // Agrega destinatarios al correo
        foreach ($datosMails as $key => $value) {
            $mail->addAddress($value['correo']);
        }

        // Adjunta el archivo Excel al correo
        $mail->addAttachment($rutaExcel, $nombreExcel);

        // Envía el correo electrónico
        $mail->send();

        // Respuesta final que se enviará como JSON
        $respuesta = [
            'data' => $nombreExcel,
            'prueba' => $mail,
            'message' => 'Message has been sent'
        ];

        echo json_encode($respuesta);
    } catch (Exception $e) {
        // En caso de error, devuelve un mensaje de error en formato JSON
        echo json_encode($e->getMessage());
    }
}

// Verifica si la solicitud es para añadir correo electrónico a un cliente y reporte
if ($_REQUEST['insertCorreo'] == "1") {
    // Obtiene los parámetros de la solicitud
    $IdCliente = $_REQUEST['IDCliente'];
    $Correo = $_REQUEST['Correo'];
    $id_reporte = $_REQUEST['idReporte'];

    // Inicia la conexión a la base de datos utilizando SQLExecute
    $db_conn = new SQLExecute('IGLSQL');

    // Comando SQL almacenado para insertar correo electrónico asociado al cliente y reporte
    $cmdsql = "EXEC insertCorreos ?,?,?;";

    // Parámetros para el comando SQL almacenado
    $params = array($IdCliente, $Correo, $id_reporte);

    // Ejecuta el comando SQL almacenado con los parámetros
    $datos = $db_conn->Execsql('SP-PARAM', $cmdsql, $params);

    // Construye la respuesta que se enviará como JSON
    $resp = [
        "error" => "ocurrió un error",
        "datos" => $datos,
    ];

    // Devuelve la respuesta en formato JSON
    echo json_encode($resp);
}

// Verifica si la solicitud es para eliminar un correo asociado a un cliente y reporte
if ($_REQUEST["eliminarCorreo"] == "1") {
    // Obtiene los parámetros de la solicitud
    $IdCliente = $_REQUEST['IdCliente'];
    $IDCorreo = $_REQUEST['Correo'];
    $id_reporte = $_REQUEST['idReporte'];

    // Inicia la conexión a la base de datos utilizando SQLExecute
    $db_conn = new SQLExecute('IGLSQL');

    // Comando SQL almacenado para eliminar un correo asociado al cliente y reporte
    $cmdsql = "EXEC eliminarDestinatarioCliente ?,?,?;";

    // Parámetros para el comando SQL almacenado
    $params = array($IdCliente, $IDCorreo, $id_reporte);

    // Ejecuta el comando SQL almacenado con los parámetros
    $datos = $db_conn->Execsql('SP-PARAM', $cmdsql, $params);

    // Construye la respuesta que se enviará como JSON
    $resp = [
        "datos" => $datos,
    ];

    // Devuelve la respuesta en formato JSON
    echo json_encode($resp);
}

// Verifica si la solicitud es para realizar un autoCompletado de clientes seleccionables
if ($_REQUEST['autoCompleteCliente'] == '1') {

    // Obtiene los parámetros de la solicitud
    $busqueda = $_REQUEST['termn'];
    $idReporte = $_REQUEST['id'];

    // Inicia la conexión a la base de datos utilizando SQLExecute
    $db_conn = new SQLExecute('IGLSQL');

    // Comando SQL almacenado para seleccionar clientes por nombre
    $cmdsqlReporte = "EXEC selectPorNombreCliente '$busqueda'";

    // Ejecuta el comando SQL almacenado
    $resp = $db_conn->Execsql("SP", $cmdsqlReporte);

    // Devuelve la respuesta del autoCompletado de clientes en formato JSON
    echo json_encode($resp);
}


function crearCorreo($nombre, $datos, $encabezados, $link)
{
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host = "smtp.office365.com";
    $mail->SMTPAuth = true;
    $mail->Username = "avisosiglnld@infinitogl.com";
    $mail->Password = "R!eD[q~ydKp^/3;z";
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->SetFrom("avisosiglnld@infinitogl.com");

    $mail->isHTML(true);
    $mail->Subject = "Alerta de notificacion de reporte: $nombre";

    // Construir el cuerpo del correo dinámicamente
    $tableRows = '';

    $tableRows .= "<tr>";
    foreach ($encabezados as $encabezado) {
        $tableRows .= "<th style='padding: 10px;'>$encabezado</th>";
    }
    $tableRows .= "</tr>";

    foreach ($datos as $dato) {
        $tableRows .= "<td style='padding: 10px;'>{$dato}</td>";
    }

    $button = "";
    $expirationDateTime = date('Y-F-d H:i:s', strtotime('+2 days'));

    if ($link != "") {
        $button = "<a href='{$link}'><button type='button'>¡Haz clic aqui para descargar el reporte!</button></a>";
    }

    // Agregar la nota de vencimiento con hora
    $expirationNote = "El enlace vencera el dia: $expirationDateTime";

    // Construir el cuerpo del correo con las filas de la tabla dinámica y la nota de vencimiento con hora
    $mail->Body = "
    <div>
    <div>
    <h3>Reporte: {$nombre}</h3>
    </div>
    <br>
    <table border='1' style='border-collapse: collapse;'>
        <tbody>
            $tableRows
        </tbody>
    </table>
    <BR>
    <div>
    $button
    </div>
    <div>
    $expirationNote
    </div>
    </div>";

    $mail->AltBody = 'Recibiste el reporte, ¡cooper!';

    return $mail;
}

?>