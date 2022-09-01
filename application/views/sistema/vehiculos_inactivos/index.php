<section class="container-fluid g-py-10 " >
  <h1>Lista de vehiculos dados de baja</h1>

  <?php if ($this->session->userdata('rol') == 1): ?>
    <a href="<?php echo base_url('Vehiculos');?>" class="btn btn-info mb-2"> <i class="fa fa-chevron-left"></i> Volver a vehiculos</a>
  <?php endif ?>

  <div class="card g-brd-darkpurple rounded-1 g-mb-30">
    <h3 class="card-header g-bg-darkpurple g-brd-transparent g-color-white g-font-size-16 rounded-0 mb-2">
      <i class="fa fa-gear g-mr-5"></i>
      Vehiculos inactivos
    </h3>

    <div class="px-2 pb-2">
      <table id="tabla_vehiculos_inactivos" class="table table-hover dt-responsive w-100">
        <thead>
          <tr>
            <th>Interno</th>
            <th>Dominio</th>
            <th>Año</th>
            <th>Marca y modelo</th>
            <th>Tipo</th>
            <th>Empresa</th>
            <th>Motivo</th>
            <th>Detalle</th>
            <th>Fecha baja</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($vehiculos as $v): ?>
            <tr id="row_<?php echo $v->id?>">
              <td id="td_interno"><?php echo $v->interno?></td>
              <td><?php echo $v->dominio?></td>
              <td><?php echo $v->anio?></td>
              <td><?php echo "$v->marca $v->modelo"?></td>
              <td><?php echo $v->tipo?></td>
              <td><?php echo $v->empresa?></td>
              <td><?php echo $v->motivo?></td>
              <td><?php echo $v->detalle?></td>
              <td><?php echo date('d-m-Y', strtotime($v->fecha_baja))?></td>
              <td>
                <button class="btn u-btn-orange btn-sm"
                        title="Reactivar"
                        onclick="modal_reactivar_vehiculo( <?php echo "'$v->id', '$v->vehiculo_id', '$v->interno', '$v->dominio'" ?> )">
                        <i class="fa fa-refresh"></i>
                </button>
                <button class="btn u-btn-blue btn-sm"
                        title="Historial"
                        onclick="modal_historial( <?php echo " '$v->vehiculo_id', '$v->interno' " ?> )">
                        <i class="fa fa-eye"></i>
                </button>
              </td>
            </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>
  </div>
</section>


<!-- ##################################### Modal reactivar vehiculo   ######################################## -->
<div class="modal fade" id="modal_reactivar_vehiculo" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">¿ Esta seguro de reactivar el vehiculo ?</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="form_reactivar_vehiculo">
          <input type="hidden" id="id_vehiculo_reactivar" name="id_vehiculo_reactivar" value="">
          <input type="hidden" id="vehiculo_inactivo_id" name="vehiculo_inactivo_id" value="">
          <div class="row">
            <p id="interno_vehiculo_reactivar" class="col-6"></p>
            <p id="dominio_vehiculo_reactivar" class="col-6"></p>
          </div>
          <!-- Select Single Date -->
          <div class="form-group g-mb-30">
            <label class="g-mb-10">Fecha reactivación</label>
            <div class="input-group g-brd-primary--focus">
              <input id="fecha_reactivacion" name="fecha_reactivacion" class="form-control form-control-md  rounded-0" type="date" value="" required>
            </div>
          </div>
          <!-- End Select Single Date -->
          <!-- Textarea Expandable -->
          <div class="form-group g-mb-20">
            <label class="g-mb-10" for="detalle_reactivacion_vehiculo">Detalle</label>
            <textarea id="detalle_reactivacion_vehiculo" name="detalle_reactivacion_vehiculo" class="form-control form-control-md u-textarea-expandable rounded-0" rows="3" placeholder="Motivo de reactivación..." required></textarea>
          </div>
          <!-- End Textarea Expandable -->
          <button type="submit" class="btn u-btn-primary">Reactivar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- ######################################################################################################### -->

<!-- ##################################### Modal historial inactividad/reactivacion   ######################################## -->
<div class="modal fade" id="modal_historial" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table id="tabla_historial" class="table table-hover dt-responsive w-100">
          <thead>
          <tr>
            <th>Motivo</th>
            <th>Baja</th>
            <th>Detalle</th>
            <th>Alta</th>
            <th>Detalle</th>
          </tr>
        </thead>
        <tbody> <!-- ajax --> </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
<!-- ######################################################################################################### -->



<script type="text/javascript">
  let tabla_historial
  let form_reactivar = $('#form_reactivar_vehiculo')

  function modal_reactivar_vehiculo( vehiculo_inactivo_id, vehiculo_id, interno_vehiculo, dominio_vehiculo ) {
    $('#id_vehiculo_reactivar').val(vehiculo_id)
    $('#vehiculo_inactivo_id').val(vehiculo_inactivo_id)
    setInputDate('#fecha_reactivacion')
    $('#interno_vehiculo_reactivar').html( `<b>Interno: </b> ${interno_vehiculo}` )
    $('#dominio_vehiculo_reactivar').html( `<b>Dominio: </b> ${dominio_vehiculo}` )
    $('#modal_reactivar_vehiculo').modal('show')
  }

  function modal_historial( vehiculo_id, interno ){
    tabla_historial.ajax.url( "<?php echo base_url('Vehiculos_Inactivos/get/')?>" + vehiculo_id )
    tabla_historial.ajax.reload()
    $('#modal_historial .modal-title').html(`Historial interno ${interno}`)
    $('#modal_historial').modal('show')
  }

  $(document).ready(function() {
    $('#form_reactivar_vehiculo').submit(function(e){
      e.preventDefault()
      e.stopImmediatePropagation()
      if (form_reactivar.valid()) {
        $.ajax({
          url: '<?php echo base_url("Vehiculos_Inactivos/reactivar/");?>',
          type: 'POST',
          data: {
            id : $('#vehiculo_inactivo_id').val(),
            vehiculo_id : $('#id_vehiculo_reactivar').val(),
            fecha_alta : $('#fecha_reactivacion').val(),
            detalle : $('#detalle_reactivacion_vehiculo').val(),
          },
          dataType: 'JSON',
          success: function(response) {
            (response.status === 'success') ? window.location.href = "<?php echo base_url('Vehiculos_Inactivos');?>" : noty_alert(response.status, response.msg)
          },
          error: function() {
            noty_alert('error','Error en el servidor, no se pudo reactivar el vehiculo')
          }
        }) // end ajax
      }   
    }) // end submit form_reactivar

    $('#tabla_vehiculos_inactivos').DataTable({ language: 
                                        { url: "<?php echo base_url('assets/vendor/datatables/spanish.json');?>" } 
                                    })
    tabla_historial= $('#tabla_historial').DataTable({ 
                                    "ordering": false,
                                    language: 
                                        { url: "<?php echo base_url('assets/vendor/datatables/spanish.json');?>" } 
                                    })
    form_reactivar.validate({
                    rules: {
                      'detalle_reactivacion_vehiculo': {required: true, minlength: 5}
                    }
                  })

  } )
</script>