<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Archivos extends CI_Controller {

	function __construct() {
	  parent::__construct();
	  date_default_timezone_set('America/Argentina/Buenos_Aires');
	}

	function index(){
		$title['title'] = 'Aseguradoras';
		$this->load->view('layout/header',$title);
		$this->load->view('layout/nav');
		$this->load->view('sistema/aseguradoras/index');
		$this->load->view('layout/footer');
	}

	function mover(){
		$data = $this->DButil->get('renovaciones_atributos');
		$tabla = 'renovaciones_atributos';
		$copiados = 0;
		$errores = array();
		foreach ($data as $d) {
			$anio = date('Y', strtotime($d->created_at));
			$mes = date('m', strtotime($d->created_at));
			$tabla_id = $d->id;
			$columna = 'renovaciones';
			
			$path_viejo = "assets/uploads/renovaciones_atributos/personas/".$d->pdf_path;
      if (file_exists( $path_viejo ) && ( !empty($d->pdf_path) ) ) {
				$copiados++;
				$extension = explode(".", $d->pdf_path);
				$path =  "assets/uploads/personas/renovaciones/".$anio.'/'.$mes.'/'.$d->id;

	      $filename = $this->Fileutil->gererar_nombre_archivo($path, $extension[1]);

				$path_nuevo = "assets/uploads/personas/renovaciones/".$anio.'/'.$mes.'/'.$d->id.'/'.$filename;
				$archivo = array(
        'tabla' => $tabla,
        'tabla_id' => $tabla_id,
        'columna' => $columna,
        'path' => $path_nuevo,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
        'user_created_id' => $this->session->userdata('id'),
        'user_last_updated_id' => $this->session->userdata('id')
	      );
	      if ($this->DButil->insert_entry('archivos',$archivo)) {
	      	if ( !file_exists( $path ) ) {
			      mkdir( $path, 0777, true );
			    }
	      	if (copy($path_viejo, $path_nuevo)) {
	      		$copiados++;
	      	} else {
	      		echo " == $d->id == ";
	      		$errores[] = $d->id;
	      	}
	      } // insert
			} // if file existe
		}
		echo $copiados;
		foreach($errores as $e) {
			echo $e.' - ';
		}
		echo 'cuento => '.count($errores);
	}
// 4081
	function test(){
		$data = $this->DButil->get('archivos');
		$copiados = 0;
		foreach ($data as $d) {
			$extension = explode(".", $d->path);
			if ($extension[1] != 'pdf') {
				echo $extension[1].' ---- ';
			}

			if ( !file_exists( $d->path ) ) {
	    	echo $d->id.'';
	    }
		}

	}

}

