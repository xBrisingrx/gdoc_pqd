<script>
	const url = "<?php echo base_url('assets/uploads/')?>"
  const tipo = document.getElementById('tipo_dato').value
  let tabla_perfiles, data_id
	let atributo_data_id = 0
	let save_method, tabla_renovaciones_atributo, tabla_atributos, form_renovacion_atributo, form_editar_renovacion
  let atributo_tipo_controller = (tipo == 1) ? "<?php echo base_url('Atributos_Personas') ?>" : "<?php echo base_url('Atributos_Vehiculos') ?>"

	function modal_cargar_atributo( id , cargado ){ 
    atributo_data_id = id
    if (rol_usuario == 1) {
      // reveer este campo, hago asignaciones redundantes
      $('#id_atributo_tipo').val( id )
      $('#vence_attr').val($('.vence_atributo_' + id ).text())
      $('#form_renovacion_atributo')[0].reset()
      $('#form_renovacion_atributo #nombre_attr').val( $('.nombre_atributo_' + id ).text() )
      // Si el atributo no tiene vencimiento deshabilitamos los campos de fecha
      if ( $('.vence_atributo_' + id ).text() === 'Si' ) {
        $('#fecha_renovacion_atributo').prop( "disabled", false )
        $('#fecha_vencimiento_atributo').prop( "disabled", false )
      } else {
        $('#fecha_renovacion_atributo').prop( "disabled", true )
        $('#fecha_vencimiento_atributo').prop( "disabled", true )
      }
    } else {
      $('#form_renovacion_atributo').hide()
    }
		tabla_renovaciones_atributo.ajax.url(`${base_url}Documentos/get_renovaciones_atributo/${tipo}/${id}`).load()
		tabla_renovaciones_atributo.ajax.reload(null,false)
    save_method = ( cargado) ? 'update' : 'create'
		$('#modal_add_attr').modal('show')
	}

  function modal_asignar_atributo_personalizado() {
    if (data_id != undefined) {
      $('#select_asignar_atributo').val(0).trigger('change');
      $('#form_asignar_atributo_personalizado #data_id').val(data_id)
      $('#modal_asignar_atributo_personalizado').modal('show')
    } else {
      noty_alert('info', 'Por favor vuelve a seleccionar')
    }
  }

  function modal_eliminar_atributo(id){
    $('#form_eliminar_atributo #atributo_data_id').val(id)
    $('#modal_eliminar_atributo').modal('show')
  }

	function save() {
		let nombre_label_attr = $('#nombre_attr').val()
    let cargar_atributo = `${base_url}Documentos/cargar_atributo/${tipo}`
    let test_data = constructor_form()
    fetch(cargar_atributo, {
      method: "POST",
      body: constructor_form()
    })
    .then( response => response.json() )
    .then( response => {
      if (response.status === 'success') {
        noty_alert( response.status , response.msg )
        $('#form_renovacion_atributo')[0].reset()
        $('#nombre_attr').val( nombre_label_attr )
        tabla_renovaciones_atributo.ajax.url(`${base_url}Documentos/get_renovaciones_atributo/${tipo}/${atributo_data_id}`)
        tabla_renovaciones_atributo.ajax.reload(null,false)
        tabla_atributos.ajax.reload(null,false)
      } else {
        noty_alert( 'error' , response.msg, 4000 )
      }
    } )
    .catch( console.log('catch') )
	}

  function constructor_form() {
    let form_data = new FormData()
    form_data.append('atributo_data_id', document.getElementById('id_atributo_tipo').value)
    form_data.append('vence', document.getElementById('vence_attr').value)
    form_data.append('fecha_renovacion', document.getElementById('fecha_renovacion_atributo').value)
    form_data.append('fecha_vencimiento', document.getElementById('fecha_vencimiento_atributo').value)

    // agregamos los archivos al formdata
    let files = document.getElementById('archivos_renovacion')
    let totalfiles = files.files.length;
    for (let i = 0; i < totalfiles; i++) {
      form_data.append("files[]", files.files[i])
    }
    return form_data
  }

  function constructor_form_update() {
    let form_data = new FormData()
    form_data.append('id', document.getElementById('renovacion_id').value)
    form_data.append('vence', document.getElementById('vence_attr').value)
    form_data.append('fecha_renovacion', document.getElementById('fecha_renovacion_atributo_edit').value)
    form_data.append('fecha_vencimiento', document.getElementById('fecha_vencimiento_atributo_edit').value)
    // agregamos los archivos al formdata
    let files = document.getElementById('archivo_renovacion_edit')
    let totalfiles = files.files.length;
    for (let i = 0; i < totalfiles; i++) {
      form_data.append("files[]", files.files[i])
    }
    return form_data
  }

  function editar_renovacion( renovacion ) {
    save_method = 'update'
    let data = renovacion.split(",")
    clean_form('form_editar_renovacion')
    $('#form_editar_renovacion #renovacion_id').val(data[0])
    $('#form_editar_renovacion #fecha_renovacion_atributo_edit').val(data[1])
    $('#form_editar_renovacion #fecha_vencimiento_atributo_edit').val(data[2])
    $('#form_editar_renovacion #fecha_renovacion_atributo_edit_anterior').val(data[1])
    $('#form_editar_renovacion #fecha_vencimiento_atributo_edit_anterior').val(data[2])
    $('#modal_editar_renovacion').modal('show')
  }

  function update() {
    fetch(`${base_url}Documentos/actualizar_renovacion_atributo/${tipo}`, {
      method: 'POST',
      body: constructor_form_update()
    })
    .then(response => response.json() )
    .then(response => {
      if (response.status === 'success') {
        tabla_renovaciones_atributo.ajax.reload(null,false)
        reload_tabla(tabla_atributos, `${base_url}Documentos/get_atributos/${tipo}/${data_id}`)
        $('#modal_editar_renovacion').modal('hide')
      }
      noty_alert( response.status , response.msg )
      
    })
    .catch(error => console.log('error: ' + error))
  }

  function modal_eliminar_renovacion(id) {
    $('#form_eliminar_renovacion #renovacion_id').val(id)
    $('#modal_eliminar_renovacion').modal('show')
  }

  document.getElementById('form_asignar_atributo_personalizado').addEventListener('submit', ( e )=> {
    e.preventDefault()
    let atributo_seleccionado = document.querySelector('#form_asignar_atributo_personalizado #select_asignar_atributo')
    atributo_seleccionado = atributo_seleccionado.options[atributo_seleccionado.selectedIndex].value

    if (atributo_seleccionado != 0) {
      $.ajax({
        url: `${atributo_tipo_controller}/create`,
        type: 'POST',
        dataType: 'JSON',
        data: {
          data_id: data_id,
          atributo_id: atributo_seleccionado
        },
        success: function( response ) {
          if (response.status === 'success') {
            reload_tabla(tabla_atributos, `${base_url}Documentos/get_atributos/${tipo}/${data_id}`)
            $('#modal_asignar_atributo_personalizado').modal('hide')
          }
          noty_alert(response.status, response.msg)
        },
        error: function() {
          noty_alert( 'error' , 'No se pudo asignar el atributo' )
        }
      })
    } else {
      noty_alert('error', 'Debe seleccionar un atributo')
    }
  })

  document.getElementById('form_eliminar_atributo').addEventListener('submit', ( e )=> {
    e.preventDefault()
    $.ajax({
      url: `${atributo_tipo_controller}/disable`,
      type: 'POST',
      dataType: 'JSON',
      data: {
        atributo_data_id: document.querySelector('#form_eliminar_atributo #atributo_data_id').value
      },
      success: function( response ) {
        if (response.status === 'success') {
          reload_tabla(tabla_atributos, `${base_url}Documentos/get_atributos/${tipo}/${data_id}`)
          $('#modal_eliminar_atributo').modal('hide')
        }
        noty_alert(response.status, response.msg)
      },
      error: function() {
        noty_alert( 'error' , 'No se pudo asignar el atributo' )
      }
    })
  })

  /* Archivos */
  function modal_archivos( tipo, registro_id, tabla = null ) {
    let url_archivo
    clean_form('form_agregar_archivos')
    $('#galeria_archivos').html('')
    $('#modal_archivos #registro_id').val( registro_id )
    $.ajax({
      url: `${base_url}Documentos/get_archivos/${tipo}/${registro_id}/${tabla}`,
      type: 'GET',
      dataType: 'JSON',
      success: function( response ) {
        if (response.archivo !== '') {
          url_archivo = `${base_url}${response.archivo}`
          dibujar_archivo( 'galeria_archivos', '0', url_archivo)
        }
        $(response.archivos).each(function(i, element){
          url_archivo = "<?php echo base_url() ?>" + element.path
          dibujar_archivo( 'galeria_archivos', element.id, url_archivo)
        })
      },
      error: function( response ){
        console.log(response)
        $('#galeria_archivos').html('No se pudieron obtener los archivos')
      }
    })

    $('#modal_archivos').modal('show')
  }

  document.getElementById('form_agregar_archivos').addEventListener('submit', function(e) {
    e.preventDefault()
    let url_archivo
    let files = this.elements[1];
    let totalfiles = files.files.length;
    if (totalfiles > 0) {
      let form_data = new FormData()
      for (let i = 0; i < totalfiles; i++) {
        form_data.append("files[]", files.files[i])
      }
      form_data.append('tipo', tipo)
      form_data.append('registro_id', this.elements[0].value)
      fetch(base_url+'Documentos/agregar_archivos_a_renovacion',{
        method: "POST",
        body: form_data,
      })
      .then(response => response.json() )
      .then(response => {
        if (response.status === 'success') {
          let archivos_nuevos = response.archivos_nuevos
          clean_form('form_agregar_archivos')
          for (let i = archivos_nuevos.length - 1; i >= 0; i--) {
            url_archivo = "<?php echo base_url() ?>" + archivos_nuevos[i].path
            dibujar_archivo( 'galeria_archivos', archivos_nuevos[i].id, url_archivo)
          }
          noty_alert( 'success' , response.msg )
        } else {
          noty_alert( 'error' , response.msg )
        }
      })
      .catch(error => console.log('error: ' + error))
    } else {
      noty_alert('info', 'No ha seleccionado archivos')
    }
  })

  function modal_delete_archivo( archivo_id ) {
    $('#archivo_delete_id').val( archivo_id )
    $('#modal_delete_archivo').modal('show')
  }

  function destroy_archivo() {
    let this_file = $('#archivo_delete_id').val() // id archivo a eliminar
    $.ajax({
      url: base_url+'Documentos/eliminar_archivos_a_renovacion',
      type: 'POST',
      data: { id: this_file },
      dataType: 'JSON',
      success: function(response){
        if (response.status == 'success') {
          $('#archivo_'+ this_file ).hide('slow')
          $('#modal_delete_archivo').modal('hide')
        }
        noty_alert( response.status, response.msg )
      },
      error: function(){
        noty_alert('error', 'No se pudo eliminar la archivo')
      }
    })
  }

  function agrupar_archivos( form_data, file_element ){
    let totalfiles = file_element.files.length
    if (totalfiles > 0 ) {
      for (let i = 0; i < totalfiles; i++) {
        form_data.append("archivos[]", file_element.files[i]);
      }
    }
  }
  /* FIN Archivos */

  function reload_tabla(tabla, ajax_url){
    tabla.ajax.url(ajax_url).load()
    tabla.ajax.reload(null,false)
  }

  $(document).ready(function() {
	  tabla_perfiles =  $('#tabla_perfiles').DataTable({
                            ajax: `${base_url}Documentos/get_atributos/${tipo}`,
	                          language: {url: datatables_lang}
	                      })
	  tabla_atributos = $('#tabla_atributos').DataTable({
                            ajax: `${base_url}Documentos/get_atributos/${tipo}`,
	                          language: {url: datatables_lang},
							              columns: [
							              		{ data: 0 },
							              		{ data: 1 },
							              		{ data: 2 },
							              		{ data: 3 },
							              		{ data: 4 },
							              		{ data: 5 },
							              		{ data: 6 }
							              ],
                            order: [[ 0, "asc" ]],
							              createdRow: function( row, data, dataIndex ){
                              $('td', row).eq(0).addClass('nombre_atributo_' + data[8])
                              $('td', row).eq(1).addClass('categoria_atributo_' + data[8])
                              $('td', row).eq(2).addClass('vence_atributo_' + data[8])
							              	if (data[7] != 1 ) {
							              		// data[7] es el campo "cargado"
							              		$('td', row).addClass('table-info')
							              	} else {
                                console.log(data[2])
                                // El atributo esta cargado
                                if ( data[9] < 15 && data[2] == 'Si' ) {
                                  // coloremos en rojo
                                  $('td', row).addClass('table-danger')
                                } else if ( data[9] < 30 && data[2] == 'Si' ) {
                                  // coloreamos en naranja
                                  $('td', row).addClass('table-warning')
                                }
                              }
							              }
	                      })
	  tabla_renovaciones_atributo = $('#tabla_renovaciones_atributo').DataTable({
	  															language: {url: datatables_lang},
                                  "ordering": false
	  })

    form_renovacion_atributo = $('#form_renovacion_atributo').validate({
                            rules: {
                              'fecha_renovacion_atributo': { 
                                required: function( element ){
                                      return $('#vence_attr').val() === 'Si'
                                    }
                                },
                              'fecha_vencimiento_atributo': { 
                                  required: function( element ){
                                    return $('#vence_attr').val() === 'Si'
                                  }
                                },
                              'pdf_renovacion': { required: true }
                          }
                        })

    form_editar_renovacion = $('#form_editar_renovacion').validate({
                        rules: {
                          'fecha_editar_renovacion_atributo': { 
                            required: function( element ){
                                  return $('#vence_renovacion_editar').val() === 'Si'
                                }
                            },
                          'fecha_editar_vencimiento_atributo': { 
                              required: function( element ){
                                return $('#vence_renovacion_editar').val() === 'Si'
                              }
                            }
                      }
                    })

    $( "#form_renovacion_atributo" ).submit(function( event ) {
      event.preventDefault()
      if (form_renovacion_atributo.valid()) {
        save()
      }
    })

    $("#form_editar_renovacion").submit(function( event ) {
      event.preventDefault()
      if ( ($('#fecha_renovacion_atributo_edit').val() != $('#fecha_renovacion_atributo_edit_anterior').val() )
          || ($('#fecha_vencimiento_atributo_edit').val() != $('#fecha_vencimiento_atributo_edit_anterior').val() )
          || ($('#archivo_renovacion_edit').val() != '') ) {
        if (form_editar_renovacion.valid()) {  update() }
      } else {
        // Si no hay cambios no hace falta guardar el registro
        $('#modal_editar_renovacion').modal('hide')
      }
    })

    $( "#form_eliminar_renovacion" ).submit(function( event ) {
      event.preventDefault()
      $.ajax({
        url: '<?php echo base_url('Documentos/destroy_renovacion') ?>',
        type: 'POST',
        dataType:'JSON',
        data: { id : $('#form_eliminar_renovacion #renovacion_id').val(), tipo : tipo },
        success: function(response) {
          if (response.status === 'success') {
            tabla_renovaciones_atributo.ajax.reload(null,false)
            tabla_atributos.ajax.reload(null,false)
            $('#modal_eliminar_renovacion').modal('hide')
          }
          noty_alert(response.status, response.msg)
        },
        error: function(error) {
          noty_alert('error', 'Ocurrio un error, no se pudo eliminar la renovacion')
        }
      })
    })

    $('#select_asignar_atributo').select2({theme: 'bootstrap4',width: '50%'})

	} )
</script>