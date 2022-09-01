<?php

class Perfiles_Vehiculos_model extends CI_Model {

// Tipo perfil: 1 para vehiculol 2 para vehiculos
  protected $table = 'perfiles_vehiculos';

  function __construct() {
    parent::__construct();
    $this->load->model('Atributos_Vehiculos_model');
  }

  function get($attr = null, $valor = null){
    $this->db->select('vehiculos.interno, vehiculos.dominio, vehiculos.anio,
                     marcas_vehiculos.nombre as marca, modelos_vehiculos.nombre as modelo,
                     perfiles.nombre as nombre_perfil, perfiles_vehiculos.fecha_inicio_vigencia,
                     perfiles_vehiculos.updated_at, perfiles_vehiculos.activo, perfiles_vehiculos.id')
                ->from('perfiles_vehiculos')
                  ->join('vehiculos', 'vehiculos.id = perfiles_vehiculos.vehiculo_id')
                  ->join('perfiles', 'perfiles.id = perfiles_vehiculos.perfil_id')
                  ->join('marcas_vehiculos', 'marcas_vehiculos.id = vehiculos.marca_id')
                  ->join('modelos_vehiculos', 'modelos_vehiculos.id = vehiculos.modelo_id')
                    ->where('perfiles_vehiculos.activo', TRUE);
    if ($attr != null && $valor != null) {
      $this->db->where( "perfiles_vehiculos.$attr", $valor );
    }    
    return $this->db->get()->result();
  }

  function get_perfil_asignado( $vehiculo_id, $perfil_id ) {
    return $this->db->get_where( $this->table,
                                 array( 'perfil_id' => $perfil_id, 'vehiculo_id' => $vehiculo_id ) )->row();
  }


  function insert_entry($entry) {
  	$this->db->trans_start();

    $this->db->insert($this->table, $entry);
    $atributos_del_perfil = $this->db->get_where( 'perfiles_atributos', array( 'perfil_id' => $entry['perfil_id'], 'activo'=> true ) )->result();
    $this->activar_atributos_de_un_perfil($atributos_del_perfil, $entry['vehiculo_id'] );

    $this->db->trans_complete();
    if ($this->db->trans_status() === FALSE) {
      return FALSE;
    } else {
      return TRUE;
    }
  }

  function update_entry($id, $entry, $reactivar = null) {
    $date_at = date('Y-m-d H:i:s');
    $user_id = $this->session->userdata('id');
    $perfil_actual = $this->db->get_where( $this->table, array( 'id' => $id ) )->row();

    $this->db->trans_start();
      $atributos_perfil_actual = $this->db->get_where( 'perfiles_atributos', array( 'perfil_id' => $perfil_actual->perfil_id, 'activo'=> true ) )->result();
      $atributos_perfil_nuevo = $this->db->get_where( 'perfiles_atributos', array( 'perfil_id' => $entry['perfil_id'], 'activo'=> true ) )->result();

      foreach($atributos_perfil_actual as $atributo) {
        // Obtengo atributos del perfil que se da de baja para desactivarlos de la persona
        if ( !$this->atributo_en_mas_de_un_perfil( $entry['vehiculo_id'], $atributo->atributo_id ) ) {
          $atributo_vehiculo = $this->db->get_where('atributos_vehiculos', 
                                          array('vehiculo_id' => $entry['vehiculo_id'], 
                                                'atributo_id' => $atributo->atributo_id ))
                                            ->row();
          // Verificamos que el atributo no haya sido eliminado o agregado manualmente
          // esos son atributos "custom" que solo se pueden volver a modificar a mano
          if ( !$atributo_vehiculo->personalizado && ($atributo_vehiculo->activo == true) ) {
            $atributo_vehiculo->updated_at = $date_at;
            $atributo_vehiculo->user_last_updated_id = $user_id;
            $atributo_vehiculo->activo = false;

            $this->db->where('id', $atributo_vehiculo->id);
            $this->db->update('atributos_vehiculos', $atributo_vehiculo);
          }
        }
      }

      // damos de baja el perfil que tiene ahora
      $perfil_actual->activo = false;
      $perfil_actual->user_last_updated_id = $user_id;
      $perfil_actual->updated_at = $date_at;
      $this->db->where('id', $perfil_actual->id);
      $this->db->update($this->table, $perfil_actual);
      // asignamos el perfil por el que se actualizo
      $entry['created_at'] = $date_at;
      $entry['user_created_id'] = $user_id;
      $this->db->insert($this->table, $entry);
      // Asignamos los atributos del nuevo perfil
      $this->activar_atributos_de_un_perfil($atributos_perfil_nuevo, $entry['vehiculo_id'] );

    $this->db->trans_complete();
    if ($this->db->trans_status() === FALSE) {
      return FALSE;
    } else {
      return TRUE;
    }
  }

  function reactivar($vehiculo_id, $perfil_id, $fecha_inicio_vigencia){
    // Reactivamos el perfil y los atributos asociados al perfil
    $this->db->trans_start();

      $entry = $this->db->get_where($this->table, array('vehiculo_id' => $vehiculo_id, 'perfil_id' => $perfil_id))->row();
      $entry->activo = true;
      $entry->fecha_inicio_vigencia = $fecha_inicio_vigencia;
      $entry->updated_at = date('Y-m-d H:i:s');
      $entry->user_last_updated_id = $this->session->userdata('id');

      $perfil_atributos = $this->db->get_where( 'perfiles_atributos', array('perfil_id' => $perfil_id, 'activo' => true ))->result();
      $this->activar_atributos_de_un_perfil($perfil_atributos, $entry->vehiculo_id);
      $this->db->where('id', $entry->id);
      $this->db->update($this->table, $entry);

    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE) {
      return FALSE;
    } else {
      return TRUE;
    }
  }

  function destroy($id) {
    $entry = $this->db->get_where($this->table, array('id' => $id))->row();
    $entry->activo = false;
    $entry->updated_at = date('Y-m-d H:i:s');
    $entry->user_last_updated_id = $this->session->userdata('id');

    $atributos_a_desactivar = $this->db->get_where('perfiles_atributos', array('perfil_id' => $entry->perfil_id, 'activo'=> true ))->result();

    $this->db->trans_start();
    // damos de baja los atributos del perfil a desactivar
      $this->desactivar_atributos_de_un_perfil( $atributos_a_desactivar, $entry->vehiculo_id );
      // damos de baja el perfil
      $this->db->where('id', $id);
      $this->db->update($this->table, $entry);
      
    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE) {
      return FALSE;
    } else {
      return TRUE;
    }
  }

  function existe_asignacion( $vehiculo_id, $perfil_id )
  {
    return $this->db->get_where( $this->table,
                                 array( 'perfil_id' => $perfil_id, 'vehiculo_id' => $vehiculo_id ) )->result();
  }

  function atributo_en_mas_de_un_perfil( $vehiculo_id, $atributo_id ) {
    /* consulto en cuantos perfiles que tiene asignados la vehiculo se encuentra este atributo */
    $cuento = $this->db->select('perfiles_atributos.atributo_id, COUNT(perfiles_atributos.atributo_id) as cuento')
                         ->from($this->table)
                          ->join('perfiles_atributos', "perfiles_atributos.perfil_id = perfiles_vehiculos.perfil_id")
                            ->where( "perfiles_vehiculos.activo", true )
                            ->where("perfiles_vehiculos.vehiculo_id", $vehiculo_id)
                            ->where("perfiles_atributos.atributo_id", $atributo_id)
                              ->group_by('perfiles_atributos.atributo_id')->get()->row();
    if (isset($cuento)) {
      return ( $cuento->cuento > 1 );
    } else {
      // El registro no existe 
      return FALSE;
    }
    
  }

  function activar_atributos_de_un_perfil($perfil_atributos, $vehiculo_id ) {
    $date_at = date('Y-m-d H:i:s');
    $user_id = $this->session->userdata('id');

    foreach($perfil_atributos as $atributo) {
      $atributo_vehiculo = $this->db->get_where('atributos_vehiculos', 
                                      array('vehiculo_id' => $vehiculo_id, 
                                            'atributo_id' => $atributo->atributo_id ))
                                        ->row();
      if ( !empty($atributo_vehiculo) ) {
        if ( !$atributo_vehiculo->personalizado && ($atributo_vehiculo->activo == false) ) {
          $atributo_vehiculo->updated_at = $date_at;
          $atributo_vehiculo->user_last_updated_id = $user_id;
          $atributo_vehiculo->activo = true;

          $this->db->where('id', $atributo_vehiculo->id);
          $this->db->update('atributos_vehiculos', $atributo_vehiculo);
        }
      } else {
        // al perfil se le sumaron atributos mientras esta vehiculo lo tenia dado de baja
         $atributo_vehiculo = array(
            'atributo_id' => $atributo->atributo_id,
            'vehiculo_id' => $vehiculo_id,
            'cargado' => false,
            'created_at' => $date_at,
            'updated_at' => $date_at,
            'user_created_id' => $user_id,
            'user_last_updated_id' => $user_id,
            'activo' => TRUE,
            'personalizado' => 0
          );
        $this->db->insert('atributos_vehiculos', $atributo_vehiculo);
      }
    } // foreach
  }

  function desactivar_atributos_de_un_perfil($perfil_atributos, $vehiculo_id ) {
    $date_at = date('Y-m-d H:i:s');
    $user_id = $this->session->userdata('id');

    foreach($perfil_atributos as $atributo) {
        if ( !$this->atributo_en_mas_de_un_perfil( $vehiculo_id, $atributo->atributo_id ) ) {
          $atributo_vehiculo = $this->db->get_where('atributos_vehiculos', 
                                          array('vehiculo_id' => $vehiculo_id, 
                                                'atributo_id' => $atributo->atributo_id ))
                                            ->row();
          // Verificamos que el atributo no haya sido eliminado o agregado manualmente
          // esos son atributos "custom" que solo se pueden volver a modificar a mano
          if (!empty($atributo_vehiculo)) {
            if ( !$atributo_vehiculo->personalizado && ($atributo_vehiculo->activo == true) ) {
              $atributo_vehiculo->updated_at = $date_at;
              $atributo_vehiculo->user_last_updated_id = $user_id;
              $atributo_vehiculo->activo = false;

              $this->db->where('id', $atributo_vehiculo->id);
              $this->db->update('atributos_vehiculos', $atributo_vehiculo);
            }
          }
        }
      }
  }

}
