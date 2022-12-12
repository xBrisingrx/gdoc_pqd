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
      	<?php echo form_open( '#',array('id' => 'form_new_periodo', 'class' => 'g-brd-around g-brd-gray-light-v4 g-pa-30 g-mb-30') ) ?>
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
					    	<option value="0" disabled> Seleccione periodo </option>
					    	<option value="Anual"> Anual </option>
					    	<option value="Semestral"> Cada 6 meses </option>
					    	<option value="Cuatrimestral"> Cada 4 meses </option>
					    </select>
					    <small class="form-control-feedback"></small>
				    </div>
				  </div>

				  <div class="form-group row g-mb-5">
				    <label class="col-sm-2 col-form-label g-mb-5" for="nombre">Prendas: </label>
				    <div class="col-sm-9">
					    <select name="ropa" id="ropa_id" class="form-control">
					    	<option value=''> Seleccione prenda </option>
					    	<?php foreach ($ropa as $r ) { ?>
					    		<option value="<?php echo $r->id ?>"> <?php echo $r->nombre ?></option>
					    	<?php } ?>
					    </select>
					    <small class="form-control-feedback"></small>
				    </div>
				    <button class="btn u-btn-primary" type="button" onclick="add_ropa('form_new_periodo')"> + </button>
				  </div>
				  <div class="ropa-list"></div>
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

<div class="modal fade" id="modal_add_prenda" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Agregar prenda al pack</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	<?php echo form_open( '#',array('id' => 'form_add_prenda', 'class' => 'g-brd-around g-brd-gray-light-v4 g-pa-30 g-mb-30') ) ?>
      		<p id="error-form" class="text-danger"></p>
      		<input type="hidden" id="periodo_entrega_id" value="">
				  <div class="form-group row g-mb-5">
				    <label class="col-sm-2 col-form-label g-mb-5" for="nombre">Prenda (*)</label>
				    <div class="col-sm-9">
					    <select name="duracion" id="duracion" required class="form-control">
					    	<option > Seleccione prenda </option>
					    	<option value="Anual"> Anual </option>
					    	<option value="Semestral"> Cada 6 meses </option>
					    	<option value="Cuatrimestral"> Cada 4 meses </option>
					    </select>
					    <small class="form-control-feedback"></small>
				    </div>
				  </div>
				  <div id="prendas_a_agregar"></div>

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
		$('#form_new_periodo .ropa-list').html('')
		reset_select_ropa('form_new_periodo')
		$('#form_new_periodo')[0].reset()
		$('.form-control').removeClass('error');
		$('#form_new_periodo .nombre_periodo').html('')
		$('#form_new_periodo #nombre').parent().removeClass('u-has-error-v1')
		$('.error').empty();
		$('#modal_new_entrega').modal('show')
	}

	function reset_select_ropa(form_id) {
		let select = $(`#${form_id} #ropa_id`);
		select.find("option").each(function(index, item) {
		  $(item).attr('disabled', false);
		});
	}
	
	function save_periodo() {
		fetch('<?php echo base_url("Periodo_entregas/")?>' + save_method, {
			method: 'POST',
			body: agrupar_datos('form_new_periodo'),
		})
		.then( response => {
			return response.json();
		} )
		.then( response => {
			if (response.status === 'success') {
					tabla_periodos.ajax.reload(null,false)
					$('#modal_new_entrega').modal('hide')
					noty_alert( response.status , response.msg )
					reset_form()
					reset_select_ropa('form_new_periodo')
				} else if ( response.status === 'existe' ) {
					$('#form_new_periodo #nombre').parent().addClass('u-has-error-v1')
					$('.nombre_periodo').text('Ya hay un pack con este nombre')
				} else {
					noty_alert( response.status , response.msg )
				}
		} )
		
	}

	function agrupar_datos(form_id) {
		let form = new FormData()
		form.append('id', $('#periodo_entrega_id').val() )
		form.append('nombre', $('#nombre').val() )
		form.append('duracion', $('#duracion').val() )
		form.append('descripcion', $('#descripcion').val() )
		form.append('ropa[]', get_ropa(form_id) )
		return form
	}

function get_ropa(form_id) {
	let ropa = []
	let nodo = document.getElementById(form_id)
	let lista = nodo.getElementsByClassName('ropa-id')
	for (let i = lista.length - 1; i >= 0; i--) {
		ropa.push(lista[i].dataset.id)
	}
	console.log('get ropa => ',ropa)
	return ropa
}

	function modal_show_prenda(id) {
		$.ajax({
			url: '<?php echo base_url("Periodo_entregas/get_ropa/")?>' + id,
			type: 'GET',
			dataType: 'JSON',
			success: function(response){
				console.log(response)
			},
			error: function(response){
				noty_alert( 'error' , 'No se pudo obtener la informacion' )
			}
		})
	}

	function add_ropa(form_id) {
		let ropa = document.querySelector(`#${form_id} #ropa_id`)
		if (ropa.value != null && ropa.value != '') {
			let ropa_name =  $(`#${form_id} #ropa_id option:selected`).text()
			$(`#${form_id} .ropa-list`).append(`
				<div class="row" id="ropa_${ropa.value}">
					<div class="mb-2  mx-4 col-6">
						<input type="text" value="${ropa_name}" class="ropa-id form-control rounded-0 " data-id=${ropa.value} disabled></input>
					</div>
					<div>
						<button type="button" class="btn u-btn-red remove-ropa" onclick="remove_ropa('${form_id}','${ropa.value}')" title="Quitar prenda"> <i class="fa fa-trash"></i> </button>
					</div>
				</div>
			`)
			$(`#${form_id} #ropa_id option:selected`).attr('disabled', 'disabled')
			$('.select-2-ropa').val('').trigger('change')
		}
	}

	function remove_ropa(form_id, id ) {
		event.preventDefault()
		let element = document.querySelector(`#${form_id} #ropa_${id}`)
		element.remove()
		$(`#${form_id} #ropa_id option[value='${id}']`).attr('disabled', false)
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