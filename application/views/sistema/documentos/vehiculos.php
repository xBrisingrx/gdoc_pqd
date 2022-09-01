<section class="container-fluid g-py-10">
  <h1>Registro y renovación de documentación</h1>

  <h3>Datos del vehiculo</h3>

<div class="col-12 mb-4">
  <form id="form_info_vehiculo">
    <!-- Select vehiculos -->
    <div class="form-group row g-mb-10">
      <label class="col-sm-2 col-form-label g-mb-10" for="vehiculo_seleccionado">Nro interno</label>
      <div class="col-sm-6">
        <select class="custom-select" id="vehiculo_seleccionado">
          <option value="" disabled selected >Seleccione interno</option>
          <?php foreach ($internos as $v): ?>
            <option value="<?php echo $v->id;?>"><?php echo $v->interno;?></option>
          <?php endforeach ?>
        </select>
      </div>
    </div>
    <!-- End Select vehiculos -->

    <!-- Input dominio -->
    <div class="form-group row g-mb-10">
      <label class="col-sm-2 col-form-label g-mb-10" for="dominio" >Dominio</label>

      <div class="col-sm-2">
        <input id="dominio" class="form-control u-form-control rounded-0" type="text" readonly>
      </div>
    </div>
    <!-- End Input dominio -->

    <!-- Input anio -->
    <div class="form-group row g-mb-10">
      <label class="col-sm-2 col-form-label g-mb-10" for="anio">Año</label>

      <div class="col-sm-9">
        <input id="anio" class="form-control u-form-control rounded-0" type="text" readonly>
      </div>
    </div>
    <!-- End Input anio -->

    <!-- Input marca -->
    <div class="form-group row g-mb-10">
      <label class="col-sm-2 col-form-label g-mb-10" for="marca">Marca</label>

      <div class="col-sm-9">
        <input id="marca" class="form-control u-form-control rounded-0" type="text" readonly>
      </div>
    </div>
    <!-- End Input marca -->

    <!-- Input modelo -->
    <div class="form-group row g-mb-10">
      <label class="col-sm-2 col-form-label g-mb-10" for="modelo">Modelo</label>

      <div class="col-sm-9">
        <input id="modelo" class="form-control u-form-control rounded-0" type="text" readonly>
      </div>
    </div>
    <!-- End Input modelo -->

    <!-- Input tipo -->
    <div class="form-group row g-mb-10">
      <label class="col-sm-2 col-form-label g-mb-10" for="tipo">Tipo vehiculo</label>

      <div class="col-sm-9">
        <input id="tipo" class="form-control u-form-control rounded-0" type="text" readonly>
      </div>
    </div>
    <!-- End Input tipo -->
  </form>
</div>

<!-- tabla perfiles asignados -->
<div class="card g-brd-darkpurple rounded-0 g-mb-30">
  <h3 class="card-header g-bg-darkpurple g-brd-transparent g-color-white g-font-size-16 rounded-0 mb-2">
    <i class="fa fa-gear g-mr-5"></i>
    Perfiles asignados al vehículo
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
      <tbody><!-- Ajax call --></tbody>
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


<?php $this->load->view('sistema/documentos/_modal_seguros_vehiculo') ?>

<?php $this->load->view('sistema/documentos/_modal_asignaciones_vehiculo') ?>

<div class="modal fade" id="modal_eliminar_renovacion_atributo" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">¿ Esta seguro de eliminar la renovacion ?</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="renovacion_delete_id" name="renovacion_delete_id" value="">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn u-btn-red" onclick="destroy_renovacion()">Eliminar</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>


<!-- modal imagenes -->
<div class="modal fade" id="modal_imagenes" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"> Imagenes </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <?php echo form_open_multipart('', array( 'id'=>'form_agregar_imagenes','class'=>'g-brd-around g-brd-gray-light-v4 col-8 mb-2', 
            'method' => 'POST'));?>
          <input type="hidden" name="vehiculo_id" id="vehiculo_id" value="">
          <div class="row">
            <div class="col-4">
              <!-- Plain File Input -->
              <div class="form-group row mb-0 ml-2">
                <label class="u-file-attach-v2 g-color-gray-dark-v5 mb-0">
                  <input id="imagenes" name="imagenes[]" type="file" multiple>
                  <i class="icon-cloud-upload g-font-size-16 g-pos-rel g-top-2 g-mr-5"></i>
                  <span class="js-value">Agregar imagenes</span>
                </label>
              </div>
              <!-- End Plain File Input -->
            </div>
            <div class="col-4">
              <button type="submit" id="btn_save_imagenes" class="btn btn-sm u-btn-primary ml-3"> Agregar </button>
            </div>
          </div>

        </form>
        <!-- Si el cargar el formulario retorna errores los imprimo en este div -->
        <div id="msg-errors"></div>
        <div class="container-fluid">
          <div id="image_galery" class="row">
            <!-- dibujamos con ajax -->
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- end modal editar imagenes -->

<!-- modal eliminar imagen -->
<div class="modal fade" id="modal_delete_imagen" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">¿ Esta seguro de eliminar esta imagen ?</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="imagen_delete_id" id="imagen_delete_id">
        <button type="button" class="btn u-btn-red" onclick="destroy_imagen()">Eliminar</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
<!-- end modal eliminar imagen -->

<!-- modal asignar atributo -->
<div class="modal fade" id="modal_asignar_atributo_personalizado" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"> Asignar atributo </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Si el cargar el formulario retorna errores los imprimo en este div -->
        <div id="msg-errors"></div>
        <?php echo form_open('', array('id' => 'form_asignar_atributo_personalizado', 'class' => 'pl-4' )) ?>
          <!-- input con el id del atributo_persona -->
          <input type="hidden" id="vehiculo_id" value="">
          <div class="row mt-2">
            <label for="asignar_atributo"> Atributo a asignar:  </label>
            <select name="asignar_atributo" id="select_asignar_atributo" class='ml-2' required>
              <option selected value="0"> Selecionar atributo </option>
              <?php foreach($atributos as $atributo) { ?>
                <option value="<?php echo $atributo->id ?>"> <?php echo $atributo->nombre ?> </option>
              <?php }  ?>
            </select>        
          </div> 
          <div class="row mt-2">
            <button id="btnAsignarAtributo" type="submit" class="btn u-btn-primary mr-2">Asignar</button>
            <button type="button" class="btn u-btn-red" data-dismiss="modal">Cerrar</button>
          </div>
        <?php echo form_close() ?>
      </div>
    </div>
  </div>
</div>
<!-- end modal asignar atributo -->

<!-- modal asignar eliminar atributo personalizados -->
<?php $this->load->view('sistema/documentos/_modal_asignar_eliminar_atributo') ?>
<!-- end modal eliminar atributo -->

<?php $this->load->view('sistema/documentos/_modal_archivos',array('titulo_modal'=>'Archivos de renovaciones')) ?>

</section>
<input type="hidden" id="tipo_dato" value="<?php echo $tipo?>">
<?php $this->load->view('sistema/documentos/_documentos_js') ?>

<script>
  let tabla_seguros_vehiculo, tabla_asignaciones_vehiculo, form_asignacion_vehiculo

  function modal_seguros_vehiculos( vehiculo_id ) {
    document.querySelector('#form_seguro_vehiculo #vehiculo_id').value =  vehiculo_id
    clean_form('form_seguro_vehiculo')
    $('#aseguradora_id').val(0).trigger('change')
    tabla_seguros_vehiculo.ajax.url(`${base_url}Seguros_Vehiculos/list/${vehiculo_id}`).load()
    tabla_seguros_vehiculo.ajax.reload(null,false)
    $('#modal_seguros_vehiculo').modal('show')
  }

  document.getElementById('form_seguro_vehiculo').addEventListener('submit', function(e) {
    e.preventDefault()
    let form_data = new FormData()
    let vehiculo_id = document.querySelector('#form_seguro_vehiculo #vehiculo_id').value
    form_data.append('vehiculo_id', parseInt(vehiculo_id) )
    form_data.append('aseguradora_id', parseInt(document.querySelector('#form_seguro_vehiculo #aseguradora_id').value) )
    form_data.append('poliza', document.querySelector('#form_seguro_vehiculo #poliza').value )
    form_data.append('fecha_alta', document.querySelector('#form_seguro_vehiculo #fecha_alta_seguro_vehiculo').value )
    form_data.append('vencimiento', document.querySelector('#form_seguro_vehiculo #fecha_vencimiento_seguro_vehiculo').value )
    agrupar_archivos( form_data, document.querySelector('#form_seguro_vehiculo #archivos_seguro') )
    fetch(`${base_url}Seguros_Vehiculos/create`, {
        method: 'POST',
        body: form_data
      })
      .then(response => response.json())
      .then(response => {
        if (response.status === 'success') {
          noty_alert( 'success' , response.msg )
          $('#aseguradora_id').val(0).trigger('change')
          clean_form('form_seguro_vehiculo')
          tabla_seguros_vehiculo.ajax.url(`${base_url}Seguros_Vehiculos/list/${vehiculo_id}`).load()
          tabla_seguros_vehiculo.ajax.reload(null,false)
        } else {
          noty_alert( 'error' , response.msg )
        }
      })
      .catch(console.log)
  })

  function modal_edit_aseguradora(seguro_vehiculo_id){
    clean_form('form_editar_seguros_vehiculo')
    fetch(`${base_url}Seguros_Vehiculos/show/${seguro_vehiculo_id}`)
      .then(response => response.json() )
      .then(response => {
        document.querySelector('#form_editar_seguros_vehiculo #seguro_vehiculo_id').value = response.id
        document.querySelector('#form_editar_seguros_vehiculo #poliza').value = response.poliza
        document.querySelector('#form_editar_seguros_vehiculo #fecha_alta_seguro_vehiculo').value = response.fecha_alta
        document.querySelector('#form_editar_seguros_vehiculo #fecha_vencimiento_seguro_vehiculo').value = response.vencimiento
        document.querySelector('#form_editar_seguros_vehiculo #aseguradora_id').value = response.aseguradora_id
      })
    $('#modal_editar_seguros_vehiculo').modal('show')
  }

  document.getElementById('form_editar_seguros_vehiculo').addEventListener('submit', (e) => {
    e.preventDefault()
    let form_data = new FormData()
    form_data.append('id', parseInt( document.querySelector('#form_editar_seguros_vehiculo #seguro_vehiculo_id').value  ) )
    form_data.append('aseguradora_id', parseInt(document.querySelector('#form_editar_seguros_vehiculo #aseguradora_id').value) )
    form_data.append('poliza', document.querySelector('#form_editar_seguros_vehiculo #poliza').value )
    form_data.append('fecha_alta', document.querySelector('#form_editar_seguros_vehiculo #fecha_alta_seguro_vehiculo').value )
    form_data.append('vencimiento', document.querySelector('#form_editar_seguros_vehiculo #fecha_vencimiento_seguro_vehiculo').value )
    agrupar_archivos( form_data, document.querySelector('#form_editar_seguros_vehiculo #archivos_seguro') )
    fetch(`${base_url}Seguros_Vehiculos/update`, {
        method: 'POST',
        body: form_data
      })
      .then(response => response.json())
      .then(response => {
        if (response.status === 'success') {
          noty_alert( 'success' , response.msg )
          $('#modal_editar_seguros_vehiculo').modal('hide')
          tabla_seguros_vehiculo.ajax.reload(null,false)
        } else {
          noty_alert( 'error' , response.msg )
        }
      })
      .catch(console.log)
  })

  function modal_asignaciones_vehiculos() {
    clean_form('form_asignacion_vehiculo')
    $('#asignacion_id').val(0).trigger('change')
    tabla_asignaciones_vehiculo.ajax.url(`${base_url}Asignaciones_vehiculo/list_asignaciones_vehiculo/${data_id}`).load()
    tabla_asignaciones_vehiculo.ajax.reload(null,false)
    $('#modal_asignaciones_vehiculo').modal('show')
  }

  document.getElementById('form_asignacion_vehiculo').addEventListener('submit', (e) => {
    e.preventDefault()
    let asignacion_id = parseInt( document.getElementById('asignacion_id').value )
    if (asignacion_id != 0) {
      let form_data = new FormData()
      form_data.append("vehiculo_id", data_id)
      form_data.append("asignacion_id", asignacion_id)
      form_data.append("fecha_alta", document.getElementById('fecha_alta_asignacion_vehiculo').value )
      agrupar_archivos( form_data, document.getElementById('archivos_asignacion') )
      fetch( `${base_url}Asignaciones_vehiculo/asignar_a_vehiculo`, {
        method: 'POST',
        body: form_data
      })
      .then(response => response.json() )
      .then(response => {
        if (response.status === 'success') {
          reload_tabla(tabla_asignaciones_vehiculo, `${base_url}Asignaciones_vehiculo/list_asignaciones_vehiculo/${data_id}`)
          clean_form('form_asignacion_vehiculo')
          $('#asignacion_id').val(0).trigger('change')
        }
        noty_alert( response.status , response.msg )
      })
      .catch(error => console.log('error: ' + error))
    } else {
      noty_alert('info', 'Debe seleccionar un lugar de asignación')
    }
  })

  function modal_baja_asignacion_vehiculo(asignacion_vehiculo_id){
    clean_form('form_eliminar_asignacion')
    document.getElementById('vehiculo_asignacion_id').value = asignacion_vehiculo_id
    $('#modal_eliminar_asignacion').modal('show')
  }

  document.getElementById('form_eliminar_asignacion').addEventListener('submit', (e) => {
    e.preventDefault()
    let form_data = new FormData()
    form_data.append("id", parseInt( document.querySelector('#form_eliminar_asignacion #vehiculo_asignacion_id').value ) )
    form_data.append("fecha_baja", document.querySelector('#form_eliminar_asignacion #fecha_baja_asignacion_vehiculo').value )
    fetch( `${base_url}Asignaciones_vehiculo/baja_asignar_vehiculo`, {
      method: 'POST',
      body: form_data
    })
    .then(response => response.json() )
    .then(response => {
      if (response.status === 'success') {
        reload_tabla(tabla_asignaciones_vehiculo, `${base_url}Asignaciones_vehiculo/list_asignaciones_vehiculo/${data_id}`)
        $('#modal_eliminar_asignacion').modal('hide')
      }
      noty_alert( response.status , response.msg )
    })
    .catch(error => console.log('error: ' + error))
  })
  /* MODAL IMAGENES */ 
  function modal_imagenes( vehiculo_id ) {
    let url_image
    clean_form('form_agregar_imagenes')
    $('#image_galery').html('')
    $('#modal_imagenes #vehiculo_id').val( vehiculo_id )
    $.ajax({
      url: `${base_url}Vehiculos/get_imagenes/${vehiculo_id}`,
      type: 'GET',
      data: { vehiculo_id: vehiculo_id },
      dataType: 'JSON',
      success: function( resp ) {
        $(resp).each(function(i, element){
          url_image = "<?php echo base_url() ?>" + element.path
          $('#image_galery').append('<div id="image_'+element.id+'" class="col-4"> <img class="img-thumbnail mb-1" src="' + url_image + '" alt="200px"></img> <a class="btn btn-xs u-btn-blue mr-4" href="'+ url_image +'" target="_blank"> ver</a><button class="btn btn-xs u-btn-red" onclick="modal_delete_imagen('+ element.id +')"> borrar</button></div>');
        });
      },
      error: function( resp ){
        $('#image_galery').html('no se pudieron obtener las imagenes')
      }
    })

    $('#modal_imagenes').modal('show')
  }

  function modal_delete_imagen( imagen_id ) {
    $('#imagen_delete_id').val( imagen_id )
    $('#modal_delete_imagen').modal('show')
  }

  function destroy_imagen() {
    let this_image = $('#imagen_delete_id').val() // id imagen a eliminar
    $.ajax({
      url: '<?php echo base_url("Vehiculos/eliminar_imagen") ?>',
      type: 'POST',
      data: { imagen_id: this_image },
      dataType: 'JSON',
      success: function(resp){
        if (resp.status == 'success') {
          $('#image_'+ this_image ).hide('slow')
          noty_alert('success', 'Imagen eliminada con exito')
          $('#modal_delete_imagen').modal('hide')
        } else {
          noty_alert('error', 'No se pudo eliminar la imagen')
        }
      },
      error: function(){
        noty_alert('error', 'error en el metodo')
      }
    })
  }

  $('#form_agregar_imagenes').submit(function(event){
    event.preventDefault();
    let imagenes = document.getElementById('imagenes')
    let totalfiles = imagenes.files.length;
    if (totalfiles > 0 ) {
      let form_data = new FormData()
      // Read selected files
      for (let i = 0; i < totalfiles; i++) {
        form_data.append("imagenes[]", imagenes.files[i]);
      }
      form_data.append('vehiculo_id', $("#form_agregar_imagenes #vehiculo_id").val() )

      $.ajax({
        url: `${base_url}Vehiculos/ajax_upload_imagen`,
        type: 'POST',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        dataType: 'JSON',
        success: function( resp ){
          if ( resp.status == 'success' ) {
            noty_alert('success', 'Imagenes agregadas')
            clean_form('form_agregar_imagenes')
            let url_imagen, imagen_id
            for(let i = 0; i < resp.imagenes.length; i++) {
              url_imagen = `${base_url}${resp.imagenes[i].path}`
              imagen_id = resp.imagenes[i].id
             // Add img element in <div id='image_galery'>
              $('#image_galery').append(`
                <div id="image_${imagen_id}" class="col-4"> <img class="img-thumbnail mb-1" src="${url_imagen}" alt="200px"></img> <a class="btn btn-xs u-btn-blue mr-4" href="${url_imagen}" target="_blank"> ver</a> <button class="btn btn-xs u-btn-red" onclick="modal_delete_imagen('${imagen_id}')"> borrar</button></div>
              `);
           }
          } else {
            noty_alert(resp.status, resp.msg)
          }
        }
      })
    } else {
      noty_alert('info', 'No ha seleccionado archivos')
    }
  })
// END MODAL IMAGENES
  $(document).on('ready', function () {
    clean_form('form_info_vehiculo')
    $('#vehiculo_seleccionado').on('change', function(){
      data_id = $('#vehiculo_seleccionado option:selected').val()
      $.ajax({
        url: '<?php echo base_url('Vehiculos/show/');?>' + data_id,
        type: 'GET',
        dataType: 'JSON',
        success: function( resp ){
          $('#dominio').val(resp.dominio)
          $('#anio').val(resp.anio)
          $('#marca').val(resp.marca)
          $('#modelo').val(resp.modelo)
          $('#tipo').val(resp.tipo)
          // ajax.url se usa para refrescar la url de donde se obtienen los datos
          tabla_perfiles.ajax.url('<?php echo base_url("Documentos/get_perfiles/2/");?>'+data_id).load()
          tabla_perfiles.ajax.reload(null,false)

          tabla_atributos.ajax.url('<?php echo base_url("Documentos/get_atributos/2/");?>'+data_id).load()
          tabla_atributos.ajax.reload(null,false)
        }
      })
    })
    tabla_seguros_vehiculo = $('#tabla_seguros_vehiculo').DataTable({
                                  language: { url: datatables_lang},
                                  "ordering": false})
    tabla_asignaciones_vehiculo = $('#tabla_asignaciones_vehiculo').DataTable({
                                  language: { url: datatables_lang},
                                  "ordering": false})

    $('#vehiculo_seleccionado').select2( { theme: 'bootstrap4', width: '50%' } )
    $('#aseguradora_id').select2( { theme: 'bootstrap4', width: '50%' } )
    $('#asignacion_id').select2( { theme: 'bootstrap4', width: '50%' } )
  } )
</script>

