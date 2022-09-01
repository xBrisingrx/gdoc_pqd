<section class="container g-py-10">
  <h1>Edición de persona</h1>

  <?php echo form_open_multipart(' ', array( 'id'=>'form_persona','class'=>'form_new_person g-brd-around g-brd-gray-light-v4 g-pa-30 g-mb-30', 'method' => 'post'));?>

    <!-- id persona a editar -->
    <input id="id" name="id" type="hidden" value="<?php echo $persona->id;?>">

    <!-- Legajo Input -->
    <div class="form-group row g-mb-10">
      <label class="col-sm-2 col-form-label g-mb-10" for="legajo">Nro. Legajo(*)</label>
      <div class="col-sm-9">
        <input id="legajo" name="legajo" class="form-control u-form-control rounded-0" type="text" placeholder="Ingrese legajo" value="<?php echo $persona->n_legajo ?>" required>
      </div>
    </div>
    <!-- End Legajo Input -->
    <!-- Apellido Input -->
    <div class="form-group row g-mb-10">
      <label class="col-sm-2 col-form-label g-mb-10" for="apellido">Apellido (*)</label>
      <div class="col-sm-9">
        <input id="apellido" name="apellido" class="form-control u-form-control rounded-0" type="text" placeholder="Ingrese apellido" value="<?php echo $persona->apellido ?>" required>
      </div>
    </div>
    <!-- End Apellido Input -->
    <!-- Nombre Input -->
    <div class="form-group row g-mb-10">
      <label class="col-sm-2 col-form-label g-mb-10" for="nombre">Nombre (*)</label>
      <div class="col-sm-9">
        <input id="nombre" name="nombre" class="form-control u-form-control rounded-0" type="text" placeholder="Ingrese nombre" value="<?php echo $persona->nombre ?>" required>
      </div>
    </div>
    <!-- End Nombre Input -->
    <!-- DNI Input -->
    <div class="form-group row g-mb-10">
      <label class="col-sm-2 col-form-label g-mb-10" for="dni">DNI (*)</label>
      <div class="col-sm-3">
        <input id="dni" name="dni" class="form-control u-form-control rounded-0" type="text" placeholder="Ingrese DNI" value="<?php echo $persona->dni ?>" required>
      </div>
      <label class="u-file-attach-v2 g-color-gray-dark-v5 mb-0">
          <input id="pdf_dni" name="pdf_dni" type="file" >
          <i class="icon-cloud-upload g-font-size-16 g-pos-rel g-top-2 g-mr-5"></i>
          <span class="js-value">Anexar PDF del DNI</span>
        </label>
    </div>

      <div class="form-group mb-0 offset-md-2">
        
      </div>

    <!-- End DNI Input -->

    <!-- Nro tramite Input -->
    <div class="form-group row g-mb-10">
      <label class="col-sm-2 col-form-label g-mb-10" for="num_tramite">Num tramite</label>
      <div class="col-sm-9">
        <input id="num_tramite" name="num_tramite" class="form-control u-form-control rounded-0 col-6" type="text" placeholder="Ingrese nro tramite" value="<?php echo $persona->num_tramite; ?>" >
      </div>
    </div>
    <!-- End Nro tramite Input -->

    <!-- CUIL Input -->
    <div class="form-group row g-mb-10">
      <label class="col-sm-2 col-form-label g-mb-10" for="legajo">CUIL (*)</label>
      <div class="col-sm-3">
        <input id="cuil" name="cuil" class="form-control u-form-control rounded-0" type="text" placeholder="XX-XXXXXXXXX-X" data-mask="99-999999999-9" value="<?php echo $persona->cuil; ?>">
      </div>

      <!-- PDF CUIL -->
        <label class="u-file-attach-v2 g-color-gray-dark-v5 mb-0">
          <input id="pdf_cuil" name="pdf_cuil" type="file">
          <i class="icon-cloud-upload g-font-size-16 g-pos-rel g-top-2 g-mr-5"></i>
          <span class="js-value">Anexar PDF del CUIL</span>
        </label>
    </div>
    <!-- End CUIL Input -->

    <!-- Fecha inicio actividad laboral -->
    <div class="form-group row g-mb-10">
      <label class="col-sm-2 col-form-label g-mb-10">Alta temprana </label>
      <div class="col-sm-3">
        <input id="fecha_inicio_actividad" name="fecha_inicio_actividad" class="form-control form-control-md " type="date" value="<?php echo date($persona->fecha_inicio_actividad); ?>" >
      </div>
      <!-- pdf alta temprana -->
      <label class="u-file-attach-v2 g-color-gray-dark-v5 mb-0">
        <input id="pdf_alta_temprana" name="pdf_alta_temprana" type="file">
        <i class="icon-cloud-upload g-font-size-16 g-pos-rel g-top-2 g-mr-5"></i>
        <span class="js-value">Anexar PDF alta temprana</span>
      </label>
    </div>
    <!-- End Fecha inicio actividad laboral -->

    <!-- Fecha nacimiento -->
    <div class="form-group row g-mb-10">
      <label class="col-sm-2 col-form-label g-mb-10">Fecha nacimiento (*)</label>
      <div class="col-sm-3">
        <input id="fecha_nacimiento" name="fecha_nacimiento" class="form-control form-control-md " type="date" value="<?php echo date($persona->fecha_nacimiento); ?>">
      </div>
    </div>
    <!-- End Fecha nacimiento -->

    <!-- Email Input -->
      <div class="form-group row g-mb-10">
        <label class="col-sm-2 col-form-label g-mb-10" for="email">Email</label>
        <div class="col-sm-9">
          <input id="email" name="email" class="form-control u-form-control rounded-0" type="text" placeholder="Ingrese email" value="<?php echo $persona->email ?>">
        </div>
      </div>
    <!-- End Email Input -->

    <!-- Nacionalidad Input -->
    <div class="form-group row g-mb-10">
      <label class="col-sm-2 col-form-label g-mb-10" for="legajo">Nacionalidad (*)</label>
      <div class="col-sm-9">
        <input id="nacionalidad" name="nacionalidad" class="form-control u-form-control rounded-0" type="text" placeholder="Ingrese nacionalidad" value="<?php echo $persona->nacionalidad; ?>" >
      </div>
    </div>
    <!-- End Nacionalidad Input -->
    <!-- Domicilio Input -->
    <div class="form-group row g-mb-10">
      <label class="col-sm-2 col-form-label g-mb-10" for="legajo">Domicilio (*)</label>
      <div class="col-sm-9">
        <input id="domicilio" name="domicilio" class="form-control u-form-control rounded-0" type="text" placeholder="Ingrese domicilio" value="<?php echo $persona->domicilio; ?>" >
      </div>
    </div>
    <!-- End Domicilio Input -->
    <!-- Telefono Input -->
    <div class="form-group row g-mb-10">
      <label class="col-sm-2 col-form-label g-mb-10" for="legajo">Teléfono (*)</label>
      <div class="col-sm-9">
        <input id="telefono" name="telefono" class="form-control u-form-control rounded-0" type="text" placeholder="Ingrese teléfono" value="<?php echo $persona->telefono ?>" >
      </div>
    </div>
    <!-- End Telefono Input -->
    <button type="submit" id="btnSubmit" class="btn btn-primary g-mr-10 g-mb-15">Grabar cambios</button>
    <a href="<?php echo base_url('Personas');?>" class="btn btn-danger g-mr-10 g-mb-15">Cancelar</a>
  </form>
</section>

<script type="text/javascript">
  let form_person = $('#form_persona')

  $(document).on('ready', function () {

    $.validator.addMethod("alfanumOespacio", function(value, element) {
              return /^[ a-záéíóúüñ]*$/i.test(value);
          }, "Ingrese sólo letras.")

    form_person.validate({
                    rules: {
                      'nombre': {
                        required: true,
                        minlength: 4,
                        alfanumOespacio: true
                      },
                      'apellido': {
                        required: true,
                        minlength: 4,
                      },
                      'legajo': {
                        required: true,
                      },
                      'dni': {
                        required: true,
                      },
                      'pdf_dni': {
                       extension: "pdf|jpg|png"
                      },
                      'pdf_cuil': {
                       extension: "pdf|jpg|png"
                      },
                      'pdf_alta_temprana': {
                       extension: "pdf|jpg|png"
                      }
                    },
                    messages: {
                      'pdf_dni': {
                       extension: 'Tipo de archivo no permitido'
                      },
                      'pdf_cuil': {
                       extension: 'Tipo de archivo no permitido'
                      },
                      'pdf_alta_temp': {
                       extension: 'Tipo de archivo no permitido'
                      }
                    }
                  })

    $('#form_persona').on('submit', function(event){
      event.preventDefault()

      if (form_person.valid()) {
        fetch( `${base_url}/Personas/update`, {
        method: "POST",
        body: agruparDatos()
        })
        .then( response => response.json() )
        .then( response => {
          if (response.status === 'success') {
            window.location.href = base_url
          } else {
            noty_alert('error', 'No se ha podido registrar a la persona')
          }
        } )
        .catch( err => console.error( 'no', err ))
      }
    })

  function agruparDatos() {
    let person = new FormData()
    person.append('id', parseInt( document.getElementById('id').value ) )
    person.append( 'n_legajo', $('#legajo').val() )
    person.append( 'apellido', $('#apellido').val() )
    person.append( 'nombre', $('#nombre').val() )
    person.append( 'dni', $('#dni').val() )
    person.append( 'cuil', $('#cuil').val() )
    person.append( 'fecha_nacimiento', $('#fecha_nacimiento').val() )
    person.append( 'nacionalidad', $('#nacionalidad').val() )
    person.append( 'domicilio', $('#domicilio').val() )
    person.append( 'telefono', $('#telefono').val() )
    person.append( 'fecha_inicio_actividad', $('#fecha_inicio_actividad').val() )
    person.append( 'pdf_dni', $('#pdf_dni').prop('files')[0] )
    person.append( 'pdf_cuil', $('#pdf_cuil').prop('files')[0] )
    person.append( 'pdf_alta_temprana', $('#pdf_alta_temprana').prop('files')[0] )
    person.append( 'email', $('#email').val() )
    person.append( 'num_tramite', $('#num_tramite').val() )
    return person;
  }

  });
</script>
