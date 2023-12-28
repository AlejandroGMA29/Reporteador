<?php



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