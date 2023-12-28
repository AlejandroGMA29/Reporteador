<!DOCTYPE html>
<html lang="en">
<?php
session_start();
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporteador IGL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>

    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <link rel="stylesheet" href="../css/ReportesIGL-Styles.css">
    <script src="../js/ReportesIGL.js"></script>
    <script src="../js/ReportesWizardIGL.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>


    <script src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.2/css/jquery.dataTables.min.css" />
</head>


<body>

    <?php
    include('ReporteModal.php');
    include('modal.php');
    include('modalCatalogo.php');
    include('modalInputUpdate.php');
    ?>
    <div class="container-fluid" id="contenedorTodo">

        <div class="row">
            <div class="col-3" style="border: 1px beige solid;" id="menuReporteador">
                <?php include_once('ReportesIGLmenu.php'); ?>
            </div>
            <div class="col-9"" id="headerNavbar">
                <header>
                    <?php include('ReporteNavbar.php'); ?>
                </header>
            <div class="col-12">
                <div id="contenedorWizard" style="display: none;">
                    <?php include('ReportesWizardIGL.php'); ?>
                </div>
                <div id="contenedorTabla" style="display: none;">
                    <table id="contenidoReporte" class="display compact nowrap">
                        <thead>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
    </div>
</body>

</html>