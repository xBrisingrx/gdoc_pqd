<section>
<div class="container-fluid">
	<h1> Asignación de perfiles de <?php echo $nombre_tipo_perfil ?> </h1>
	<!-- tabla asignacion de perfiles -->
  <table id="profile_assign_table" class="table table-hover dt-responsive w-100  mb-5 display compact">
      <thead class="thead-dark">
        <tr>
          <?php for ($i=0; $i < count($columnas_tabla); $i++) { ?>
        		<th><?php echo $columnas_tabla[$i]; ?></th>
      		<?php  } ?>
        </tr>
      </thead>
      <tbody>
      	<!-- Completo con ajax -->
      </tbody>
  </table>
	<!-- End tabla asignacion de perfiles -->
		<br><br>
</div>
</section>


<div class="modal fade" id="modal_add_profile_assign" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	<form id="form_profile_assign" class="g-brd-around g-brd-gray-light-v4 g-pa-30 g-mb-10">
      		<p id="error-asignacion" class="text-danger"></p>
      		<!-- input que uso cuando edito -->
      		<input type="hidden" id="profile_assign_id" value="">
      		<input type="hidden" id="asign_id_update" value="">
				  <!-- Select persona o vehiculo -->
				  <?php if ( $tipo_perfil == 1 ): ?>
					  <div class="form-group">
					  	<label class="mr-sm-3 mb-3 mb-lg-0" for="asign_id"> Persona (*) </label>
					    <select class="person_select" id="asign_id" name="asign_id" required >
					      <option value="0" selected disabled> Seleccione persona a asignar </option>
					      	<?php foreach ($asigno as $a): ?>
					      		<option value="<?php echo $a->id;?>"><?php echo $a->apellido." ".$a->nombre;?></option>
					      	<?php endforeach ?>
					    </select>
					  </div>
				  <?php else: ?>
					  <div class="form-group">
					  	<label class="mr-sm-3 mb-3 mb-lg-0" for="asign_id"> Vehículo (*) </label>
					    <select class="vehicle_select" id="asign_id" name="asign_id" required >
					      <option value="0" selected disabled> Seleccione vehículo a asignar </option>
					      	<?php foreach ($asigno as $a): ?>
					      		<option value="<?php echo $a->id;?>">Interno: <?php echo $a->interno." Dominio: ".$a->dominio ;?></option>
					      	<?php endforeach ?>
					    </select>
					  </div>
				  <?php endif ?>
				  <!-- End select persona o vehiculo -->

				  <!-- Select tipo perfil -->
				  <div class="form-group">
				    <label class="mr-sm-3 mb-3 mb-lg-0" for="profile_id">Perfil a asignar (*)</label>
				    <select class="profile_select" id="profile_id" name="profile_id" required>
				      <option value="0" selected disabled > Seleccione perfil a asignar </option>
				      <?php foreach ($perfiles as $p): ?>
				      	<option value="<?php echo $p->id;?>"><?php echo $p->nombre;?></option>
				      <?php endforeach ?>
				    </select>
				  </div>
				  <!-- End select tipo perfil -->

				  <!-- Select Single Date -->
				  <div class="form-group g-mb-30">
				    <label class="g-mb-10">Fecha inicio vigencia(*)</label>
				    <div class="input-group g-brd-primary--focus">
				      <input id="fecha_inicio_vigencia" name="fecha_inicio_vigencia" class="form-control form-control-md  rounded-0" type="date" required>
				      <div class="input-group-addon d-flex align-items-center g-bg-white g-color-gray-dark-v5 rounded-0">
				        <i class="icon-calendar"></i>
				      </div>
				    </div>
				  </div>
				  <!-- End Select Single Date -->
					<button id="btnSaveAssign" type="submit" class="btn btn-primary">Asignar perfil</button>
        	<button type="button" class="btn u-btn-red btnClose" data-dismiss="modal">Cerrar</button>
      	</form>
      </div>
    </div>
  </div>
</div>



<!-- Modal para eliminar asignacion -->
<div class="modal fade" id="modal_delete_assign" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">¿ Esta seguro de eliminar esta asignación ?</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	<input type="hidden" id="profile_assign_id" name="profile_assign_id" value="">
       	<p id="name_assign_delete"></p>
       	<br>
       	<p id="name_profile_delete"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-md u-btn-red g-mr-10" onclick="destroy()">Eliminar</button>
        <button type="button" data-dismiss="modal" class="btn btn-md u-btn-indigo g-mr-10"> Cerrar </button>
      </div>
    </div>
  </div>
</div>
<!-- Fin modal eliminar asignacion -->

<script>
	let profile_assign_table
	let save_method
	let asign_type = '<?php echo $tipo_perfil;?>'
	// Los uso porque en el edit selecciono en el drop estos id y con el reset no vuelven al por defecto
	let perfil_id_edit = 0
	let persona_id_edit = 0
	let asign_selected  // lo uso para resetear el select
	let profile_selected // lo uso para resetear el select
	var select_personas
	var select_vehiculos
	var select_perfiles
	var form_profile_assign

	function modal_assign() {
		save_method = 'create'
		$('#modal_add_profile_assign .modal-title').text('Alta de perfil');
		clean_form('form_profile_assign')
		reset_selects()
		$('#error-asignacion').text('')
		$('#asign_id').prop( "disabled", false )
		$('#modal_add_profile_assign .modal-title').text('Asignacion de perfil')
		$('#modal_add_profile_assign #btnSaveAssign').text('Asignar perfil')
		$('#modal_add_profile_assign').modal('show')
	}

	function edit(id, type) {
		clean_form('form_profile_assign')
		reset_selects()
		$('#error-asignacion').text('')
		save_method = 'update'
		$('#modal_add_profile_assign #btnSaveAssign').text('Actualizar')
		$('#modal_add_profile_assign .modal-title').text('Modificacion de asignacion')
		$.ajax({
			url: "<?php echo base_url('Asignacion_Perfiles/edit/');?>" + id + "/" + type,
			type: "GET",
			dataType: "JSON",
			success: function(response) {
				if (type == 1) {
					select_personas.val(response.persona_id).trigger('change')
					$('#asign_id_update').val(response.persona_id)
				} else {
					select_vehiculos.val(response.vehiculo_id).trigger('change')
					$('#asign_id_update').val(response.vehiculo_id)
				}
				// persona/vehiculo 
				$('#asign_id').prop( "disabled", true )
				$('#form_profile_assign #profile_assign_id').val(response.id)
				select_perfiles.val(response.perfil_id).trigger('change')
				$('#fecha_inicio_vigencia').val(response.fecha_inicio_vigencia)
				$('#modal_add_profile_assign').modal('show')
			},
			error: function() {
				noty_alert('error', 'Error al obtener los datos')
			}
		})
	}

	$('.btnClose').on('click', function(event) {
		$('#btnSaveAssign').attr('disabled', false)
		window.setTimeout(reset_selects(), 500)
		$('#error-asignacion').text('')
	})

	// save assign profile
	function save() {
		$('#btnSaveAssign').attr('disabled', true)
		let asign_id
		let profile_id = $('#form_profile_assign #profile_id').val()
		let start_date_validity = $('#form_profile_assign #fecha_inicio_vigencia').val()

		let id = $('#profile_assign_id').val()

		if (save_method === 'create') {
			// Asign id es el id de persona a vehiculo asignado
			asign_id = $('#form_profile_assign #asign_id').val()
			msg_notify = 'Perfil asignado con éxito'
		} else if (save_method === 'update') {
			// Asign id es el id de persona a vehiculo asignado
			asign_id = $('#form_profile_assign #asign_id_update').val()
			
		}

		$.ajax({
			url: '<?php echo base_url("Asignacion_Perfiles/")?>' + save_method,
			type: "POST",
			data: {
				id: id,
				asign_id: asign_id,
				profile_id: profile_id,
				fecha_inicio_vigencia: start_date_validity,
				asign_type: asign_type },
				dataType: 'JSON',
			success: function(response) {
				if (response.status === 'success') {
					profile_assign_table.ajax.reload(null,false);
					$('#modal_add_profile_assign').modal('hide')
					noty_alert( response.status, response.msg)
				} else {
					$('#error-asignacion').text(response.msg)
				}
				$('#btnSaveAssign').attr('disabled', false)
			},
			error: function(jqXHR, textStatus, errorThrown) {
				noty_alert('error', 'No se pudo asignar el perfil')
    		$('#btnSaveAssign').attr('disabled', false)
			}
		})
	}

	function modal_destroy(id, type)
	{
		$.ajax({
			url: '<?php echo base_url("Perfiles/get_assign_profile/")?>' + id + '/' + type,
			type: "GET",
			dataType: "JSON",
			success: function(resp)
			{
				if (type === 1) {
					$('#modal_delete_assign #name_assign_delete').html('<strong>Persona: </strong>'+ resp[0].apellido_persona + ' ' + resp[0].nombre_persona )
				} else {
					$('#modal_delete_assign #name_assign_delete').html('<strong>Interno: </strong>'+ resp[0].interno + ' <strong>Dominio: </strong>' + resp[0].dominio )
				}
				$('#modal_delete_assign #name_profile_delete').html('<strong>Perfil: </strong>' + resp[0].nombre_perfil)
				$('#modal_delete_assign #profile_assign_id').val(resp[0].id)
				$('#modal_delete_assign').modal('show')
			},
			error: function()
			{
				anoty_alert('error', 'Error al eliminar')
			}
		})
	}

	function destroy() {
		let id = $('#modal_delete_assign #profile_assign_id').val()
		let type_assign = '<?php echo $tipo_perfil ?>'
		$.ajax({
			url: '<?php echo base_url("Perfiles/destroy_assign_profile/");?>' + id + '/' + type_assign,
			type: 'POST',
			success: function(resp) {
				if (resp === 'ok') {
					$('#modal_delete_assign').modal('hide')
					profile_assign_table.ajax.reload(null,false);
				} else {
					noty_alert('error', 'Error al eliminar la asignación')
				}
			},
			error: function() {
				noty_alert('error', 'Error de servidor: no se pudo eliminar la asignacion')
			}
		})
	}

	function reset_selects(asign_selected , profile_selected) {
		$('.person_select').val(0).trigger('change');
		$('.vehicle_select').val(0).trigger('change');
		$('.profile_select').val(0).trigger('change');
	}

	$(document).on('ready', function () {

		select_personas = $('.person_select').select2({theme: 'bootstrap4', width: '60%'})
		select_vehiculos =  $('.vehicle_select').select2({theme: 'bootstrap4', width: '60%'})
		select_perfiles =  $('.profile_select').select2({theme: 'bootstrap4', width: '60%'})

		form_profile_assign = $('#form_profile_assign').validate({
																rules: {
																	'asign_id': { required: true },
																	'profile_id': { required: true }
																}
															})

		$('#form_profile_assign').submit(function(e){
			e.preventDefault();
			if (form_profile_assign.valid()) { save() }

		})

		profile_assign_table = $('#profile_assign_table').DataTable( {
															dom: "<'row'<'col-sm-2'<'toolbar'>><'col-sm-2 text-center'><'col-sm-8'f>>" +
																	 "<'row'<'col-sm-12'tr>>" +
																		 "<'row'<'col-sm-5'i><'col-sm-7'p>>", //estilo del datatables + toolbar
															lengthChange: false,
												      ajax : "<?php echo base_url('Asignacion_Perfiles/list/').$tipo_perfil;?>",
															language: {
											                url: "<?php echo base_url(); ?>assets/vendor/datatables/spanish.json"
											              },
											        initComplete: function(){ //insertamos el boton en el toolbar de datatables
												        								if ( rol_usuario == 1 ) {
												        									$("div.toolbar")
																		              .html('<button id="btn_add_profile_assign" class="btn u-btn-primary" type="button" onclick="modal_assign()" > <i class="fa fa-plus"></i> Asignar perfil </button>')
												        								} else {
												        									$("div.toolbar").html('')
												        								}
																	            }
															})
	});
</script>
