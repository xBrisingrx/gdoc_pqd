<?php

class Vehiculo_model extends CI_Model {

	protected $table = 'vehiculos';

  function __construct() {
    parent::__construct();
  }

  function get($attr = null, $value = null){
    $this->db->select('vehiculos.id,vehiculos.interno, vehiculos.dominio, vehiculos.anio,marcas_vehiculos.nombre as marca,
                       modelos_vehiculos.nombre as modelo, tipos_vehiculos.nombre as tipo, vehiculos.n_chasis,
                       vehiculos.n_motor, vehiculos.cant_asientos, empresas.nombre as empresa, vehiculos.observaciones, vehiculos.patentamiento')
                ->from($this->table)
                  ->join('marcas_vehiculos', 'marcas_vehiculos.id = vehiculos.marca_id')
                  ->join('modelos_vehiculos', 'modelos_vehiculos.id = vehiculos.modelo_id')
                  ->join('tipos_vehiculos', 'tipos_vehiculos.id = vehiculos.tipo_id')
                  ->join('empresas', 'empresas.id = vehiculos.empresa_id')
                    ->where('vehiculos.activo', true);
    if($attr != null and $value != null) {
      $this->db->where( "vehiculos.$attr", $value );
    }
	 return $this->db->order_by('interno', 'ASC')->get()->result();
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

  function get_info( $id = null ) {
    if ( $id != null ) {
        $this->db->select('vehiculos.id,vehiculos.interno, vehiculos.dominio, vehiculos.anio,marcas_vehiculos.nombre as marca,
                           modelos_vehiculos.nombre as modelo, tipos_vehiculos.nombre as tipo, vehiculos.n_chasis,
                           vehiculos.n_motor, vehiculos.cant_asientos, empresas.nombre as empresa, vehiculos.observaciones')
                    ->from('vehiculos')
                      ->join('marcas_vehiculos', 'marcas_vehiculos.id = vehiculos.marca_id')
                      ->join('modelos_vehiculos', 'modelos_vehiculos.id = vehiculos.modelo_id')
                      ->join('tipos_vehiculos', 'tipos_vehiculos.id = vehiculos.tipo_id')
                      ->join('empresas', 'empresas.id = vehiculos.empresa_id')
                        ->where('vehiculos.id', $id);
        return $this->db->get()->row();
    } else {
      return 'no data';
    }
  }

  function get_internos() {
    // paso los internos a numeros, ese campo es un string
    // return $this->db->query('SELECT (interno * 1) as interno, id FROM vehiculos ORDER BY interno')->result();
    // al necesitar usar eltras en el interno no podemos usar lo de castear a integer
    return $this->db->query('SELECT interno, id FROM vehiculos WHERE activo=true ORDER BY interno')->result();
  }

  function insert_entry($table, $value) {
    return $this->db->insert($table, $value);
  }

  function update_entry( $id, $entry ) {
    $this->db->where('id', $id);
    return $this->db->update($this->table, $entry);
  }

  function destroy($vehiculo) {
    // Dejamos el vehiculo inactivo y registramos el moviniento en tabla vehiculos_inactivos
    $entry = $this->db->get_where('vehiculos', array('id' => $vehiculo['vehiculo_id']))->row();
    $entry->activo = false;
    $entry->updated_at = date('Y-m-d H:i:s');
    $entry->user_last_updated_id = $this->session->userdata('id');
    $this->db->where('id', $vehiculo['vehiculo_id']);
    return ( $this->db->update('vehiculos', $entry) ) && ( $this->db->insert('vehiculos_inactivos', $vehiculo) );
  }

  function reactivar( $id, $entry ) {
    $this->db->trans_start();
    // Se reactiva el vehiculo
    $this->db->query('UPDATE vehiculos SET activo = 1 WHERE id = '.$entry['vehiculo_id']);
    // Se desactiva el registro vehiculo_inactivo
    $this->db->where('id', $id);
    $this->db->update('vehiculos_inactivos', $entry);
    $this->db->trans_complete();

    if ($this->db->trans_status() == FALSE) {
      $this->db->trans_rollback();
      return false;
    } else {
      $this->db->trans_commit();
      return true;
    }
  }

	function get_motivos_baja() {
    return $this->db->select('id, motivo')
                      ->from('motivos_baja')
                        ->where( array('tipo' => 2, 'activo' => true) )
                          ->order_by('motivo', 'ASC')
                            ->get()->result();
  }


/* Operaciones de marca/modelo/tipo vehiculo */ 
	function get_attr($table, $attr = null, $value = null) {
    if ($attr != null and $value != null) {
      return $this->db->select('*')->from($table)->where( array($attr => $value, 'activo' => 1) )->order_by('nombre', 'ASC')->get()->result();
    } else {
        if ($table == 'modelos_vehiculos') {
          $this->db->select('modelos_vehiculos.id AS id, modelos_vehiculos.nombre as nombre,marcas_vehiculos.nombre as nombre_marca')
                      ->from($table)
                        ->join('marcas_vehiculos', 'marcas_vehiculos.id = modelos_vehiculos.marca_vehiculo_id')
                          ->where('modelos_vehiculos.activo', true)
                            ->order_by('nombre', 'ASC');
          return $this->db->get()->result();
        } else {
          return $this->db->select('*')->from($table)->where( array('activo' => 1) )->order_by('nombre', 'ASC')->get()->result();
      }
    }
  }

  function modelo_vehiculo_unico($marca_id,$name) {
    // Si retorna 0 es que el valor no se encuentra en la BD
    $query = $this->db->select('*')
                ->from('modelos_vehiculos')
                  ->join('marcas_vehiculos', 'marcas_vehiculos.id = modelos_vehiculos.marca_vehiculo_id')
                    ->where(array('modelos_vehiculos.nombre' => $name, 'modelos_vehiculos.marca_vehiculo_id' => $marca_id))
                      ->get();
    if ( $query->num_rows() > 0 ) {
      return false;
    } else {
      return true;
    }
  }

  function get_last_id() {
    $query = $this->db->select('*')
                        ->from('vehiculos')
                          ->limit(1)
                            ->order_by('id', 'DESC')
                              ->get()->row();
    if (!empty( $query )) {
      return $query->id;
    } else {
      return 0;
    }
  }

/* Marca */
  function destroy_marca($id) {
    $this->db->trans_start();
    $this->db->query('UPDATE modelos_vehiculos SET activo = 0 WHERE marca_vehiculo_id = '.$id);
    $this->db->query('UPDATE marcas_vehiculos SET activo = 0 WHERE id = '.$id);
    $this->db->trans_complete();

    if ($this->db->trans_status() == FALSE) {
      $this->db->trans_rollback();
      return false;
    } else {
      $this->db->trans_commit();
      return true;
    }
  }

  /* obtengo las asignaciones que tenga un vehiculo */
  function get_asginaciones_vehiculo($vehiculo_id){
    return $this->db->select('va.id, va.fecha_alta, va.fecha_baja,va.asignacion_id ,va.activo,asignaciones_vehiculo.nombre')
                      ->from('vehiculos_asignaciones as va')
                        ->join('asignaciones_vehiculo', 'asignaciones_vehiculo.id = va.asignacion_id')
                          ->where('va.vehiculo_id', $vehiculo_id)->get()->result();
  }

}