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
          ->where('periodo_entrega_ropa.periodo_entrega_id', $periodo_entrega_id);
    return $this->db->get()->result();
  }

  function insert_entry($entry) {
    if (!$this->existe( $entry )) {
      return $this->db->insert($this->table, $entry);
    } else {
      return false;
    }
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

  function existe( $entry, $entry_id = null ) {
    $this->db->select('id, activo')
                ->from($this->table)
                  ->where('periodo_entrega_id', $entry['periodo_entrega_id'])
                  ->where('ropa_id', $entry['ropa_id']);
    if ($entry_id != null) {
      $this->db->where('id !=', $entry_id);
    }
    $query = $this->db->get();
    return ( $query->num_rows() > 0 );
  }

  function get_ropa($id) {
    $this->db->select('ropa.nombre')
                ->from($this->table)
                  ->join('ropa', $this->table.'.ropa_id = ropa.id')
                    ->where($this->table.'.periodo_entrega_id', $id);
    return $this->db->get();
  }

}