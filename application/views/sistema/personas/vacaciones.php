<section class="container-fluid g-py-10">
  <h1>Registro de vacaciones</h1>

  <div class="card g-brd-darkpurple rounded-0 g-mb-30">
    <h3 class="card-header g-bg-darkpurple g-brd-transparent g-color-white g-font-size-16 rounded-0 mb-0">
      <i class="fa fa-gear g-mr-5"></i>
      Registro de vacaciones
    </h3>

    <div class="table-responsive">
      <table id="tabla_vacaciones" class="table table-hover u-table--v1 mb-0">
      <thead>
        <tr>
          <th>Apellido</th>
          <th>Nombre</th>
          <th>Dias a favor</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
        <tbody>

        </tbody>
      </table>
    </div>
  </div>
</section>


<!-- Modal alta licencia -->
<div class="modal fade" id="modal_alta_licencia" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"> Registrar licencia </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

        <form id="form_registrar_licencia" class="g-brd-around g-brd-gray-light-v4 g-pa-30 g-mb-30" method="POST">
          <input type="hidden" id="person_id">
          <div class="form-inline">
            <label  for="fecha_inicio_licencia">Desde </label>
            <input class="form-control rounded-0 form-control-md mr-sm-3 mb-3 mb-lg-0" id="fecha_inicio_licencia" name="fecha_inicio_licencia" type="date" required="required">

            <label  for="fecha_fin_licencia">Hasta </label>
            <div class="input-group mr-sm-3 mb-3 mb-lg-0">
              <input class="form-control rounded-0 form-control-md" id="fecha_fin_licencia" name="fecha_fin_licencia" type="date" required="required">
            </div>
          </div>
          <small id="error-form" class="text-danger">  </small>
          <br>
          <button type="submit" class="btn u-btn-red">Registrar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        </form>

      </div>
    </div>
  </div>
</div>


<!-- Modal detalle licencias -->
<div class="modal fade" id="modal_detalle_licencia" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"> Detalle licencias </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="">
          <table id="tabla_detalle_vacaciones" class="table table-hover" width="100%">
          <thead>
            <tr>
              <th>Desde</th>
              <th>Hasta</th>
              <th>Acciones</th>
            </tr>
          </thead>
            <tbody>

            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal agregar dias -->
<div class="modal fade" id="modal_agregar_dias" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"> Agregar dias </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

        <form id="form_agregar_dias" class="g-brd-around g-brd-gray-light-v4 g-pa-30 g-mb-30" method="POST">
          <input type="hidden" id="persona_id">
          <div class="form-group row">
            <label  for="cantidad_dias">Cantidad </label>
            <input class="form-control rounded-0 form-control-md mr-sm-3 mb-3 mb-lg-0" id="cantidad_dias" name="cantidad_dias" type="text" required>
          </div>
          <div class="form-group row">
            <label class="g-mb-5" for="detalle">Motivo: </label>
            <textarea id="detalle" name="detalle" class="form-control form-control-md u-textarea-expandable rounded-0" rows="3" placeholder="Ingrese el porque se le agregan los días" required></textarea>
          </div>
          <br>
          <button type="submit" class="btn u-btn-primary">Registrar</button>
          <button type="button" class="btn btn-secondary" onclick="cerrar_modal_agregar_dias()">Cerrar</button>
        </form>

      </div>
    </div>
  </div>
</div>


<!-- Modal detalle dias agregados -->

<div class="modal fade" id="modal_detalle_dias_agregados" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"> Detalle dias agregados </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="">
          <table id="tabla_detalle_dias_agregados" class="table table-hover" width="100%">
          <thead>
            <tr>
              <th>Cantidad dias</th>
              <th>Motivo</th>
              <th>Acciones</th>
            </tr>
          </thead>
            <tbody>

            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  var base_url = '<?php echo base_url();?>';
  var table_vacations_details
  var table_detalle_dias_agregados

  var form_agregar_dias = $('#form_agregar_dias').validate({
                    rules: {
                      'cantidad_dias': {
                        number: true
                      }
                    }
                  })

  function registrar_licencia( person_id )
  {
    $('#form_registrar_licencia #person_id').val( person_id )
    $('#modal_alta_licencia').modal('show')
  }

  $('#form_registrar_licencia').submit(function(e){
    e.preventDefault();
    $.ajax({
      url: '<?php echo base_url("Personas/registrar_licencia") ?>',
      type: "POST",
      data: agrupar_datos(),
      success: function( rta ){
        if (rta === 'ok') {
            table_vacations.ajax.reload(null,false)
            $('#modal_alta_licencia').modal('hide')
        } else {
          alert('error al registrar licencia')
        }
      },
      error: function(){

      }
    })
  })

  $('#form_agregar_dias').submit(function(e){
    e.preventDefault();
    if (form_agregar_dias.valid()) {
      $.ajax({
        url: '<?php echo base_url("Personas/agregar_dias_licencia") ?>',
        type: "POST",
        data: agrupar_form_dias_agregados(),
        success: function( rta ){
          if (rta === 'ok') {
              table_vacations.ajax.reload(null,false)
              $('#modal_agregar_dias').modal('hide')
              $('#form_agregar_dias')[0].reset()
              $('#form_agregar_dias .form-control').removeClass('error');
              $('#form_agregar_dias .error').empty();
          } else {
            alert('error al registrar dias')
          }
        },
        error: function(){

        }
      })
    }


  })


  function agrupar_datos()
  {
    datos = {
      'person_id': $('#person_id').val(),
      'desde': $('#fecha_inicio_licencia').val(),
      'hasta': $('#fecha_fin_licencia').val(),
    }

    return datos
  }

  function agrupar_form_dias_agregados()
  {
    datos = {
        'persona_id': $('#form_agregar_dias #persona_id').val(),
        'cantidad_dias': $('#form_agregar_dias #cantidad_dias').val(),
        'detalle': $('#form_agregar_dias #detalle').val()
    }
    return datos
  }

  function detalle_licencias( person_id )
  {
    table_vacations_details.ajax.url('<?php echo base_url("Personas/vacaciones_tomadas_list/");?>'+person_id).load()
    table_vacations_details.ajax.reload(null,false)
    $('#modal_detalle_licencia').modal('show')
  }

  function agregar_dias(person_id)
  {
    $('#form_agregar_dias #persona_id').val(person_id)
    $('#modal_agregar_dias').modal('show')
  }

  function cerrar_modal_agregar_dias()
  {
    $('#modal_agregar_dias').modal('hide')
    $('#form_agregar_dias .form-control').removeClass('error');
    $('#form_agregar_dias .error').empty();
    $('#form_agregar_dias')[0].reset()
  }

  function detalle_dias_agregados( person_id )
  {
    table_detalle_dias_agregados.ajax.url('<?php echo base_url("Personas/dias_agregados_list/");?>'+person_id).load()
    table_detalle_dias_agregados.ajax.reload(null,false)
    $('#modal_detalle_dias_agregados').modal('show')
  }

  function eliminar_vacaciones(id)
  {
    $.ajax({
      url: '<?php echo base_url("Personas/eliminar_licencia/licencias/") ?>' + id,
      success: function( rta ){
        if (rta === 'ok') {
            table_vacations.ajax.reload(null,false)
            $('#modal_detalle_licencia').modal('hide')
        } else {
          alert('error al registrar dias')
        }
      },
      error: function(){

      }
    })
  }

  function eliminar_dias_agregados(id)
  {
    $.ajax({
      url: '<?php echo base_url("Personas/eliminar_licencia/licencias_dias_ganados/") ?>' + id,
      success: function( rta ){
        if (rta === 'ok') {
            table_vacations.ajax.reload(null,false)
            $('#modal_detalle_dias_agregados').modal('hide')
        } else {
          alert('error al registrar dias')
        }
      },
      error: function(){

      }
    })
  }

  $(document).on('ready', function () {
  table_vacations = $('#tabla_vacaciones').DataTable( {
                          lengthChange: true,
                          ajax : '<?php echo base_url('Personas/vacaciones_list/');?>',
                          language: {
                                        url: "<?php echo base_url(); ?>assets/vendor/DataTables/Spanish.json"
                                      }
                        })

  table_vacations_details = $('#tabla_detalle_vacaciones').DataTable( {
                          lengthChange: true,
                          ajax : '<?php echo base_url('Personas/vacaciones_tomadas_list/');?>',
                          language: {
                                        url: "<?php echo base_url(); ?>assets/vendor/DataTables/Spanish.json"
                                      },
                          responsive: {
                                      details: {
                                          display: $.fn.dataTable.Responsive.display.modal( {
                                              header: function ( row ) {
                                                  var data = row.data();
                                                  return 'Details for '+data[0]+' '+data[1];
                                              }
                                          } ),
                                          renderer: $.fn.dataTable.Responsive.renderer.tableAll( {
                                              tableClass: 'table'
                                          } )
                                      }
                                  }
                        })
  table_detalle_dias_agregados = $('#tabla_detalle_dias_agregados').DataTable( {
                          lengthChange: true,
                          ajax : '<?php echo base_url('Personas/dias_agregados_list/');?>',
                          language: {
                                        url: "<?php echo base_url(); ?>assets/vendor/DataTables/Spanish.json"
                                      }
                        })



  })
</script>