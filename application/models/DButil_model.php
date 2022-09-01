<?php

class DButil_model extends CI_Model {

  public function __construct() {
    parent::__construct();
  }

  function get( $table, $filtros = null) {
  	if($filtros != null) {
  		return $this->db->get_where($table, $filtros )->result();
  	} else {
    	 return $this->db->get($table)->result();
    }
  }

  function get_for_id($table, $id){
    return $this->db->get_where($table, array('id'=>$id))->row();
  }

  function get_last_id($tabla) {
    $query = $this->db->select('*')
                        ->from($tabla)
                          ->limit(1)
                            ->order_by('id', 'DESC')
                              ->get()->row();
    if (!empty( $query )) {
      return $query->id;
    } else {
      return 0;
    }
  }

  function get_archivo($tabla, $id, $carpeta){
    // Esta fn la uso me trae el nombre del archivo en pdf_path, ese campo se usaba cuando se subia un solo archivo por registro
    $data = $this->get_for_id( $tabla, $id);
    if (!empty($data->pdf_path) && file_exists("$carpeta/$data->pdf_path") ) {
      return "$carpeta/$data->pdf_path";
    } else {
      return '';
    }
  }

  public function insert_entry($table, $entry) {
  	return $this->db->insert($table, $entry);
  }

  public function update_entry($table, $id, $entry) {
    $this->db->where('id', $id);
    return $this->db->update($table, $entry);
  }

  function destroy_entry($table, $id) {
    $entry = $this->db->get_where($table, array('id'=>$id))->row();
    $entry->activo = false;
    $entry->updated_at = date('Y-m-d H:i:s');
    $entry->user_last_updated_id = $this->session->userdata('id');
    $this->db->where('id', $id);
    return $this->db->update($table, $entry);
  }

  function existe($table, $col, $value, $id = null){
    $this->db->select("id, $col")
                ->from($table)
                  ->where( $col, $value );
    if ($id != null) {
      $this->db->where('id !=', $id);
    }
    $query = $this->db->get();
    return ( $query->num_rows() > 0 );
  }

}