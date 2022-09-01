<section class="container-fluid g-py-10">
	<h1>Atributos registrados en el sistema</h1>
	<?php if ($this->session->userdata('rol') == 1): ?>
		<button class="btn btn-primary mb-2" onclick="create_attribute()"> Nuevo atributo </button>
	<?php endif ?>

	<!-- Hover Rows -->
	<div class="card g-brd-darkpurple rounded-0 g-mb-30">
	  <h3 class="card-header g-bg-darkpurple g-brd-transparent g-color-white g-font-size-16 rounded-0 mb-2">
	    <i class="fa fa-gear g-mr-5"></i>
	    Listado de atributos de <?php echo $nombre_atributo ?> registrados
	  </h3>
	  <div class="px-2 pb-2">
	  	<table id="tabla_atributos" class="table table-hover dt-responsive w-100 u-table--v1 mb-0">
	      <thead>
	        <tr>
	          <th>Nombre atributo</th>
	          <th>Descripción</th>
	          <th>Dato obligatorio</th>
	          <th>Categoría</th>
	          <th>Tiene venc.</th>
	          <th>Tipo venc.</th>
	          <th>Periodo venc.</th>
	          <th>Permite modif. prox. venc.</th>
	          <th>Permite anexo</th>
	          <th>Observaciones generales</th>
	          <th>Metodología de renovacion</th>
	          <th>Fecha inicio vigencia</th>
	          <th>Presenta resumen mensual</th>
	          <th>Fecha baja</th>
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
</section>


<!-- Modal con form para crear/editar atributo -->

<div class="modal fade" id="modal_form_atributo" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

      	<?php echo form_open('', array( 'id'=>'form_atributos', 'class'=>'g-brd-around g-brd-gray-light-v4 g-pa-30 g-mb-30' )) ?>
	        <!-- Tipo de atributo -->
	        	<input type="hidden" id="tipo" name="tipo" value="<?php echo $tipo_atributo;?>">

	        <!-- ID atributo -->
	        	<input type="hidden" name="atributo_id" id="atributo_id" value="">

				  <!-- Text Input -->
				  <div class="form-group row g-mb-5">
				    <label class="col-sm-2 col-form-label g-mb-5" for="nombre">Nombre del atributo(*)</label>
				    <div class="col-sm-9">
					    <input id="nombre" name="nombre" class="form-control form-control-md rounded-0" placeholder="Ingrese nombre de atributo" type="text" required>
					    <small class="form-control-feedback"></small>
				    </div>
				  </div>
				  <!-- End Text Input -->

				  <!-- Textarea Expandable -->
				  <div class="form-group row g-mb-5">
				    <label class="col-sm-2 col-form-label g-mb-5" for="descripcion">Descripción(*)</label>
				    <div class="col-sm-9">
							<textarea id="descripcion" name="descripcion" class="form-control form-control-md u-textarea-expandable rounded-0" rows="3" placeholder="Ingrese descripción del atributo"></textarea>
				    </div>
				  </div>
				  <!-- End Textarea Expandable -->

				  <!-- Checkbox dato obligatorio  -->
				  <div class="form-group g-mb-5">
				  	<label class="form-check-inline u-check g-pl-25">
					  	Dato obligatorio
					    <input id="dato_obligatorio" name="dato_obligatorio" class="g-hidden-xs-up g-pos-abs g-top-0 g-left-0" type="checkbox" value="">
					    <div class="u-check-icon-checkbox-v6 g-absolute-centered--y g-left-0">
					      <i class="fa" data-check-icon=""></i>
					    </div>
					  </label>
				  </div>
				  <!-- End Checkbox dato obligatorio -->

				  <!-- Select categoria -->
				  <div class="form-group row g-mb-5">
				    <label class="mr-sm-3 mb-3 mb-lg-0 col-sm-2" for="categoria">Categoria(*)</label>
				    <select class="custom-select mb-3 col-sm-4" id="categoria" required>
				      <option value="0" disabled selected>Seleccione categoria</option>
				      <option value="1">General</option>
				      <option value="2">Liquidación de haberes</option>
				      <option value="3">Otros</option>
				      <option value="4">Seguros</option>
				      <option value="5">Sindicatos</option>
				    </select>
				  </div>
				  <!-- End select categoria -->

				  <!-- Checkbox dato tiene vencimiento  -->
				  <div class="form-group g-mb-5">
					  <label class="form-check-inline u-check g-pl-25">
					  	Tiene vencimiento
					    <input id="tiene_vencimiento" name="tiene_vencimiento" class="g-hidden-xs-up g-pos-abs g-top-0 g-left-0" type="checkbox" value="">
					    <div class="u-check-icon-checkbox-v6 g-absolute-centered--y g-left-0">
					      <i class="fa" data-check-icon=""></i>
					    </div>
					  </label>
				  </div>
				  <!-- End Checkbox dato tiene vencimiento -->

				  <!-- Select tipo vencimiento -->
				  <div id="select_tipo_vencimiento" class="form-group row g-mb-5" style="display: none">
				    <label class="mr-sm-3 mb-3 mb-lg-0 col-sm-2" for="tipo_vencimiento">Tipo de vencimiento(*)</label>
				    <select class="custom-select mb-3 col-sm-4" id="tipo_vencimiento">
				      <option value="0" disabled selected>Seleccione tipo vencimiento</option>
				      <option value="7">Semanal</option>
				      <option value="15">Quincenal</option>
				      <option value="30">Mensual</option>
				      <option value="180">Semestral</option>
				      <option value="365">Anual</option>
				      <option value="1">Otro</option>
				    </select>
				  </div>
				  <!-- End select tipo vencimiento -->

				  <!-- Numb Input periodo vencimiento -->
				  <div id="input_periodo_vencimiento" class="form-group row g-mb-5" style="display: none">
				    <label class="g-mb-10 col-sm-2" for="periodo_vencimiento">Período vencimiento (días) (*)</label>
				    <div class="col-sm-6">
					    <input id="periodo_vencimiento" name="periodo_vencimiento" class="form-control rounded-0" type="number" required>
					    <small class="form-control-feedback"></small>
				    </div>
				  </div>
				  <!-- End Numb Input periodo vencimiento -->

				  <!-- Checkbox permite modificar proximo vencimiento  -->
				  <div id="check_permite_edit_prox_venc" class="form-group g-mb-10" style="display: none">
					  <label class="form-check-inline u-check g-pl-25">
					  	Permite modificar proximo vencimiento
					    <input id="permite_edit_prox_vencimiento" name="permite_edit_prox_vencimiento" class="g-hidden-xs-up g-pos-abs g-top-0 g-left-0" type="checkbox" value="">
					    <div class="u-check-icon-checkbox-v6 g-absolute-centered--y g-left-0">
					      <i class="fa" data-check-icon=""></i>
					    </div>
					  </label>
				  </div>
				  <!-- End Checkbox permite modificar proximo vencimiento -->

				  <!-- Checkbox dato permite anexar pdf  -->
				  <div class="form-group g-mb-10">
					  <label class="form-check-inline u-check g-pl-25">
					  	Permite anexar PDF
					    <input id="permite_pdf" name="permite_pdf" class="g-hidden-xs-up g-pos-abs g-top-0 g-left-0" type="checkbox" value="">
					    <div class="u-check-icon-checkbox-v6 g-absolute-centered--y g-left-0">
					      <i class="fa" data-check-icon=""></i>
					    </div>
					  </label>
				  </div>
				  <!-- End Checkbox dato permite anexar pdf -->

				  <!-- Textarea Expandable observaciones grales -->
				  <div class="form-group row g-mb-10">
				    <label class="g-mb-10 col-sm-2" for="observaciones">Observaciones generales</label>
				    <div class="col-sm-9">
				    	<textarea id="observaciones" name="observaciones" class="form-control form-control-md u-textarea-expandable rounded-0" rows="3" placeholder="Ingrese información general del atributo"></textarea>
				    </div>
				  </div>
				  <!-- End Textarea Expandable observaciones grales -->

				  <!-- Textarea Expandable metodologia renovacion -->
				  <div class="form-group row g-mb-10">
				    <label class="g-mb-10 col-sm-2" for="metodologia_renovacion">Metodología de renovación</label>
				   <div class="col-sm-9">
				   	 <textarea id="metodologia_renovacion" name="metodologia_renovacion" class="form-control form-control-md u-textarea-expandable rounded-0" rows="3" placeholder="Ingrese metodología de renovación del atributo"></textarea>
				   </div>
				  </div>
				  <!-- End Textarea Expandable metodologia renovacion -->

				  <!-- Select Single Date -->
				  <div class="form-group row">
				    <label class="col-sm-2">Fecha inicio vigencia(*)</label>
				    <div class="col-9 input-group g-brd-primary--focus">
				      <input id="fecha_inicio_vigencia" name="fecha_inicio_vigencia" class="form-control form-control-md  rounded-0 col-sm-6" type="date" required>
				    </div>
				  </div>
				  <!-- End Select Single Date -->

				  <!-- Checkbox dato permite anexar pdf  -->
				  <div class="form-group g-mb-20">
					  <label class="form-check-inline u-check g-pl-25">
					  	Presenta en resumen mensual
					    <input id="presenta_resumen_mensual" name="presenta_resumen_mensual" class="g-hidden-xs-up g-pos-abs g-top-0 g-left-0" type="checkbox" value="">
					    <div class="u-check-icon-checkbox-v6 g-absolute-centered--y g-left-0">
					      <i class="fa" data-check-icon=""></i>
					    </div>
					  </label>
				  </div>
				  <!-- End Checkbox dato permite anexar pdf -->
				<button id="btnSave" type="submit" class="btn btn-primary" ></button>
        <button type="button" data-dismiss="modal" class="btn u-btn-red"> Cerrar </button>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- Fin modal form -->

<!-- Modal para eliminar atributo -->
<div class="modal fade" id="modal_delete_attribute" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">¿ Esta seguro de eliminar este atributo ?</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	<input type="hidden" id="id_attribute_delete" name="id_attribute_delete" value="">
       	<p id="name_attribute_delete"><strong>Nombre: </strong> </p>
       	<br>
       	<p id="description_attribute_delete"><strong>Detalle: </strong></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-md u-btn-red g-mr-10" onclick="destroy_attribute()">Eliminar</button>
        <button type="button" data-dismiss="modal" class="btn btn-md u-btn-indigo g-mr-10"> Cerrar </button>
      </div>
    </div>
  </div>
</div>
<!-- Fin modal eliminar atributo -->

<script type="text/javascript">
	let save_method
	let table_atributos =	$('#tabla_atributos')
	let form_atributos = $('#form_atributos')
	const tipo_atributo = "<?php echo $tipo_atributo ?>"

	$('#tiene_vencimiento').on('click', function(){
		if ($(this).is(':checked')) {
			$('#select_tipo_vencimiento').show();
			$('#check_permite_edit_prox_venc').show();
		} else {
			$('#select_tipo_vencimiento').hide();
			$('#check_permite_edit_prox_venc').hide();
		}
	});

	$('#tipo_vencimiento').on('change', function(){
		if ($(this).val() == '1') {
			$('#input_periodo_vencimiento').show();
		} else {
			$('#input_periodo_vencimiento').hide();
		}
	});

	function create_attribute() {
		disable_btn_save( false )
		save_method = 'create';
		clean_form('form_atributos')
		$('#observaciones').text('')
		$('#metodologia_renovacion').text('')
		$('#form_atributos #atributo_id').val('')
		$('#categoria option:first').prop('selected',true)
		$('#tipo_vencimiento option:first').prop('selected',true)
		$('#select_tipo_vencimiento').hide()
		$('#input_periodo_vencimiento').hide()
		$('#check_permite_edit_prox_venc').hide()
		$('#modal_form_atributo .modal-title').text('Alta de atributo');
		$('#modal_form_atributo').modal('show');
	}

	function edit_attribute(id) {
		disable_btn_save( false )
		save_method = 'update';
		$('#form_atributos')[0].reset()
		$('#categoria option:first').prop('selected',true)
		$('#tipo_vencimiento option:first').prop('selected',true)
		$('#select_tipo_vencimiento').hide()
		$('#input_periodo_vencimiento').hide()
		$('#check_permite_edit_prox_venc').hide()
		$('.form-control').removeClass('error')
		$('.error').empty()

		$.ajax({
			url: "<?php echo base_url('Atributos/edit/');?>" + id,
			type: "GET",
			dataType: "JSON",
			success: function(resp) {
				let data = resp[0]
				$('#atributo_id').val(data.id)
				$('[name=nombre]').val(data.nombre)
				$('[name=descripcion]').val(data.descripcion)
				$('[id=dato_obligatorio]').prop('checked', data.dato_obligatorio == 1)
				$('#categoria option').prop('selected', false)
				$('#categoria option').filter(function(){
					return this.text == data.categoria
				}).attr('selected', true)
				$('#tiene_vencimiento').prop('checked', data.tiene_vencimiento == 1)
				if (data.tiene_vencimiento == 1) {
					$('#check_permite_edit_prox_venc').show()
					$('#select_tipo_vencimiento').show()
						$('#tipo_vencimiento option').prop('selected', false)
						$('#tipo_vencimiento option').filter(function(){
							return this.text == data.tipo_vencimiento
						}).attr('selected', true)
						$('#permite_edit_prox_vencimiento').prop('checked', data.permite_modificar_proximo_vencimiento == 1)
				}
				if (data.tipo_vencimiento == 'Otro') {
					$('#input_periodo_vencimiento').show()
					$('#periodo_vencimiento').val(data.periodo_vencimiento)
				}
				$('#permite_pdf').prop('checked', data.permite_pdf == 1)
				$('#observaciones').text(data.observaciones)
				$('#metodologia_renovacion').text(data.metodologia_renovacion)
				$('[name=fecha_inicio_vigencia]').val(data.fecha_inicio_vigencia)

				$('#presenta_resumen_mensual').prop('checked', data.presenta_resumen_mensual == 1)

				$('#modal_form_atributo .modal-title').text('Modificar perfil');
				$('#modal_form_atributo #btnSave').text('Actualizar perfil');
				$('#modal_form_atributo').modal('show');
			},
			error: function(jqXHR, textStatus, errorThrown) {
					noty_alert( 'error' , 'Error obteniendo datos ' )
			}
		});
	}

	function agrupar_datos() {
		datos = {
			'id': $('#form_atributos #atributo_id').val(),
			'tipo': parseInt( $('#form_atributos #tipo').val() ),
			'nombre': $('#form_atributos #nombre').val(),
			'descripcion': $('#form_atributos #descripcion').val(),
			'categoria': $('#form_atributos #categoria option:selected').text(),
			'dato_obligatorio': $('#form_atributos #dato_obligatorio').is(':checked'),
			'tiene_vencimiento': $('#form_atributos #tiene_vencimiento').is(':checked'),
			'tipo_vencimiento': set_tipo_vencimiento(),
			'periodo_vencimiento': set_periodo_vencimiento(),
			'permite_pdf': $('#form_atributos #permite_pdf').is(':checked'),
			'observaciones': $('#form_atributos #observaciones').val(),
			'metodologia_renovacion': $('#form_atributos #metodologia_renovacion').val(),
			'fecha_inicio_vigencia': $('#form_atributos #fecha_inicio_vigencia').val(),
			'permite_edit_prox_vencimiento': $('#form_atributos #permite_edit_prox_vencimiento').is(':checked'),
			'presenta_resumen_mensual': $('#form_atributos #presenta_resumen_mensual').is(':checked'),
		}
		return datos
	}
	
	function save() {
		let url = "<?php echo base_url('Atributos/');?>" + save_method
		disable_btn_save( true )
    $.ajax({
    	url: url,
    	type: "POST",
    	data: agrupar_datos(),
    	dataType: 'JSON',
    	success: function(response) {
    		if (response.status === 'success') {
    			table_atributos.ajax.reload(null,false)
    			noty_alert( response.status, response.msg )
    			$('#modal_form_atributo').modal('hide')
    		} else if(response.status === 'existe'){
    			noty_alert( 'info', response.msg )
    		} else {
    			noty_alert( response.status, response.msg )
    		}
    		disable_btn_save( false )
  		},
    	error: function(jqXHR, textStatus, errorThrown){
    		noty_alert( 'error' , 'No se pudo generar el atributo ' )
    		disable_btn_save( false )
  		}
  	})
	}

	function modal_destroy_attribute(id) {
		$.ajax({
			url: "<?php echo base_url('Atributos/edit/');?>" + id,
			type: "GET",
			dataType: "JSON",
			success: function(data) {
				let resp = data[0]
				$('#modal_delete_attribute #id_attribute_delete').val(resp.id)
				$('#modal_delete_attribute #name_attribute_delete').html(`<b>Nombre: </b> ${resp.nombre}`)
				$('#modal_delete_attribute #description_attribute_delete').html(`<b>Detalle: </b> ${resp.descripcion}`)
				$('#modal_delete_attribute').modal('show')
			},
			error: function() {
				noty_alert( 'error' , 'Error obteniendo datos ' )
			}
		});
	}

	function destroy_attribute() {
		$.ajax({
			url: "<?php echo base_url('Atributos/destroy/');?>" + $('#id_attribute_delete').val(),
			type: "GET",
			success: function(response) {
				if (response.status === 'success') {
					table_atributos.ajax.reload(null,false)
					$('#modal_delete_attribute').modal('hide')
				}
				noty_alert( response.status, response.msg )
			},
			error: function(jqXHR, textStatus, errorThrown) {
				noty_alert( 'error' , 'Fallo el eliminar atributo ' )
			}
		});
	}

	function set_tipo_vencimiento() {
		let tiene_vencimiento = $('#form_atributos #tiene_vencimiento').is(':checked')
		if (tiene_vencimiento) {
			return $('#form_atributos #tipo_vencimiento option:selected').text()
		} else {
			return ' '
		}
	}

	function set_periodo_vencimiento(){
		let cant_dias = $('#form_atributos #tipo_vencimiento option:selected').val()
		if (cant_dias == 1) {
			return $('#form_atributos #periodo_vencimiento').val()
		} else {
			return cant_dias
		}
	}

	function disable_btn_save( disable ){
		if (disable) {
			$('#btnSave').text('guardando...') //change button text
		} else {
			$('#btnSave').text('Guardar') //change button text
		}
		$('#btnSave').attr('disabled',disable) //set button disable
	}

  $(document).on('ready', function () {
		$.validator.addMethod("alfanumOespacio", function(value, element) {
		        return /^[ a-záéíóúüñ]*$/i.test(value);
		    }, "Ingrese sólo letras.");

		form_atributos.validate({
										rules: {
											'nombre': { minlength: 3, 
												remote: {
	                        url: "<?php echo base_url('Atributos/existe');?>",
	                        type: "POST",
	                        data: {
	                          nombre: function() {
	                            return $('#nombre').val()
                            },
                        		tipo: function() {
                        			return tipo_atributo
                        		},
                        		atributo_id: function() {
                        			return $('#form_atributos #atributo_id').val()
                        		}
                          }
                        }
											},
											'descripcion': { minlength: 10 },
											'tipo_vencimiento': {
												required: function(){
													return $('#tiene_vencimiento').is(':checked')
												}
											},
											'periodo_vencimiento': {
												required: function(){
													return ( $('#tipo_vencimiento').val() == 1 )
												}
											}
										},
										messages: {
													'nombre': {
														remote: 'Este atributo ya se encuentra registrado'
													}
												}
									})

		$('#form_atributos').submit(function(e){
			e.preventDefault()
			e.stopImmediatePropagation()
			if (form_atributos.valid()) {
				save()
			}
		})

		table_atributos =	$('#tabla_atributos').DataTable( {
																							lengthChange: true,
																							ajax : '<?php echo base_url('Atributos/ajax_list_attributes/');?>' + tipo_atributo,
																							language: { url: "<?php echo base_url('assets/vendor/datatables/spanish.json'); ?>" }
																						})
  })
</script>