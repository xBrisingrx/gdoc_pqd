<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_atributos_personas extends CI_Migration {

  public function up() {
    $this->dbforge->add_field(array(
      'id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'unsigned' => TRUE,
        'auto_increment' => TRUE
      ),
      'persona_id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'unsigned' => TRUE
      ),
      'atributo_id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'unsigned' => TRUE
      ),
      'cargado' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
                'default'=>0,
      ),
      'fecha_vencimiento' => array(
        'type' => 'date',
        'null' => TRUE
      ),
      'personalizado' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
                'default'=> 0,
      ),
      'pdf_path' => array(
        'type' => 'VARCHAR',
        'constraint' => 100,
        'null' => TRUE
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
      'CONSTRAINT atributo_persona_usuario_cfk FOREIGN KEY(`user_created_id`) REFERENCES `usuarios`(`id`)',
      'CONSTRAINT atributo_persona_usuario_ufk FOREIGN KEY(`user_last_updated_id`) REFERENCES `usuarios`(`id`)',
      'CONSTRAINT atributo_persona_atributo_fk FOREIGN KEY(`atributo_id`) REFERENCES `atributos`(`id`)',
      'CONSTRAINT atributo_persona_persona_fk FOREIGN KEY(`persona_id`) REFERENCES `personas`(`id`)',
    ));
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('atributos_personas');
  }

  public function down() {
    $this->dbforge->drop_table('atributos_personas');
  }
}
