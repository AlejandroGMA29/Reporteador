<?php
// Ubicación de la carpeta de los reportes creados
$path = '/var/www/html/aleLaps/Reporteador/Reportes/';
// Arreglo con todos los nombres de los archivos
$files = array_diff(scandir($path), array('.', '..'));

// Obtener la fecha y hora actual
$currentDateTime = new DateTime();

foreach ($files as $file) {
    $filePath = $path . $file;

    // Obtener la fecha y hora de creación del archivo
    $fileDateTime = new DateTime();
    $fileDateTime->setTimestamp(filectime($filePath));

    // Calcular la diferencia entre la fecha y hora actual y la del archivo
    $interval = $fileDateTime->diff($currentDateTime);

    // Definir el umbral para eliminar el archivo (por ejemplo, más de 2 días)
    $umbralDias = 2;

    // Verificar si la diferencia supera el umbral
    if ($interval->days > $umbralDias || ($interval->days == $umbralDias && $fileDateTime < $currentDateTime)) {
        // Eliminar el archivo
        unlink($filePath);
        echo "Archivo eliminado: " . $file . "<br>";
    }
}
?>