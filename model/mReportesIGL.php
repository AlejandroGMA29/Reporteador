<?php
// Requiere la clase SQLExecute para la conexión a la base de datos
require_once('/var/www/html/proyecto/public_html/CexecuteSQL.php');

require_once('/var/www/html/proyecto/resources/excel/Classes/PHPExcel.php');
require_once('/var/www/html/proyecto/resources/excel/Classes/PHPExcel/Writer/Excel2007.php');

require_once('/var/www/html/include/PHPMailer/class.phpmailer.php');   // falta revisar localizacion en server.
require_once('/var/www/html/include/PHPMailer/class.smtp.php');  // falta revisar localizacion en server.

class mReportesIGL
{
    // Conexión a la base de datos
    private $db_conn;

    // Constructor que establece la conexión a la base de datos
    public function __construct()
    {
        // Crear una instancia de SQLExecute con la conexión 'IGLSQL'
        $this->db_conn = new SQLExecute('IGLSQL');
    }

    // Función para obtener datos de reportes
    public function obtenerDatosReportes() {
        // Inicia la sesión
        session_start();

        // Consulta SQL para obtener todos los reportes
        $cmdsqlReporte = "SELECT * FROM catalogoReportes";

        // Si el usuario no es admin, filtra solo los reportes activos
        $concatenacion = $_SESSION["Usuario"] == 1 ? "" : " WHERE activo = 1";

        // Agrega la condición al comando SQL
        $cmdsqlReporte .= $concatenacion . " ORDER BY descripcion ASC";

        // Ejecuta la consulta SQL
        $datosReporte = $this->db_conn->Execsql("SELECT", $cmdsqlReporte);

        // Devuelve los datos de los reportes en formato JSON
        return $datosReporte;
    }

    // Función para eliminar archivos Excel
    public function eliminarArchivosExcel($valoresArray, $ruta_archivo) {
        // Array para almacenar mensajes de respuesta
        $resp = array();

        // Itera sobre cada valor en el array
        foreach ($valoresArray as $valores) {
            // Construye la ruta completa del archivo a eliminar
            $ruta_completa = $ruta_archivo . $valores;

            // Intenta eliminar el archivo
            if (unlink($ruta_completa)) {
                $resp[] = array(
                    "mensaje" => 'Se eliminó el archivo correctamente',
                    "datos" => $ruta_archivo,
                    "Nombre" => $valores,
                );
            } else {
                $resp[] = array(
                    "mensaje" => 'No se eliminó el archivo',
                    "datos" => $ruta_archivo,
                    "Nombre" => $valores,
                );
            }
        }

        // Devuelve el array de respuestas
        return $resp;
    }

    public function cargarReporte($valoresArray, $titulosArray, $id_reporte) {
        // Consulta SQL para obtener información del reporte
        $cmdsqlReporte = "SELECT * FROM catalogoReportes WHERE id_reporte = $id_reporte";
        $datosReporte = $this->db_conn->Execsql("SELECT", $cmdsqlReporte);

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
        $datos = $this->db_conn->Execsql('SP-PARAM', $resultcmdsql, $params);

        // Crea el archivo Excel y obtiene detalles sobre el proceso
        $resp = crearExcel($nombre, $datos); // Asegúrate de tener la función crearExcel implementada

        // Nombre del archivo Excel generado
        $nombreExcel = $resp['Nombre'];

        // Respuesta final que se enviará como JSON
        $respuesta = [
            "datos" => $datos,
            "resp" => $resp,
            "sp" => $cmdsql,
            "error" => 0
        ];
        return $respuesta;
    }

    public function agregarReporte($nombreRepo, $SPRepor, $activo, $pesado) {
        // Definir el comando SQL para insertar un nuevo reporte
        $cmdsqlReporte = "exec insertCatalogoReporte ?, ?, ?, ?";

        // Preparar los parámetros para el comando SQL
        $params = array($nombreRepo, $SPRepor, $activo, $pesado);

        // Ejecutar el comando SQL utilizando el método 'Execsql' con el modo 'SP-PARAM'
        $datosInputs = $this->db_conn->Execsql("SP-PARAM", $cmdsqlReporte, $params);
        return $datosInputs;
    }

    public function modificarReporte($idRepo, $nombreRepo, $SPRepor, $activo, $pesado) {
        // Definir el comando SQL para actualizar un reporte en la base de datos
        $cmdsqlReporte = "exec updateCatalogoReporte ?, ?, ?, ?, ?";

        // Preparar los parámetros para el comando SQL
        $params = array($idRepo, $nombreRepo, $SPRepor, $activo, $pesado);

        // Ejecutar el comando SQL utilizando el método 'Execsql' con el modo 'SP-PARAM'
        $datosInputs = $this->db_conn->Execsql("SP-PARAM", $cmdsqlReporte, $params);
        return $datosInputs;
    }

    public function datosReporte($idReporte) {
        // Definir el comando SQL para seleccionar los datos específicos del reporte con el ID especificado
        $cmdsqlReporte = "SELECT * FROM catalogoReportes WHERE id_reporte = {$idReporte}";

        // Ejecutar el comando SQL utilizando el método 'Execsql' con el modo 'SELECT'
        $datosReporte = $this->db_conn->Execsql("SELECT", $cmdsqlReporte);
        return $datosReporte;
    }


    function crearExcel($nombre, $datos){
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
}
?>
