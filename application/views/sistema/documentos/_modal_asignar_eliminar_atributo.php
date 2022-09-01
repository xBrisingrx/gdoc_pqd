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
          <input type="hidden" id="data_id" value="">
          <div class="row mt-2">
            <label for="asignar_atributo"> Atributo a asignar:  </label>
            <select name="asignar_atributo" id="select_asignar_atributo" class='ml-2' required>
              <option selected value="0"> Selecionar atributo </option>
              <?php foreach($atributos as $atributo) { ?>
                <option value="<?php echo $atributo->id ?>"> <?php echo $atributo->nombre ?> </option>
              <?php }  ?>
            </select>        
          </div> 
          <div class="mt-2">
            <button id="btnAsignarAtributo" type="submit" class="btn u-btn-primary">Asignar</button>
            <button type="button" class="btn u-btn-red" data-dismiss="modal">Cerrar</button>
          </div>
        <?php echo form_close() ?>
      </div>
    </div>
  </div>
</div>
<!-- end modal asignar atributo -->

<!-- modal eliminar atributo -->
<div class="modal fade" id="modal_eliminar_atributo" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"> Eliminar atributo </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Confirma que quiere eliminar este atributo?</p>
        <!-- Si el cargar el formulario retorna errores los imprimo en este div -->
        <div id="msg-errors"></div>
        <?php echo form_open('', array('id' => 'form_eliminar_atributo' )) ?>
          <!-- input con el id del atributo_persona -->
          <input type="hidden" id="atributo_data_id" value="">         
          <button id="btnEliminarAtributo" type="submit" class="btn btn-primary">Eliminar</button>
          <button type="button" class="btn u-btn-red" data-dismiss="modal">Cerrar</button>
        <?php echo form_close() ?>
      </div>
    </div>
  </div>
</div>
<!-- end modal eliminar atributo -->