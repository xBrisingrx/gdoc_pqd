<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Atributos extends CI_Controller {

	function __construct() {
	  parent::__construct();
	  $this->load->model('Atributo_model');
	  date_default_timezone_set('America/Argentina/Buenos_Aires');
	  if ( empty( $this->session->nombre_usuario ) ) {
	  	redirect('Login');
	  }
	}

	function index($tipo) {
		if ($tipo != null) {
			$title['title'] = ($tipo == 1) ? 'Atributos de Personal' : 'Atributos de Vehiculos';
			$data['nombre_atributo'] = ($tipo == 1) ? 'Personal' : 'Vechículos';
			$data['tipo_atributo'] = $tipo;
			$data['atributos'] = $this->Atributo_model->get('tipo',$tipo);

			$this->load->view('layout/header',$title);
			$this->load->view('layout/nav');
			$this->load->view('sistema/atributos/index',$data);
			$this->load->view('layout/footer');
		} else {
			$this->load->view('layout/header');
			$this->load->view('layout/nav');
			$this->load->view('layout/404');
			$this->load->view('layout/footer');
		}
	}

	function create() {
		$this->form_validation->set_rules('tipo', 'Tipo', 'required|callback_tipo_valido');
		$this->form_validation->set_rules('nombre', 'Nombre atributo', "required");

		if ($this->form_validation->run() == FALSE) {
			echo json_encode( array( 'status' => 'error', 'msg' => validation_errors() ) );
		} else {
			$tipo_atributo = $this->input->post('tipo');
			$nombre = $this->input->post('nombre');
			if (!$this->existe($tipo_atributo, $nombre)) {
				$atributo = array(
					'tipo' => $this->input->post('tipo'),
					'nombre' => $this->input->post('nombre'),
					'descripcion' => $this->input->post('descripcion'),
					'categoria'  => $this->input->post('categoria'),
					'dato_obligatorio' => ($this->input->post('dato_obligatorio') == 'true') ? 1 : 0,
					'tiene_vencimiento' => ($this->input->post('tiene_vencimiento') == 'true') ? 1 : 0,
					'tipo_vencimiento' => ( $this->input->post('tiene_vencimiento') == 'true' ) ? $this->input->post('tipo_vencimiento') : '',
					'permite_modificar_proximo_vencimiento' => ($this->input->post('permite_edit_prox_vencimiento') == 'true') ? 1 : 0,
					'periodo_vencimiento' => $this->input->post('periodo_vencimiento'),
					'permite_pdf' => ($_POST['permite_pdf'] == 'true') ? 1 : 0,
					'observaciones' => $this->input->post('observaciones'),
					'metodologia_renovacion' => $this->input->post('metodologia_renovacion'),
					'fecha_inicio_vigencia' => $this->input->post('fecha_inicio_vigencia'),
					'presenta_resumen_mensual' => ( $_POST['presenta_resumen_mensual'] == 'true' ) ? 1 : 0,
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s'),
					'user_created_id' => $this->session->userdata('id'),
					'user_last_updated_id' => $this->session->userdata('id')
				);		

				$data = $this->security->xss_clean($atributo);
				if ($this->Atributo_model->insert_entry($data)) {
					echo json_encode( array('status' => 'success', 'msg' => 'Atributo registrado con éxito') );
				} else {
					echo json_encode( array('status' => 'warning', 'msg' => 'Ocurrio un error, no se pudo crear el atributo') );
				}
			} else {
				echo json_encode(array('status' => 'existe', 'msg' => 'Esta empresa ya se encuentra registrada'));
			}
		}
	} // end new atribute

	function edit($id) {
		echo json_encode( $this->Atributo_model->get('id',$id) );
	}

	function update() {
		$this->form_validation->set_rules('tipo', 'Tipo', 'required|callback_tipo_valido');
		$this->form_validation->set_rules('nombre', 'Nombre atributo', "required");
		$this->form_validation->set_rules('id', 'ID', "required");
		if ($this->form_validation->run() == FALSE) {
			echo json_encode( array( 'status' => 'error', 'msg' => validation_errors() ) );
		} else {
			$id = $this->input->post('id');
			$tipo_atributo = $this->input->post('tipo');
			$nombre = $this->input->post('nombre');
			if (!$this->existe($tipo_atributo, $nombre, $id)) {
				$atributo = array(
					'tipo' => $this->input->post('tipo'),
					'nombre' => $this->input->post('nombre'),
					'descripcion' => $this->input->post('descripcion'),
					'categoria'  => $this->input->post('categoria'),
					'dato_obligatorio' => ($this->input->post('dato_obligatorio') != 'false') ? true : false,
					'tiene_vencimiento' => ( $this->input->post('tiene_vencimiento') == 'true') ? true : false,
					'permite_modificar_proximo_vencimiento' => ($this->input->post('permite_edit_prox_vencimiento') == 'true') ? true : false,
					'tipo_vencimiento' =>  ( $this->input->post('tiene_vencimiento') == 'true' ) ? $this->input->post('tipo_vencimiento') : ' ',
					'periodo_vencimiento' => $this->input->post('periodo_vencimiento'),
					'permite_pdf' => ($this->input->post('permite_pdf') == 'true') ? true : false,
					'observaciones' => $this->input->post('observaciones'),
					'metodologia_renovacion' => $this->input->post('metodologia_renovacion'),
					'fecha_inicio_vigencia' => $this->input->post('fecha_inicio_vigencia'),
					'presenta_resumen_mensual' => ( $this->input->post('presenta_resumen_mensual') == 'true' ) ? true : false,
					'updated_at' => date('Y-m-d H:i:s'),
					'user_last_updated_id' => $this->session->userdata('id')
				);
				$atributo = $this->security->xss_clean($atributo);
				if ( $this->Atributo_model->update_entry($id, $atributo) ) {
					echo json_encode( array( 'status' => 'success', 'msg' => 'Datos actualizados' ) );
				} else {
					echo json_encode( array( 'status' => 'error', 'msg' => 'No se pudieron actualizar los datos' ) );
				}
			} else {
				echo json_encode(array('status' => 'existe', 'msg' => 'Esta empresa ya se encuentra registrada'));
			}
		}
	}

	function destroy($id) {
		if ($this->Atributo_model->destroy($id)) {
			echo json_encode( array('status' => 'success', 'msg' => 'Atributo eliminado') );
		} else {
			echo json_encode( array('status' => 'error', 'msg' => 'No se pudo eliminar el atributo') );
		}
	}

// Obtengo los datos de mi tabla y los devuelvo en formato json para insertar en datatables
	function ajax_list_attributes($tipo) {
		$atributos = $this->Atributo_model->get('tipo',$tipo);
		$data = array();

		foreach ($atributos as $a) {
			$row = array();
			$row[] = $a->nombre;
			$row[] = $a->descripcion;
			$row[] = ($a->dato_obligatorio) ? 'Si' : 'No';
			$row[] = $a->categoria;
			$row[] = ($a->tiene_vencimiento) ? 'Si' : 'No';
			$row[] = $a->tipo_vencimiento;
			$row[] = ( $a->periodo_vencimiento == 0 ) ? '' : $a->periodo_vencimiento;
			$row[] = ($a->permite_modificar_proximo_vencimiento) ? 'Si' : 'No';
			$row[] = ($a->permite_pdf) ? 'Si' : 'No';
			$row[] = $a->observaciones;
			$row[] = $a->metodologia_renovacion;
			$row[] = date('d-m-Y', strtotime($a->fecha_inicio_vigencia));
			$row[] = ($a->presenta_resumen_mensual) ? 'Si' : 'No';
			$row[] = (!$a->activo) ? $a->update_at : ' ';
			if ($this->session->userdata('rol') == 1) {
				if ($a->activo) {
					$row[] = '<button class="btn btn-xs u-btn-primary mr-2 " title="Editar" onclick="edit_attribute('."'".$a->id."'".')" ><i class="fa fa-edit"></i></button> <button class="btn btn-xs u-btn-red " title="Eliminar" onclick="modal_destroy_attribute('."'".$a->id."'".')" ><i class="fa fa-trash-o"></i></button>';
				} else {
					$row[] = '<button class="btn btn-xs u-btn-primary mr-2 " title="Editar" onclick="edit_attribute('."'".$a->id."'".')" disabled ><i class="fa fa-edit"></i></button> <button class="btn btn-xs u-btn-aqua" title="Reactivar" onclick="reactivate_attribute('."'".$a->id."'".')" ><i class="fa fa-retweet"></i></button>';
				}
			} else {
				$row[] = '';
			}
			$data[] = $row;
		}
		echo json_encode(array("data" => $data));
	}

	function ajax_get_attributes($tipo) {
		$attributes = $this->Atributo_model->get('tipo',$tipo);
		echo( json_encode($attributes) );
	}


	function tipo_valido($value) {
		if ($value == 1 || $value == 2) {
			return TRUE;
		} else {
			$this->form_validation->set_message('tipo_valido', 'Se envio un valor invalido');
      return FALSE;
		}
	} 

	function existe( $name = null, $tipo = null, $atributo_id = null ){
		// chequeo que el atributo no exista
		// si llega una variable como null es q estoy consultando via ajax
		if ($name == null || $tipo == null) {
			if ($this->Atributo_model->existe( $_POST['nombre'], $_POST['tipo'], $_POST['atributo_id'] )) {
				echo 'false';
			} else {
				echo 'true';
			}
		} else {
			return $this->Atributo_model->existe($name, $tipo, $atributo_id);
		}
	}

}