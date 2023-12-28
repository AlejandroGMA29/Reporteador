<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Inputs</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body container-fluid">
                <div id="inpIDs" style="display: none;" class="row">
                    <label for="inpId" class="col-12">Id input</label>
                    <input type="text" class="col-12" name="inpId" id="inpId" disabled>
                </div>
                <div class="row mb-2">
                    <label for="inpNombreInput" class="form-label col-12">Nombre</label>
                    <input type="text" class="form-control col-8" name="inpNombreInput" id="inpNombreInput">
                </div>
                <div class="mb-2">
                    <label for="inpTipo" class="form-label">Tipo: </label>
                    <select name="inpTipo" id="inpTipo" style="margin-bottom: 10px;">
                        <option value="text">Text</option>
                        <option value="autoComplete">auto complete</option>
                        <option value="select">Select</option>
                        <option value="checkbox">Checkbox</option>
                        <option value="password">Password</option>
                        <option value="date">fecha</option>
                    </select>
                </div>
                <div class="row mb-2">
                    <label for="inpInformacionAdicional" class="form-label col-12">Informacion adicional</label>
                    <input type="text" name="inpInformacionAdicional" id="inpInformacionAdicional"
                        class="form-control col-12">
                </div>
                <div id="opcionesAutoComplete" style="display:none;" class="row">
                    <label for="inpTablaAutComplete" style="display: Block;" class="form-label col-12">Tabla auto
                        complete</label>
                    <input type="text" name="inpTablaAutComplete" style="display: Block;" class="form-control col-12"
                        id="inpTablaAutComplete">
                    <label for="inpIdAutoComplete" style="display: Block;" class="form-label col-12">id auto
                        complete</label>
                    <input type="text" name="inpIdAutoComplete" style="display: Block;" id="inpIdAutoComplete"
                        class="form-control col-12">
                    <label for="inpValorAutoComplete" style="display: Block;" class="form-label col-12">valor auto
                        complete</label>
                    <input type="text" name="inpValorAutoComplete" style="display: Block;" id="inpValorAutoComplete"
                        class="form-control col-12">
                </div>
                <div id="opcionesSelectComplete" style="display:none;">
                    <div class="mb-2 mt-2" style="display: block;">
                        <label>Información select</label>
                        <button class="btn btn-outline-success btn-sm" type="button" id="btnAñadirOpcion">+</button>
                    </div>

                    <div id="grupoSelect">

                    </div>
                </div>
                <div id="opcionesCheckBox" style="display:none">
                    <label for="inpSeleccionado" class="form-check-label">esta seleccionado?</label>
                    <input type="checkbox" name="inpSeleccionado" class="form-check-input" id="inpSeleccionado">
                </div>
                <div id="opcionesDate" style="display:none">
                    <label for="inpHorario" class="form-label">Hora establecida</label>
                    <input type="text" name="inpHorario" id="inpHorario" class="form-control"
                        style="margin-bottom: 10px;">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="btnAñadirInput" class="btn btn-primary" data-bs-dismiss="modal">Añadir
                    input</button>
                <button type="button" id="btnAñadirInputMenu" class="btn btn-primary" data-bs-dismiss="modal"
                    style="display:none;" data-bs-toggle="modal" data-bs-target="#modalInputs">Añadir
                    input</button>
                <button type="button" id="btnActualizarInputBD" class="btn btn-primary" data-bs-dismiss="modal"
                    style="display:none;" data-bs-toggle="modal" data-bs-target="#modalInputs">Actualizar Input</button>
            </div>
        </div>
    </div>
</div>