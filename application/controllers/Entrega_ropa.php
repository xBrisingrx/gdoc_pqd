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

	function list() {

	}


}
