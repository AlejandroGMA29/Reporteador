<?php
// Requiere la clase SQLExecute para la conexión a la base de datos
require_once('/var/www/html/proyecto/public_html/CexecuteSQL.php');

require_once('/var/www/html/proyecto/resources/excel/Classes/PHPExcel.php');
require_once('/var/www/html/proyecto/resources/excel/Classes/PHPExcel/Writer/Excel2007.php');

require_once('/var/www/html/include/PHPMailer/class.phpmailer.php');   // falta revisar localizacion en server.
require_once('/var/www/html/include/PHPMailer/class.smtp.php');  // falta revisar localizacion en server.


class mInpt
{
    private $db_conn;

    // Constructor que establece la conexión a la base de datos
    public function __construct()
    {
        // Crear una instancia de SQLExecute con la conexión 'IGLSQL'
        $this->db_conn = new SQLExecute('IGLSQL');
    }

    // Obtener inputs
    public function obtenerInputs()
    {
        // Define el comando SQL para seleccionar todos los inputs de la tabla 'INPUTS'
        $cmdsqlInputs = "SELECT * FROM INPUTS";

        // Ejecuta el comando SQL utilizando el método 'Execsql' con el modo 'SELECT'
        $datosInputs = $this->db_conn->Execsql("SELECT", $cmdsqlInputs);
        return $datosInputs;
    }

    // Cargar inputs de un reporte
    public function cargarInputs($id_reporte)
    {
        // Definir el comando SQL almacenado para seleccionar inputs de un reporte
        $cmdsqlReporte = "EXEC selectInputReportes ?";

        // Preparar los parámetros para el comando SQL almacenado
        $params = array($id_reporte);

        // Ejecutar el comando SQL utilizando el método 'Execsql' con el modo 'SP-PARAM'
        $datosInputs = $this->db_conn->Execsql("SP-PARAM", $cmdsqlReporte, $params);
        return $datosInputs;
    }

    public function obtenerInputPorId($idInput)
    {
        // Definir el comando SQL para seleccionar los inputs con el ID especificado
        $cmdsqlReporte = "SELECT * FROM Inputs WHERE idInput = {$idInput}";

        // Ejecutar el comando SQL utilizando el método 'Execsql' con el modo 'SELECT'
        $datosReporte = $this->db_conn->Execsql("SELECT", $cmdsqlReporte);
        return $datosReporte;
    }
    public function cargarInputsFuera($id_reporte)
    {
        // Definir el comando SQL almacenado para seleccionar inputs fuera de un reporte
        $cmdsqlReporte = "EXEC selectInputsFueraReporte ?";

        // Preparar los parámetros para el comando SQL almacenado
        $params = array($id_reporte);

        // Ejecutar el comando SQL utilizando el método 'Execsql' con el modo 'SP-PARAM'
        $datosInputs = $this->db_conn->Execsql("SP-PARAM", $cmdsqlReporte, $params);

        return $datosInputs;
    }
    public function cargarTodosInputs()
    {
        // Comando SQL para seleccionar todos los inputs de la tabla 'inputs'
        $cmdsqlReporte = "SELECT * FROM inputs";

        // Ejecutar el comando SQL utilizando el método 'Execsql' con el modo 'SELECT'
        $datosInputs = $this->db_conn->Execsql("SELECT", $cmdsqlReporte);
        return $datosInputs;
    }
    public function actualizarInformacion($data)
    {
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
            $datosInputs = $this->db_conn->Execsql("SP-PARAM", $cmdsqlReporte, $params);
        }
        return $datosInputs;
    }

    public function actualizarInformacionEliminar($data)
    {
        // Itera sobre cada elemento en los datos decodificados
        foreach ($data as $item) {
            // Extrae la información de cada elemento
            $idInput = $item['idInput'];
            $id_reporte = $item['idReporte'];

            // Define el comando SQL almacenado para eliminar la vinculación entre el input y el reporte
            $cmdsqlReporte = "EXEC eliminarReporteInputPosicion ?, ?";
            $params = array($idInput, $id_reporte);

            // Ejecuta el comando SQL utilizando el método 'Execsql' con el modo 'SP-PARAM'
            $datosInputs = $this->db_conn->Execsql("SP-PARAM", $cmdsqlReporte, $params);
        }

        // Devuelve la respuesta en formato JSON
        return $datosInputs;
    }

    public function insertInputs($nombre, $tipo, $valor, $valorAutocomplete, $valorId, $valorSelect, $textoSelect, $valorSeleccionado, $informacionAdicional, $hora)
    {
        // Define el comando SQL almacenado para insertar un nuevo input en la base de datos
        $cmdsqlReporte = "EXEC createInput ?,?,?,?,?,?,?,?,?,?";

        // Define los parámetros del procedimiento almacenado dependiendo del tipo de input
        $params = "";
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
        $datosInputs = $this->db_conn->Execsql("SP-PARAM", $cmdsqlReporte, $params);
        return $datosInputs;
    }
    public function updateInputs($idInput, $nombre, $tipo, $valor, $valorAutocomplete, $valorId, $valorSelect, $textoSelect, $valorSeleccionado, $informacionAdicional, $hora)
    {
        // Define el comando SQL para actualizar el input en la base de datos
        $cmdsqlUpdate = "EXEC actualizarInputsReportes ?,?,?,?,?,?,?,?,?,?,?";
        $params = "";

        // Determina el tipo de input y asigna los parámetros correspondientes
        if ($tipo == 'autoComplete') {
            $params = array(
                $idInput, $nombre, $tipo, $valor, $valorAutocomplete, $valorId, null, null, null, $informacionAdicional, null,
            );
        } else if ($tipo == 'select') {
            $valorAutocomplete = null;
            $valorId = null;
            $params = array(
                $idInput, $nombre, $tipo, $valor, $valorAutocomplete, $valorId, $valorSelect, $textoSelect, null, $informacionAdicional, null,
            );
        } else if ($tipo == 'checkbox') {
            $valorAutocomplete = null;
            $valorId = null;
            $valor = null;
            $valorSeleccionado = $valorSeleccionado == "true" ? 1 : 0;
            $params = array($idInput, $nombre, $tipo, $valor, $valorAutocomplete, $valorId, null, null, $valorSeleccionado, $informacionAdicional, null, );
        } else if ($tipo == 'date') {
            $valorAutocomplete = null;
            $valorId = null;
            $valor = null;
            $params = array(
                $idInput, $nombre, $tipo, $valor, $valorAutocomplete, $valorId, null, null, null, $informacionAdicional, $hora,
            );
        } else {
            $valorAutocomplete = null;
            $valorId = null;
            $valor = null;
            $params = array($idInput, $nombre, $tipo, $valor, $valorAutocomplete, $valorId, null, null, null, $informacionAdicional, null,
            );
        }

        // Ejecuta el comando SQL utilizando el método 'Execsql' con el modo 'SP-PARAM'
        $datosInputs = $this->db_conn->Execsql("SP-PARAM", $cmdsqlUpdate, $params);

        return $datosInputs;
    }
    // Función para realizar la búsqueda de autocompletar en tablas
    public function autoCompleteTablas($busqueda)
    {
        // Define el comando SQL almacenado para realizar la búsqueda de autocompletar en tablas
        $cmdsqlReporte = "EXEC seletecTablaAutoComplete '$busqueda'";

        // Ejecuta el comando SQL utilizando el método 'Execsql' con el modo 'SP'
        $resp = $this->db_conn->Execsql("SP", $cmdsqlReporte);
        return $resp;
    }
    
    public function optionHeaders($busqueda, $nombreTabla)
    {
        // Define el comando SQL almacenado para obtener las columnas de una tabla específica
        $cmdsqlReporte = "EXEC obtenerColumnas '$nombreTabla', '$busqueda'";

        // Ejecuta el comando SQL utilizando el método 'Execsql' con el modo 'SP'
        $resp = $this->db_conn->Execsql("SP", $cmdsqlReporte);
        return $resp;
    }


}

?>