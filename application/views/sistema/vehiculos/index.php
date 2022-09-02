<section class="container-fluid g-py-10 " >
  <h1>Vehiculos registrados en el sistema</h1>

  <?php if ($this->session->userdata('rol') == 1): ?>
    <a href="<?php echo base_url('Vehiculos/new');?>" class="btn btn-success mb-2">Nuevo vehiculo</a>
  <?php endif ?>
  <a href="<?php echo base_url('Vehiculos_Inactivos');?>" class="btn btn-info mb-2">Ver vehiculos inactivo</a>
  <!-- <button type="button" class="btn btn-primary justify-content-start mb-2" data-toggle="modal" data-target="#modal_excel" > Exportar a excel </button> -->

  <div class="card g-brd-darkpurple rounded-0 g-mb-30">
    <h3 class="card-header g-bg-darkpurple g-brd-transparent g-color-white g-font-size-16 rounded-0 mb-2">
      <i class="fa fa-gear g-mr-5"></i>
      Vehiculos registrados
    </h3>
    <div class="px-2 pb-2">
    	<table id="tabla_vehiculos" class="table table-hover dt-responsive w-100">
	      <thead>
	        <tr>
	          <th>Interno</th>
	          <th>Dominio</th>
	          <th>Año</th>
	          <th>Marca</th>
	          <th>Modelo</th>
	          <th>Tipo</th>
	          <th>Nro. chasis</th>
	          <th>Nro. motor</th>
	          <th>Cant. asientos</th>
	          <th>Empresa</th>
	          <th>Observaciones</th>
	          <?php if ($this->session->userdata('rol') == 1): ?>
	            <th>Acciones</th>
	          <?php endif ?>
	        </tr>
	      </thead>
	      <tbody>
	        <!-- Ajax -->
	      </tbody>
	    </table>
    </div>
  </div>
</section>

<!-- Modal -->
<div class="modal fade" id="modal_edit_vehiculo" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Editar vehiculo</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
        <!-- Form alta de vehiculo -->
        <?php echo form_open_multipart('#', array( 'id'=>'form_edit_vehiculo','class'=>'form_edit_vehiculo g-brd-around g-brd-gray-light-v4 g-pa-20', 'method' => 'POST'));?>
          <input type="hidden" id="vehiculo_id" name="vehiculo_id" value="">
          <?php $this->load->view('sistema/vehiculos/_form')?>
          <div class="row mb-2">
            <button type="button" id="btn_edit_vehiculo" class="btn btn-md u-btn-primary g-mr-10"> Grabar cambios </button>
            <button type="button" data-dismiss="modal" class="btn btn-md u-btn-red g-mr-10"> Cerrar </button>
          </div>
        </form>
        <!-- End form alta vehiculo -->
    </div>
  </div>
</div>


<!-- ##################################### Modal destroy vehiculo   ############################################################## -->
<div class="modal fade" id="modal_delete_vehiculo" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">¿ Esta seguro de eliminar este vehiculo ?</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p id="vehiculo_dominio_delete"> </p>
        <form id="form_baja_vehiculo">
          <input type="hidden" id="vehiculo_delete_id" name="vehiculo_delete_id" value="">
          <!-- Select empresa -->
          <div class="form-group g-mb-20">
            <div class="row">
              <label class="g-mb-10 g-ml-20" for="motivo_baja_vehiculo"> Motivo (*)</label>
            </div>    
            <select class="form_control" name="motivo_baja_vehiculo" id="motivo_baja_vehiculo" required>
              <option value="" disabled selected > Seleccione motivo de baja </option>
              <?php foreach ($motivos_baja as $motivo): ?>
                <option value="<?php echo $motivo->id ?>"><?php echo $motivo->motivo ?></option>
              <?php endforeach ?>
            </select>
          </div>
          <!-- End select empresa -->
          <!-- Select Single Date -->
          <div class="form-group g-mb-30">
            <label class="g-mb-10">Fecha baja</label>
            <div class="input-group g-brd-primary--focus">
              <input id="fecha_baja_vehiculo" name="fecha_baja_vehiculo" class="form-control form-control-md  rounded-0" type="date" value="" required>
            </div>
          </div>
          <!-- End Select Single Date -->
          <!-- Textarea Expandable -->
          <div class="form-group g-mb-20">
            <label class="g-mb-10" for="detalle_baja_vehiculo">Detalle (*)</label>
            <textarea id="detalle_baja_vehiculo" name="detalle_baja_vehiculo" class="form-control form-control-md u-textarea-expandable rounded-0" rows="3" placeholder="" required></textarea>
          </div>
          <!-- End Textarea Expandable -->
          <button type="submit" class="btn u-btn-red">Eliminar</button>
          <button type="button" class="btn u-btn-indigo" data-dismiss="modal">Cerrar</button>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- ############################################################################################################################# -->

<!-- Modal exportar a Excel -->
<div class="modal fade" id="modal_excel" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"> Seleccione los filtros </h5>     
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <form id="form_descargar_excel" method="POST" action="<?php echo base_url('Informes/excel_vehiculos');?>">
            <!-- motivo baja input -->
            <div class="form-group row g-mb-5">
              <label class="col-sm-2 col-form-label g-mb-5">Estado </label>
              <select id="estado_vehiculo" name="estado_vehiculo" class="form-control">
                <option value="1">Activos</option>
                <!-- <option value="0">Inactivos</option> -->
              </select>
            </div>

            <div class="form-group row g-mb-5">
              <label class="col-sm-2 col-form-label g-mb-5 w-80">Empresa </label>
              <select id="empresa_vehiculo" name="empresa_vehiculo" class="custom-select  w-80 col-sm-8">
                <option value="0"> Todas las empresas </option>
                <?php foreach ($empresas as $e): ?>
                  <option value="<?php echo $e->id ?>" > <?php echo $e->nombre ?> </option>
                <?php endforeach ?>
              </select>
            </div>

            <div class="form-group row g-mb-5">
              <label class="col-sm-2 col-form-label g-mb-5 w-80">Perfil </label>
              <select id="perfil_vehiculo" name="perfil_vehiculo" class="custom-select  w-80 col-sm-8">
                <option value="0"> Todos los perfiles </option>
                <?php foreach ($perfiles as $p): ?>
                  <option value="<?php echo $p->id ?>" > <?php echo $p->nombre ?> </option>
                <?php endforeach ?>
              </select>
            </div>

            <button type="submit" class="btn u-btn-primary" >Descargar</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          </form>
      </div>
    </div>
  </div>
</div>
<!-- Fin modal exportar a excel -->

<script type="text/javascript">
	let tabla_vehiculos
	let form_edit_vehiculo = $('#form_edit_vehiculo')
  let form_baja_vehiculo = $('#form_baja_vehiculo')

  function print_option_select(select_id, type, id_seleccionado, attr = null, id = null) {
    if (type != 'empresa') {
      if (id !== null) {
        url = "<?php echo base_url('Vehiculos/get_attr/');?>"+type+"/"+attr+"/"+id
        } else {
          url = "<?php echo base_url('Vehiculos/get_attr/');?>"+type
      }
    } else {
      url = '<?php echo base_url("Empresas/get");?>'
    }
    $.ajax({
      url: url,
      type: 'GET',
      dataType: 'JSON',
      success: function(response){
        $(`#${select_id}`).find('option').remove().end().append('<option value="" disabled >Seleccione '+type+'</option>')
        $(response).each(function(i, element){
          if (element.id == id_seleccionado) {
            $('#'+select_id+'').append("<option value="+element.id+" selected>"+element.nombre+"</option>");
          } else {
            $('#'+select_id+'').append("<option value="+element.id+">"+element.nombre+"</option>");
          }
        });
      },
      error: function(jqXHR, textStatus, errorThrown){
        $('#'+select_id+'').find('option').remove().end().append('<option value="" disabled selected >Seleccione '+type+'</option>');
        $('#'+select_id+'').append("<option>No se pudieron obtener los "+type+"</option>");
      }
    });
  }

  $('#marca').on('change', function(){
    let marca_id = $('#marca').val()
    print_option_select('modelo','modelo', '', 'marca_vehiculo_id',marca_id)
  })

  function modal_edit_vehiculo(id) {
    $('#form_edit_vehiculo')[0].reset()
    $('#form_edit_vehiculo #vehiculo_id').val('')
    $('.vehicle_edit_select').val(0).trigger('change')
    $('.form-control').removeClass('error')
    $('.error').empty()
    $('#btn_save_vehiculo').text('Guardar')
    $('#btn_save_vehiculo').prop('disabled', false)

    $.ajax({
      url: base_url + 'Vehiculos/edit/' + id,
      type: 'GET',
      dataType: 'JSON',
      success: function(response) {
        $('#form_edit_vehiculo #vehiculo_id').val(response.id)
        $('#form_edit_vehiculo #interno').val(response.interno)
        $('#form_edit_vehiculo #dominio').val(response.dominio)
        $('#form_edit_vehiculo #anio').val(response.anio)
        $('#form_edit_vehiculo #patentamiento').val(response.patentamiento)
        $('#form_edit_vehiculo #chasis').val(response.n_chasis)
        $('#form_edit_vehiculo #motor').val(response.n_motor)
        $('#form_edit_vehiculo #asientos').val(response.cant_asientos)
        $('#form_edit_vehiculo #empresa').val(response.empresa_id)
        $('#form_edit_vehiculo #observaciones').val(response.observaciones)
        print_option_select('marca', 'marca', response.marca_id)
        print_option_select('modelo','modelo', response.modelo_id, 'marca_vehiculo_id', response.marca_id)
        print_option_select('tipo', 'tipo', response.tipo_id)
        print_option_select('empresa', 'empresa', response.empresa_id)
        $(`.asignacion-select`).hide()
        $(`.btn-crud`).hide()
        $('#modal_edit_vehiculo').modal('show')
      },
      error: function() {
        noty_alert( 'error' , 'Error recuperando datos de vehiculo' )
      }
    })
  }

  function agrupar_datos() {
    let data = new FormData()
    data.append('id', document.getElementById('vehiculo_id').value)
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

    let total_files = document.getElementById('imagenes').files.length
    for (let index = 0; index < total_files; index++) {
      data.append("imagenes[]", document.getElementById('imagenes').files[index]);
    }

    return data
  }

  document.getElementById('btn_edit_vehiculo').addEventListener('click', function(e){
    e.preventDefault()
    e.stopPropagation()
    $('#btn_save_vehiculo').text('Grabando...')
    $('#btn_save_vehiculo').prop('disabled', true)
    if( form_edit_vehiculo.valid() ){
      fetch( "<?php echo base_url('Vehiculos/update')?>", {
        method: 'POST',
        body: agrupar_datos()
      } )
      .then(response => response.json() )
      .then(response => {
        if (response.status === 'success') {
          tabla_vehiculos.ajax.reload(null,false)
          $('#modal_edit_vehiculo').modal('hide')
          $('#modal_edit_vehiculo #btn_save_vehiculo').text('Grabar cambios')
          $('#modal_edit_vehiculo #btn_save_vehiculo').prop('disabled', false)
        } else {
          $('#modal_edit_vehiculo #btn_save_vehiculo').text('Grabar cambios')
          $('#modal_edit_vehiculo #btn_save_vehiculo').prop('disabled', false)
        }
        noty_alert( response.status, response.msg )
      } )
      .catch( error => {
        noty_alert( 'error' , 'No se pudo editar el vehiculo' )
        $('#modal_edit_vehiculo #btn_save_vehiculo').text('Grabar cambios')
        $('#modal_edit_vehiculo #btn_save_vehiculo').prop('disabled', false)
      } )
    } else {
      console.info('no valid')
    }
  })

  function modal_delete(id) {
    clean_form('form_baja_vehiculo')
    $('#modal_delete_vehiculo #vehiculo_dominio_delete').html('<strong>Dominio: </strong>')
    $.ajax({
      url: "<?php echo base_url('Vehiculos/edit/');?>" + id,
      type: "GET",
      dataType: "JSON",
      success: function(response) {
        $('#modal_delete_vehiculo #vehiculo_delete_id').val(response.id);
        $('#modal_delete_vehiculo #vehiculo_dominio_delete').append(response.dominio);
        setInputDate('#fecha_baja_vehiculo')
        $('#modal_delete_vehiculo').modal('show');
      },
      error: function() {
        noty_alert( 'error' , 'Error al obtener los datos' )
      }
    });
  }

  document.getElementById('form_baja_vehiculo').addEventListener('submit', function(e){
    event.preventDefault()
    if ( form_baja_vehiculo.valid() ) {
      $.ajax({
        url: base_url+'Vehiculos/destroy',
        type: "POST",
        data: {
          vehiculo_id : $('#form_baja_vehiculo #vehiculo_delete_id').val(),
          motivo_baja_vehiculo : $('#form_baja_vehiculo #motivo_baja_vehiculo').val(),
          detalle_baja_vehiculo : $('#form_baja_vehiculo #detalle_baja_vehiculo').val(),
          fecha_baja : $('#form_baja_vehiculo #fecha_baja_vehiculo').val()
        },
        dataType: 'JSON',
        success: function(response) {
          if (response.status === 'success') {
            tabla_vehiculos.ajax.reload(null,false);
            $('#modal_delete_vehiculo').modal('hide');
            noty_alert( response.status, response.msg )
          } else {
            noty_alert( response.status, response.msg )
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
          noty_alert( 'error' , 'Error en el servidor, no se pudo dar de baja el vehiculo' );
        }
      })
    }
  })

$(document).on('ready', function () {
  $('#empresa').select2( { theme: 'bootstrap4', width: '70%' } )
  $('#marca').select2( { theme: 'bootstrap4', width: '70%' } )
  $('#modelo').select2({theme: 'bootstrap4', width: '70%'})
  $('#tipo').select2( { theme: 'bootstrap4', width: '70%' } )
  $('#estado_vehiculo').select2({ theme: 'bootstrap4', width: '70%' })
  $('#empresa_vehiculo').select2({ theme: 'bootstrap4', width: '70%' })
  $('#perfil_vehiculo').select2({ theme: 'bootstrap4', width: '70%' })
  $('#motivo_baja_vehiculo').select2({ theme: 'bootstrap4', width: '70%' })

  $.validator.addMethod("alfanumOespacio", function(value, element) {
            return /^[a-z\- áéíóúüñ0-9]*$/i.test(value);
        }, "Ingrese sólo letras y numeros.");

  form_edit_vehiculo.validate({
    rules: {
      'interno': { alfanumOespacio: true, 
         required: true,
          remote: {
            url: "<?php echo base_url('Vehiculos/num_interno_libre/');?>",
            type: "POST",
            data: {
                  interno: function() {
                    return $('#interno').val()
                },
                vehiculo_id: function() {
                    return $('#vehiculo_id').val()
                }
            }
          } 
        },
      'diminio': {required: true},
      'anio': {number: true,required: true, minlength: 4, maxlength:4}
    },
    messages: {
    'interno': {
      remote: 'Este numero de interno pertenece a otro vehiculo'
    }
    }
    })

  form_baja_vehiculo.validate({
    rules: {
      'motivo_baja_vehiculo': {required: true},
      'detalle_baja_vehiculo': {required: true, minlength: 5},
      'fecha_baja_vehiculo': {required: true}
    }
  })

  tabla_vehiculos =  $('#tabla_vehiculos').DataTable({
      ajax: base_url + 'Vehiculos/list',
      language: {url: "<?php echo base_url('assets/vendor/datatables/spanish.json')?>"}
  })
} )
</script>