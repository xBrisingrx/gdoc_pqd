<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Perfiles_Atributos extends CI_Controller {

	function __construct() {
	  parent::__construct();
	  $this->load->model('Perfiles_Atributos_model');
	  date_default_timezone_set('America/Argentina/Buenos_Aires');
	  if ( empty( $this->session->nombre_usuario ) ) {
	  	redirect('Login');
	  }
	}

	function ajax_list($tipo) {
		$perfiles_atributos = $this->Perfiles_Atributos_model->get('tipo',$tipo);
		$data = array();
		foreach ($perfiles_atributos as $p) {
			$row = array();
			$row[] = $p->nombre_perfil;
			$row[] = $p->nombre_atributo;
			$row[] = ( $p->fecha_inicio_vigencia != '0000-00-00' ) ? date('d-m-Y', strtotime($p->fecha_inicio_vigencia)) : '';
			$row[] = ($p->activo) ? ' ' : date('d-m-Y', strtotime($p->updated_at));
			if ( $this->session->userdata('rol') == 1 ) { 
				$row[] = '<button class="btn u-btn-primary btn-sm mr-2" title="Editar" onclick="modal_edit_attribute('."'".$p->id."'".')" ><i class="fa fa-edit"></i></button> <button class="btn u-btn-red btn-sm mr-2" title="Eliminar" onclick="modal_delete_attribute_profile('."'".$p->id."'".')" ><i class="fa fa-trash-o"></i></button>';
			} else {
				$row[] = ' ';
			}
			$data[] = $row;
		}
		echo json_encode(array("data" => $data));
	}

	function create() {
		$this->form_validation->set_rules('tipo', 'Tipo', 'required');
		$this->form_validation->set_rules('perfil_id', 'Perfil', "required");
		$this->form_validation->set_rules('atributo_id', 'Atributo', "required");
		// $this->form_validation->set_rules('fecha_inicio_vigencia', 'Fecha inicio vigencia', "required|callback_valid_date");

		if ($this->form_validation->run() == FALSE) {
			echo json_encode( array( 'status' => 'error', 'class' => 'danger' ,'msg' => validation_errors() ) );
		} else {
			$profile_attribute = array(
				'tipo' => $this->input->post('tipo'),
				'perfil_id' => $this->input->post('perfil_id'),
				'atributo_id' => $this->input->post('atributo_id'),
				'fecha_inicio_vigencia' => $this->input->post('fecha_inicio_vigencia'),
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
				'user_created_id' => $this->session->userdata('id'),
				'user_last_updated_id' => $this->session->userdata('id')
			);
			$profile_attribute = $this->security->xss_clean($profile_attribute);
			if (!$this->existe($profile_attribute) ) {
				if ( $this->Perfiles_Atributos_model->insert_entry($profile_attribute) ) {
					echo json_encode(array('status' => 'success','msg' => 'Atributo asignado al perfil'));
				} else {
					echo json_encode( $this->Perfiles_Atributos_model->insert_entry($profile_attribute) );
				}
			} else {
				$entry = $this->Perfiles_Atributos_model->get_perfil_atributo($profile_attribute['perfil_id'], $profile_attribute['atributo_id'] );
				if ($entry->activo) {
					echo json_encode(array('status' => 'error','class' => 'info', 'msg' => 'Este perfil ya tiene este atributo'));
				} else {
					// El perfil ya tenia este atributo  y habia sido dado de baja
					$entry->activo = TRUE;
					$entry->fecha_inicio_vigencia = $this->input->post('fecha_inicio_vigencia');
					$entry->updated_at = date('Y-m-d H:i:s');
					$entry->user_last_updated_id = $this->session->id;
					if ( $this->Perfiles_Atributos_model->update_entry($entry->id, $entry) ) {
						echo json_encode(array('status' => 'success','class' => 'success', 'msg' => 'Atributo asignado al perfil'));
					} else {
						echo json_encode( array('status' => 'error', 'msg' => 'No se pudo reactivar el atributo en este perfil') );
					}
				}
			}
		}
	}

	function edit($id){
		$perfil_atributo = $this->Perfiles_Atributos_model->get('id', $id);
		echo json_encode($perfil_atributo[0]);
	}

	function update(){
		// De la asignacion perfil_atributo solo se puede modificar el inicio de vigencia
		$id = $this->input->post('id');
		$data = array(
			'fecha_inicio_vigencia' => $this->input->post('fecha_inicio_vigencia'),
			'updated_at' => date('Y-m-d H:i:s'),
			'user_last_updated_id' => $this->session->id,
		);
		if ($this->Perfiles_Atributos_model->update_entry($id, $data)) {
			echo json_encode(array('status' => 'success','class' => 'success', 'msg' => 'Datos actualizados'));
		} else {
			echo json_encode(array('status' => 'error','class' => 'danger', 'msg' => 'No se pudo actualizar la informacion'));
		}
	}

	function destroy($id){
		// Elimino un atributo de un perfil
		if ($this->Perfiles_Atributos_model->destroy($id)) {
			echo json_encode(array('status' => 'success', 'msg' => 'Atributo eliminado'));
		} else {
			echo json_encode(array('error' => 'success', 'msg' => 'No se pudo eliminar el atributo'));
		}
	}

	function existe($entry , $id = null) {
		// Si enviamos $perfil_atributo_id es una edicion
		return !empty( $this->Perfiles_Atributos_model->existe($entry, $id) );
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

}