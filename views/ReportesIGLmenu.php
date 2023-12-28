<div style="height: 100vh;" class="offcanvas-start" tabindex="-1" id="offcanvasExample"
    aria-labelledby="offcanvasExampleLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasExampleLabel">Reporteador IGL</h5>
    </div>
    <div class="offcanvas-body">
        <div style="width: 100%;">
            <div class="accordion accordion-flush" id="acordionMenu">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                            Reporte
                        </button>
                    </h2>
                    <div id="flush-collapseOne" class="accordion-collapse collapse show" data-bs-parent="#acordionMenu">
                        <div class="accordion-body" style="height: 250px">
                            <label id="lblReporte" for="number">selecciona un reporte</label>
                            <select name="number" id="number" style="margin-bottom: 10px;">
                                <option value="0"></option>
                            </select>
                            <div id="contenedorAdmin" style="display: none;">
                                <button id="btnModificarReporte" class="btn btn-primary mt-2"
                                    style="display: block;">Modificar reporte</button>
                                <button id="btnAgregarReporte" class="btn btn-primary mt-2"
                                    style="display: block;">Agregar nuevo reporte</button>
                                <button id="btnCatalogoInputs" class="btn btn-primary mt-2" style="display: block;"
                                    data-bs-toggle="modal" data-bs-target="#modalInputs">Catalogo de Inputs</button>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                            Datos
                        </button>
                    </h2>
                    <div id="flush-collapseTwo" class="accordion-collapse collapse" data-bs-parent="#acordionMenu">
                        <div class="accordion-body" id="contenedorInputs">
                        </div>
                        <div class="btn-group col-12 sm" role="group" aria-label="button group" id="enviarReporteGrupo">
                            <button id="btnBuscarReporte" class="btn btn-primary" type="button">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"
                                    style="display: none;"></span>
                                <span>Generar reporte</span>
                            </button>
                            <button id="btnExcel" class="btn btn-success" disabled>Excel</button>
                            <button id="btnAbrirCorreos" class="btn btn-success" data-bs-toggle="modal"
                                data-bs-target="#modalCorreos" disabled>Enviar reporte</button>
                        </div>
                        <div class="btn-group col-12 sm" role="group" aria-label="button group" id="enviarReporteGrupo">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="chbInterno">
                                <label class="form-check-label" for="flexCheckDefault">
                                    Reporte interno
                                </label>
                            </div>
                        </div>
                        <div class="btn-group col-12 sm" id="enviarReportePesado" role="group" aria-label="button group"
                            style="display: none;">
                            <button id="btnAbrirCorreoPesado" class="btn btn-success" data-bs-toggle="modal"
                                data-bs-target="#modalCorreos">Enviar reporte pesado</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>