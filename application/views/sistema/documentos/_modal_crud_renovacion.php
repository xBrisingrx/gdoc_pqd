<div class="modal fade" id="modal_add_attr" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"> Cargar documento </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	<!-- Si el cargar el formulario retorna errores los imprimo en este div -->
      	<div id="msg-errors"></div>
				<?php echo form_open('', array( 'class' => 'g-brd-around g-brd-gray-light-v4 g-pa-30 g-mb-30',
																				'id' => 'form_renovacion_atributo', 'enctype' =>"multipart/form-data" )) ?>
      		<!-- input con el id del atributo_persona -->
      		<input type="hidden" id="id_atributo_tipo" value="">
          <input type="hidden" id="vence_attr" value="">
          <!-- Input nombre atributo -->
          <div class="form-group row g-mb-10">
            <label class="col-sm-2 col-form-label g-mb-10" for="nombre_attr">Atributo</label>
            <div class="col-sm-6">
              <input id="nombre_attr" name="nombre_attr"
                     class="form-control u-form-control rounded-0" type="text" value="" disabled>
            </div>
          </div>
          <!-- End Input nombre atributo -->

				  <!-- Fecha de renovacion atributo -->
				  <div class="row form-group g-mb-10">
				    <label for="fecha_renovacion_atributo" class="col-sm-2 col-md-2 col-form-label">Fecha renovacion (*)</label>
				    <div class="col-md-6 col-sm-6">
				      <input id="fecha_renovacion_atributo" name="fecha_renovacion_atributo" class="form-control form-control-md rounded-0" type="date" required>
				    </div>
				  </div>
				  <!-- End Fecha de renovacion atributo -->

				  <!-- Fecha de vencimiento atributo -->
				  <div class="row form-group g-mb-10">
				    <label for="fecha_vencimiento_atributo" class="col-sm-2 col-md-2 col-form-label">Fecha vencimiento (*)</label>
				    <div class="col-md-6 col-sm-6">
				      <input id="fecha_vencimiento_atributo" name="fecha_vencimiento_atributo" class="form-control form-control-md rounded-0" type="date" required>
				    </div>
				  </div>
				  <!-- End Fecha de vencimiento atributo -->

				  <!-- Anexar PDF Atributo -->
			    <div class="form-group mb-0 offset-md-2">
			      <label class="u-file-attach-v2 g-color-gray-dark-v5 mb-0">
			        <input id="archivos_renovacion" name="archivos_renovacion[]" type="file" multiple required>
			        <i class="icon-cloud-upload g-font-size-16 g-pos-rel g-top-2 g-mr-5"></i>
			        <span class="js-value">Anexar documento renovacion</span>
			      </label>
			    </div>
			     <!-- End Anexar PDF Atributo -->
					<button id="btnSaveRenovacion" type="submit" class="btn btn-primary">Cargar</button>
      	<?php echo form_close() ?>
				<!-- Tabla renovaciones atributo -->
				<div id="div_tabla_renovaciones_atributo">
			    <table id="tabla_renovaciones_atributo" class="table table-hover u-table--v1 mb-0 w-100">
			      <thead>
			        <tr>
			          <th>Fecha renovación</th>
			          <th>Fecha vencimiento</th>
			          <th>PDF</th>
                <?php if ($this->session->userdata('rol') == 1): ?>
			           <th>Acciones</th>
                <?php endif ?>
			        </tr>
			      </thead>

			      <tbody>
			      	<!-- Ajax call -->
			      </tbody>
			    </table>
				</div>
				<!-- End tabla renovaciones atributo -->
        <button type="button" class="btn u-btn-red" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="modal_editar_renovacion" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"> Editar renovacion </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Si el cargar el formulario retorna errores los imprimo en este div -->
        <div id="msg-errors"></div>
        <?php echo form_open('', array( 'class' => 'g-brd-around g-brd-gray-light-v4 g-pa-30 g-mb-30',
                                        'id' => 'form_editar_renovacion' )) ?>
          <!-- input con el id del atributo_persona -->
          <input type="hidden" id="renovacion_id" value="">
          <input type="hidden" id="vence_renovacion_editar" value="">
          <input type="hidden" id="fecha_renovacion_atributo_edit_anterior" value=""></input>
          <input type="hidden" id="fecha_vencimiento_atributo_edit_anterior" value=""></input>
          <!-- Fecha de renovacion atributo -->
          <div class="row form-group g-mb-10">
            <label for="fecha_renovacion_atributo_edit" class="col-sm-2 col-md-2 col-form-label">Fecha renovacion (*)</label>
            <div class="col-md-6 col-sm-6">
              <input id="fecha_renovacion_atributo_edit" name="fecha_renovacion_atributo_edit" class="form-control form-control-md rounded-0" type="date" required>
            </div>
          </div>
          <!-- End Fecha de renovacion atributo -->

          <!-- Fecha de vencimiento atributo -->
          <div class="row form-group g-mb-10">
            <label for="fecha_vencimiento_atributo_edit" class="col-sm-2 col-md-2 col-form-label">Fecha vencimiento (*)</label>
            <div class="col-md-6 col-sm-6">
              <input id="fecha_vencimiento_atributo_edit" name="fecha_vencimiento_atributo_edit" class="form-control form-control-md rounded-0" type="date" required>
            </div>
          </div>
          <!-- End Fecha de vencimiento atributo -->

          <!-- Anexar PDF Atributo -->
          <div class="form-group mb-0 offset-md-2">
            <label class="u-file-attach-v2 g-color-gray-dark-v5 mb-0">
              <input id="archivo_renovacion_edit" name="archivo_renovacion_edit[]" type="file" multiple>
              <i class="icon-cloud-upload g-font-size-16 g-pos-rel g-top-2 g-mr-5"></i>
              <span class="js-value">Anexar archivo</span>
            </label>
          </div>
           <!-- End Anexar PDF Atributo -->
          <button id="btnSaveRenovacionEdit" type="submit" class="btn btn-primary">Guardar</button>
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
        <h5 class="modal-title"> Eliminar renovacion </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Confirma que quiere eliminar esta renovacion?</p>
        <!-- Si el cargar el formulario retorna errores los imprimo en este div -->
        <div id="msg-errors"></div>
        <?php echo form_open('', array('id' => 'form_eliminar_renovacion' )) ?>
          <!-- input con el id del atributo_persona -->
          <input type="hidden" id="renovacion_id" value="">         
          <button id="btnEliminarRenovacion" type="submit" class="btn btn-primary">Eliminar</button>
          <button type="button" class="btn u-btn-red" data-dismiss="modal">Cerrar</button>
        <?php echo form_close() ?>
      </div>
    </div>
  </div>
</div>
<!-- end modal eliminar renovacion -->