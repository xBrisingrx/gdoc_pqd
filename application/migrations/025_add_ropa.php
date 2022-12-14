<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_ropa extends CI_Migration {

  public function up() {
    $this->dbforge->add_field(array(
      'id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'unsigned' => TRUE,
        'auto_increment' => TRUE
      ),
      'nombre' => array(
        'type' => 'varchar',
        'constraint' => '100',
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
      'CONSTRAINT ropa_usuario_cfk FOREIGN KEY(`user_created_id`) REFERENCES `usuarios`(`id`)',
      'CONSTRAINT ropa_usuario_ufk FOREIGN KEY(`user_last_updated_id`) REFERENCES `usuarios`(`id`)',
    ));
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('ropa');
  }

  public function down() {
    $this->dbforge->drop_table('ropa');
  }
}