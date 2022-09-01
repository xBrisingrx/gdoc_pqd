<?php

class Motivos_baja_model extends CI_Model {

  protected $table = 'motivos_baja';

  public function __construct() {
          parent::__construct();
  }

  public function get($attr = null, $valor = null) {
  	if($attr != null and $valor != null) {
  		return $this->db->get_where($this->table, array($attr => $valor, 'activo' => true))->result();
  	} else {
  		return $this->db->get($this->table)->result();
    }
  }

  public function insert_entry($motivo) {
  	return $this->db->insert($this->table, $motivo);
  }

  public function update_entry($id, $motivo) {
    $this->db->where('id', $id);
    return $this->db->update($this->table, $motivo);
  }

  function existe_motivo($tipo, $motivo) {
    return $this->db->get_where( $this->table, array('tipo'=>$tipo, 'motivo'=>$motivo) )->row();
  }  

  function destroy($id) {
	  $entry = $this->db->get_where($this->table, array('id' => $id))->row();
	  $entry->activo = false;
	  $entry->updated_at = date('Y-m-d H:i:s');
	  $entry->user_last_updated_id = $this->session->userdata('id');

	  $this->db->where('id', $id);
	  return $this->db->update($this->table, $entry);
  } 

}