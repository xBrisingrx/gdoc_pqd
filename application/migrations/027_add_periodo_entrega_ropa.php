<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_periodo_entrega_ropa extends CI_Migration {

  public function up() {
    $this->dbforge->add_field(array(
      'id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'unsigned' => TRUE,
        'auto_increment' => TRUE
      ),
      'ropa_id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'unsigned' => TRUE
      ),
      'periodo_entrega_id' => array(
        'type' => 'INT',
        'constraint' => 11,
        'unsigned' => TRUE
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
      'CONSTRAINT periodo_entrega_ropa_usuario_cfk FOREIGN KEY(`user_created_id`) REFERENCES `usuarios`(`id`)',
      'CONSTRAINT periodo_entrega_ropa_usuario_ufk FOREIGN KEY(`user_last_updated_id`) REFERENCES `usuarios`(`id`)',
      'CONSTRAINT periodo_entrega_ropa_ropa_fk FOREIGN KEY(`ropa_id`) REFERENCES `ropa`(`id`)',
      'CONSTRAINT periodo_entrega_ropa_periodo_entrega_fk FOREIGN KEY(`periodo_entrega_id`) REFERENCES `periodo_entregas`(`id`)',
    ));
    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('periodo_entrega_ropa');
  }

  public function down() {
    $this->dbforge->drop_table('periodo_entrega_ropa');
  }
}
