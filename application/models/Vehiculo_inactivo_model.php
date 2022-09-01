<?php

class Vehiculo_inactivo_model extends CI_Model {

  protected $table = 'vehiculos_inactivos';

  public function __construct() {
    parent::__construct();
  }

  function get_internos() {
    // paso los internos a numeros, ese campo es un string
    return $this->db->query('SELECT (interno * 1) as interno, id FROM vehiculos ORDER BY interno')->result();
  }

  function get() {
    $this->db->select("$this->table.id,$this->table.fecha_baja, $this->table.detalle, $this->table.vehiculo_id, vehiculos.interno, 
                       vehiculos.dominio, vehiculos.anio,marcas_vehiculos.nombre as marca,
                       modelos_vehiculos.nombre as modelo, tipos_vehiculos.nombre as tipo, vehiculos.n_chasis,
                       vehiculos.n_motor, vehiculos.cant_asientos, empresas.nombre as empresa,
                       motivos_baja.motivo")
                ->from($this->table)
                  ->join('vehiculos', "$this->table.vehiculo_id = vehiculos.id")
                  ->join('modelos_vehiculos', 'modelos_vehiculos.id = vehiculos.modelo_id')
                  ->join('marcas_vehiculos', 'marcas_vehiculos.id = vehiculos.marca_id')
                  ->join('tipos_vehiculos', 'tipos_vehiculos.id = vehiculos.tipo_id')
                  ->join('empresas', 'empresas.id = vehiculos.empresa_id')
                  ->join('motivos_baja', "motivos_baja.id = $this->table.motivo_baja_id")
                    ->where("$this->table.activo", true);
    return $this->db->get()->result();
  }

  function get_historial_vehiculo($vehiculo_id) {
    $this->db->select("$this->table.id, $this->table.fecha_baja, $this->table.detalle, $this->table.fecha_alta, $this->table.detalle_alta, motivos_baja.motivo")
               ->from($this->table)
                ->join('motivos_baja', "$this->table.motivo_baja_id = motivos_baja.id")
                  ->where("$this->table.vehiculo_id", $vehiculo_id)
                    ->order_by('id', 'DESC');
    return $this->db->get()->result();
  }


  function insert_entry($entry) {
    return $this->db->insert($this->table, $entry);
  }

  public function update_entry( $id, $entry ) {
    $this->db->where('id', $id);
    return $this->db->update($this->table, $entry);
  }

  public function destroy($table, $id) {
      $query = $this->db->get_where($table, array('id' => $id))->row();
      $query->activo = false;
      $query->update_at = date('Y-m-d H:i:s');

      $this->db->where('id', $id);
      return $this->db->update($table, $query);
  }

  function reactivar( $entry ) {
    $this->db->trans_start();
    $this->db->query('UPDATE vehiculos SET activo = 1 WHERE id = '.$entry['vehiculo_id']);
    $this->db->query('UPDATE detalle_bajas SET activo = 0 WHERE id = '.$entry['detalle_baja_id']);
    $this->db->trans_complete();

    if ($this->db->trans_status() == FALSE) {
      $this->db->trans_rollback();
      return false;
    } else {
      $this->db->trans_commit();
      return true;
    }
  }

  function get_ultimo_interno() {
    $query = $this->db->select('*')
                        ->from('vehiculos')
                          ->where('activo', true)
                            ->limit(1)
                              ->order_by('interno', 'DESC')
                                ->get()->row();
    return $query->interno;
  }

  function get_motivos_baja() {
    return $this->db->get_where('motivos_baja', array('tipo' => 2, 'activo' => true))->result();
  }

}
