<?php

class Perfiles_Atributos_model extends CI_Model {
// Tipo perfil: 1 para personal 2 para vehiculos
  protected $table = 'perfiles_atributos';

  public function __construct() {
    parent::__construct();
    $this->load->model('Atributos_Personas_model');
    $this->load->model('Atributos_Vehiculos_model');
  }

  function get($attr = null, $valor = null){
    $this->db->select('perfiles_atributos.id, perfiles.id as profile_id, atributos.id as attribute_id,perfiles.nombre as nombre_perfil, 
                       atributos.nombre as nombre_atributo, perfiles_atributos.updated_at, perfiles_atributos.fecha_inicio_vigencia, 
                                perfiles_atributos.activo, perfiles_atributos.tipo')
                       ->from( $this->table )
                         ->join( 'perfiles', 'perfiles_atributos.perfil_id = perfiles.id' )
                         ->join( 'atributos', 'perfiles_atributos.atributo_id = atributos.id' )
                           ->where($this->table.'.activo', true);
    if($attr != null and $valor != null) {
      $this->db->where( $this->table.'.'.$attr, $valor  );
    }
    return $this->db->get()->result();
  } // end GET
  
  function get_perfil_atributo( $perfil_id, $atributo_id ){
    return $this->db->get_where( $this->table, array('perfil_id' => $perfil_id, 'atributo_id'=> $atributo_id) )->row();
  }

  function insert_entry($perfil_atributo) {
    if ( $perfil_atributo['tipo'] == 1 ) {
      $profile_table = 'perfiles_personas';
      $atribute_table = 'atributos_personas';
      $type_id = 'persona_id';
      $model = 'Atributos_Personas_model';
    } else {
      $profile_table = 'perfiles_vehiculos';
      $atribute_table = 'atributos_vehiculos';
      $type_id = 'vehiculo_id';
      $model = 'Atributos_Vehiculos_model';
    }
    $this->db->trans_start();
    if ( $this->db->insert('perfiles_atributos', $perfil_atributo) ) {
      // Busco las personas/vehiculos que tengan el perfil al que se le agrego un atributo
      $afectados = $this->db->get_where( $profile_table, 
                                         array('perfil_id' => $perfil_atributo['perfil_id'], 'activo' => true) )->result();
      // Lo recorro para agregarle el atributo correspondiente
      foreach ($afectados as $a) {
        if ( !$this->$model->existe( $a->$type_id, $perfil_atributo['atributo_id'] ) ) {
          // La asociacion no existe, la creamos
          $entry = array();
          $entry['atributo_id'] = $perfil_atributo['atributo_id'];
          $entry[$type_id] = $a->$type_id;
          $entry['cargado'] = false;
          $entry['created_at'] = date('Y-m-d H:i:s');
          $entry['updated_at'] = date('Y-m-d H:i:s');
          $entry['user_created_id'] = $this->session->userdata('id');
          $entry['user_last_updated_id'] = $this->session->userdata('id');
          $this->db->insert( $atribute_table, $entry );
        } else {
          // Verifico si el atributo esta activo/inactivo, en caso de estar inactivo reactivamos
          $data = $this->db->get_where($atribute_table, array( $type_id => $a->$type_id, 'atributo_id' => $perfil_atributo['atributo_id'] ))->row();
          if ( !$data->activo && !$data->personalizado ) {
            $data->activo = TRUE;
            $data->user_last_updated_id = $this->session->userdata('id');
            $data->updated_at = date('Y-m-d H:i:s');
            $this->db->where('id', $data->id);
            $this->db->update($atribute_table, $data);
          }
        } 
      } // foreach afectados
    }

    $this->db->trans_complete();
    if ($this->db->trans_status() === FALSE) {
      return FALSE;
    } else {
      return TRUE;
    }
  }

  function update_entry($id, $perfil_atributo) {
    $perfil_atributo_a_editar = $this->db->get_where( $this->table, array( 'id' => $id ) )->row();
    if ($perfil_atributo_a_editar->activo) {
      // Si el registro se encuentra activo es una edicion y solo nos llega un cambio de inicio vigencia
      $this->db->where('id', $id);
      return $this->db->update($this->table, $perfil_atributo);
    } else {
      // Si no esta activo se trata de una reactivacion
      if ( $perfil_atributo->tipo == 1 ) {
      $profile_table = 'perfiles_personas';
      $atribute_table = 'atributos_personas';
      $type_id = 'persona_id';
      $atributo_asociacion_model = 'Atributos_Personas_model';
      } else {
        $profile_table = 'perfiles_vehiculos';
        $atribute_table = 'atributos_vehiculos';
        $type_id = 'vehiculo_id';
        $atributo_asociacion_model = 'Atributos_Vehiculos_model';
      }
      $this->db->trans_start();
      $this->db->where('id', $id);
      if ( $this->db->update( $this->table , $perfil_atributo) ) {
        // Busco las personas/vehiculos que tengan el perfil al que se le agrego un atributo
        $afectados = $this->db->get_where( $profile_table, array('perfil_id' => $perfil_atributo->perfil_id, 'activo' => true) )->result();
        // Lo recorro para agregarle el atributo correspondiente
        foreach ($afectados as $a) {
          if (!$this->$atributo_asociacion_model->existe( $a->$type_id, $perfil_atributo->atributo_id )) {
            $entry = array(
             'atributo_id' => $perfil_atributo->atributo_id,
             $type_id => $a->$type_id,
             'created_at' => date('Y-m-d H:i:s'),
             'updated_at' => date('Y-m-d H:i:s'),
             'user_created_id' => $this->session->userdata('id'),
             'user_last_updated_id' => $this->session->userdata('id')
            );
            $this->db->insert( $atribute_table, $entry );
          } else {
            // Verifico si el atributo esta activo/inactivo, en caso de estar inactivo reactivamos
            $data = $this->db->get_where($atribute_table, array($type_id => $a->$type_id, 'atributo_id' => $perfil_atributo->atributo_id ))->row();
            if ( !$data->activo && !$data->personalizado ) {
              $data->activo = TRUE;
              $data->user_last_updated_id = $this->session->userdata('id');
              $data->updated_at = date('Y-m-d H:i:s');
              $this->db->where('id', $data->id);
              $this->db->update($atribute_table, $data);
            }
          }
        } // foreach afectados
      }

      $this->db->trans_complete();
      if ($this->db->trans_status() === FALSE) {
        return FALSE;
      } else {
        return TRUE;
      }
    } // end reactivacion
  } // end update_entry

  function destroy($id){
    $perfil_atributo = $this->DButil->get_for_id($this->table,$id);
    $atribute_table = ( $perfil_atributo->tipo == 1 ) ? 'atributos_personas' : 'atributos_vehiculos';
    $tipo_id = ( $perfil_atributo->tipo == 1 ) ? 'persona_id' : 'vehiculo_id';
    $registros_a_eliminar = $this->no_tiene_atributo_en_mas_de_un_perfil( $perfil_atributo->tipo, $perfil_atributo->atributo_id );
    $fecha = date('Y-m-d H:i:s');
    $usuario = $this->session->id;

    $this->db->trans_begin();
      foreach ($registros_a_eliminar as $entry) {
        if ( ($entry->perfil_id == $perfil_atributo->perfil_id) ) {
          $atributo_baja = $this->db->get_where($atribute_table, array( $tipo_id => $entry->tipo_id, 'atributo_id' => $perfil_atributo->atributo_id ) )->row();
          if ( !$atributo_baja->personalizado ) {
            $atributo_baja->activo = FALSE;
            $atributo_baja->updated_at = $fecha;
            $atributo_baja->user_last_updated_id = $usuario;
            $this->db->where('id', $atributo_baja->id);
            $this->db->update($atribute_table, $atributo_baja);
          }
        }
      }
      $perfil_atributo->activo = FALSE;
      $perfil_atributo->updated_at = $fecha;
      $perfil_atributo->user_last_updated_id = $usuario;
      $this->db->where('id', $perfil_atributo->id);
      $this->db->update('perfiles_atributos', $perfil_atributo);
    $this->db->trans_complete();
    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      return false;
    } else {
      $this->db->trans_commit();
      return true;
    }
  }

  function existe( $entry, $id = null ){
    $this->db->select('*')->from($this->table)
               ->where('perfil_id', $entry['perfil_id'])
               ->where('atributo_id', $entry['atributo_id']);
    if ($id != null) {
      $this->db->where('id', $id);
    }
    return $this->db->get()->result();
  }

  // Cuento la cantidad de perfiles activos que tienen las personas/vehiculos 
  function no_tiene_atributo_en_mas_de_un_perfil($tipo, $atributo_id) {
    if ($tipo == 1) {
      $tabla_tipo = 'personas';
      $tipo_id = 'persona_id';
    } else {
      $tabla_tipo = 'vehiculos';
      $tipo_id = 'vehiculo_id';
    }

    $this->db->select("perfiles_$tabla_tipo.id, perfiles_$tabla_tipo.perfil_id ,perfiles_$tabla_tipo.$tipo_id AS tipo_id,COUNT(perfiles_$tabla_tipo.$tipo_id) AS cuento")
                ->from("perfiles_$tabla_tipo")
                  ->join('perfiles_atributos', "perfiles_atributos.perfil_id = perfiles_$tabla_tipo.perfil_id")
                  ->join("$tabla_tipo", "$tabla_tipo.id = perfiles_$tabla_tipo.$tipo_id")
                    ->where("perfiles_$tabla_tipo.activo", true)
                    ->where("perfiles_atributos.atributo_id", $atributo_id)
                    ->where("perfiles_atributos.activo", true)
                      ->group_by("perfiles_$tabla_tipo.$tipo_id")
                        ->having('cuento = 1');
    return $this->db->get()->result();
  }

}