<div class="container-fluid g-py-10 mb-4">
	<h1>Aseguradoras vehiculos</h1>

	<?php if ($this->session->userdata('rol') == 1) { ?>
		<button class="btn u-btn-blue mb-2" onclick="new_aseguradora()"> Nueva </button>			
	<?php } ?>
	<!-- Hover Rows -->
	<div class="card g-brd-darkpurple rounded-0 g-mb-30">
	  <h3 class="card-header g-bg-darkpurple g-brd-transparent g-color-white g-font-size-16 rounded-0 mb-2">
	    <i class="fa fa-gear g-mr-5"></i>
	    Listado de aseguradoras
	  </h3>
	   <div class="px-2  pb-2">
	    <table id="tabla_aseguradoras" class="table table-hover dt-responsive w-100 u-table--v1">
	      <thead>
	        <tr>
	          <th>Lugar</th>
	          <th>Comentario</th>
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

<div class="modal fade" id="modal_aseguradoras" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Alta de aseguradora</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	<?php echo form_open( '',array('id' => 'form_aseguradoras', 'class' => 'g-brd-around g-brd-gray-light-v4 g-pa-30 g-mb-30') ) ?>
      		<p id="error-form" class="text-danger"></p>
      		<input type="hidden" id="aseguradora_id" value="">
				  <!--Input nombre usuario -->
				  <div class="form-group row g-mb-5">
				    <label class="col-sm-2 col-form-label g-mb-5" for="nombre">Nombre (*)</label>
				    <div class="col-sm-9">
					    <input id="nombre" name="nombre" class="form-control form-control-md rounded-0" 
					    				placeholder="Nombre aseguradora" type="text" required>
					    <small class="form-control-feedback nombre_aseguradora"></small>
				    </div>
				  </div>
				  <!-- Input nombre usuario -->

				  <!-- Textarea Expandable -->
				  <div class="form-group g-mb-20">
				    <label class="g-mb-10" for="inputGroup2_2">Comentario </label>
				    <textarea id="descripcion" name="descripcion" class="form-control form-control-md u-textarea-expandable rounded-0" rows="3" placeholder="Ingrese un comentario"></textarea>
				  </div>
				  <!-- End Textarea Expandable -->

					<button type="submit" class="btn btn-primary">Guardar</button>
        	<button type="button" class="btn u-btn-red closeModal" data-dismiss="modal">Cerrar</button>
      	</form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_destroy_aseguradora" tabindex="-1" role="dialog" >
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">¿ Esta seguro de eliminar esta aseguradora ?</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	<input type="hidden" id="id_aseguradora_delete" name="id_aseguradora_delete" value="">
       	<p id="name_aseguradora_delete"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn u-btn-primary" onclick="destroy_aseguradora()">Eliminar</button>
        <button type="button" class="btn u-btn-red closeModal" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
	let save_method
	let tabla_aseguradora
	let url
	let form_aseguradoras = $('#form_aseguradoras')

	function new_aseguradora() {
		save_method = 'create'
		$('#form_aseguradoras')[0].reset()
		$('.form-control').removeClass('error');
		$('#form_aseguradoras #nombre').parent().removeClass('u-has-error-v1')
		$('.error').empty();
		$('#modal_aseguradoras').modal('show')
	}

	function modal_edit_aseguradora( id ) {
		save_method = 'update';
		$('#form_aseguradoras')[0].reset()
		$('.form-control').removeClass('error')
		$('.error').empty()

		$.ajax({
			url: "<?php echo base_url('aseguradoras/edit/');?>" + id,
			type: "GET",
			dataType: "JSON",
			success: function(data) {
					$('#aseguradora_id').val(data.id)
					$('#nombre').val(data.nombre)
					$('#descripcion').val(data.descripcion)
					$('#modal_aseguradoras').modal('show')
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert('Error obteniendo datos');
			}
		})
	}

	function save() {
		$.ajax({
			url: '<?php echo base_url("aseguradoras/")?>' + save_method,
			type: 'POST',
			data: agrupar_datos(),
			dataType: 'JSON',
			success: function(response){
				if (response.status === 'success') {
					tabla_aseguradora.ajax.reload(null,false)
					$('#modal_aseguradoras').modal('hide')
					noty_alert( response.status , response.msg )
					reset_form()
				} else if ( response.status === 'existe' ) {
					$('#form_aseguradoras #nombre').parent().addClass('u-has-error-v1')
					$('.nombre_aseguradora').text('Esta aseguradora se encuentra registrada')
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
			'id' : $('#aseguradora_id').val(),
			'nombre' : $('#nombre').val(),
			'descripcion' : $('#descripcion').val()
		}
		return datos
	}

  $('.closeModal').on('click', function(event) {
    reset_form()
  })

  function reset_form() {
		$('#form_aseguradoras')[0].reset()
    $('.nombre_aseguradora').text('')
    $('#form_aseguradoras #nombre').parent().removeClass('u-has-error-v1')
  }

// Llamo al modal de advertencia para eliminar el usuario
	function modal_destroy_aseguradora( id ) {
		$.ajax({
			url: "<?php echo base_url('aseguradoras/edit/');?>" + id,
			type: "GET",
			dataType: "JSON",
			success: function(data) {
				$('#modal_destroy_aseguradora #id_aseguradora_delete').val(data.id)
				$('#modal_destroy_aseguradora #name_aseguradora_delete').text('')
				$('#modal_destroy_aseguradora #name_aseguradora_delete').append(`<strong>Nombre:</strong> ${data.nombre}`)
				$('#modal_destroy_aseguradora').modal('show')
			},
			error: function() {
				noty_alert( 'error', 'No se pudieron obtener los datos' )
			}
		});
	}
	// Elimino el usuario
	function destroy_aseguradora() {
		var id_aseguradora = $('#id_aseguradora_delete').val();
		$.ajax({
			url: "<?php echo base_url('aseguradoras/destroy/');?>" + id_aseguradora,
			type: "GET",
			dataType: 'JSON',
			success: function(response) {
				if (response.status === 'success') {
					tabla_aseguradora.ajax.reload(null,false);
					$('#modal_destroy_aseguradora').modal('hide');
				}
				noty_alert( response.status, response.msg )
			},
			error: function(jqXHR, textStatus, errorThrown)  {
				noty_alert( 'error', 'Error: no se pudo dar de baja a la aseguradora' )
			}
		});
	}

  $(document).on('ready', function () {

		form_aseguradoras.validate({
									rules: {
										nombre: { required: true, alfanumOespacio: true, minlength: 3 }
										}
									})

		$.validator.addMethod("alfanumOespacio", function(value, element) {
		        return /^[ / -a-záéíóúüñ]*$/i.test(value)
		    }, "Ingrese sólo letras.")

		$('#form_aseguradoras').submit(function(e){
			e.preventDefault()
			if ( form_aseguradoras.valid() ) {
				save()
			}
		})
	
		tabla_aseguradora = $('#tabla_aseguradoras').DataTable({
										ajax: '<?php echo base_url("aseguradoras/list/");?>',
										language: { url: "<?php echo base_url('assets/vendor/datatables/spanish.json');?>" }
		})
  })
</script>
