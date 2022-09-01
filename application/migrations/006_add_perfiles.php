<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_perfiles extends CI_Migration {

  public function up() {
    $this->dbforge->add_field(array(
      'id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'unsigned' => TRUE,
        'auto_increment' => TRUE
      ),
      'tipo' => array(
        'type' => 'INT',
        'constraint' => 11
      ),
      'nombre' => array(
        'type' => 'varchar',
        'constraint' => '100',
      ),
      'descripcion' => array(
        'type' => 'TEXT',
        'null' => TRUE
      ),
      'fecha_inicio_vigencia' => array(
        'type' => 'date'
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
      'CONSTRAINT perfil_usuario_cfk FOREIGN KEY(`user_created_id`) REFERENCES `usuarios`(`id`)',
      'CONSTRAINT perfil_usuario_ufk FOREIGN KEY(`user_last_updated_id`) REFERENCES `usuarios`(`id`)'
    ));
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('perfiles');
  }

  public function down() {
    $this->dbforge->drop_table('perfiles');
  }
}
