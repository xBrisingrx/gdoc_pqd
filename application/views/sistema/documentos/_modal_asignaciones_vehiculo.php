<div class="modal fade" id="modal_asignaciones_vehiculo" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"> Asignaciones del vehiculo </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	<!-- Si el cargar el formulario retorna errores los imprimo en este div -->
      	<div id="msg-errors"></div>
				<?php echo form_open('', array( 'class' => 'g-brd-around g-brd-gray-light-v4 g-pa-10 g-mb-20',
																				'id' => 'form_asignacion_vehiculo', 'enctype' =>"multipart/form-data" )) ?>
          <!-- Input nombre atributo -->
          <div class="form-group row g-mb-10">
            <label class="col-sm-3 col-form-label g-mb-10" for="nombre_attr">Asignar a:</label>
            <div class="col-sm-9">
              <select id="asignacion_id" name="asignacion_id" class="form-control form-control-md form-control-lg rounded-0 g-mb-10 col-sm-6" required>
					      <option value='0'>Seleccione asignacion</option>
					      <?php foreach ($asignaciones as $a): ?>
					        <option value="<?php echo $a->id;?>"><?php echo $a->nombre;?></option>
					      <?php endforeach ?>
					    </select>
            </div>
          </div>
          <!-- End Input nombre atributo -->

				  <!-- Fecha de renovacion atributo -->
				  <div class="row form-group g-mb-10">
				    <label for="fecha_alta_asignacion_vehiculo" class="col-sm-3 col-md-3 col-form-label">Fecha incio (*)</label>
				    <div class="col-md-6 col-sm-6">
				      <input id="fecha_alta_asignacion_vehiculo" name="fecha_alta_asignacion_vehiculo" class="form-control form-control-md rounded-0" type="date" required>
				    </div>
				  </div>
				  <!-- End Fecha de renovacion atributo -->

				  <!-- Anexar PDF Atributo -->
			    <div class="form-group mb-0 offset-md-2">
			      <label class="u-file-attach-v2 g-color-gray-dark-v5 mb-0">
			        <input id="archivos_asignacion" name="archivos_asignacion[]" type="file" multiple>
			        <i class="icon-cloud-upload g-font-size-16 g-pos-rel g-top-2 g-mr-5"></i>
			        <span class="js-value">Anexar documentos asignacion</span>
			      </label>
			    </div>
			     <!-- End Anexar PDF Atributo -->
					<button id="btnSaveAsignacion" type="submit" class="btn btn-primary">Cargar</button>

      	<?php echo form_close() ?>

				<!-- Tabla asignaciones vehiculo -->
				<table id="tabla_asignaciones_vehiculo" class="table table-hover u-table--v1 mb-0 w-100 mt-2">
		      <thead>
		        <tr>
		        	<th>Lugar de asignación</th>
		        	<th>Desde</th>
		          <th>Hasta</th>
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

<div class="modal fade" id="modal_eliminar_asignacion" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"> Eliminar renovacion </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Confirma que quiere finalizar la asignación?</p>
        <!-- Si el cargar el formulario retorna errores los imprimo en este div -->
        <div id="msg-errors"></div>
        <?php echo form_open('', array('id' => 'form_eliminar_asignacion' )) ?>
          <!-- input con el id del atributo_persona -->
          <input type="hidden" id="vehiculo_asignacion_id" value="">   
          <div class="row form-group g-mb-10">
				    <label for="fecha_baja_asignacion_vehiculo" class="col-sm-3 col-md-3 col-form-label">Fecha finalización (*)</label>
				    <div class="col-md-6 col-sm-6">
				      <input id="fecha_baja_asignacion_vehiculo" name="fecha_baja_asignacion_vehiculo" class="form-control form-control-md rounded-0" type="date" required>
				    </div>
				  </div>
          <button id="btnEliminarAsignacion" type="submit" class="btn btn-primary">Eliminar</button>
          <button type="button" class="btn u-btn-red" data-dismiss="modal">Cerrar</button>
        <?php echo form_close() ?>
      </div>
    </div>
  </div>
</div>