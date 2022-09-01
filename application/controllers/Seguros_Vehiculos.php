<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Seguros_Vehiculos extends CI_Controller {

	function __construct() {
	  parent::__construct();
	  $this->load->model('Seguros_Vehiculos_model');
	  date_default_timezone_set('America/Argentina/Buenos_Aires');
	}

	function show($id){
		echo json_encode( $this->DButil->get_for_id('seguros_vehiculos', $id) );
	}

	function list($id){
		$seguros_vehiculo = $this->Seguros_Vehiculos_model->get_seguros_vehiculo($id);
		$tipo = '2';
		$tabla = 'seguros_vehiculo';
		$data = array();
		foreach ($seguros_vehiculo as $a) {
			$row = array();
			$row[] = $a->nombre;
			$row[] = $a->poliza;
			$row[] = date('d-m-Y', strtotime($a->fecha_alta));
			$row[] = date('d-m-Y', strtotime($a->vencimiento));
			$row[] =  '<button class="btn btn-sm u-btn-orange g-mr-5 g-mb-5" title="Ver archivos" onclick="modal_archivos('."'".$tipo."',".$a->id.','."'seguros_vehiculos'".')" ><i class="fa fa-file"></i></button>';
			if ($this->session->userdata('rol') == 1) {
				$row[] = '<button class="btn btn-sm u-btn-primary mr-2" title="Editar" onclick="modal_edit_aseguradora('."'".$a->id."'".')" ><i class="fa fa-edit"></i></button> <button class="btn btn-sm u-btn-red " title="Eliminar" onclick="modal_destroy_aseguradora('."'".$a->id."'".')" ><i class="fa fa-trash-o"></i></button>';
			} else {
				$row[] = '';
			}
			$data[] = $row;
		}
		echo json_encode(array("data" => $data));
	}

	function create(){
		$this->_validate_rules();
		if ( $this->form_validation->run() == FALSE ) {
			echo json_encode(array('status' => 'error', 'msg' => validation_errors() )); 
		} else {
			$entry = array(
				'aseguradora_id' => $this->input->post('aseguradora_id'),
				'vehiculo_id' => $this->input->post('vehiculo_id'),
				'fecha_alta' => $this->input->post('fecha_alta'),
				'vencimiento' => $this->input->post('vencimiento'),
				'poliza' => $this->input->post('poliza'),
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
				'user_created_id' => $this->session->userdata('id'),
	      'user_last_updated_id' => $this->session->userdata('id'),
			);
			$entry = $this->security->xss_clean($entry);
			if ( $this->Seguros_Vehiculos_model->insert_entry($entry) ) {
				$estado_archivos['status'] = TRUE;
				$folder = 'vehiculos/polizas';
				$tabla_id = $this->DButil->get_last_id('seguros_vehiculos');
				if ($_FILES['archivos']) {
          $estado_archivos = $this->Fileutil->subir_multiples_archivos( $_FILES['archivos'], $folder, 'seguros_vehiculos', $tabla_id, 'poliza' );
        }
        if ($estado_archivos['status'] === TRUE) {
          echo json_encode( array( 'status' => 'success', 'msg' => 'Poliza registrada') );
        } else {
          echo json_encode( array( 'status' => 'error', 'msg' => $estado_archivos['errors'] ) );
        }
			} else {
				echo json_encode( array( 'status' => 'error', 'msg' => 'No se pudo registrar la poliza') );
			}
		}
	}

	function edit($id){
		echo json_encode( $this->DButil->get_for_id('aseguradoras', $id) );
	}

	function update(){
		$this->_validate_rules(TRUE);
		if ( $this->form_validation->run() == FALSE ) {
			echo json_encode(array('status' => 'error', 'msg' => validation_errors() )); 
		} else {
			$seguro_vehiculo_id = $this->input->post('id');
			$entry = array(
				'aseguradora_id' => $this->input->post('aseguradora_id'),
				'fecha_alta' => $this->input->post('fecha_alta'),
				'vencimiento' => $this->input->post('vencimiento'),
				'poliza' => $this->input->post('poliza'),
				'updated_at' => date('Y-m-d H:i:s'),
	      'user_last_updated_id' => $this->session->userdata('id')
			);
			$entry = $this->security->xss_clean($entry);
			if ( $this->Seguros_Vehiculos_model->update_entry( $seguro_vehiculo_id,$entry ) ) {
				$estado_archivos['status'] = TRUE;
				$folder = 'vehiculos/polizas';
				$tabla_id = $seguro_vehiculo_id;
				if (isset($_FILES['archivos'])) {
          $estado_archivos = $this->Fileutil->subir_multiples_archivos( $_FILES['archivos'], $folder, 'seguros_vehiculos', $tabla_id, 'poliza' );
        }
        if ($estado_archivos['status'] === TRUE) {
          echo json_encode( array( 'status' => 'success', 'msg' => 'Datos actualizados') );
        } else {
          echo json_encode( array( 'status' => 'error', 'msg' => $estado_archivos['errors'] ) );
        }
			} else {
				echo json_encode( array( 'status' => 'error', 'msg' => 'No se pudieron actualizar los datos') );
			}
		}
	}

	function destroy($id){
		if ($this->DButil->destroy_entry('aseguradoras', $id)) {
			echo json_encode(array('status' => 'success', 'msg' => 'Aseguradora eliminada' )); 
		} else {
			echo json_encode(array('status' => 'error', 'msg' => 'No se pudo eliminar la aseguradora' )); 
		}
	}

	function _validate_rules($edit = false){
		$this->form_validation->set_rules('fecha_alta', 'Fecha alta', 'required');
		$this->form_validation->set_rules('vencimiento', 'Fecha vencimiento', 'required');
		$this->form_validation->set_rules('poliza', 'Poliza', 'required');
		$this->form_validation->set_rules('aseguradora_id', 'Aseguradora', 'required|callback_no_zero');
		if ($edit) {
			$this->form_validation->set_rules('id', 'ID', 'required');
		} else {
			// en una edicion no se puede cambiar el vehiculo solo la aseguradora asignada
			$this->form_validation->set_rules('vehiculo_id', 'Vehiculo', 'required');
		}
	}

	function no_zero($value) {
		if ( $value != '0' || $value != 0 ) {
			return TRUE;
		} else {
			$this->form_validation->set_message('no_zero', 'Debe seleccionar una aseguradora');
      return FALSE;
		}
	} 

	function existe($nombre, $id = null ){
		return $this->DButil->existe('aseguradoras', 'nombre', $nombre, $id);
	}

}
