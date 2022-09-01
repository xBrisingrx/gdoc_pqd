<section class="container-fluid g-py-10">
	<h1>Usuarios registrados en el sistema</h1>

	<?php if ($this->session->userdata('rol') == 1) { ?>
		<button class="btn btn-primary mb-2" onclick="new_user()"> Nuevo usuario </button>
	<?php } ?>
	<!-- Hover Rows -->
	<div class="card g-brd-darkpurple rounded-0 g-mb-30">
	  <h3 class="card-header g-bg-darkpurple g-brd-transparent g-color-white g-font-size-16 rounded-0 mb-2">
	    <i class="fa fa-gear g-mr-5"></i>
	    Listado de usuarios registrados
	  </h3>

	  <div class="px-2  pb-2">
	  	<table id="tabla_usuarios" class="table table-hover dt-responsive w-100 u-table--v1 mb-0 px-2">
	      <thead>
	        <tr>
	          <th>Nombre</th>
	          <th>Correo electrónico</th>
	          <th>Nombre de usuario</th>
	          <th>Rol</th>
	          <th>Acciones</th>
	        </tr>
	      </thead>

	      <tbody>
	      	<!-- Completo con ajax -->

	      </tbody>
	    </table>
	  </div>
	</div>
	<!-- End Hover Rows -->
	<br><br>
</section>

<div class="modal fade" id="modal_new_user" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Alta de usuario</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	<?php echo form_open('', array( 'id' => 'form_new_user', 'class'=>'g-brd-around g-brd-gray-light-v4 g-pa-30 g-mb-30')) ?>
      		<input type="hidden" id="user_id" value="">

				  <!--Input nombre usuario -->
				  <div class="form-group row g-mb-5">
				    <label class="col-sm-2 col-form-label g-mb-5" for="nombre">Nombre(*)</label>
				    <div class="col-sm-9">
					    <input id="nombre" name="nombre" class="form-control form-control-md rounded-0" placeholder="Ingrese nombre de usuario" type="text" required>
					    <small class="form-control-feedback"></small>
				    </div>
				  </div>
				  <!-- Input nombre usuario -->

				  <!--Input email usuario -->
				  <div class="form-group row g-mb-5">
				    <label class="col-sm-2 col-form-label g-mb-5" for="email">Correo electrónico(*)</label>
				    <div class="col-sm-9">
					    <input id="email" name="email" class="form-control form-control-md rounded-0" placeholder="Ingrese correo de usuario" type="email" required>
					    <small class="form-control-feedback"></small>
				    </div>
				  </div>
				  <!-- Input email usuario -->


				  <!--Input nombre_usuario usuario -->
				  <div class="form-group row g-mb-5">
				    <label class="col-sm-2 col-form-label g-mb-5" for="nombre_usuario">Nombre usuario(*)</label>
				    <div class="col-sm-9">
					    <input id="nombre_usuario" name="nombre_usuario" class="form-control form-control-md rounded-0" placeholder="Ingrese nombre usuario de usuario" type="text" required>
					    <small class="form-control-feedback"></small>
				    </div>
				  </div>
				  <!-- Input nombre_usuario usuario -->

				  <!-- input rol -->
		  	  <div class="form-group row g-mb-5">
				    <label class="col-2 col-form-label g-mb-5">Rol</label>
				    <select id="rol" name="rol" class="form-control col-6">
				      <option value="1">Administrador</option>
				      <option value="2">Consulta</option>
				    </select>
				  </div>
				  <!-- end input rol -->
				  <!--Input contrasenia usuario -->
				  <div class="form-group row g-mb-5">
				    <label class="col-sm-2 col-form-label g-mb-5" for="password">Contraseña(*)</label>
				    <div class="col-sm-9">
					    <input id="password" name="password" class="form-control form-control-md rounded-0" placeholder="Ingrese contraseña de usuario" type="password">
					    <small class="form-control-feedback"></small>
				    </div>
				  </div>
				  <!-- Input contrasenia usuario -->

				  <!--Input repetir contrasenia usuario -->
				  <div class="form-group row g-mb-5">
				    <label class="col-sm-2 col-form-label g-mb-5" for="passconf">Repetir Contraseña(*)</label>
				    <div class="col-sm-9">
					    <input id="passconf" name="passconf" class="form-control form-control-md rounded-0" placeholder="Repita la contraseña" type="password">
					    <small class="form-control-feedback"></small>
				    </div>
				  </div>
				  <!-- Input repetir contrasenia usuario -->

					<button type="submit" class="btn btn-primary">Generar usuario</button>
        	<button type="button" class="btn u-btn-red" data-dismiss="modal">Cerrar</button>
      	</form>
      </div>
    </div>
  </div>
</div>




<div class="modal fade" id="modal_destroy_user" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">¿ Esta seguro de eliminar este usuario ?</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	<input type="hidden" id="id_user_delete" name="id_user_delete" value="">
       	<p id="name_user_delete"> </p>
       	<br>
       	<p id="email_user_delete"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn u-btn-red" onclick="destroy_user()">Eliminar</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>


<script type="text/javascript">
	var save_method
	let table_users
	var url
	var form_user = $('#form_new_user')
	let csrf

	


	$('#form_new_user').submit(function(e){
		e.preventDefault()
		if ( form_user.valid() ) {
			save()
		}
	})

	function new_user() {
		save_method = 'create'
		$('#form_new_user')[0].reset()
		$('.form-control').removeClass('error');
		$('.error').empty();
		$(`#rol option[value=2]`).attr('selected', false)
		$(`#rol option[value=1]`).attr('selected', 'selected')
		document.getElementById("password").required = true
		document.getElementById("passconf").required = true
		$('#modal_new_user').modal('show')
	}

	function edit_user( id ) {
		save_method = 'update';
		$('#form_new_user')[0].reset()
		$('.form-control').removeClass('error')
		$('.error').empty()
		document.getElementById("password").required = false
		document.getElementById("passconf").required = false

		$.ajax({
			url: "<?php echo base_url('Usuarios/edit/');?>" + id,
			type: "GET",
			dataType: "JSON",
			success: function(response) {
				if (response.status === 'success') {
					let data = response.usuario
					$('#user_id').val(data.id)
					$('[name=nombre]').val(data.nombre)
					$('#email').val(data.email)
					if (data.rol == 1) {
						$(`#rol option[value=1]`).attr('selected', 'selected'); 
						$(`#rol option[value=2]`).attr('selected', false); 
					} else {
						$(`#rol option[value=1]`).attr('selected', false); 
						$(`#rol option[value=2]`).attr('selected', 'selected'); 
					}
					$('#nombre_usuario').val(data.nombre_usuario)
					// $('#password').val(data.password)
					// $('#passconf').val(data.password)
					$('#modal_new_user').modal('show');
				} else {
					noty_alert( response.status, response.msg )
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				noty_alert( 'error', 'No se pudieron obtener los datos del usuarios' )
			}
		});

	}


	function save() {
		$.ajax({
			url: '<?php echo base_url("Usuarios/")?>' + save_method,
			type: 'POST',
			data: agrupar_datos(),
			dataType: 'JSON',
		})
		.success(function(response) {
			if (response.status === 'success') {
				table_users.ajax.reload(null,false)
				$('#modal_new_user').modal('hide')
			}
			noty_alert( response.status, response.msg )
		})
		.error( function() {
			noty_alert( 'error', 'Error en el servidor: no se pudo registrar al usuario' )
		})
		.fail(function() {
			noty_alert( 'error', 'Error en el servidor: no se pudo registrar al usuario' )
			console.log("FAIL");
		})
	}

	function agrupar_datos() {
		datos = {
			'id' : $('#user_id').val(),
			'nombre' : $('#nombre').val(),
			'email' : $('#email').val(),
			'nombre_usuario' : $('#nombre_usuario').val(),
			'password' : $('#password').val(),
			'passconf' : $('#passconf').val(),
			'rol' : $('#rol').val()
		}
		return datos
	}


// Llamo al modal de advertencia para eliminar el usuario
	function delete_user( id ) {
		$('#modal_destroy_user #id_user_delete').val('')
		$('#modal_destroy_user #name_user_delete').html('')
		$('#modal_destroy_user #email_user_delete').html('')
		$.ajax({
			url: "<?php echo base_url('Usuarios/edit/');?>" + id,
			type: "GET",
			dataType: "JSON",
			success: function(response) {
				if (response.status === 'success') {
					let data = response.usuario
					$('#modal_destroy_user #id_user_delete').val(data.id)
					$('#modal_destroy_user #name_user_delete').append(`<b>Nombre:</b> ${data.nombre_usuario}`)
					$('#modal_destroy_user #email_user_delete').append(`<b>Correo:</b> ${data.email}`)
					$('#modal_destroy_user').modal('show')
				} else {
					noty_alert(response.status, response.msg)
				}
			},
			error: function() {
				noty_alert('error', 'No se pudieron obtener los datos del usuario')
			}
		})
	}
	// Elimino el usuario
	function destroy_user() {
		let id_user = $('#id_user_delete').val();
		$.ajax({
			url: "<?php echo base_url('Usuarios/destroy/');?>" + id_user,
			type: "POST",
			dataType: "JSON",
			success: function(response) {
				if (response.status === 'success') {
					table_users.ajax.reload(null,false);
					$('#modal_destroy_user').modal('hide');
				}
				noty_alert(response.status, response.msg)
			},
			error: function(jqXHR, textStatus, errorThrown) {
				noty_alert('error', 'Error: no se pudo eliminar el usuario')
			}
		});
	}

  $(document).on('ready', function () {


	form_user.validate({
									rules: {
										nombre: { required: true, alfanumOespacio: true,
										 						minlength: 3 },
										email: { email: true },
										nombre_usuario: {lettersonly: true},
										password: {minlength: 3},
										passconf: {equalTo: "#password"}
										},
									messages: {
										passconf: {
											equalTo: 'Las contraseñas deben ser iguales.'
										}
									}
									})

		$.validator.addMethod("alfanumOespacio", function(value, element) {
		        return /^[ a-záéíóúüñ]*$/i.test(value)
		    }, "Ingrese sólo letras.")

		table_users = $('#tabla_usuarios').DataTable({
																	ajax: '<?php echo base_url("Usuarios/ajax_list");?>',
																	language: {
												                url: "<?php echo base_url('assets/vendor/datatables/spanish.json');?>"
												              }
		})
  })
</script>
