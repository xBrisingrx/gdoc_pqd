<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Entrega_ropa extends CI_Controller {

	// administro las entregas de ropa a las personas

	function __construct() {
	  parent::__construct();
	  date_default_timezone_set('America/Argentina/Buenos_Aires');
	}

	function index(){
		$title['title'] = 'Ropa';
		$this->load->view('layout/header',$title);
		$this->load->view('layout/nav');
		$this->load->view('sistema/entrega_ropa/index');
		$this->load->view('layout/footer');
	}

	function create_periodo_entrega(){
		
	}

	function list($id) {
		$periodo_entregas = $this->DButil->get_periodo_ropa($id);
		$data = array();
		foreach ($periodo_entregas as $a) {
			$row = array();
			$row[] = $a->nombre;
			$row[] = $a->duracion;
			$row[] = $a->descripcion;
			if ($this->session->userdata('rol') == 1) {
				$row[] = '<button class="btn btn-sm u-btn-primary mr-2" title="Agregar prenda" onclick="modal_add_prenda('."'".$a->id."'".')" ><i class="fa fa-plus"></i></button> <button class="btn btn-sm u-btn-purple mr-2" title="Agregar prenda" onclick="modal_add_prenda('."'".$a->id."'".')" ><i class="fa fa-eye"></i></button>';
			} else {
				$row[] = '';
			}
			$data[] = $row;
		}
		echo json_encode(array("data" => $data));
	}

	function create_prendas() {
		$entry = array(
					'nombre' => 'Mameluco',
					'created_at' => date('Y-m-d H:i:s'),
	        'updated_at' => date('Y-m-d H:i:s'),
	        'user_created_id' => $this->session->userdata('id'),
	        'user_last_updated_id' => $this->session->userdata('id')
				);
		// $this->DButil->insert_entry('ropa', $entry);
	}


}
