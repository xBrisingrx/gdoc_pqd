<?php

class Atributos_Vehiculos_model extends CI_Model {

// Tipo perfil: 1 para vehiculol 2 para vehiculos
  protected $table = 'atributos_vehiculos';

  public function __construct() {
    parent::__construct();
  }

  public function get($valor = null) {
    if($valor != null) {
      $this->db->select('atributos.nombre, atributos.categoria, atributos.tiene_vencimiento,
                         atributos.permite_pdf,
                         atributos_vehiculos.id, atributos_vehiculos.pdf_path,
                         atributos_vehiculos.cargado, atributos_vehiculos.fecha_vencimiento')
                  ->from('atributos_vehiculos')
                    ->join('atributos', 'atributos_vehiculos.atributo_id = atributos.id')
                        ->join('vehiculos', 'atributos_vehiculos.vehiculo_id = vehiculos.id')
                          ->where('vehiculos.id', $valor)
                          ->where('atributos_vehiculos.activo', TRUE);
        return $this->db->get()->result();
    }
  }

  function get_attr_vehiculo( $attr = null , $valor = null  )
  {
    if ( $attr != null && $valor != null ) {
      return $this->db->get_where($this->table, array( $attr => $valor ))->result();
    }
  }

  function get_atributo_vehiculo($vehiculo_id, $atributo_id){
    return $this->db->get_where($this->table, array( 'vehiculo_id' => $vehiculo_id, 'atributo_id' => $atributo_id ))->row();
  } 

  function get_attr_edit($vehiculo_id)
  {
    /* Obtengo la lista de attr para editar */
    return $this->db->get_where(
                          $this->table,
                          array('vehiculo_id' => $vehiculo_id))->result();
  }

  // Obtenemos las renovaciones de un atributo asignado
  function get_renovaciones($atributo_vehiculo_id)
  {
    $this->db->select('renovaciones_atributos_vehiculos.fecha_renovacion, renovaciones_atributos_vehiculos.fecha_vencimiento,         
                        renovaciones_atributos_vehiculos.pdf_path, atributos.tiene_vencimiento, renovaciones_atributos_vehiculos.id')
                ->from('renovaciones_atributos_vehiculos')
                  ->join('atributos_vehiculos', 'atributos_vehiculos.id = renovaciones_atributos_vehiculos.atributo_vehiculo_id')
                  ->join('atributos', 'atributos.id = atributos_vehiculos.atributo_id')
                    ->where('atributo_vehiculo_id', $atributo_vehiculo_id)
                    ->where('renovaciones_atributos_vehiculos.activo', true)
                      ->order_by('fecha_vencimiento', 'DESC');
    return $this->db->get()->result();
  }

  // Consultamos si un registro ya existe
  function existe($atributo_id, $vehiculo_id) {
    $query = $this->db->get_where(
                          $this->table,
                          array('atributo_id' => $atributo_id, 'vehiculo_id' => $vehiculo_id));

    if ( $query->num_rows() == 1 ) {
      return true;
    } else {
      return false;
    }
  }

  function get_ultima_renovacion($atributo_vehiculo_id){ // obtengo la ultima renovacion de este atributo
    return $this->db->select('id')
                      ->from('renovaciones_atributos_vehiculos')
                        ->where('atributo_vehiculo_id', $atributo_vehiculo_id)
                          ->order_by('fecha_vencimiento', 'DESC')
                            ->get()->row();
  }

  // Nos llega un array con los atributos asociados al perfil que se le carga a un vehiculo
  // Cargado por defecto en FALSE, eso se carga en Documentos
  // fecha_vencimiento y pdf_path son campos que se cargan en documentos
  public function insert_entry($atributos_perfil, $vehiculo_id)
  {
    $date_at = date('Y-m-d H:i:s');
    $user_id = $this->session->userdata('id');

    $this->db->trans_start();
      foreach ($atributos_perfil as $attr) {
        if (!$this->existe($attr->attribute_id, $vehiculo_id)) {
          $entry = array(
            'atributo_id' => $attr->attribute_id,
            'vehiculo_id' => $vehiculo_id,
            'cargado' => false,
            'created_at' => $date_at,
            'updated_at' => $date_at,
            'user_created_id' => $user_id,
            'user_last_updated_id' => $user_id,
            'activo' => TRUE
          );
          $this->db->insert($this->table, $entry);
        } else {
          $atributo_actualizar = $this->db->get_where($this->table, array( 'atributo_id' => $attr->attribute_id, 'vehiculo_id' => $vehiculo_id ))->row();
          if (!$atributo_actualizar->personalizado) {
            $atributo_actualizar->activo = true;
            $atributo_actualizar->updated_at = $date_at;
            $atributo_actualizar->user_last_updated_id = $user_id;
            $this->db->where('id', $atributo_actualizar->id);
            $this->db->update($this->table, $atributo_actualizar);
          }
        }
      }
    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE) {
      return FALSE;
    } else {
      return TRUE;
    }
  }

  function update_data( $entry )
  { // Actualizo los datos luego de eliminar una renovacion
    $this->db->where('id', $entry->id);
    return $this->db->update($this->table, $entry);
  }

  function update_entry($atributos_perfil, $vehiculo_id, $atributos_a_actualizar)
  {
    /* atributos_perfil son los atributos q se le van a asignar luego de modificar el perfil */
    /* atributos_a_actualizar son los atributos que ya tenia asignados */
    $date_at = date('Y-m-d H:i:s');
    $user_id = $this->session->userdata('id');
    $corresponde_atributo = false;
    foreach ($atributos_a_actualizar as $attr_a_actualizar) {
      foreach ($atributos_perfil as $attr) {
        if ($attr_a_actualizar->atributo_id == $attr->attribute_id) {
          $corresponde_atributo = true;
        }
      }
      /* si corresponde atributo es true significa que el atributo que ya tenia asignado no debe eliminarse */
      if (!$corresponde_atributo) {
        $this->destroy( $attr_a_actualizar->id );
      }
      $corresponde_atributo = false;
    }

    $this->db->trans_start();
      foreach ($atributos_perfil as $attr) {
        if (!$this->existe($attr->attribute_id, $vehiculo_id)) {
          $entry = array(
            'atributo_id' => $attr->attribute_id,
            'vehiculo_id' => $vehiculo_id,
            'cargado' => false,
            'created_at' => $date_at,
            'updated_at' => $date_at,
            'user_created_id' => $user_id,
            'user_last_updated_id' => $user_id,
            'activo' => TRUE
          );
          $this->db->insert($this->table, $entry);
        } else {
          $entry_update = $this->db->get_where($this->table, array('atributo_id' => $attr->attribute_id, 'vehiculo_id' =>  $vehiculo_id))->row();
          $entry_update->activo = true;
          $this->db->where('id', $entry_update->id);
          $this->db->update($this->table, $entry_update);
        }
      }
    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE) {
      return FALSE;
    } else {
      return TRUE;
    }
  }

  public function destroy($id)
  {
    $entry = $this->db->get_where($this->table, array('id' => $id))->row();
    $entry->activo = false;
    $entry->updated_at = date('Y-m-d H:i:s');

    $this->db->where('id', $id);
    return $this->db->update($this->table, $entry);
  }

  // Consultas para informes
  function informe_matriz($fecha_inicio, $fecha_fin, $atributo_ids) {
    $this->db->select('vehiculos.id, vehiculos.interno as interno,
                       atributos.nombre as nombre_atributo,
                       atributos_vehiculos.fecha_vencimiento, atributos_vehiculos.cargado, atributos.tiene_vencimiento, atributos.id as atributo_id')
                ->from('atributos_vehiculos')
                  ->join('vehiculos', 'vehiculos.id = atributos_vehiculos.vehiculo_id')
                    ->join('atributos', 'atributos.id = atributos_vehiculos.atributo_id')
                      ->where('atributos_vehiculos.activo', TRUE)
                      ->where('vehiculos.activo', TRUE);

  if ($fecha_inicio != null && $fecha_fin != null) {
      $this->db->where('atributos_vehiculos.fecha_vencimiento >=', $fecha_inicio );
      $this->db->where('atributos_vehiculos.fecha_vencimiento <=', $fecha_fin );
    }

    if ( $atributo_ids != null ) {
      $this->db->where('atributos_vehiculos.atributo_id', $atributo_ids[0]);
      for ($i=1; $i < count($atributo_ids) ; $i++) { 
        $this->db->or_where('atributos_vehiculos.atributo_id = ', $atributo_ids[$i]);
      }
    }

    $this->db->order_by('vehiculos.id', 'ASC');
    $this->db->order_by('atributos.nombre', 'ASC');
    return $this->db->get()->result();
  }

  function informe_entre_fechas($fecha_inicio, $fecha_fin, $atributo_id) {
    $this->db->select('vehiculos.id, vehiculos.interno as interno,
                       atributos.nombre as nombre_atributo,
                       atributos_vehiculos.fecha_vencimiento, atributos_vehiculos.cargado, atributos.tiene_vencimiento, atributos.id as atributo_id')
                        ->from($this->table)
                          ->join('vehiculos', 'vehiculos.id = atributos_vehiculos.vehiculo_id')
                            ->join('atributos', 'atributos.id = atributos_vehiculos.atributo_id')
                              ->where('atributos_vehiculos.activo', TRUE);
    if ($atributo_id != 0) {
      $this->db->where('atributos.id', $atributo_id);
    }
    if ($fecha_inicio != null) {
      $this->db->where('atributos_vehiculos.fecha_vencimiento >=', $fecha_inicio );
    }
    if ($fecha_fin != null) {
      $this->db->where('atributos_vehiculos.fecha_vencimiento <=', $fecha_fin );
    }
    return $this->db->order_by('vehiculos.interno', 'ASC')
                    ->order_by('atributos.nombre', 'ASC')
                      ->get()->result();
  }

  function insert_personalizado($entry){
    if (!$this->existe($entry['atributo_id'], $entry['vehiculo_id'])) {
      return $this->db->insert($this->table, $entry);
    } else {
      $data = $this->db->get_where( $this->table, array( 'vehiculo_id'=> $entry['vehiculo_id'], 'atributo_id'=> $entry['atributo_id'] ) )->row();
      $data->activo = $entry['activo'];
      $data->personalizado = $entry['personalizado'];
      $data->updated_at = $entry['updated_at'];
      $data->user_last_updated_id =$entry['user_last_updated_id'];
      $this->db->where('id', $data->id);
      return $this->db->update($this->table, $data);
    }
  }

  function disable_personalizado($id) {
    $entry = $this->db->get_where($this->table, array('id' => $id) )->row();
    $entry->activo = false;
    $entry->personalizado = true;
    $entry->updated_at = date('Y-m-d H:i:s');
    $entry->user_last_updated_id = $this->session->userdata('id');
    $this->db->where('id', $entry->id);
    return $this->db->update($this->table, $entry);
  }

  function exists($atributo_id, $vehiculo_id) {
    $query = $this->db->get_where(
                          $this->table,
                          array('atributo_id' => $atributo_id, 'vehiculo_id' => $vehiculo_id));

    if ( $query->num_rows() == 1 ) {
      return true;
    } else {
      return false;
    }
  }
}
