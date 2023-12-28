<div class="modal fade" id="modalInputs" tabindex="-1" aria-labelledby="modalInputsLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Catalogo inputs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body row">
                <div id="contenedorInputsTable" class="container-fluid">
                    <table id="inputsTable" class="display compact nowrap">
                        <thead>
                            <tr>
                                <th>ID Input</th>
                                <th>Nombre</th>
                                <th>Tipo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button id="btnAgregarNuevoInput" type="button" class="btn btn-primary" data-bs-toggle="modal"
                    data-bs-target="#exampleModal">
                    agregar input</button>
            </div>
        </div>
    </div>
</div>