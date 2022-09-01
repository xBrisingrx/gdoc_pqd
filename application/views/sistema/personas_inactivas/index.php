<section class="container-fluid g-py-10 " >
  <h1>Lista de personas dados de baja</h1>

  <?php if ($this->session->userdata('rol') == 1): ?>
    <a href="<?php echo base_url('Personas');?>" class="btn btn-info mb-2"> <i class="fa fa-chevron-left"></i> Volver a personas</a>
  <?php endif ?>

  <div class="card g-brd-darkpurple rounded-1 g-mb-30">
    <h3 class="card-header g-bg-darkpurple g-brd-transparent g-color-white g-font-size-16 rounded-0 mb-2">
      <i class="fa fa-gear g-mr-5"></i>
      Personas inactivas
    </h3>

   <div class="px-2  pb-2">
      <table id="tabla_personas_inactivas" class="table table-hover dt-responsive w-100">
        <thead>
          <tr>
            <th>Nro. Legajo</th>
            <th>Nombre</th>
            <th>DNI</th>
            <th>Empresa</th>
            <th>Motivo</th>
            <th>Fecha baja</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($personal_inactivo as $persona): ?>
            <tr id="row_<?php echo $persona->id?>">
              <td id="td_legajo"><?php echo $persona->n_legajo?></td>
              <td><?php echo "$persona->apellido $persona->nombre"?></td>
              <td><?php echo $persona->dni?></td>
              <td><?php echo "$persona->empresa"?></td>
              <td><?php echo $persona->motivo?></td>
              <td><?php echo date('d-m-Y', strtotime($persona->fecha_baja))?></td>
              <td>
                <?php if ( $persona->activo ): ?>
                  <button class="btn u-btn-orange btn-sm"
                    title="Reactivar"
                    onclick="modal_reactivar_persona( <?php echo "'$persona->id', '$persona->persona_id', '$persona->n_legajo' ,
                                                      '$persona->apellido $persona->nombre'" ?> )">
                    <i class="fa fa-refresh"></i>
                </button>
                <?php endif ?>
                <button class="btn u-btn-blue btn-sm"
                        title="Historial"
                        onclick="modal_historial( <?php echo " '$persona->persona_id', '$persona->apellido $persona->nombre' " ?> )">
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


<!-- ##################################### Modal reactivar persona   ######################################## -->
<div class="modal fade" id="modal_reactivar_persona" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">¿ Esta seguro de reactivar a esta persona ?</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="form_reactivar_persona">
          <input type="hidden" id="id_persona_reactivar" name="id_persona_reactivar" value="">
          <input type="hidden" id="persona_inactiva_id" name="persona_inactiva_id" value="">
          <div class="row">
            <p id="legajo_persona_reactivar" class="col-6"></p>
            <p id="nombre_persona_reactivar" class="col-6"></p>
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
            <label class="g-mb-10" for="detalle_reactivacion_persona">Detalle</label>
            <textarea id="detalle_reactivacion_persona" name="detalle_reactivacion_persona" class="form-control form-control-md u-textarea-expandable rounded-0" rows="3" placeholder="Motivo de reactivación..." required></textarea>
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
  let form_reactivar = $('#form_reactivar_persona')

  function modal_reactivar_persona( persona_inactiva_id, persona_id, n_legajo_persona, nombre_persona ) {
    $('#id_persona_reactivar').val(persona_id)
    $('#persona_inactiva_id').val(persona_inactiva_id)
    setInputDate('#fecha_reactivacion')
    $('#nombre_persona_reactivar').html( `<b>Nombre: </b> ${nombre_persona}` )
    $('#legajo_persona_reactivar').html( `<b>Legajo: </b> ${n_legajo_persona}` )
    $('#modal_reactivar_persona').modal('show')
  }

  function modal_historial( persona_id, nombre ){
    tabla_historial.ajax.url( "<?php echo base_url('Personas_Inactivas/get/')?>" + persona_id )
    tabla_historial.ajax.reload()
    $('#modal_historial .modal-title').html(`Historial de ${nombre}`)
    $('#modal_historial').modal('show')
  }

  $(document).ready(function() {
    $('#form_reactivar_persona').submit(function(e){
      e.preventDefault()
      e.stopImmediatePropagation()
      if (form_reactivar.valid()) {
        $.ajax({
          url: '<?php echo base_url("Personas_Inactivas/reactivar/");?>',
          type: 'POST',
          data: {
            id : $('#persona_inactiva_id').val(),
            persona_id : $('#id_persona_reactivar').val(),
            fecha_alta : $('#fecha_reactivacion').val(),
            detalle : $('#detalle_reactivacion_persona').val(),
          },
          dataType: 'JSON',
          success: function(response) {
            (response.status === 'success') ? window.location.href = "<?php echo base_url('Personas_Inactivas');?>" : noty_alert(response.status, response.msg)
          },
          error: function() {
            noty_alert('error','Error en el servidor, no se pudo reactivar el persona')
          }
        }) // end ajax
      }   
    }) // end submit form_reactivar

    $('#tabla_personas_inactivas').DataTable({ language: 
                                        { url: "<?php echo base_url('assets/vendor/datatables/spanish.json');?>" } 
                                    })
    tabla_historial= $('#tabla_historial').DataTable({ 
                                    "ordering": false,
                                    language: 
                                        { url: "<?php echo base_url('assets/vendor/datatables/spanish.json');?>" } 
                                    })
    form_reactivar.validate({
                    rules: {
                      'detalle_reactivacion_persona': {required: true, minlength: 5}
                    }
                  })

  } )
</script>