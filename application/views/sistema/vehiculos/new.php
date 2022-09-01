<section class="container g-py-10">
	<h1> Alta de vehiculo </h1>

	<?php if($this->session->flashdata('errors')){ ?> 
		<div class="alert alert-danger" role="alert">
		  <?php echo $this->session->flashdata('errors'); ?>
		</div>
	<?php } ?>
	<!-- Form alta de vehiculo -->
	<?php echo form_open_multipart('Vehiculos/create', array( 'id'=>'form_alta_vehiculo','class'=>'form_new_vehiculo g-brd-around g-brd-gray-light-v4 g-pa-15 g-mb-15', 'method' => 'POST'));?>
	  <?php $this->load->view('sistema/vehiculos/_form') ?>
	  <div class="row g-mb-10 ml-2 mt-2">
  		<button type="submit" id="btn_save_vehiculo" class="btn btn-md u-btn-primary g-mr-10"> Grabar vehiculo </button>
  		<a href="<?php echo base_url('Vehiculos');?>" class="btn btn-md u-btn-red g-mr-10"> Cancelar </a>
  	</div>
	</form>
	<!-- End form alta vehiculo -->
</section>



<!-- Modales -->

<div class="modal fade" id="modal_crud_attr_vehiculos" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	<form id="form_attr_vehiculo" class="form_attr_vehiculo">
      		<!-- Tipo de attr para discriminar entre marca/modelo/tip -->
      		<input type="hidden" id="tipo_attr" name="tipo_attr" value="">
      		<div class="form-group row">
      			<label id="label_name_attr" class="col-form-label col-2"></label>
      			<input type="text" id="name_attr" name="name_attr" class="form-control u-form-control rounded-0 col-4" required>
      			<button type="submit" id="btn_save_name_attr" href="" class="ml-2 btnSave btn u-btn-primary"> </button>
      		</div>
      		<br>
      		<div id="alert-msg-pers" class="alert-msg-vehiculos"></div>
      	</form>
      	<br>
      	<div>
      		<table id="tabla_attr_vehiculos" class="table table-hover w-100 compact">
      			<thead>
      				<tr>
      					<th>Nombre</th>
      					<th>Acciones</th>
      				</tr>
      			</thead>
      			<tbody>
      				<!-- populate with ajax -->
      			</tbody>
      		</table>
      	</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn u-btn-primary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal CRUD Modelos vehiculos -->
<div class="modal fade" id="modal_crud_attr_modelos_vehiculos" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	<form id="form_modelo_vehiculo" class="">
      		<!-- Tipo de attr para discriminar entre marca/modelo/tip -->
      		<input type="hidden" id="tipo_attr" name="tipo_attr" value="">

      		<!-- ID marca para cuando creamos/editamos un modelo -->
      		<!-- Select marca -->
				  <div class="form-group row g-mb-10">
					  <label class="col-sm-2 col-form-label g-mb-10" for="marca_attr_id">Marca (*)</label>
					  <div class="col-sm-6">
					  	<select class="custom-select" id="marca_attr_id" name="marca_attr_id" >
					    <option selected=""> Seleccione marca </option>
					    <!-- ajax -->
					  </select>
					  </div>
				  </div>
	 				 <!-- End Select marca -->

      		<label id="label_name_attr">Nombre modelo: </label>
      		<input type="text" id="name_attr_modelo" name="name_attr_modelo" >
      		<button type="submit" id="btn_save_modelo" href="" class="btnSave btn u-btn-primary"> </button>
      		<br>
      		<div id="alert-msg-pers" class="alert-msg-vehiculos"></div>
      	</form>
      	<br>
	      	<div class="table-responsive">
	      		<table id="tabla_attr_modelo_vehiculos" class="table table-hover w-100">
	      			<thead>
	      				<tr>
	      					<th>Marca</th>
	      					<th>Modelo</th>
	      					<th>Acciones</th>
	      				</tr>
	      			</thead>
	      			<tbody>
	      				<!-- populate with ajax -->
	      			</tbody>
	      		</table>
	      	</div>
      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn u-btn-primary" data-dismiss="modal">Cerrar</button>
	      </div>
      </div>
    </div>
</div>


<!-- Modal baja de atributo del vehiculo -->
<div class="modal fade" id="modal_delete_attr_vehiculo" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	<input type="hidden" id="id_attr_vehiculo" name="id_attr_vehiculo" value="">
      	<input type="hidden" id="tipo_attr_vehiculo" name="tipo_attr_vehiculo" value="">
       	<p id="name_attr"><strong>Nombre: </strong> </p>
       	<br>
       	<div id="text-caution-delete-marca" class=""><small class="text-danger">¡ATENCION! Al eliminar la marca todos los modelos de esta marca igual seran eliminados.</small></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn u-btn-red" onclick="destroy_attr_vehiculo()">Eliminar</button>
      </div>
    </div>
  </div>
</div>
<!-- Fin modal baja de atributo del vehiculo -->

<script>
	let tabla_attr_vehiculos,tabla_modelos_vehiculos
	let vehiculo_url, name_attr,save_method
	let form_attr_vehiculo,form_modelo_vehiculo,form_vehiculo


	// Genero el option select , el type es el atributo que vamos a elegir, el cual puede ser marca, modelo o tipo vehiculo
	// attr es attribute de la tabla
	function print_attributes(select_id, type, attr = null, id = null) {
		if (id !== null) {
			vehiculo_url = "<?php echo base_url('Vehiculos/get_attr/');?>"+type+"/"+attr+"/"+id
		} else {
			vehiculo_url = "<?php echo base_url('Vehiculos/get_attr/');?>"+type
		}

		$.ajax({
			url: vehiculo_url,
			type: 'GET',
			dataType: 'JSON',
			success: function(response){
				$('#'+select_id+'').find('option').remove().end().append('<option value="" disabled selected >Seleccione '+type+'</option>')
				$(response).each(function(i, element){
					$('#'+select_id+'').append("<option value="+element.id+">"+element.nombre+"</option>");
				});
			},
			error: function(jqXHR, textStatus, errorThrown){
				$('#'+select_id+'').find('option').remove().end().append('<option value="" disabled selected >Seleccione '+type+'</option>');
				$('#'+select_id+'').append("<option>No se pudieron obtener los "+type+"</option>");
			}
		});
	}

	function edit_attr_vehiculo(name)
	{

	}

	function save_attr_vehiculo(table, nombre, marca_id) {
		if (table != 'modelo') {
			marca_id = 'a'
		}

		$.ajax({
			url: "<?php echo base_url('Vehiculos/');?>"+save_method+'/'+table,
			type: 'POST',
			cache: false,
			data: {
				nombre: nombre,
				marca_id: marca_id
			},
			dataType: 'JSON',
			success: function(response){
				if (response.status === 'success') {
					if (table != 'modelo') {
						$('.btnSave').prop( "disabled", false )
						$('.btnSave').text( 'Grabar '+table )
						$('#form_attr_vehiculo')[0].reset()
						$('.alert-msg-vehiculos').html('')
						print_attributes(table, table)
						tabla_attr_vehiculos.ajax.reload(null,false)
					} else {
						$('#btn_save_modelo').text('Grabar modelo')
						$('#btn_save_modelo').prop( "disabled", false )
						$('#form_modelo_vehiculo')[0].reset()
						tabla_modelos_vehiculos.ajax.reload(null,false)
					}
					$('.alert-msg-vehiculos').html('')
					print_attributes(table, table)
					noty_alert(response.status, response.msg)
				} else {
					$('.btnSave').prop( "disabled", false )
					$('.btnSave').text( 'Grabar '+table )
					noty_alert(response.status, response.msg)
				}
			},
			error: function(jqXHR, textStatus, errorThrown){
				$('.btnSave').prop( "disabled", false )
				$('.btnSave').text( 'Grabar ' + table )
				$('#btn_save_modelo').text('Grabar modelo')
				$('#btn_save_modelo').prop( "disabled", false )
				noty_alert( 'error' , 'No se pudo guardar la informacion' )
			}
		})
	}

	function modal_delete_attr_vehiculo(type, id) {
		if (type = 'marca') {
			$('#text-caution-delete-marca').show()
			vehiculo_url = '<?php echo base_url("Vehiculos/get_attr/marca/id/");?>'+id
		} else {
			$('#text-caution-delete-marca').hide()
			vehiculo_url = '<?php echo base_url("Vehiculos/get_attr/");?>'+type+'/id'+id
		}

		$.ajax({
			url: vehiculo_url,
			type: 'GET',
			dataType: "JSON",
			success: function(resp)
			{
				$('#modal_delete_attr_vehiculo #name_attr').append(resp[0].nombre)
				$('#modal_delete_attr_vehiculo #tipo_attr_vehiculo').val(type)
				$('#modal_delete_attr_vehiculo #id_attr_vehiculo').val(resp[0].id)

				$('#modal_delete_attr_vehiculo').modal('show')
			},
			error: function()
			{
				noty_alert( 'error' , 'No se pudiron obtener los datos' )
			}
		})
	}

	function destroy_attr_vehiculo() {
		let type = $('#modal_delete_attr_vehiculo #tipo_attr_vehiculo').val()
		let id = $('#modal_delete_attr_vehiculo #id_attr_vehiculo').val()

		if (type == 'marca') {
			vehiculo_url = '<?php echo base_url("Vehiculos/destroy_marca/");?>'+id
		} else {
			vehiculo_url = '<?php echo base_url("Vehiculos/destroy/");?>'+id
		}
		$.ajax({
			url: vehiculo_url,
			type: "POST",
			dataType: 'JSON',
			success: function(response) {
				if (response.status === 'success') {
					tabla_attr_vehiculos.ajax.reload(null,false);
					print_attributes(type, type)
					$('#modal_delete_attr_vehiculo').modal('hide');
				}
				noty_alert( response.status, response.msg )
			},
			error: function(jqXHR, textStatus, errorThrown) {
				noty_alert( 'error' , 'No se pudo eliminar' )
			}
		});
	}

	$('#marca').on('change', function(){
		var marca_id = $('#marca').val()
		print_attributes('modelo','modelo', 'marca_vehiculo_id',marca_id)
	})

	print_attributes('marca','marca')
	print_attributes('tipo','tipo')


	function modal_crud_attr(nombre_attr){
		vehiculo_url = "<?php echo base_url('Vehiculos/list_attr/');?>"+nombre_attr
		$('.btnSave').prop( "disabled", false )
		$('.alert-msg-vehiculos').html('')
		$('.form-control').removeClass('error');
		$('.error').empty();
		if (nombre_attr == 'modelo') {
			$('#form_modelo_vehiculo')[0].reset()
			tabla_modelos_vehiculos.ajax.url(vehiculo_url).load()
			print_attributes('marca_attr_id','marca')
			$('#marca_attr_id').select2({theme: 'bootstrap4', width: '70%'})
			$('#form_modelo_vehiculo #tipo_attr').val(nombre_attr)
			$('#btn_save_modelo').text('Grabar '+nombre_attr)
			$('#modal_crud_attr_modelos_vehiculos .modal-title').html(`Administracion de ${nombre_attr}s`)
			$('#modal_crud_attr_modelos_vehiculos').modal('show')
		} else {
			$('#form_attr_vehiculo')[0].reset()
 			tabla_attr_vehiculos.ajax.url(vehiculo_url).load()
			$('#tipo_attr').val(nombre_attr)
			$('#label_name_attr').text('Nombre '+nombre_attr)
			$('#btn_save_name_attr').text('Grabar '+nombre_attr)
			$('#modal_crud_attr_vehiculos .modal-title').html(`Administracion de ${nombre_attr}s`)
			$('#modal_crud_attr_vehiculos').modal('show')
		}
	}

	function agrupar_datos() {
		let data = new FormData()
		data.append('interno', document.getElementById('interno').value)
		data.append('dominio', document.getElementById('dominio').value)
		data.append('anio', document.getElementById('anio').value)
		data.append('patentamiento', document.getElementById('patentamiento').value)
		data.append('marca_id', document.getElementById('marca').value)
		data.append('modelo_id', document.getElementById('modelo').value)
		data.append('tipo_id', document.getElementById('tipo').value)
		data.append('chasis', document.getElementById('chasis').value)
		data.append('motor', document.getElementById('motor').value)
		data.append('asientos', document.getElementById('asientos').value)
		data.append('observaciones', document.getElementById('observaciones').value)
		// data.append('asignacion', document.getElementById('asignacion').value)
		// data.append('fecha_alta_asignacion', document.getElementById('fecha_alta_asignacion').value)

		let total_files = document.getElementById('imagenes').files.length
    for (let index = 0; index < total_files; index++) {
      data.append("imagenes[]", document.getElementById('imagenes').files[index]);
    }

		return data
	}

	function add_attr_vehiculo() {
		save_method = 'create_attr_vehiculo'
		var type = $('#tipo_attr').val()
		var marca_id
		if (type != 'modelo') {
			name_attr = $('#name_attr').val()
			$('#btn_save_name_attr').prop( "disabled", true )
			$('#btn_save_name_attr').text('Grabando...')
		} else {
			name_attr = $('#name_attr_modelo').val()
			marca_id = $('#marca_attr_id option:selected').val()
			$('#btn_save_modelo').prop( "disabled", true )
			$('#btn_save_modelo').text('Grabando...')
		}
		save_attr_vehiculo(type, name_attr, marca_id)
	}

	$(document).on('ready', function () {

	$.validator.addMethod("alfanumOespacio", function(value, element) {
	        return /^[a-z\- áéíóúüñ0-9]*$/i.test(value);
	    }, "Ingrese sólo letras y numeros.");

	form_attr_vehiculo = $('.form_attr_vehiculo').validate({
															rules: {
																'nombre_attr': { required: true	}
															}
														});

	form_modelo_vehiculo = $('#form_modelo_vehiculo').validate({
															rules: {
																'name_attr_modelo': { required: true	},
																'marca_attr_id': { required: true	}
															}
														})

	form_vehiculo = $('#form_alta_vehiculo').validate({
												rules: {
													'interno': { alfanumOespacio: true, 
																			 required: true,
	                                      remote: {
	                                        url: "<?php echo base_url('Vehiculos/num_interno_libre');?>",
	                                        type: "POST",
	                                        data: {
                                          			interno: function() {
                                            			return $('#interno').val()
		                                          }
		                                      }
		                                    } 
																			},
													'fecha_alta_asignacion': {
														required: function (element){
				                      return $('#asignacion').val() != ''
				                    }
													},
													'dominio': {
														required: true
													}
												},
												messages: {
													'interno': {
														remote: 'Este numero de interno pertenece a otro vehiculo'
													}
												}
	})

	document.getElementById('form_alta_vehiculo').addEventListener('submit', function(e){
		e.preventDefault()
		e.stopPropagation()
		if( form_vehiculo.valid() ){
			console.log( `${form_vehiculo.valid()} =>` )
			fetch( "<?php echo base_url('Vehiculos/create')?>", {
				method: 'POST',
	      body: agrupar_datos()
			} )
			.then(response => response.json() )
	    .then(response => {
	    	if (response.status === 'success') {
	    		window.location.href = '<?php echo base_url('Vehiculos'); ?>'
	    	} else {
	    		noty_alert( response.status, response.msg )
	    	}
	    } )
	    .catch(error => noty_alert( 'error', 'No se pudo registrar el vehiculo' ) )
		} else {
			console.info('no valid')
		}
	})

	$('#form_attr_vehiculo').submit(function(e){
		e.preventDefault()
		if (form_attr_vehiculo.valid()) {
			save_method = 'create_attr_vehiculo'
			let type = $('#tipo_attr').val()
			let marca_id
			if (type != 'modelo') {
				name_attr = $('#name_attr').val()
				$('#btn_save_name_attr').prop( "disabled", true )
				$('#btn_save_name_attr').text('Grabando...')
			} else {
				name_attr = $('#name_attr_modelo').val()
				marca_id = $('#marca_attr_id option:selected').val()
				$('#btn_save_modelo').prop( "disabled", true )
				$('#btn_save_modelo').text('Grabando...')
			}
			save_attr_vehiculo(type, name_attr, marca_id)
		}
	})

	$('#form_modelo_vehiculo').submit(function(e){
		e.preventDefault()
		if (form_modelo_vehiculo.valid()) {
			save_method = 'create_attr_vehiculo'
			let type = $('#tipo_attr').val()
			let marca_id

			name_attr = $('#name_attr_modelo').val()
			marca_id = $('#marca_attr_id option:selected').val()
			$('#btn_save_modelo').prop( "disabled", true )
			$('#btn_save_modelo').text('Grabando...')

			save_attr_vehiculo('modelo', name_attr, marca_id)
		}
	})

	tabla_attr_vehiculos = $('#tabla_attr_vehiculos').DataTable( {
															lengthChange: false,
															responsive: true,
															ajax : "<?php echo base_url('Vehiculos/list_attr/marca');?>",
															language: { url: "<?php echo base_url('assets/vendor/datatables/spanish.json');?>"}
														})

	tabla_modelos_vehiculos = $('#tabla_attr_modelo_vehiculos').DataTable( {
															lengthChange: false,
															responsive: false,
															ajax : "<?php echo base_url('Vehiculos/list_attr/modelo');?>",
															language: {url: "<?php echo base_url('assets/vendor/datatables/spanish.json');?>"}
														})
		
    $('#empresa').select2( { theme: 'bootstrap4', width: '70%' } )
    $('#marca').select2( { theme: 'bootstrap4', width: '70%' } )
    $('#modelo').select2({theme: 'bootstrap4', width: '70%'})
    $('#tipo').select2( { theme: 'bootstrap4', width: '70%' } )
    $('#asignacion').select2( { theme: 'bootstrap4', width: '70%' } )
	})
</script>