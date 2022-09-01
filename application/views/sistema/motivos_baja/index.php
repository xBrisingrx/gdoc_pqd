<section class="container-fluid g-py-10">
	<h1>Motivos de baja</h1>
		<?php if ($this->session->userdata('rol') == 1): ?>
			<button class="btn btn-success justify-content-end mb-2" onclick="new_motivo()"> Nuevo motivo </button>
		<?php endif ?>
	<!-- Hover Rows -->
	<div class="card g-brd-darkpurple rounded-0 g-mb-30">
	  <h3 class="card-header g-bg-darkpurple g-brd-transparent g-color-white g-font-size-16 rounded-0 mb-2">
	    <i class="fa fa-gear g-mr-5"></i>
	    <?php echo $title_table?>
	  </h3>

	  <div class="px-2">
	  	<table id="tabla_motivs_baja" class="table table-hover dt-responsive w-100u-table--v1 mb-0">
	      <thead>
	        <tr>
	          <th>motivo</th>
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
	<br><br>
</section>

<div class="modal fade" id="modal_new_motivo_baja" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Alta de motivo baja</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	<form id="form_new_motivo_baja" class="g-brd-around g-brd-gray-light-v4 g-pa-30 g-mb-30">
      		<p id="error-form" class="text-danger"></p>
      		<input type="hidden" id="motivo_baja_id" value="">
      		<input type="hidden" id="tipo_motivo" value="<?php echo $tipo_motivo;?>">
				  <!--Input motivo usuario -->
				  <div class="form-group row g-mb-5">
				    <label class="col-sm-2 col-form-label g-mb-5" for="motivo_baja">Motivo (*)</label>
				    <div class="col-sm-9">
					    <input id="motivo_baja" name="motivo_baja" class="form-control form-control-md rounded-0" placeholder="Ingrese motivo" type="text" required>
					    <small class="form-control-feedback"></small>
				    </div>
				  </div>
				  <!-- Input motivo usuario -->

				  <!-- Textarea Expandable -->
				  <div class="form-group g-mb-20">
				    <label class="g-mb-10" for="inputGroup2_2">Descripción </label>
				    <textarea id="descripcion" name="descripcion" class="form-control form-control-md u-textarea-expandable rounded-0" rows="3" placeholder="Ingrese descripción del motivo"></textarea>
				  </div>
				  <!-- End Textarea Expandable -->

					<button type="submit" class="btn btn-primary">Guardar</button>
        	<button type="button" class="btn u-btn-red closeModal" data-dismiss="modal">Cerrar</button>
      	</form>
      </div>
    </div>
  </div>
</div>




<div class="modal fade" id="modal_destroy_motivo_baja" tabindex="-1" role="dialog" >
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">¿ Esta seguro de eliminar este usuario ?</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	<input type="hidden" id="id_motivo_baja_delete" name="id_motivo_baja_delete" value="">
       	<p id="name_motivo_delete"><strong>Motivo: </strong> </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn u-btn-primary" onclick="destroy_motivo()">Eliminar</button>
        <button type="button" class="btn u-btn-red closeModal" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>


<script type="text/javascript">
	var save_method
	let table_motivos_baja
	var url
	var form_motivo_baja = $('#form_new_motivo_baja')
	var tipo_motivo = <?php echo $tipo_motivo;?>



	function new_motivo() {
		save_method = 'create'
		$('#form_new_motivo_baja')[0].reset()
		$('.form-control').removeClass('error');
		$('.error').empty();
		$('#modal_new_motivo_baja').modal('show')
	}

	function edit_motivo( id ) {
		save_method = 'update';
		$('#form_new_motivo_baja')[0].reset()
		$('.form-control').removeClass('error')
		$('.error').empty()

		$.ajax({
			url: "<?php echo base_url('Motivos_baja/get/');?>" + id,
			type: "GET",
			dataType: "JSON",
			success: function(data){
				$('#motivo_baja_id').val(data[0].id)
				$('#motivo_baja').val(data[0].motivo)
				$('#descripcion').val(data[0].descripcion)
				$('#modal_new_motivo_baja').modal('show')
			},
			error: function(jqXHR, textStatus, errorThrown){
				alert('Error obteniendo datos');
			}
		});

	}

	function save() {
		$.ajax({
			url: '<?php echo base_url("Motivos_baja/")?>' + save_method,
			type: 'POST',
			data: agrupar_datos(),
			dataType: 'JSON',
			success: function(response){
				if (response.status === 'success') {
					table_motivos_baja.ajax.reload(null,false)
					$('#modal_new_motivo_baja').modal('hide')
					reset_form()
					noty_alert( response.status, response.msg )
				} else if ( response.status === 'existe' ) {
					$('#error-form').text('Esta motivo ya existe.')
				} else {
					noty_alert( response.status, response.msg )
				}
			},
			error: function(response){
				noty_alert( 'error' , 'No se pudo guardar la información' )
			}
		})
	}



	function agrupar_datos() {
		datos = {
			'id' : $('#motivo_baja_id').val(),
			'motivo' : $('#motivo_baja').val(),
			'descripcion' : $('#descripcion').val(),
			'tipo' : $('#tipo_motivo').val()
		}
		return datos
	}

  $('.closeModal').on('click', function(event) {
    reset_form()
  })

  function reset_form() {
		$('#form_new_motivo_baja')[0].reset()
    $('#error-form').text('')
  }

// Llamo al modal de advertencia para eliminar el usuario
	function delete_motivo( id ) {
		$.ajax({
			url: "<?php echo base_url('Motivos_baja/edit/');?>" + id,
			type: "GET",
			dataType: "JSON",
			success: function(data){
				$('#modal_destroy_motivo_baja #id_motivo_baja_delete').val(data[0].id)
				$('#modal_destroy_motivo_baja #name_motivo_delete').append(data[0].motivo)
				$('#modal_destroy_motivo_baja').modal('show')
			},
			error: function(){
				noty_alert('error','No se pudo obtener los datos');
			}
		});
	}
	// Elimino el usuario
	function destroy_motivo() {
		var id_motivo = $('#id_motivo_baja_delete').val();
		$.ajax({
			url: "<?php echo base_url('Motivos_baja/destroy/');?>" + id_motivo,
			type: "POST",
			dataType:'JSON',
			success: function(response){
				if (response.status === 'success') {
					table_motivos_baja.ajax.reload(null,false);
					$('#modal_destroy_motivo_baja').modal('hide');
				} 
				noty_alert( response.status, response.msg )
			},
			error: function(jqXHR, textStatus, errorThrown){
				noty_alert( 'error' , 'No se pudo eliminar el motivo' )
			}
		});
	}

  $(document).on('ready', function () {

		form_motivo_baja.validate({
									rules: {
										motivo: { required: true, alfanumOespacio: true,
										 						minlength: 3 }
										}
									})

		$.validator.addMethod("alfanumOespacio", function(value, element) {
		        return /^[ a-záéíóúüñ]*$/i.test(value)
		    }, "Ingrese sólo letras.")


		$('#form_new_motivo_baja').submit(function(e){
			e.preventDefault()
			if ( form_motivo_baja.valid() ) {
				save()
			}
		})
	
		table_motivos_baja = $('#tabla_motivs_baja').DataTable({
																	ajax: '<?php echo base_url("Motivos_baja/ajax_list/");?>' + tipo_motivo,
																	language: {
												                url: "<?php echo base_url(); ?>assets/vendor/datatables/spanish.json"
												              }
		})
  })
</script>
