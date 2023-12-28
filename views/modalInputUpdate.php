<div class="modal fade" id="modalUpdate" tabindex="-1" aria-labelledby="modalUpdateLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Inputs</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div>
                    <label for="inpIdInput">id</label>
                    <input type="text" name="inpIdInput" id="inpIdInput">
                </div>
                <div>
                    <label for="inpNombreInput">Nombre</label>
                    <input type="text" name="inpNombreInput" id="inpNombreInput">
                </div>
                <div>
                    <label for="inpTipo">Tipo</label>
                    <select name="inpTipo" id="inpTipo" style="margin-bottom: 10px;">
                        <option value="text">Text</option>
                        <option value="autoComplete">auto complete</option>
                        <option value="select">Select</option>
                        <option value="multidata">Multi data</option>
                        <option value="checkbox">Checkbox</option>
                        <option value="password">Password</option>
                        <option value="date">fecha</option>
                    </select>
                </div>
                <div>
                    <label for="inpInformacionAdicional">Informacion adicional</label>
                    <input type="text" name="inpInformacionAdicional" id="inpInformacionAdicional">
                </div>
                <div id="opcionesAutoComplete" style="display:none;">
                    <label for="inpTablaAutComplete" style="display: Block;">Tabla auto complete</label>
                    <input type="text" name="inpTablaAutComplete" style="display: Block;" id="inpTablaAutComplete">
                    <label for="inpIdAutoComplete" style="display: Block;">id auto complete</label>
                    <input type="text" name="inpIdAutoComplete" style="display: Block;" id="inpIdAutoComplete">
                    <label for="inpValorAutoComplete" style="display: Block;">valor auto complete</label>
                    <input type="text" name="inpValorAutoComplete" style="display: Block;" id="inpValorAutoComplete">
                </div>
                <div id="opcionesSelectComplete" style="display:none;">
                    <div class="mb-2 mt-2" style="display: block;">
                        <label>Información select</label>
                        <button class="btn btn-outline-success btn-sm" type="button" id="btnAñadirOpcion">+</button>
                    </div>
                    <div id="grupoSelect">
                        <div class="input-group" id="inputClone">
                            <input type="text" class="form-control" name="inpValorSelect" placeholder="valor"
                                style="display: block;" id="inpValorSelect">
                            <input type="text" class="form-control" name="inpTextoSelect" placeholder="Texto"
                                style="display: block;" id="inpTextoSelect">
                            <button class="btn btn-outline-danger" type="button" id="btnEliminarOpcion">X</button>
                        </div>
                    </div>
                </div>
                <div id="opcionesCheckBox" style="display:none">
                    <label for="inpSeleccionado">esta seleccionado?</label>
                    <input type="checkbox" name="inpSeleccionado" id="inpSeleccionado">
                </div>
                <div id="opcionesDate" style="display:none">
                    <label for="inpHorario">Hora establecida</label>
                    <select name="inpHorario" id="inpHorario" style="margin-bottom: 10px;">
                        <option selected></option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="btnActualizarInput" class="btn btn-primary" data-bs-dismiss="modal">actualizar input</button>
            </div>
        </div>
    </div>
</div>