<section class="container-fluid g-py-10">
	<h1>Perfiles registrados en el sistema</h1>

  <?php if ($this->session->userdata('rol') == 1): ?>
  	<div class="row g-py-10">
    	<button class="btn u-btn-primary g-ml-15" onclick="create_profile()"> Nuevo perfil </button>
    </div>
  <?php endif ?>

	<!-- Hover Rows -->
	<div class="card g-brd-darkpurple rounded-0 g-mb-30">
	  <h3 class="card-header g-bg-darkpurple g-brd-transparent g-color-white g-font-size-16 rounded-0 mb-2">
	    <i class="fa fa-gear g-mr-5"></i>
	    Listado de perfiles registrados
	  </h3>
  	<div class="px-2  pb-2">
  		<table id="tabla_perfiles" class="table table-hover dt-responsive w-100 u-table--v1 mb-0">
	      <thead>
	        <tr>
	          <th>Nombre del perfil de <?php echo $nombre_perfil; ?></th>
	          <th>Descripción</th>
	          <th>Fecha inicio vigencia</th>
	          <th>Fecha baja</th>
	          <th>Acciones</th>
	        </tr>
	      </thead>
	      <tbody> <!-- Completo con ajax --> </tbody>
	    </table>
  	</div>
	</div>
	<!-- End Hover Rows -->
	<br>
	<?php if ($this->session->userdata('rol') == 1): ?>
		<div class="g-py-10">
			<button class="btn u-btn-indigo " onclick="modal_assign_attribute()">Asignar atributo</button>
		</div>
	<?php endif ?>
	<!-- Hover Rows -->
	<div class="card g-brd-darkpurple rounded-0 g-mb-30">
	  <h3 class="card-header g-bg-darkpurple g-brd-transparent g-color-white g-font-size-16 rounded-0 mb-2">
	    <i class="fa fa-gear g-mr-5"></i>
	    Asignacion de atributos al perfil de <?php echo $nombre_perfil; ?>
	  </h3>
	  <div class="px-2 pb-2">
	  	<table id="tabla_perfiles_atributos" class="table table-hover dt-responsive w-100 u-table--v1 mb-0 display compact">
	      <thead>
	        <tr>
	          <th>Perfil</th>
	          <th>Atributo</th>
	          <th>Fecha inicio vigencia</th>
	          <th>Fecha baja</th>
	          <th>Acciones</th>
	        </tr>
	      </thead>
	      <tbody>
	      	<!-- Populate with ajax -->
	      </tbody>
	    </table>
	  </div>
	</div>
	<!-- End Hover Rows -->
</section>

<div class="modal fade" id="modal_form_perfil" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="form_perfiles" class="g-brd-around g-brd-gray-light-v4 g-pa-30 g-mb-30">
	        <!-- Tipo de perfil -->
	        	<input type="hidden" id="tipo" name="tipo" value="<?php echo $tipo_perfil;?>">

	        <!-- ID perfil -->
	        	<input type="hidden" id="id" name="id" value="">

				  <!-- Text Input -->
				  <div class="form-group g-mb-20">
				    <label class="g-mb-10" for="inputGroup1_1">Nombre del perfil de <?php echo $nombre_perfil;?>(*)</label>
				    <input id="nombre" name="nombre" class="form-control form-control-md rounded-0" placeholder="Ingrese nombre de perfil" type="text" required>
				    <small class="form-control-feedback"></small>
				  </div>
				  <!-- End Text Input -->

				  <!-- Textarea Expandable -->
				  <div class="form-group g-mb-20">
				    <label class="g-mb-10" for="inputGroup2_2">Descripción(*)</label>
				    <textarea id="descripcion" name="descripcion" class="form-control form-control-md u-textarea-expandable rounded-0" rows="3" 
				    	placeholder="Ingrese descripción del perfil"></textarea>
				  </div>
				  <!-- End Textarea Expandable -->

				  <!-- Select Single Date -->
				  <div class="form-group g-mb-30">
				    <label class="g-mb-10">Fecha inicio vigencia(*)</label>
				    <div class="input-group g-brd-primary--focus">
				      <input id="fecha_inicio_vigencia" name="fecha_inicio_vigencia" class="form-control form-control-md  rounded-0" type="date">
				    </div>
				  </div>
				  <!-- End Select Single Date -->
				<button id="btnSave" type="submit" class="btn btn-primary"></button>
        <button type="button" class="btn u-btn-red" data-dismiss="modal">Cerrar</button>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_add_attribute" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	<form id="form_asignar_atributo" class="g-brd-around g-brd-gray-light-v4 g-pa-30 g-mb-30">
      		<div id="error_form" class="" style="display:none" role="alert"></div>
      		<!-- ID profile_attribute -->
      		<input type="hidden" id="id_profile_attribute" name="id_profile_attribute" value="">
      		<!-- Tipo ID -->
      		<input type="hidden" id="tipo_perfil_atributo" name="tipo_perfil_atributo" value="<?php echo $tipo_perfil;?>">
				  <!-- Select perfil -->
				  <div class="form-group g-mb-20">
				    <label class="mr-sm-3 mb-3 mb-lg-0" for="profile_id">Perfil (*)</label>
				    <select class="custom-select mb-3" id="profile_id" required>
				      <!-- Populate with ajax -->
				    </select>
				  </div>
				  <!-- End select perfil -->

				  <!-- Select tipo vencimiento -->
				  <div class="form-group g-mb-20">
				    <label class="mr-sm-3 mb-3 mb-lg-0" for="attribute_id">Atributo (*)</label>
				    <select class="custom-select mb-3" id="attribute_id" name="attribute_id" required>
				      <!-- Populate with ajax -->
				    </select>
				  </div>
				  <!-- End select tipo vencimiento -->

				  <!-- Select Single Date -->
				  <div class="form-group g-mb-30">
				    <label class="g-mb-10">Fecha inicio vigencia(*)</label>
				    <div class="input-group g-brd-primary--focus">
				      <input id="fecha_inicio_vigencia_atributo_perfil" name="fecha_inicio_vigencia_atributo_perfil" class="form-control form-control-md  rounded-0" type="date">
				      <div class="input-group-addon d-flex align-items-center g-bg-white g-color-gray-dark-v5 rounded-0">
				        <i class="icon-calendar"></i>
				      </div>
				    </div>
				  </div>
				  <!-- End Select Single Date -->
					<button id="btnSaveAssign" type="submit" class="btn btn-primary">Asignar atributo</button>
        	<button type="button" class="btn u-btn-red" data-dismiss="modal">Cerrar</button>
      	</form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_delete_profile" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">¿ Esta seguro de eliminar este perfil ?</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	<input type="hidden" id="id_profile_delete" name="id_profile_delete" value="">
       	<p id="name_profile_delete"><strong>Nombre: </strong> </p>
       	<br>
       	<p id="description_profile_delete"><strong>Detalle: </strong></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn u-btn-red" onclick="destroy_profile()">Eliminar</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal baja de atributo del perfil -->
<div class="modal fade" id="modal_delete_attribute_profile" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">¿ Esta seguro de quitar este atributo al perfil ?</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	<input type="hidden" id="id_attribute_profile_delete" name="id_attribute_profile_delete" value="">
       	<p id="name_profile"></p>
       	<p id="name_attribute_delete"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn u-btn-red" onclick="destroy_attribute_profile()">Eliminar</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
<!-- Fin modal baja de atributo del perfil -->

<script type="text/javascript">
	let save_method
	let table_perfiles
	let table_perfiles_atributos
	let form_perfiles
	let form_assign_attribute


	function create_profile() {
		save_method = 'create';
		clean_form('form_perfiles')
		$("#form_perfiles #id").val('')
		$('#modal_form_perfil .modal-title').text('Alta de perfil')
		$('#modal_form_perfil #btnSave').text('Grabar perfil')

		$('#modal_form_perfil').modal('show')
	}

	function edit_profile(id) {
		$('#error_form').removeClass('alert alert-danger alert-info')
		save_method = 'update';
		clean_form('form_perfiles')

		$.ajax({
			url: "<?php echo base_url('Perfiles/edit/');?>" + id,
			type: "GET",
			dataType: "JSON",
			success: function(data) {
				$('[name=id]').val(data.id)
				$('[name=nombre]').val(data.nombre)
				$('[name=descripcion]').val(data.descripcion)
				$('[name=fecha_inicio_vigencia]').val(data.fecha_inicio_vigencia)

				$('#modal_form_perfil .modal-title').text('Modificar perfil')
				$('#modal_form_perfil #btnSave').text('Actualizar perfil')
				$('#modal_form_perfil').modal('show')
			},
			error: function(jqXHR, textStatus, errorThrown) {
				noty_alert( 'error' , 'Error obteniendo datos' )
			}
		});
	}

	function save() {
		let url = "<?php echo base_url();?>Perfiles/" + save_method;
		disable_btn_save( true )
    $.ajax({
    	url: url,
    	type: "POST",
    	data: $('#form_perfiles').serializeArray(),
    	dataType: 'JSON',
    	success: function(response) {
    		if (response.status === 'success') {
    			table_perfiles.ajax.reload(null,false);
    			noty_alert( response.status, response.msg )
    			$('#modal_form_perfil').modal('hide');
    		} else if(response.status === 'existe'){
    			noty_alert( 'info', response.msg )
    		} else {
    			noty_alert( response.status, response.msg )
    		}
    		disable_btn_save( false )
    	},
    	error: function(jqXHR, textStatus, errorThrown){
    		noty_alert( 'error' , 'No se pudieron guardar los datos' )
    		disable_btn_save( false )
    	}
    });
	}
// Llamo al modal de advertencia para eliminar el perfil
	function modal_destroy_profile(id) {
		$.ajax({
			url: "<?php echo base_url('Perfiles/edit/');?>" + id,
			type: "GET",
			dataType: "JSON",
			success: function(resp){
				$('#modal_delete_profile #id_profile_delete').val(resp.id);
				$('#modal_delete_profile #name_profile_delete').append(resp.nombre);
				$('#modal_delete_profile #description_profile_delete').append(resp.descripcion);
				$('#modal_delete_profile').modal('show');
			},
			error: function(){
				noty_alert( 'error' , 'Error al obtener los datos' )
			}
		});
	}
	// Elimino el perfil
	function destroy_profile() {
		var id_profile = $('#id_profile_delete').val();
		$.ajax({
			url: "<?php echo base_url('Perfiles/destroy/');?>" + id_profile,
			type: "GET",
			dataType: 'JSON',
			success: function(response){
				if (response.status === 'success') {
					table_perfiles.ajax.reload(null,false);
					$('#modal_delete_profile').modal('hide');
				}
				noty_alert( response.status, response.msg )
			},
			error: function(jqXHR, textStatus, errorThrown){
				noty_alert( 'error' , 'No se pudo eliminar el perfil' )
			}
		});
	}

// ###############################################################
// ###############################################################
// Funciones para operaciones de asignacion de atributos al perfil

	// Obtengo los perfiles para imprimirlos en el select de asignar atributo al perfil
	function print_profiles(profile_id) {
		$.ajax({
			url: '<?php echo base_url("Perfiles/ajax_get_profiles/").$tipo_perfil?>',
			type: 'GET',
			success: function(resp){
				var profiles = $.parseJSON(resp)
				// Si me traigo el ID para editar
				if (typeof(profile_id) !== "undefined") {
					$('#profile_id').find('option').remove().end().append('<option >Seleccione perfil</option>')
					$('#profile_id').attr('disabled', true)
					$(profiles).each(function(i, element){
						// Comparo para dejar seleccionado el id de atributo
						if (profile_id == element.id) {
							$('#profile_id').append("<option value="+element.id+" selected>"+element.nombre+"</option>");
						} else {
							$('#profile_id').append("<option value="+element.id+">"+element.nombre+"</option>");
						}
					});
				} else {
					$('#profile_id').attr('disabled', false)
					$('#profile_id').find('option').remove().end().append('<option disabled selected >Seleccione perfil</option>')
					$(profiles).each(function(i, element){
						$('#profile_id').append("<option value="+element.id+">"+element.nombre+"</option>");
					});
				}
			},
			error: function(jqXHR, textStatus, errorThrown){
				$('#profile_id').find('option').remove().end().append('<option value="" disabled selected >Seleccione perfil</option>');
				$('#profile_id').append("<option value=''>No se pudieron obtener los perfiles</option>");
			}
		});
	}

	// Obtengo los atributos para el modal asignar perfil atributo
	function print_attributes(attribute_id){
		$.ajax({
			url: '<?php echo base_url("Atributos/ajax_get_attributes/").$tipo_perfil; ?>',
			type: 'GET',
			success: function(resp){
				var attributes = $.parseJSON(resp)
				// Si me traigo el ID para editar
				if (typeof(attribute_id) !== "undefined") {
					$('#attribute_id').find('option').remove().end().append('<option disabled >Seleccione atributo</option>')
					$('#attribute_id').attr('disabled', true)
					$(attributes).each(function(i, element){
						// Comparo para dejar seleccionado el id de atributo
						if (attribute_id == element.id) {
							$('#attribute_id').append("<option value="+element.id+" selected>"+element.nombre+"</option>");
						} else {
							$('#attribute_id').append("<option value="+element.id+">"+element.nombre+"</option>");
						}
					});
				} else {
					$('#attribute_id').attr('disabled', false)
					$('#attribute_id').find('option').remove().end().append('<option value="" disabled selected >Seleccione atributo</option>')
					$(attributes).each(function(i, element){
						$('#attribute_id').append("<option value="+element.id+">"+element.nombre+"</option>");
					});
				}
			},
			error: function(jqXHR, textStatus, errorThrown){
				$('#profile_id').find('option').remove().end().append('<option value="" disabled selected >Seleccione atributo</option>');
				$('#profile_id').append("<option value=''>No se pudieron obtener los atributos</option>");
			}
		});
	}

	function modal_assign_attribute() {
		save_method = 'create';
		$('#form_asignar_atributo')[0].reset();
		$('#modal_add_attribute .modal-title').text('Asignación de atributo al perfil de  <?php echo $nombre_perfil; ?>');
		$('#modal_add_attribute #btnSave').text('Asignar atributo');
		$('#form_asignar_atributo .form-control').removeClass('error');
		$('.error').empty();
		$('#error_form').hide(500)
		$('#error_form').removeClass('alert alert-danger alert-info')
		print_profiles();
		print_attributes();
		$('#modal_add_attribute').modal('show');
	}

	function modal_edit_attribute(id) {
		save_method = 'update'
		$('#error_form').hide(500)
		$('#error_form').removeClass('alert alert-danger alert-info')
		$('#form_asignar_atributo')[0].reset()
		$.ajax({
			url: "<?php echo base_url('Perfiles_Atributos/edit/');?>" + id,
			type: "GET",
			dataType: "JSON",
			success: function(data) {
				print_profiles(data.profile_id);
				print_attributes(data.attribute_id);
				$('#modal_add_attribute .modal-title').text('Modificación de atributo al perfil de  <?php echo $nombre_perfil; ?>');
				$('#id_profile_attribute').val(data.id)
				$('#fecha_inicio_vigencia_atributo_perfil').val(data.fecha_inicio_vigencia)
				$('.form-control').removeClass('error');
				$('.error').empty();
				$('#modal_add_attribute').modal('show');
			},
			error: function(jqXHR, textStatus, errorThrown){
				noty_alert( 'error' , 'Error al obtener los datos' )
			}
		})
	}

	function save_asign_attribute() {
		let data = {
			id : $('#id_profile_attribute').val(),
			tipo : $('#tipo_perfil_atributo').val(),
			perfil_id : $('#profile_id').val(),
			atributo_id : $('#attribute_id').val(),
			fecha_inicio_vigencia : $('#fecha_inicio_vigencia_atributo_perfil').val()
		}
		let url = '<?php echo base_url("Perfiles_Atributos/");?>'+save_method
		$.ajax({
			url: url,
			type: 'POST',
			cache: false,
			data: data ,
			dataType: 'JSON',
			success: function( response ){
				if ( response.status === 'success' ) {
					table_perfiles_atributos.ajax.reload(null,false);
					noty_alert(response.status, response.msg)
					$('#modal_add_attribute').modal('hide');
				} else {
					$('#error_form').addClass(`alert alert-${response.class}`)
					$('#error_form').html(response.msg)
					$('#error_form').show('slow')
				}
			},
			error: function(jqXHR, textStatus, errorThrown){
				noty_alert( 'error' , 'No se pudo guardar la informacion' )
			}
		});
	}

// Llamo al modal de advertencia para eliminar el atributo del perfil
	function modal_delete_attribute_profile(id) {
		$.ajax({
			url: "<?php echo base_url('Perfiles_Atributos/edit/');?>" + id,
			type: "GET",
			dataType: "JSON",
			success: function(response) {
				$('#modal_delete_attribute_profile #id_attribute_profile_delete').val(response.id)
				$('#modal_delete_attribute_profile #name_profile').html(`<b>Perfil:</b> ${response.nombre_perfil}`);
				$('#modal_delete_attribute_profile #name_attribute_delete').html(`<b>Atributo:</b> ${response.nombre_atributo}`);
				$('#modal_delete_attribute_profile').modal('show');
			},
			error: function() {
				noty_alert( 'error' , 'Error al obtener los datos' )
			}
		});
	}
	// Elimino el perfil
	function destroy_attribute_profile() {
		let id_attribute_profile = $('#id_attribute_profile_delete').val();
		$.ajax({
			url: "<?php echo base_url('Perfiles_Atributos/destroy/');?>" + id_attribute_profile,
			type: "POST",
			dataType: 'JSON',
			success: function(response) {
				if (response.status === 'success') {
					table_perfiles_atributos.ajax.reload(null,false);
					$('#modal_delete_attribute_profile').modal('hide');
				}
				noty_alert( response.status, response.msg )
			},
			error: function(jqXHR, textStatus, errorThrown) {
				noty_alert( 'error' , 'No se pudo desvincular el atributo' )
			}
		})
	}

	function disable_btn_save( disable ){
		if (disable) {
			$('#btnSave').text('guardando...') //change button text
		} else {
			$('#btnSave').text('Guardar') //change button text
		}
		$('#btnSave').attr('disabled',disable) //set button disable
	}

  $(document).on('ready', function () {


		$.validator.addMethod("alfanumOespacio", function(value, element) {
		        return /^[ a-záéíóúüñ ,]*$/i.test(value);
		    }, "Ingrese sólo letras.");

		form_perfiles = $('#form_perfiles').validate({
																rules: {
																	'nombre': { alfanumOespacio: false,
																	 						minlength: 3,
																	 						required: true,
																	 						remote: {
												                        url: "<?php echo base_url('Perfiles/existe');?>",
												                        type: "POST",
												                        data: {
												                              nombre: function() {
												                                return $('#nombre').val()
												                            	},
											                            		tipo: function() {
											                            			return $('#form_perfiles #tipo').val()
											                            		},
											                            		id: function(){
											                            			return $('#form_perfiles #id').val()
											                            		}
											                          }
											                        } 
                    							},
																	'descripcion': { minlength: 5 }
																},
																messages: {
																	'nombre': {
																		remote: 'Este perfil ya se encuentra registrado'
																	}
																}
															})

		form_assign_attribute = $('#form_asignar_atributo').validate({
																rules: {
																	'profile_id': { required: true },
																	'attribute_id': { required: true }
																}
						})	

	$('#form_perfiles').submit(function(e){
		e.preventDefault();
		e.stopImmediatePropagation()
		if (form_perfiles.valid()) { save() }
	})

	$('#form_asignar_atributo').submit(function(e){
		e.preventDefault();
		e.stopImmediatePropagation()
		if (form_assign_attribute.valid()) { save_asign_attribute(); }
	})

		table_perfiles = $('#tabla_perfiles').DataTable( {
													ajax : "<?php echo base_url('Perfiles/ajax_list_perfiles/').$tipo_perfil;?>",
													columns: [
														{ "data": "nombre"  },
														{ "data": "descripcion" },
														{ "data": "fecha_inicio_vigencia" },
														{ "data": "fecha_baja" },
														{ "data": "acciones" }
													],
													language: {
								                url: "<?php echo base_url(); ?>assets/vendor/datatables/spanish.json"
								              }
												});

		table_perfiles_atributos = $('#tabla_perfiles_atributos').DataTable({
																	ajax: '<?php echo base_url("Perfiles_Atributos/ajax_list/").$tipo_perfil;?>',
																	language: {
												                url: "<?php echo base_url(); ?>assets/vendor/datatables/spanish.json"
												              }
		});
		$('#attribute_id').select2({ theme: 'bootstrap4', width: '60%' })
    
		$('#profile_id').select2({ theme: 'bootstrap4',width: '60%' })

  });
</script>
