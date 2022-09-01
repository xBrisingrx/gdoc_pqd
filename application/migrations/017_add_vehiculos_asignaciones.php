<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_vehiculos_asignaciones extends CI_Migration {

  public function up() {
    $this->dbforge->add_field(array(
      'id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'unsigned' => TRUE,
        'auto_increment' => TRUE
      ),
      'vehiculo_id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'unsigned' => TRUE
      ),
      'asignacion_id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'unsigned' => TRUE
      ),
      'fecha_alta' => array(
        'type' => 'date'
      ),
      'fecha_baja' => array(
        'type' => 'date',
        'null' => true
      ),
      'created_at datetime default current_timestamp',
      'updated_at datetime default current_timestamp on update current_timestamp',
      'user_created_id' => array(
              'type' => 'INT',
              'constraint' => 11,
              'unsigned' => TRUE
      ),
      'user_last_updated_id' => array(
              'type' => 'INT',
              'constraint' => 11,
              'unsigned' => TRUE
      ),
      'activo' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
                'default'=>1,
      ),
      'CONSTRAINT vehiculo_asignacione_usuario_cfk FOREIGN KEY(`user_created_id`) REFERENCES `usuarios`(`id`)',
      'CONSTRAINT vehiculo_asignacione_usuario_ufk FOREIGN KEY(`user_last_updated_id`) REFERENCES `usuarios`(`id`)',
      'CONSTRAINT vehiculo_asignacione_vehiculo_fk FOREIGN KEY(`vehiculo_id`) REFERENCES `vehiculos`(`id`)',
      'CONSTRAINT vehiculo_asignacione_asignacion_fk FOREIGN KEY(`asignacion_id`) REFERENCES `asignaciones_vehiculo`(`id`)',
    ));
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('vehiculos_asignaciones');
  }

  public function down() {
    $this->dbforge->drop_table('vehiculos_asignaciones');
  }
}