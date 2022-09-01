<div class="modal fade" id="modal_seguros_vehiculo" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"> Seguros del vehiculo </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	<!-- Si el cargar el formulario retorna errores los imprimo en este div -->
      	<div id="msg-errors"></div>
				<?php echo form_open('', array( 'class' => 'g-brd-around g-brd-gray-light-v4 g-pa-10 g-mb-20',
																				'id' => 'form_seguro_vehiculo', 'enctype' =>"multipart/form-data" )) ?>
      		<?php $this->load->view('sistema/documentos/_form_seguro_vehiculo');?>
      		<!-- Anexar PDF Atributo -->
					<div class="form-group mb-0 offset-md-2">
					  <label class="u-file-attach-v2 g-color-gray-dark-v5 mb-0">
					    <input id="archivos_seguro" name="archivos_seguro[]" type="file" multiple required>
					    <i class="icon-cloud-upload g-font-size-16 g-pos-rel g-top-2 g-mr-5"></i>
					    <span class="js-value">Anexar documentos seguro</span>
					  </label>
					</div>
					 <!-- End Anexar PDF Atributo -->
					<button id="btnSaveRenovacion" type="submit" class="btn btn-primary">Cargar</button>
      	<?php echo form_close() ?>

				<!-- Tabla seguros vehiculo -->
				<table id="tabla_seguros_vehiculo" class="table table-hover u-table--v1 mb-0 w-100 mt-2">
		      <thead>
		        <tr>
		        	<th>Aseguradora</th>
		        	<th>Poliza</th>
		          <th>Fecha alta</th>
		          <th>Fecha vencimiento</th>
		          <th>Archivos</th>
              <?php if ($this->session->userdata('rol') == 1): ?>
		           <th>Acciones</th>
              <?php endif ?>
		        </tr>
		      </thead>

		      <tbody>
		      	<!-- Ajax call -->
		      </tbody>
		    </table>
				<!-- End tabla renovaciones atributo -->
        <button type="button" class="btn u-btn-red mt-2" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_editar_seguros_vehiculo" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"> Editar seguro </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Si el cargar el formulario retorna errores los imprimo en este div -->
        <div id="msg-errors"></div>
        <?php echo form_open('', array( 'class' => 'g-brd-around g-brd-gray-light-v4 g-pa-30 g-mb-30',
                                        'id' => 'form_editar_seguros_vehiculo' )) ?>
          <input type="hidden" id="seguro_vehiculo_id" value="">
         	<?php $this->load->view('sistema/documentos/_form_seguro_vehiculo');?>
         	<!-- Anexar PDF Atributo -->
					<div class="form-group mb-0 offset-md-2">
					  <label class="u-file-attach-v2 g-color-gray-dark-v5 mb-0">
					    <input id="archivos_seguro" name="archivos_seguro[]" type="file" multiple>
					    <i class="icon-cloud-upload g-font-size-16 g-pos-rel g-top-2 g-mr-5"></i>
					    <span class="js-value">Anexar documentos seguro</span>
					  </label>
					</div>
					 <!-- End Anexar PDF Atributo -->
          <button id="btnSaveRenovacionEdit" type="submit" class="btn btn-primary">Actualizar</button>
          <button type="button" class="btn u-btn-red" data-dismiss="modal">Cerrar</button>
        <?php echo form_close() ?>
      </div>
    </div>
  </div>
</div>

<!-- modal eliminar renovacion -->
<div class="modal fade" id="modal_eliminar_renovacion" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"> Eliminar seguro </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Confirma que quiere eliminar el seguro al vehiculo?</p>
        <!-- Si el cargar el formulario retorna errores los imprimo en este div -->
        <div id="msg-errors"></div>
        <?php echo form_open('', array('id' => 'form_eliminar_renovacion' )) ?>
          <!-- input con el id del atributo_persona -->
          <input type="hidden" id="seguro_vehiculo_id" value="">         
          <button id="btnEliminarSeguro" type="submit" class="btn btn-primary">Eliminar</button>
          <button type="button" class="btn u-btn-red" data-dismiss="modal">Cerrar</button>
        <?php echo form_close() ?>
      </div>
    </div>
  </div>
</div>
<!-- end modal eliminar renovacion -->