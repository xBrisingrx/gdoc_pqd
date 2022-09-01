<!-- modal archivos -->
<div class="modal fade" id="modal_archivos" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"> <?php echo ( isset($titulo_modal) ) ? $titulo_modal : 'Archivos' ?> </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <?php echo form_open_multipart('', array( 'id'=>'form_agregar_archivos','class'=>'g-brd-around g-brd-gray-light-v4 col-8 mb-2', 
            'method' => 'POST'));?>
          <input type="hidden" name="registro_id" id="registro_id" value="">
          <div class="row">
            <div class="col-4">
              <!-- Plain File Input -->
              <div class="form-group row mb-0 ml-2">
                <label class="u-file-attach-v2 g-color-gray-dark-v5 mb-0">
                  <input id="archivos" name="archivos[]" type="file" multiple>
                  <i class="icon-cloud-upload g-font-size-16 g-pos-rel g-top-2 g-mr-5"></i>
                  <span class="js-value">Agregar archivos</span>
                </label>
              </div>
              <!-- End Plain File Input -->
            </div>
            <div class="col-4">
              <button type="submit" id="btn_save_archivos" class="btn btn-sm u-btn-primary ml-3"> Agregar </button>
            </div>
          </div>

        </form>
        <!-- Si el cargar el formulario retorna errores los imprimo en este div -->
        <div id="msg-errors"></div>
        <div class="container-fluid mt-2 mb-2">
          <div id="galeria_archivos" class="row col-12">
            <!-- dibujamos con ajax -->
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- end modal editar archivos -->

<!-- modal eliminar archivo -->
<div class="modal fade" id="modal_delete_archivo" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">¿ Esta seguro de eliminar este archivo ?</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="archivo_delete_id" id="archivo_delete_id">
        <button type="button" class="btn u-btn-red" onclick="destroy_archivo()">Eliminar</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
<!-- end modal eliminar archivo -->