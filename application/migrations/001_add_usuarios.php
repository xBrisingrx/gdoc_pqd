<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_usuarios extends CI_Migration {

  public function up() {
    $this->dbforge->add_field(array(
      'id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'unsigned' => TRUE,
        'auto_increment' => TRUE
      ),
      'nombre_usuario' => array(
        'type' => 'VARCHAR',
        'constraint' => '60'
      ),
      'password' => array(
        'type' => 'VARCHAR',
        'constraint' => '200'
      ),
      'rol' => array(
        'type' => 'INT',
        'constraint' => 11,
        'unsigned' => TRUE
      ),
      'nombre' => array(
        'type' => 'VARCHAR',
        'constraint' => '60',
        'null' => TRUE
      ),
      'apellido' => array(
        'type' => 'VARCHAR',
        'constraint' => '60',
        'null' => TRUE
      ),
      'email' => array(
        'type' => 'VARCHAR',
        'constraint' => '60',
        'null' => TRUE
      ),
      'created_at datetime default current_timestamp',
      'updated_at datetime default current_timestamp on update current_timestamp',
      'activo' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
                'default'=>1,
      )
    ));
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('usuarios');
  }

  public function down() {
    $this->dbforge->drop_table('usuarios');
  }
}