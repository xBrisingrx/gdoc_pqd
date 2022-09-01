<div class="container-fluid g-py-10 mb-4">
	<h1>Empresas registradas en el sistema</h1>

	<?php if ($this->session->userdata('rol') == 1) { ?>
		<button class="btn u-btn-blue mb-2" onclick="new_empresa()"> Nueva empresa </button>			
	<?php } ?>
	<!-- Hover Rows -->
	<div class="card g-brd-darkpurple rounded-0 g-mb-30">
	  <h3 class="card-header g-bg-darkpurple g-brd-transparent g-color-white g-font-size-16 rounded-0 mb-2">
	    <i class="fa fa-gear g-mr-5"></i>
	    Listado de empresas para <?php echo $label_tipo_empresa?> registradas
	  </h3>
	   <div class="px-2  pb-2">
	    <table id="tabla_empresas" class="table table-hover dt-responsive w-100 u-table--v1">
	      <thead>
	        <tr>
	          <th>Nombre</th>
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

<div class="modal fade" id="modal_new_empresa" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Alta de empresa</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	<?php echo form_open( 'Empresas/create',array('id' => 'form_new_empresa', 'class' => 'g-brd-around g-brd-gray-light-v4 g-pa-30 g-mb-30') ) ?>
      		<p id="error-form" class="text-danger"></p>
      		<input type="hidden" id="empresa_id" value="">
      		<input type="hidden" id="tipo_empresa" value="<?php echo $tipo_empresa;?>">
				  <!--Input nombre usuario -->
				  <div class="form-group row g-mb-5">
				    <label class="col-sm-2 col-form-label g-mb-5" for="nombre">Nombre(*)</label>
				    <div class="col-sm-9">
					    <input id="nombre" name="nombre" class="form-control form-control-md rounded-0" 
					    				placeholder="Ingrese nombre de usuario" type="text" required>
					    <small class="form-control-feedback nombre_empresa"></small>
				    </div>
				  </div>
				  <!-- Input nombre usuario -->

				  <!-- Textarea Expandable -->
				  <div class="form-group g-mb-20">
				    <label class="g-mb-10" for="inputGroup2_2">Descripción </label>
				    <textarea id="descripcion" name="descripcion" class="form-control form-control-md u-textarea-expandable rounded-0" rows="3" placeholder="Ingrese descripción de la empresa"></textarea>
				  </div>
				  <!-- End Textarea Expandable -->

					<button type="submit" class="btn btn-primary">Guardar</button>
        	<button type="button" class="btn u-btn-red closeModal" data-dismiss="modal">Cerrar</button>
      	</form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_destroy_empresa" tabindex="-1" role="dialog" >
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">¿ Esta seguro de eliminar esta empresa ?</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	<input type="hidden" id="id_empresa_delete" name="id_empresa_delete" value="">
       	<p id="name_empresa_delete"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn u-btn-primary" onclick="destroy_empresa()">Eliminar</button>
        <button type="button" class="btn u-btn-red closeModal" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
	var save_method
	let table_empresas
	let url
	var form_empresa = $('#form_new_empresa')
	const tipo_empresa = <?php echo $tipo_empresa;?>

	function new_empresa() {
		save_method = 'create'
		$('#form_new_empresa')[0].reset()
		$('.form-control').removeClass('error');
		$('#form_new_empresa #nombre').parent().removeClass('u-has-error-v1')
		$('.error').empty();
		$('#modal_new_empresa').modal('show')
	}

	function edit_empresa( id ) {
		save_method = 'update';
		$('#form_new_empresa')[0].reset()
		$('.form-control').removeClass('error')
		$('.error').empty()

		$.ajax({
			url: "<?php echo base_url('Empresas/get/');?>" + id,
			type: "GET",
			dataType: "JSON",
			success: function(data) {
					$('#empresa_id').val(data[0].id)
					$('#nombre').val(data[0].nombre)
					$('#descripcion').val(data[0].descripcion)
					$('#modal_new_empresa').modal('show')
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert('Error obteniendo datos');
			}
		})
	}

	function save() {
		$.ajax({
			url: '<?php echo base_url("Empresas/")?>' + save_method,
			type: 'POST',
			data: agrupar_datos(),
			dataType: 'JSON',
			success: function(response){
				if (response.status === 'success') {
					table_empresas.ajax.reload(null,false)
					$('#modal_new_empresa').modal('hide')
					noty_alert( response.status , response.msg )
					reset_form()
				} else if ( response.status === 'existe' ) {
					$('#form_new_empresa #nombre').parent().addClass('u-has-error-v1')
					$('.nombre_empresa').text('Esta empresa se encuentra registrada')
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
			'id' : $('#empresa_id').val(),
			'nombre' : $('#nombre').val(),
			'descripcion' : $('#descripcion').val(),
			'tipo' : $('#tipo_empresa').val()
		}
		return datos
	}

  $('.closeModal').on('click', function(event) {
    reset_form()
  })

  function reset_form() {
		$('#form_new_empresa')[0].reset()
    $('.nombre_empresa').text('')
    $('#form_new_empresa #nombre').parent().removeClass('u-has-error-v1')
  }

// Llamo al modal de advertencia para eliminar el usuario
	function delete_empresa( id ) {
		$.ajax({
			url: "<?php echo base_url('Empresas/edit/');?>" + id,
			type: "GET",
			dataType: "JSON",
			success: function(data) {
				$('#modal_destroy_empresa #id_empresa_delete').val(data[0].id)
				$('#modal_destroy_empresa #name_empresa_delete').text('')
				$('#modal_destroy_empresa #name_empresa_delete').append(`<strong>Nombre:</strong> ${data[0].nombre}`)
				$('#modal_destroy_empresa').modal('show')
			},
			error: function() {
				noty_alert( 'error', 'No se pudieron obtener los datos' )
			}
		});
	}
	// Elimino el usuario
	function destroy_empresa() {
		var id_empresa = $('#id_empresa_delete').val();
		$.ajax({
			url: "<?php echo base_url('Empresas/destroy/');?>" + id_empresa,
			type: "POST",
			dataType: 'JSON',
			success: function(response) {
				if (response.status === 'success') {
					table_empresas.ajax.reload(null,false);
					$('#modal_destroy_empresa').modal('hide');
				}
				noty_alert( response.status, response.msg )
			},
			error: function(jqXHR, textStatus, errorThrown)  {
				noty_alert( 'error', 'Error: no se pudo dar de baja a la empresa' )
			}
		});
	}

  $(document).on('ready', function () {

		form_empresa.validate({
									rules: {
										nombre: { required: true, alfanumOespacio: true, minlength: 3 }
										}
									})

		$.validator.addMethod("alfanumOespacio", function(value, element) {
		        return /^[ / -a-záéíóúüñ]*$/i.test(value)
		    }, "Ingrese sólo letras.")

		$('#form_new_empresa').submit(function(e){
			e.preventDefault()
			if ( form_empresa.valid() ) {
				save()
			}
		})
	
		table_empresas = $('#tabla_empresas').DataTable({
										ajax: '<?php echo base_url("Empresas/ajax_list/");?>' + tipo_empresa,
										language: { url: "<?php echo base_url('assets/vendor/datatables/spanish.json');?>" }
		})
  })
</script>
