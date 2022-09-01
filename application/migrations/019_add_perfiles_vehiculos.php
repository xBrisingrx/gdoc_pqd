<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_perfiles_vehiculos extends CI_Migration {

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
      'perfil_id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'unsigned' => TRUE
      ),
      'fecha_inicio_vigencia' => array(
        'type' => 'date',
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
      'CONSTRAINT perfil_vehiculo_usuario_cfk FOREIGN KEY(`user_created_id`) REFERENCES `usuarios`(`id`)',
      'CONSTRAINT perfil_vehiculo_usuario_ufk FOREIGN KEY(`user_last_updated_id`) REFERENCES `usuarios`(`id`)',
      'CONSTRAINT perfil_vehiculo_perfil_fk FOREIGN KEY(`perfil_id`) REFERENCES `perfiles`(`id`)',
      'CONSTRAINT perfil_vehiculo_vehiculo_fk FOREIGN KEY(`vehiculo_id`) REFERENCES `vehiculos`(`id`)',
    ));
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('perfiles_vehiculos');
  }

  public function down() {
    $this->dbforge->drop_table('perfiles_vehiculos');
  }
}
