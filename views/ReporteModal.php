<div class="modal fade" id="modalCorreos" tabindex="-1" aria-labelledby="modalCorreosLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Enviar correos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body row">
                <div>
                    <ul id="ulCorreos" style="height: 200px; max-height: 200px; overflow-y: scroll;"
                        class="list-group row">
                    </ul>
                </div>
                <hr>
                <div id="contenedorCliente">
                    <span>Cliente</span>
                    <div class="input-group">

                        <input class="form-control" type="text" name="text" id="inpClientesCorreos"
                            placeholder="Cliente" aria-label="Cliente">
                        <button id="btnAsignarClientesMostrar" id="btnAsignarClientesMostrar"
                            class="btn btn-outline-success" type="button">Asignar</button>
                    </div>
                </div>
                <span>agregar correo</span>
                <div class="input-group">
                    <input class="form-control" type="email" name="correo" id="inpCorreo" placeholder="exaple@mail.com"
                        aria-label="Correo que desea agregar">
                    <button id="btnAgregarCorreos" id="btnAgregarCorreos" class="btn btn-outline-success"
                        type="button">Agregar</button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button id="btnEnviarCorreos" type="button" class="btn btn-primary">
                    <span class="spinner-border spinner-border-sm" aria-hidden="true" style="visibility:hidden;"></span>
                    <span class="visually-hidden" role="status" style="visibility:hidden;">Loading...</span>
                    Enviar correos</button>
                <button id="btnEnviarCorreosPesados" type="button" class="btn btn-primary" style="display: none;">
                    <span class="spinner-border spinner-border-sm" aria-hidden="true" style="visibility:hidden;"></span>
                    <span class="visually-hidden" role="status" style="visibility:hidden;">Loading...</span>
                    Enviar correos pesado</button>
            </div>
        </div>
    </div>
</div>