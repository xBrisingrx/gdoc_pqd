<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Asignaciones_vehiculo extends CI_Controller {
// Las asignaciones son los lugares a donde se asigna un vehiculo
	function __construct() {
	  parent::__construct();
	  $this->load->model('Vehiculo_model');
	  date_default_timezone_set('America/Argentina/Buenos_Aires');
	}

	function index(){
		$title['title'] = 'Asignacion vehiculos';
		$this->load->view('layout/header',$title);
		$this->load->view('layout/nav');
		$this->load->view('sistema/asignacion_vehiculos/index');
		$this->load->view('layout/footer');
	}

	function list(){
		$asignaciones = $this->DButil->get('asignaciones_vehiculo', array('activo' => true) );
		$data = array();
		foreach ($asignaciones as $a) {
			$row = array();
			$row[] = $a->nombre;
			$row[] = $a->descripcion;
			if ($this->session->userdata('rol') == 1) {
				$row[] = '<button class="btn btn-sm u-btn-primary mr-2" title="Editar" onclick="modal_edit_asignacion('."'".$a->id."'".')" ><i class="fa fa-edit"></i></button> <button class="btn btn-sm u-btn-red " title="Eliminar" onclick="modal_destroy_asignacion('."'".$a->id."'".')" ><i class="fa fa-trash-o"></i></button>';
			} else {
				$row[] = '';
			}
			$data[] = $row;
		}
		echo json_encode(array("data" => $data));
	}

	function list_asignaciones_vehiculo($vehiculo_id){ /* listo las asignaciones realizadas a un vehiculo */
		$asignaciones = $this->Vehiculo_model->get_asginaciones_vehiculo($vehiculo_id);
		$tipo = '2';
		$data = array();
		foreach ($asignaciones as $a) {
			$row = array();
			$row[] = $a->nombre;
			$row[] = date('d-m-Y', strtotime($a->fecha_alta));
			$row[] = ($a->activo) ? '' : date('d-m-Y', strtotime($a->fecha_baja));
			$row[] =  '<button class="btn btn-sm u-btn-orange g-mr-5 g-mb-5" title="Ver archivos" onclick="modal_archivos('."'".$tipo."',".$a->id.','."'vehiculos_asignaciones'".')" ><i class="fa fa-file"></i></button>';
			if ( ($this->session->userdata('rol') == 1) && $a->activo ) {
				$row[] = '<button class="btn btn-sm u-btn-red " title="Eliminar" onclick="modal_baja_asignacion_vehiculo('."'".$a->id."'".')" ><i class="fa fa-trash-o"></i></button>';
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
			if ( !$this->existe( $this->input->post('nombre') ) ) {
				$entry = array(
					'nombre' => $this->input->post('nombre'),
					'descripcion' => $this->input->post('descripcion'),
					'created_at' => date('Y-m-d H:i:s'),
	        'updated_at' => date('Y-m-d H:i:s'),
	        'user_created_id' => $this->session->userdata('id'),
	        'user_last_updated_id' => $this->session->userdata('id')
				);
				$entry = $this->security->xss_clean($entry);
				if ($this->DButil->insert_entry('asignaciones_vehiculo', $entry)) {
					 echo json_encode(array('status' => 'success', 'msg' => 'Registro exitoso' )); 
				} else {
					echo json_encode(array('status' => 'error', 'msg' => 'Error al crear asignacion' )); 
				}
			} else {
				echo json_encode(array('status' => 'existe', 'msg' => 'Este lugar ya se encuentra registrado' )); 
			}
		}
	}

	function edit($id){
		echo json_encode( $this->DButil->get_for_id('asignaciones_vehiculo', $id) );
	}

	function update(){
		$this->_validate_rules(true);
		if ( $this->form_validation->run() == FALSE ) {
			echo json_encode(array('status' => 'error', 'msg' => validation_errors() )); 
		} else {
			$id = $this->input->post('id');
			$nombre = $this->input->post('nombre');
			if ( !$this->existe($nombre, $id) ) {
				$entry = array(
					'nombre' => $nombre,
					'descripcion' => $this->input->post('descripcion'),
					'updated_at' => date('Y-m-d H:i:s'),
					'user_last_updated_id' => $this->session->userdata('id')
				);
				$entry = $this->security->xss_clean($entry);
				if ($this->DButil->update_entry('asignaciones_vehiculo', $id, $entry)) {
					 echo json_encode(array('status' => 'success', 'msg' => 'Datos actualizados' )); 
				} else {
					echo json_encode(array('status' => 'error', 'msg' => 'No se pudieron actualizar los datos' )); 
				}
			} else {
				echo json_encode(array('status' => 'existe', 'msg' => 'Este lugar ya se encuentra registrado' )); 
			}
		}
	}

	function destroy($id){
		if ($this->DButil->destroy_entry('asignaciones_vehiculo', $id)) {
			echo json_encode(array('status' => 'success', 'msg' => 'Lugar eliminado' )); 
		} else {
			echo json_encode(array('status' => 'error', 'msg' => 'No se pudo eliminar el lugar' )); 
		}
	}

	function _validate_rules( $edit = false ){
		$this->form_validation->set_rules('nombre', 'Lugar', 'required');
		if ($edit) {
			$this->form_validation->set_rules('id', 'id', 'required');
		}
	}

	function existe($nombre, $id = null ){
		return $this->DButil->existe('asignaciones_vehiculo', 'nombre', $nombre, $id);
	}

	/* Asignar a vehiculo */
	function asignar_a_vehiculo(){
		$this->form_validation->set_rules('vehiculo_id', 'vehiculo', 'required');
		$this->form_validation->set_rules('asignacion_id', 'Asignacion', 'required');
		$this->form_validation->set_rules('fecha_alta', 'Fecha', 'required');
		if ( $this->form_validation->run() == FALSE ) {
			echo json_encode(array('status' => 'error', 'msg' => validation_errors() )); 
		} else {
			$entry = array(
				'asignacion_id' => $this->input->post('asignacion_id'),
				'vehiculo_id' => $this->input->post('vehiculo_id'),
				'fecha_alta' => $this->input->post('fecha_alta'),
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
				'user_created_id' => $this->session->userdata('id'),
	      'user_last_updated_id' => $this->session->userdata('id')
			);
			$entry = $this->security->xss_clean($entry);
			if ( $this->DButil->insert_entry( 'vehiculos_asignaciones', $entry ) ) {
				$estado_archivos['status'] = TRUE;
				$folder = 'vehiculos/asignaciones';
				$tabla_id = $this->DButil->get_last_id('vehiculos_asignaciones');
				if (isset($_FILES['archivos'])) {
          $estado_archivos = $this->Fileutil->subir_multiples_archivos( $_FILES['archivos'], $folder, 'vehiculos_asignaciones', $tabla_id, 'asignacion_vehiculo' );
        }
        if ($estado_archivos['status'] === TRUE) {
          echo json_encode( array( 'status' => 'success', 'msg' => 'Asignación exitosa') );
        } else {
          echo json_encode( array( 'status' => 'error', 'msg' => $estado_archivos['errors'] ) );
        }
			} else {
				echo json_encode( array( 'status' => 'error', 'msg' => 'No se pudo asignar el vehiculo') );
			}
		}
	}

	function baja_asignar_vehiculo(){
		$this->form_validation->set_rules('id', 'ID', 'required');
		$this->form_validation->set_rules('fecha_baja', 'Fecha baja', 'required');
		if ( $this->form_validation->run() == FALSE ) {
			echo json_encode(array('status' => 'error', 'msg' => validation_errors() )); 
		} else {
			$entry = array(
				'fecha_baja' => $this->input->post('fecha_baja'),
				'updated_at' => date('Y-m-d H:i:s'),
	      'user_last_updated_id' => $this->session->userdata('id'),
	      'activo' => FALSE
			);
			$entry = $this->security->xss_clean($entry);
			if ( $this->DButil->update_entry( 'vehiculos_asignaciones', $this->input->post('id') ,$entry ) ) {
				echo json_encode( array( 'status' => 'success', 'msg' => 'Asignación finalizada') );
			} else {
				echo json_encode( array( 'status' => 'error', 'msg' => 'La asignación no se pudo finalizar') );
			}
		}
	}

}
