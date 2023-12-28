<div style="height: 100vh;">
    <div id="tabs">
        <ul>
            <li><a href="#tabs-1">Datos Reporte</a></li>
            <li id="tab2"><a href="#tabs-2">Editar Inputs</a></li>
        </ul>

        <div id="tabs-1" style="height: 80vh;">
            <p>
                <label class="form-label" for="idReporte" style="display: none;" id="lblRepo">ID Reporte</label>
                <input class="form-control mb-1" type="text" name="idReporte" id="idReporte" style="display: none;"
                    disabled>
                <label class="form-label" for="DescripcionReporte">Nombre</label>
                <input class="form-control mb-1" type="text" name="DescripcionReporte" id="DescripcionReporte">
                <label class="form-label" for="SPReporte">SP Del reporte</label>
                <input class="form-control mb-1" type="text" name="SPReporte" id="SPReporte">
                <label class="form-label" for="activo">Activo</label>
                <select class="form-select mb-1" name="activo" id="activo">
                    <option></option>
                    <option value="1">1</option>
                    <option value="0">0</option>
                </select>
                <label class="form-label" for="repoPesado">Pesado</label>
                <select class="form-select mb-1" name="Pesado" id="repoPesado">
                    <option></option>
                    <option value="1">1</option>
                    <option value="0">0</option>
                </select>
                <button id="btnAgregarReporteBD" class="btn btn-primary mt-1" style="display:none">agregar
                    reporte</button>
                <button id="btnActualizarReporteBD" class="btn btn-primary mt-1" style="display:none;">actualizar
                    reporte</button>
            </p>
        </div>

        <div id="tabs-2" style="height: 80vh;">
            <p>

            <h6 style="display: block;" class="form-label">agregar input al reporte</h6>
            <div>
                <label for="busquedaInputsFuera" class="form-label">Buscar:</label>
                <input type="text" id="busquedaInputsFuera" class="form-control mb-2"
                    placeholder="Ingrese término de búsqueda">
            </div>
            <div class="row container-fluid">
                <div id="contenedorInputs" class="inputView col-5"
                    style="border: 1px LightSteelBlue solid;  box-shadow: 10px 10px 5px Gainsboro; border-radius: 5px;">
                    <ul id="sortable" class="list-group">
                    </ul>
                </div>
                <div class="col-2">
                    <button type="button" class="btn btn-outline-success w-100" id="moverDatos">
                        < mover>
                    </button>
                    <button type="button" class="btn btn-outline-warning w-100 mt-2" id="actualizarInputs"> Actualizar </button>
                    <button type="button" class="btn btn-outline-info w-100 mt-2" id="añadirInput" data-bs-toggle="modal"
                        data-bs-target="#exampleModal">Añadir Input</button>
                </div>
                <div class="inputView col-5"
                    style="border: 1px LightSteelBlue solid;  box-shadow: 10px 10px 5px Gainsboro; border-radius: 5px;">
                    <ul id="sortableInputs" class="list-group">
                    </ul>
                </div>
            </div>
            </p>
        </div>

    </div>
</div>