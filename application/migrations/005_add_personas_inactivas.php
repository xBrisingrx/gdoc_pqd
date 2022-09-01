<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_personas_inactivas extends CI_Migration {

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
      'motivo_baja_id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'unsigned' => TRUE
      ),
      'fecha_baja' => array(
        'type' => 'date'
      ),
      'detalle' => array(
        'type' => 'TEXT',
        'null' => TRUE
      ),
      'fecha_alta' => array(
        'type' => 'date',
        'null' => TRUE
      ),
      'detalle_alta' => array(
        'type' => 'TEXT',
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
      'CONSTRAINT persona_inactiva_persona_fk FOREIGN KEY(`persona_id`) REFERENCES `personas`(`id`)',
      'CONSTRAINT persona_inactiva_motivo_fk FOREIGN KEY(`motivo_baja_id`) REFERENCES `motivos_baja`(`id`)',
      'CONSTRAINT persona_inactiva_usuario_cfk FOREIGN KEY(`user_created_id`) REFERENCES `usuarios`(`id`)',
      'CONSTRAINT persona_inactiva_usuario_ufk FOREIGN KEY(`user_last_updated_id`) REFERENCES `usuarios`(`id`)'
    ));
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('personas_inactivas');
  }

  public function down() {
    $this->dbforge->drop_table('personas_inactivas');
  }
}
