<section class="container-fluid g-py-10">
	<h1>Administracion de entregas de ropa</h1>	

	<button class="btn u-btn-blue mb-2" onclick="new_periodo()"> Nuevo periodo </button>

	<div class="card g-brd-blue rounded-0 g-mb-30">
	  <h3 class="card-header g-bg-blue g-brd-transparent g-color-white g-font-size-16 rounded-0 mb-2">
	    <i class="fa fa-gear g-mr-5"></i>
	    Packs de ropa
	  </h3>
	  <div class="px-2 pb-2">
	  	<table id="tabla_periodos" class="table table-hover dt-responsive w-100 u-table--v1 mb-0">
	      <thead>
	        <tr>
	          <th>Nombre</th>
	          <th>Periodo</th>
	          <th>Descripcion</th>
	          <th>Acciones</th>
	        </tr>
	      </thead>

	      <tbody>
	      	<!-- Completo con ajax -->
	      </tbody>
	    </table>
	  </div>
</section>

<div class="modal fade" id="modal_new_entrega" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Alta de pack</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	<?php echo form_open( 'Periodo_entrega_ropa/create',array('id' => 'form_new_periodo', 'class' => 'g-brd-around g-brd-gray-light-v4 g-pa-30 g-mb-30') ) ?>
      		<p id="error-form" class="text-danger"></p>
      		<input type="hidden" id="periodo_entrega_id" value="">
				  <!--Input nombre usuario -->
				  <div class="form-group row g-mb-5">
				    <label class="col-sm-2 col-form-label g-mb-5" for="nombre">Nombre(*)</label>
				    <div class="col-sm-9">
					    <input id="nombre" name="nombre" class="form-control form-control-md rounded-0" 
					    				placeholder="Ingrese nombre de usuario" type="text" required>
					    <small class="form-control-feedback nombre_periodo"></small>
				    </div>
				  </div>
				  <!-- Input nombre usuario -->
				  <div class="form-group row g-mb-5">
				    <label class="col-sm-2 col-form-label g-mb-5" for="nombre">Periodo (*)</label>
				    <div class="col-sm-9">
					    <select name="duracion" id="duracion" required class="form-control">
					    	<option > Seleccione periodo </option>
					    	<option value="Anual"> Anual </option>
					    	<option value="Semestral"> Cada 6 meses </option>
					    	<option value="Cuatrimestral"> Cada 4 meses </option>
					    </select>
					    <small class="form-control-feedback nombre_periodo"></small>
				    </div>
				  </div>
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

<script type="text/javascript">
	var save_method
	var tabla_periodos
	var form_periodo = $('#form_new_periodo')

	function new_periodo() {
		save_method = 'create'
		$('#form_new_periodo')[0].reset()
		$('.form-control').removeClass('error');
		$('#form_new_periodo #nombre').parent().removeClass('u-has-error-v1')
		$('.error').empty();
		$('#modal_new_entrega').modal('show')
	}

	function save_periodo() {
		$.ajax({
			url: '<?php echo base_url("Periodo_entregas/")?>' + save_method,
			type: 'POST',
			data: agrupar_datos(),
			dataType: 'JSON',
			success: function(response){
				if (response.status === 'success') {
					table_periodos.ajax.reload(null,false)
					$('#modal_new_entrega').modal('hide')
					noty_alert( response.status , response.msg )
					reset_form()
				} else if ( response.status === 'existe' ) {
					$('#form_new_periodo #nombre').parent().addClass('u-has-error-v1')
					$('.nombre_periodo').text('Ya hay un pack con este nombre')
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
			'id' : $('#periodo_entrega_id').val(),
			'nombre' : $('#nombre').val(),
			'descripcion' : $('#descripcion').val(),
			'duracion' : $('#duracion').val()
		}
		return datos
	}

	function modal_show_prenda(id) {
		$.ajax({
			url: '<?php echo base_url("Periodo_entrega_ropa/list/")?>' + id,
			type: 'GET',
			dataType: 'JSON',
			success: function(response){
				if (response.status === 'success') {
					console.log(response)
				} 
			},
			error: function(response){
				noty_alert( 'error' , 'No se pudo guardar la información' )
			}
		})
	}

	$(document).on('ready', function () {

		form_periodo.validate({
									rules: {
										nombre: { required: true, alfanumOespacio: true, minlength: 3 },
										duracion: { required: true }
										}
									})

		$.validator.addMethod("alfanumOespacio", function(value, element) {
		        return /^[ / -a-záéíóúüñ]*$/i.test(value)
		    }, "Ingrese sólo letras.")

		$('#form_new_periodo').submit(function(e){
			e.preventDefault()
			if ( form_periodo.valid() ) {
				save_periodo()
			}
		})

		tabla_periodos = $('#tabla_periodos').DataTable({
										ajax: '<?php echo base_url("periodo_entregas/list/");?>',
										language: { url: "<?php echo base_url('assets/vendor/datatables/spanish.json');?>" }
		})
	
  })

</script>