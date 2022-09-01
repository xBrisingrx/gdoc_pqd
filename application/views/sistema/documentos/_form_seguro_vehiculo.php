<input type="hidden" id="vehiculo_id" value="">
<!-- Input nombre atributo -->
<div class="form-group row g-mb-10">
  <label class="col-sm-3 col-form-label g-mb-10" for="nombre_attr">Aseguradora</label>
  <div class="col-sm-9">
    <select id="aseguradora_id" name="aseguradora_id" class="form-control form-control-md form-control-lg rounded-0 g-mb-10 col-sm-6" required>
      <option value='0'>Seleccione aseguradora</option>
      <?php foreach ($aseguradoras as $a): ?>
        <option value="<?php echo $a->id;?>"><?php echo $a->nombre;?></option>
      <?php endforeach ?>
    </select>
  </div>
</div>
<!-- End Input nombre atributo -->
<div class="form-group row g-mb-10">
  <label class="col-sm-3 col-form-label g-mb-10" for="poliza">Poliza</label>
  <div class="col-sm-6">
    <input id="poliza" name="poliza" class="form-control u-form-control rounded-0" type="text" value="" required>
  </div>
</div>

<!-- Fecha de renovacion atributo -->
<div class="row form-group g-mb-10">
  <label for="fecha_alta_seguro_vehiculo" class="col-sm-3 col-md-3 col-form-label">Fecha alta (*)</label>
  <div class="col-md-6 col-sm-6">
    <input id="fecha_alta_seguro_vehiculo" name="fecha_alta_seguro_vehiculo" class="form-control form-control-md rounded-0" type="date" required>
  </div>
</div>
<!-- End Fecha de renovacion atributo -->

<!-- Fecha de vencimiento atributo -->
<div class="row form-group g-mb-10">
  <label for="fecha_vencimiento_seguro_vehiculo" class="col-sm-3 col-md-3 col-form-label">Fecha vencimiento (*)</label>
  <div class="col-md-6 col-sm-6">
    <input id="fecha_vencimiento_seguro_vehiculo" name="fecha_vencimiento_seguro_vehiculo" class="form-control form-control-md rounded-0" type="date" required>
  </div>
</div>
<!-- End Fecha de vencimiento atributo -->