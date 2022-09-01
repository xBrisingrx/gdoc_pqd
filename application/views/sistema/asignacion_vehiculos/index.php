<div class="container-fluid g-py-10 mb-4">
	<h1>Lugares para asignar vehiculos</h1>

	<?php if ($this->session->userdata('rol') == 1) { ?>
		<button class="btn u-btn-blue mb-2" onclick="new_asignacion()"> Nueva </button>			
	<?php } ?>
	<!-- Hover Rows -->
	<div class="card g-brd-darkpurple rounded-0 g-mb-30">
	  <h3 class="card-header g-bg-darkpurple g-brd-transparent g-color-white g-font-size-16 rounded-0 mb-2">
	    <i class="fa fa-gear g-mr-5"></i>
	    Listado de lugares para asignar vehiculos
	  </h3>
	   <div class="px-2  pb-2">
	    <table id="tabla_asignaciones" class="table table-hover dt-responsive w-100 u-table--v1">
	      <thead>
	        <tr>
	          <th>Lugar</th>
	          <th>Descripción</th>
	          <?php if ($this->session->userdata('rol') == 1): ?>
							<th>Acciones</th>			
						<?php endif ?>
	        </tr>
	      </thead>

	      <tbody>
	      	<!-- Completo con ajax -->
	      </tbody>
	    </table>
	  </div>
	</div>
	<!-- End Hover Rows -->
</div>

<div class="modal fade" id="modal_asignaciones" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Alta de asignacion</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	<?php echo form_open( '',array('id' => 'form_asignaciones', 'class' => 'g-brd-around g-brd-gray-light-v4 g-pa-30 g-mb-30') ) ?>
      		<p id="error-form" class="text-danger"></p>
      		<input type="hidden" id="asignacion_id" value="">
				  <!--Input nombre usuario -->
				  <div class="form-group row g-mb-5">
				    <label class="col-sm-2 col-form-label g-mb-5" for="nombre">Lugar (*)</label>
				    <div class="col-sm-9">
					    <input id="nombre" name="nombre" class="form-control form-control-md rounded-0" 
					    				placeholder="Lugar de asignacion" type="text" required>
					    <small class="form-control-feedback nombre_asignacion"></small>
				    </div>
				  </div>
				  <!-- Input nombre usuario -->

				  <!-- Textarea Expandable -->
				  <div class="form-group g-mb-20">
				    <label class="g-mb-10" for="inputGroup2_2">Descripción </label>
				    <textarea id="descripcion" name="descripcion" class="form-control form-control-md u-textarea-expandable rounded-0" rows="3" placeholder="Ingrese descripción de la asignacion"></textarea>
				  </div>
				  <!-- End Textarea Expandable -->

					<button type="submit" class="btn btn-primary">Guardar</button>
        	<button type="button" class="btn u-btn-red closeModal" data-dismiss="modal">Cerrar</button>
      	</form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_destroy_asignacion" tabindex="-1" role="dialog" >
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">¿ Esta seguro de eliminar esta asignacion ?</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	<input type="hidden" id="id_asignacion_delete" name="id_asignacion_delete" value="">
       	<p id="name_asignacion_delete"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn u-btn-primary" onclick="destroy_asignacion()">Eliminar</button>
        <button type="button" class="btn u-btn-red closeModal" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
	let save_method
	let tabla_asignacione
	let url
	let form_asignaciones = $('#form_asignaciones')

	function new_asignacion() {
		save_method = 'create'
		$('#form_asignaciones')[0].reset()
		$('.form-control').removeClass('error');
		$('#form_asignaciones #nombre').parent().removeClass('u-has-error-v1')
		$('.error').empty();
		$('#modal_asignaciones').modal('show')
	}

	function modal_edit_asignacion( id ) {
		save_method = 'update';
		$('#form_asignaciones')[0].reset()
		$('.form-control').removeClass('error')
		$('.error').empty()

		$.ajax({
			url: "<?php echo base_url('Asignaciones_vehiculo/edit/');?>" + id,
			type: "GET",
			dataType: "JSON",
			success: function(data) {
					$('#asignacion_id').val(data.id)
					$('#nombre').val(data.nombre)
					$('#descripcion').val(data.descripcion)
					$('#modal_asignaciones').modal('show')
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert('Error obteniendo datos');
			}
		})
	}

	function save() {
		$.ajax({
			url: '<?php echo base_url("Asignaciones_vehiculo/")?>' + save_method,
			type: 'POST',
			data: agrupar_datos(),
			dataType: 'JSON',
			success: function(response){
				if (response.status === 'success') {
					tabla_asignacione.ajax.reload(null,false)
					$('#modal_asignaciones').modal('hide')
					noty_alert( response.status , response.msg )
					reset_form()
				} else if ( response.status === 'existe' ) {
					$('#form_asignaciones #nombre').parent().addClass('u-has-error-v1')
					$('.nombre_asignacion').text('Esta asignacion se encuentra registrada')
				} else {
					noty_alert( response.status , response.msg )
				}
			},
			error: function(response){
				noty_alert( 'error' , 'No se pudo guardar la información' )
			}
		})
	}

	function agrupar_datos() {
		datos = {
			'id' : $('#asignacion_id').val(),
			'nombre' : $('#nombre').val(),
			'descripcion' : $('#descripcion').val()
		}
		return datos
	}

  $('.closeModal').on('click', function(event) {
    reset_form()
  })

  function reset_form() {
		$('#form_asignaciones')[0].reset()
    $('.nombre_asignacion').text('')
    $('#form_asignaciones #nombre').parent().removeClass('u-has-error-v1')
  }

// Llamo al modal de advertencia para eliminar el usuario
	function modal_destroy_asignacion( id ) {
		$.ajax({
			url: "<?php echo base_url('Asignaciones_vehiculo/edit/');?>" + id,
			type: "GET",
			dataType: "JSON",
			success: function(data) {
				$('#modal_destroy_asignacion #id_asignacion_delete').val(data.id)
				$('#modal_destroy_asignacion #name_asignacion_delete').text('')
				$('#modal_destroy_asignacion #name_asignacion_delete').append(`<strong>Nombre:</strong> ${data.nombre}`)
				$('#modal_destroy_asignacion').modal('show')
			},
			error: function() {
				noty_alert( 'error', 'No se pudieron obtener los datos' )
			}
		});
	}
	// Elimino el usuario
	function destroy_asignacion() {
		var id_asignacion = $('#id_asignacion_delete').val();
		$.ajax({
			url: "<?php echo base_url('Asignaciones_vehiculo/destroy/');?>" + id_asignacion,
			type: "GET",
			dataType: 'JSON',
			success: function(response) {
				if (response.status === 'success') {
					tabla_asignacione.ajax.reload(null,false);
					$('#modal_destroy_asignacion').modal('hide');
				}
				noty_alert( response.status, response.msg )
			},
			error: function(jqXHR, textStatus, errorThrown)  {
				noty_alert( 'error', 'Error: no se pudo dar de baja a la asignacion' )
			}
		});
	}

  $(document).on('ready', function () {

		form_asignaciones.validate({
									rules: {
										nombre: { required: true, alfanumOespacio: true, minlength: 3 }
										}
									})

		$.validator.addMethod("alfanumOespacio", function(value, element) {
		        return /^[ / -a-záéíóúüñ]*$/i.test(value)
		    }, "Ingrese sólo letras.")

		$('#form_asignaciones').submit(function(e){
			e.preventDefault()
			if ( form_asignaciones.valid() ) {
				save()
			}
		})
	
		tabla_asignacione = $('#tabla_asignaciones').DataTable({
										ajax: '<?php echo base_url("Asignaciones_vehiculo/list/");?>',
										language: { url: "<?php echo base_url('assets/vendor/datatables/spanish.json');?>" }
		})
  })
</script>
