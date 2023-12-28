<?php
require_once('/var/www/html/proyecto/public_html/CexecuteSQL.php');

require_once('/var/www/html/proyecto/resources/excel/Classes/PHPExcel.php');
require_once('/var/www/html/proyecto/resources/excel/Classes/PHPExcel/Writer/Excel2007.php');

require_once('/var/www/html/include/PHPMailer/class.phpmailer.php');   // falta revisar localizacion en server.
require_once('/var/www/html/include/PHPMailer/class.smtp.php');  // falta revisar localizacion en server.

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

// Verifica si la solicitud es para cargar los inputs del reporte seleccionado
if ($_REQUEST["cargarInputs"] == "1") {
    try {
        // Obtiene el ID del reporte seleccionado desde la solicitud
        $id_reporte = $_REQUEST["idReporte"];

        // Inicia la conexión a la base de datos utilizando SQLExecute
        $db_conn = new SQLExecute('IGLSQL');

        // Comando SQL almacenado para seleccionar los inputs de un reporte
        $cmdsqlReporte = "EXEC selectInputReportes ?";

        // Parámetros para el comando SQL almacenado
        $params = array($id_reporte);

        // Ejecuta el comando SQL almacenado con los parámetros
        $datosInputs = $db_conn->Execsql("SP-PARAM", $cmdsqlReporte, $params);

        // Devuelve los datos de los inputs en formato JSON
        echo json_encode($datosInputs);
    } catch (Exception $e) {
        // En caso de error, devuelve un mensaje de error en formato JSON
        echo json_encode(array("error" => $e->getMessage()));
    }
}

// Verifica si la solicitud es para realizar un autoCompletado en los catálogos
if ($_REQUEST['autoComplete'] == '1') {
    try {
        // Obtiene los parámetros de la solicitud
        $tabla = $_REQUEST['tabla'];
        $id = $_REQUEST['id'];
        $valor = $_REQUEST['valor'];
        $busqueda = $_REQUEST['termn'];

        // Inicia la conexión a la base de datos utilizando SQLExecute
        $db_conn = new SQLExecute('IGLSQL');

        // Comando SQL almacenado para realizar el autoCompletado en el reporteador
        $cmdsqlReporte = "EXEC autoCompleteReporteador ?,?,?,?";

        // Parámetros para el comando SQL almacenado
        $params = array($busqueda, $tabla, $id, $valor);

        // Ejecuta el comando SQL almacenado con los parámetros
        $resp = $db_conn->Execsql("SP-PARAM", $cmdsqlReporte, $params);

        // Devuelve la respuesta del autoCompletado en formato JSON
        echo json_encode($resp);
    } catch (Exception $e) {
        // En caso de error, devuelve un mensaje de error en formato JSON
        echo json_encode(['error' => $e->getMessage()]);
    }
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

//devuelve el usuario en sesion 
if ($_REQUEST["sesion"] == "1") {
    session_start();
    $usuario = $_SESSION["Usuario"];
    echo json_encode($usuario);
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

function crearExcel($nombre, $datos)
{
    // Reemplaza espacios en blanco en el nombre
    $nombreF = str_replace(' ', '', $nombre);

    // Crea un nuevo objeto PHPExcel
    $objPHPExcel = new PHPExcel();

    // Obtiene la fecha y hora actual
    $dt = new DateTime();
    $fechaHora = $dt->format('Y-m-d' . '_' . 'H' . 'i' . 's');
    $fechaReporte = $dt->format('d/m/Y' . ' ' . 'h' . ':' . 'i' . ' ' . 'A');

    // Establece el título y la fecha de generación en el Excel
    $objPHPExcel->getActiveSheet()->setCellValue('E2', $nombre);
    $objPHPExcel->getActiveSheet()->setCellValue('E3', 'Fecha de Generación: ' . $fechaReporte);

    // Establece estilos para el título y la fecha
    $tituloReporte = array(
        'font' => array(
            'bold' => true
        )
    );
    $fechaGen = array(
        'font' => array(
            'size' => 10
        )
    );
    $objPHPExcel->getActiveSheet()->getStyle('E2')->applyFromArray($tituloReporte);
    $objPHPExcel->getActiveSheet()->getStyle('E3')->applyFromArray($fechaGen);

    // Inserta el logo (imagen) en el Excel
    $gdImage = imagecreatefromjpeg('logoIGL.jpg');
    $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
    $objDrawing->setName('Logo IGL');
    $objDrawing->setDescription('Logo');
    $objDrawing->setImageResource($gdImage);
    $objDrawing->setCoordinates('A1');
    $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
    $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
    $objDrawing->setHeight(60);
    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

    // Formateo del cuerpo del proyecto
    // Obtiene los encabezados (claves) del primer elemento de los datos
    $primerElemento = $datos[1][0];
    $encabezados = array_keys($primerElemento);
    $total = count($datos[1]);
    $totalColumnas = count($encabezados);

    // Crea encabezados en el archivo Excel
    for ($col = 0; $col < $totalColumnas; $col++) {
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, 8, $encabezados[$col]);
    }

    // Llena los datos en el archivo Excel
    $fila = 9; // Comienza desde la segunda fila
    foreach ($datos[1] as $dato) {
        for ($col = 0; $col < $totalColumnas; $col++) {
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $fila, $dato[$encabezados[$col]]);
        }
        $fila++;
    }

    // Establece estilos para la tabla de datos
    // (Estilos para encabezados)
    $styleArray = array(
        'font' => array(
            'bold' => true,
            'color' => array('rgb' => 'FFFFFF'), // Color de texto blanco
        ),
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => '0070C0'), // Color de fondo azul
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, // Alinea verticalmente al centro
        ),
    );

    // Aplica estilos a la primera fila (encabezados)
    $objPHPExcel->getActiveSheet()->getStyle('A8:' . PHPExcel_Cell::stringFromColumnIndex($totalColumnas - 1) . '8')->applyFromArray($styleArray);

    // Establece estilos para los datos
    $styleArrayData = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, // Alinea verticalmente al centro
        ),
    );

    // Aplica estilos a los datos
    $objPHPExcel->getActiveSheet()->getStyle('A9:' . PHPExcel_Cell::stringFromColumnIndex($totalColumnas - 1) . ($fila))->applyFromArray($styleArrayData);

    // Establece un patrón blanco/gris en las filas
    for ($i = 9; $i <= $fila - 1; $i++) {
        if ($i % 2 == 0) { // Fila par
            $objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':' . PHPExcel_Cell::stringFromColumnIndex($totalColumnas - 1) . $i)
                ->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFFFF'); // Blanco
        } else { // Fila impar
            $objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':' . PHPExcel_Cell::stringFromColumnIndex($totalColumnas - 1) . $i)
                ->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('E0E0E0'); // Gris
        }
    }

    // Ajusta automáticamente el ancho de las columnas
    $ultimaColumna = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
    $columnaActual = 'A';

    while ($columnaActual != $ultimaColumna) {
        $objPHPExcel->getActiveSheet()->getColumnDimension($columnaActual)->setAutoSize(true);
        $columnaActual = incrementarLetra($columnaActual);
    }

    // Asegura que también ajuste la última columna
    $objPHPExcel->getActiveSheet()->getColumnDimension($ultimaColumna)->setAutoSize(true);

    // Establece el título del archivo Excel y la ruta de archivo
    $objPHPExcel->getActiveSheet(0)->setTitle($nombre);
    $ruta_archivo = "/var/www/html/aleLaps/Reporteador/Reportes/$nombreF" . "_" . "$fechaHora.xlsx";

    // Crea el escritor de PHPExcel y guarda el archivo Excel
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save($ruta_archivo);

    // Verifica si el archivo se ha creado exitosamente
    if (file_exists($ruta_archivo)) {
        $error = 0;
        $mensaje = "Archivo fue creado";
        $datos = $ruta_archivo;
    } else {
        $error = 1;
        $mensaje = "Archivo no creado";
        $datos = 0;
    }

    // Nombre del archivo
    $nombrearchivo = "$nombreF" . "_" . "$fechaHora.xlsx";

    // Ruta del archivo para ser utilizada en la respuesta
    $ruta_archivo = "../aleLaps/Reporteador/Reportes/$nombreF" . "_" . "$fechaHora.xlsx";

    // Construye la respuesta que se enviará
    $resp = [
        "error" => $error,
        "mensaje" => $mensaje,
        "datos" => $ruta_archivo,
        "Nombre" => $nombrearchivo,
    ];

    // Devuelve la respuesta
    return $resp;
}


function incrementarLetra($letra)
{
    // Obtiene la longitud de la cadena de letras
    $longitud = strlen($letra);

    // Inicializa la variable de desbordamiento
    $overflow = true;

    // Itera sobre cada carácter de la cadena de letras de derecha a izquierda
    for ($i = $longitud - 1; $i >= 0; $i--) {
        $caracter = $letra[$i];

        // Verifica si hay desbordamiento
        if ($overflow) {
            // Si el carácter es 'Z', reinicia a 'A'
            if ($caracter == 'Z') {
                $letra[$i] = 'A';
            } else {
                // Incrementa el carácter en uno
                $letra[$i] = chr(ord($caracter) + 1);
                $overflow = false;
            }
        }
    }

    // Si hay desbordamiento, agrega 'A' al principio de la cadena
    if ($overflow) {
        $letra = 'A' . $letra;
    }

    // Devuelve la cadena de letras incrementada
    return $letra;
}

?>