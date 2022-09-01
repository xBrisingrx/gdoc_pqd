<section class="container-fluid g-py-10 g-mb-30">
	<h1>Registro y renovación de documentación</h1>

	<h3>Datos básicos</h3>

	<form id="datos_persona">
	  <!-- Select personas -->
	  <div class="form-group row g-mb-10">
		  <label class="col-sm-2 col-form-label g-mb-10" for="persona_seleccionada">Apellido y nombre</label>
		  <div class="col-sm-6">
			  <select class="custom-select" id="persona_seleccionada">
			  	<option value="" disabled selected >Seleccione la persona</option>
			   	<?php foreach ($personas as $p): ?>
			   		<option value="<?php echo $p->id;?>"><?php echo $p->apellido.' '.$p->nombre;?></option>
			   	<?php endforeach ?>
			  </select>
		  </div>
	  </div>
	  <!-- End Select personas -->
    <input type="hidden" name="legajo" id="legajo">
	  <!-- Input dni -->
	  <div class="form-group row g-mb-10">
	    <label class="col-sm-2 col-form-label g-mb-10" for="dni" >DNI</label>

	    <div class="col-sm-2">
	      <input id="dni" class="form-control u-form-control rounded-0" type="text" readonly>
	    </div>
	  </div>
	  <!-- End Input dni -->

	  <!-- Input cuil -->
	  <div class="form-group row g-mb-10">
	    <label class="col-sm-2 col-form-label g-mb-10" for="cuil">CUIL</label>

	    <div class="col-sm-5">
	      <input id="cuil" class="form-control u-form-control rounded-0" type="text" readonly>
	    </div>
	  </div>
	  <!-- End Input cuil -->

	  <!-- Input fecha_nacimiento -->
	  <div class="form-group row g-mb-10">
	    <label class="col-sm-2 col-form-label g-mb-10" for="fecha_nacimiento">Fecha de nacimiento</label>

	    <div class="col-sm-5">
	      <input id="fecha_nacimiento" class="form-control u-form-control rounded-0" type="date" readonly>
	    </div>
	  </div>
	  <!-- End Input fecha_nacimiento -->

	  <!-- Input nacionalidad -->
	  <div class="form-group row g-mb-10">
	    <label class="col-sm-2 col-form-label g-mb-10" for="nacionalidad">Nacionalidad</label>

	    <div class="col-sm-5">
	      <input id="nacionalidad" class="form-control u-form-control rounded-0" type="text" readonly>
	    </div>
	  </div>
	  <!-- End Input nacionalidad -->

	  <!-- Input domicilio -->
	  <div class="form-group row g-mb-10">
	    <label class="col-sm-2 col-form-label g-mb-10" for="domicilio">Domicilio</label>

	    <div class="col-sm-5">
	      <input id="domicilio" class="form-control u-form-control rounded-0" type="text" readonly>
	    </div>
	  </div>
	  <!-- End Input domicilio -->

	  <!-- Input telefono -->
	  <div class="form-group row g-mb-10">
	    <label class="col-sm-2 col-form-label g-mb-10" for="telefono">Teléfono</label>

	    <div class="col-sm-5">
	      <input id="telefono" class="form-control u-form-control rounded-0" type="text" readonly>
	    </div>
	  </div>
	  <!-- End Input telefono -->
	</form>

<!-- tabla perfiles asignados -->
<div class="card g-brd-darkpurple rounded-0 g-mb-30">
  <h3 class="card-header g-bg-darkpurple g-brd-transparent g-color-white g-font-size-16 rounded-0 mb-2">
    <i class="fa fa-gear g-mr-5"></i>
    Perfiles asignados a la persona
  </h3>

  <div class="table-responsive px-2 pb-2">
    <table id="tabla_perfiles" class="table table-hover u-table--v1 mb-0">
      <thead>
        <tr>
          <th>Nombre del perfil</th>
          <th>Fecha desde</th>
          <th>Fecha hasta</th>
        </tr>
      </thead>

      <tbody>
      	<!-- Ajax call -->
      </tbody>
    </table>
  </div>
</div>
<!-- End tabla perfiles asignados -->
<button type="button" class="btn u-btn-cyan my-2" title="Asignar atributo" onclick="modal_asignar_atributo_personalizado()" > Asignar un atributo </button>
<!-- Tabla atributos -->
<div class="card g-brd-darkpurple rounded-0 g-mb-30 min_height " >
  <h3 class="card-header g-bg-darkpurple g-brd-transparent g-color-white g-font-size-16 rounded-0 mb-2">
    <i class="fa fa-gear g-mr-5"></i>
    Listado de atributos asignados
  </h3>

  <div class="table-responsive px-2 pb-2">
    <table id="tabla_atributos" class="table table-hover u-table--v1 mb-0">
      <thead>
        <tr>
          <th>Nombre del atributo</th>
          <th>Categoria</th>
          <th>Vence</th>
          <th>Fecha vencimiento</th>
          <th>Permite anexar</th>
          <th>PDF</th>
          <th>Acciones</th>
        </tr>
      </thead>

      <tbody>
      	<!-- Ajax call -->
      </tbody>
    </table>
  </div>
</div>
<!-- End Tabla atributos -->


<!-- Modal cargar atributo -->
  <?php $this->load->view('sistema/documentos/_modal_crud_renovacion') ?>
<!-- End modal cargar atributo -->

<?php $this->load->view('sistema/documentos/_modal_asignar_eliminar_atributo') ?>

<?php $this->load->view('sistema/documentos/_modal_archivos',array('titulo_modal'=>'Archivos de renovaciones')) ?>

</section>

<input type="hidden" id="tipo_dato" value="<?php echo $tipo?>">

<?php $this->load->view('sistema/documentos/_documentos_js')?>

<script>
let persona_id

$(document).ready(function() {
  clean_form('datos_persona')
  $('#persona_seleccionada').on('change', function(){
    data_id = $('#persona_seleccionada option:selected').val()
    $.ajax({
      url: '<?php echo base_url('Personas/show/');?>' + data_id,
      type: 'GET',
      dataType: 'JSON',
      success: function( response ){
        $('#legajo').val(response.n_legajo)
        $('#dni').val(response.dni)
        $('#cuil').val(response.cuil)
        $('#fecha_nacimiento').val(response.fecha_nacimiento)
        $('#nacionalidad').val(response.nacionalidad)
        $('#domicilio').val(response.domicilio)
        $('#telefono').val(response.telefono)
        $('#pdf_dni').attr('href', url + response.dni_pdf_path)
        $('#pdf_cuil').attr('href', url + response.cuil_pdf_path)
        // ajax.url se usa para regrescar la url de donde se obtienen los datos
        tabla_perfiles.ajax.url('<?php echo base_url("Documentos/get_perfiles/1/");?>'+data_id).load()
        tabla_perfiles.ajax.reload(null,false)

        tabla_atributos.ajax.url('<?php echo base_url("Documentos/get_atributos/1/");?>'+data_id).load()
        tabla_atributos.ajax.reload(null,false)
      }
    })
  })  
  $('#persona_seleccionada').select2({ theme: 'bootstrap4', width: '50%' })
}) // end document ready
</script>