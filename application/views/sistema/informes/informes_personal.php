<section class="container g-py-10">
  <h1>Informes de personal</h1>

  <h3>Seleccione el informe que desea descargar</h3>

  <a href="<?php echo base_url('Informes/informe_matriz') ?>" class="btn btn-md u-btn-primary g-mr-10 g-mb-15">Informe matriz de personas</a>
  <!-- form entre fechas -->
  <form id="form_informe_entre_fechas" action="<?php echo base_url('Informes/informe_matriz') ?>" class="g-brd-around g-brd-gray-light-v4 g-pa-30 g-mb-30" method="GET">
    <div class="row g-mb-10">
      <div class="col-3">
        <label  for="fecha_inicio">Desde </label>
        <input class="form-control rounded-0 form-control-md mr-sm-3 mb-3 mb-lg-0" id="fecha_inicio" name="fecha_inicio" type="date" required="required">
      </div>
      <div class="col-3">
        <label  for="fecha_fin">Hasta </label>
        <div class="input-group mr-sm-3 mb-3 mb-lg-0">
          <input class="form-control rounded-0 form-control-md" id="fecha_fin" name="fecha_fin" type="date" required="required">
        </div>
      </div>
      <div class="col-6">
        <div class="row">
          <label class="g-mb-10" for="atributo_id"> Atributos</label>
        </div>    
        <select class="persona_multiples_atributos" name="atributo_id[]" id="atributo_id" multiple="multiple">
          <?php foreach ($atributos_personas as $attr_per): ?>
            <option value="<?php echo $attr_per->id;?>"> <?php echo $attr_per->nombre ?> </option>
          <?php endforeach ?>
        </select>
      </div>
    </div>
      <div class="row ">
        <button id="btnDate" type="submit" class="btn btn-md u-btn-primary rounded-0 g-ml-15">Descargar</button>
      </div>
    <small id="error-form" class="text-danger">  </small>
  </form>
  <!-- end form entre fechas -->
</section>

<script>
$( document ).ready(function() {
  setInputDate('#fecha_inicio')
  setInputDate('#fecha_fin')

  $('.persona_multiples_atributos').select2({
      theme: 'bootstrap4',
      width: '90%'
  })
 
})

</script>

