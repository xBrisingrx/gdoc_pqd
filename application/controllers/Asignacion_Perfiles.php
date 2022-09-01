<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Asignacion_Perfiles extends CI_Controller {
	/*
		Los perfiles se pueden asignar y dar de baja a gusto
		Para guardar el registro de estos cambios no se borra el perfil cuando se da de baja, solo se lo deja al registro desactivado
		Solo pueden tener el perfil asignado en 1 solo registro activo
	*/
	function __construct() {
	  parent::__construct();
	  $this->load->model(array(
	  	'Perfil_model',
	  	'Perfiles_Atributos_model',
	  	'Persona_model',
	  	'Perfiles_Personas_model',
	  	'Vehiculo_model',
	  	'Perfiles_Vehiculos_model'
	  ));
	  date_default_timezone_set('America/Argentina/Buenos_Aires');
	  if ( empty( $this->session->nombre_usuario ) ) {
	  	redirect('Login');
	  }
	}

	function administrar($tipo_perfil = NULL){
		if ($tipo_perfil != NULL) {
			$title['title'] = 'Asignacion de perfiles';
			$data['nombre_tipo_perfil'] = ($tipo_perfil == 1) ? 'Personal' : 'Vechículos';

			$data['perfiles'] = $this->Perfil_model->get('tipo',$tipo_perfil);
			$data['tipo_perfil'] = $tipo_perfil;
			// A quien asigno el perfil y encabezado de la tabla
			if ($tipo_perfil == 1) {
				// Personas
				$data['asigno'] = $this->Persona_model->get();
				$data['columnas_tabla'] = array('Apellido', 'Nombre', 'DNI', 'CUIL', 'Perfil',
																				'Fecha inicio vigencia', 'Fecha baja', 'Acciones');
			} else {
				// Vehiculos
				$data['asigno'] = $this->Vehiculo_model->get();
				$data['columnas_tabla'] = array('Interno', 'Dominio', 'Marca', 'Año', 'Modelo', 'Perfil',
																				'Fecha inicio vigencia', 'Fecha baja', 'Acciones');
			}
			$this->load->view('layout/header',$title);
			$this->load->view('layout/nav');
			$this->load->view('sistema/asignacion_perfiles/index',$data);
			$this->load->view('layout/footer');
		} else {
			$this->load->view('layout/header');
			$this->load->view('layout/nav');
			$this->load->view('layout/404');
			$this->load->view('layout/footer');
		}
	}

	function list($tipo_perfil) {
		$data = array();
		if ($tipo_perfil == 1) {
			$perfiles_asignados = $this->Perfiles_Personas_model->get();
			foreach ($perfiles_asignados as $p) {
				$row = array();
				$row[] = $p->apellido_persona;
				$row[] = $p->nombre_persona;
				$row[] = $p->dni;
				$row[] = $p->cuil;
				$row[] = $p->nombre_perfil;
				$row[] = date('d-m-Y', strtotime($p->fecha_inicio_vigencia));
				$row[] = ($p->activo) ? ' ' : date('d-m-Y', strtotime($p->updated_at));
				if ($this->session->userdata('rol') == 1 ) {
					$row[] = '<button class="btn btn-sm u-btn-primary mr-2 " title="Editar" onclick="edit('."'".$p->id."'".', 1 )" ><i class="fa fa-edit"></i></button> <button class="btn btn-sm u-btn-red " title="Eliminar" onclick="modal_destroy('."'".$p->id."'".', 1 )" ><i class="fa fa-trash-o"></i></button>';
				} else {
					$row[] = '';
				}
				
				$data[] = $row;
			} // end foreach
		} else {
			$perfiles_asignados = $this->Perfiles_Vehiculos_model->get();
			foreach ($perfiles_asignados as $p) {
				$row = array();
				$row[] = $p->interno;
				$row[] = $p->dominio;
				$row[] = $p->anio;
				$row[] = $p->marca;
				$row[] = $p->modelo;
				$row[] = $p->nombre_perfil;
				$row[] = date('d-m-Y', strtotime($p->fecha_inicio_vigencia));
				$row[] = ($p->activo) ? ' ' : date('d-m-Y', strtotime($p->updated_at));
				if ($this->session->userdata('rol') == 1 ) {
					$row[] = '<button class=" btn btn-sm u-btn-primary mr-2" title="Editar"  onclick="edit('."'".$p->id."'".', 2 )"><i class="fa fa-edit"></i></button> <button class="btn btn-sm u-btn-red" title="Eliminar" onclick="modal_destroy('."'".$p->id."'".', 2 )" ><i class="fa fa-trash-o"></i></button>';
				} else {
					$row[] = '';
				}
				
				$data[] = $row;
			} // end foreach
		} // end else

		$output = array("data" => $data);
		echo json_encode($output);
	}

	function create() {
		$this->form_validation->set_rules('asign_id', 'asignacion', 'required');
		$this->form_validation->set_rules('profile_id', 'perfil', 'required');
		$this->form_validation->set_rules('asign_type', 'tipo', 'required');
		$this->form_validation->set_rules('fecha_inicio_vigencia', 'Fecha inicio vigencia', 'required|callback_valid_date');
		if ( $this->form_validation->run() == FALSE ) {
			echo json_encode( validation_errors() );
		} else {
			$asignacion_id = $this->input->post('asign_id');
			$perfil_id = $this->input->post('profile_id');
			$tipo_perfil = $this->input->post('asign_type');

			$col_asignacion_id = ( $tipo_perfil == 1 ) ? 'persona_id' : 'vehiculo_id';
			$perfil_tipo_model = ( $tipo_perfil == 1 ) ? 'Perfiles_Personas_model' : 'Perfiles_Vehiculos_model';
			$atributos_tipo_model = ( $tipo_perfil == 1 ) ? 'Atributos_Personas_model' : 'Atributos_Vehiculos_model';

			if ( !$this->existe_activo( $tipo_perfil, $asignacion_id, $perfil_id ) ) {
				$atributos = $this->Perfiles_Atributos_model->get('perfil_id', $this->input->post('profile_id'));

				$data = array(
					$col_asignacion_id => $asignacion_id,
					'perfil_id' => $perfil_id,
					'fecha_inicio_vigencia' => $this->input->post('fecha_inicio_vigencia'),
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s'),
					'user_created_id' => $this->session->userdata('id'),
					'user_last_updated_id' => $this->session->userdata('id'),
				);
				$data = $this->security->xss_clean($data);
				// Asignacion del perfil y atributos correspondientes
				if ($this->$perfil_tipo_model->insert_entry($data) && $this->$atributos_tipo_model->insert_entry($atributos, $asignacion_id)) {
					echo json_encode( array('status' => 'success', 'msg' => 'Perfil asignado con éxito') );
				} else {
					echo json_encode( array('status' => 'error', 'msg' => 'No se pudo asignar el perfil') );
				}
			} else {
				echo json_encode( array('status' => 'info', 'msg' => 'El perfil ya se encuentra asignado') );
			}
		}
	}
	
	function edit($id, $type) {
		$table = ($type == 1) ? 'perfiles_personas' : 'perfiles_vehiculos' ;
		$data = $this->DButil->get_for_id($table, $id);
		echo json_encode($data);
	}

	function update() {
		$this->form_validation->set_rules('id', 'ID', 'required');
		$this->form_validation->set_rules('profile_id', 'perfil', 'required');
		$this->form_validation->set_rules('asign_id', 'asignacion', 'required');
		$this->form_validation->set_rules('asign_type', 'tipo', 'required');
		$this->form_validation->set_rules('fecha_inicio_vigencia', 'Fecha inicio vigencia', 'required|callback_valid_date');
		if ( $this->form_validation->run() == FALSE ) {
			echo json_encode( validation_errors() );
		} else {
			$id = $this->input->post('id');
			$tipo_perfil = $this->input->post('asign_type');
			$asignacion_id = ( $tipo_perfil == 1) ? 'persona_id' : 'vehiculo_id';
			$tabla = ( $tipo_perfil == 1) ? 'perfiles_personas' : 'perfiles_vehiculos';
			$entry_to_update = $this->DButil->get_for_id($tabla, $id);
			$perfil_id = $this->input->post('profile_id');
			$perfil_tipo_model = ( $tipo_perfil == 1 ) ? 'Perfiles_Personas_model' : 'Perfiles_Vehiculos_model';

			$data = array(
				$asignacion_id => $entry_to_update->$asignacion_id,
				'perfil_id' => $perfil_id,
				'fecha_inicio_vigencia' => $this->input->post('fecha_inicio_vigencia'),
				'updated_at' => date('Y-m-d H:i:s'),
				'user_last_updated_id' => $this->session->userdata('id')
			);
			$data = $this->security->xss_clean($data);
			/* Chequeo si es la primera vez que se le asigna el perfil o si lo tiene pero no se encuentra activo */
			if ( !$this->existe_activo( $tipo_perfil, $entry_to_update->$asignacion_id, $perfil_id ) ) {
				if ( $this->$perfil_tipo_model->update_entry($id, $data) ) {
					echo json_encode( array('status' => 'success', 'msg' => 'Perfil asignado con éxito') );
				} else {
					echo json_encode( array('status' => 'success', 'msg' => 'Perfil asignado con éxito') );
				}
			} elseif( $perfil_id == $entry_to_update->perfil_id ) {
				// la asociacion existe y es activa, si el perfil no cambia significa q solo se cambia la fecha de inicio de vigencia
				if ($this->DButil->update_entry($tabla, $id, $data)) {
					echo json_encode( array('status' => 'success', 'msg' => 'Datos actualizados') );
				} else {
					echo json_encode( array('status' => 'error', 'msg' => 'No se pudieron actualizar los datos') );
				}
			} else {
				echo json_encode( array('status' => 'existe', 'msg' => 'El perfil ya se encuentra asociado a la persona') );
			}
		}
	}

	function valid_date($date){
		$data = strtotime( $date );
		$invalid_date = strtotime( "0001-01-01" );
		$invalid_date2 = strtotime("1980-01-01");
		if ( ($data == $invalid_date) || ( $data < $invalid_date2 ) ) {
			$this->form_validation->set_message('valid_date', 'La %s ingresada no es valida');
      return FALSE;
		} else {
			return TRUE;
		}
	}

	function existe_activo( $tipo_perfil, $asignacin_id, $perfil_id, $perfil_asginacion_id = null ) {
		// Si existe perfil_asignacion_id es que es un edit
		if ( $tipo_perfil == 1  ) {
			$asignacion = $this->Perfiles_Personas_model->existe_activo( $asignacin_id, $perfil_id, $perfil_asginacion_id );
		} else {
			$asignacion = $this->Perfiles_Vehiculos_model->existe_asignacion( $asignacin_id, $perfil_id );
		}
		return $asignacion;
	}

}