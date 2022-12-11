<?php

class Periodo_entrega_ropa_model extends CI_Model {

  protected $table = 'periodo_entrega_ropa';

  public function __construct() {
    parent::__construct();
    
  }

  
  
  function get_periodo_ropa($periodo_entrega_id) {
  	$this->db->select('ropa.nombre as nombre_ropa')
      ->from($this->table)
        ->join('ropa', 'periodo_entrega_ropa.ropa_id = ropa.id')
          ->where('periodo_entrega_ropa.activo', true)
          ->where('periodo_entrega_ropa.periodo_entrega_id', $periodo_entrega_id)
    return $this->db->get()->result();
  }

  public function insert_entry($empresa) {
  	return $this->db->insert($this->table, $empresa);
  }

  public function update_entry($id, $empresa) {
    $this->db->where('id', $id);
    return $this->db->update($this->table, $empresa);
  }

  function existe_empresa($tipo, $nombre) {
    return $this->db->select('*')
                      ->from($this->table)
                        ->where('tipo', $tipo)
                        ->where('nombre', $nombre)
                          ->get()
                            ->row();
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