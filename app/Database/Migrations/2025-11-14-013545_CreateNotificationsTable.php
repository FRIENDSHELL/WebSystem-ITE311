<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNotificationsTable extends Migration
{
    public function up()
    {
         $this->forge->addField([
        'id'         => ['type' => 'INT', 'auto_increment' => true],
        'user_id'    => ['type' => 'INT'],
        'message'    => ['type' => 'VARCHAR', 'constraint' => '255'],
        'is_read'    => ['type' => 'TINYINT', 'default' => 0],
        'created_at' => ['type' => 'DATETIME']
    ]);
    $this->forge->addKey('id', true);
    $this->forge->createTable('notifications');
    }

    public function down()
    {
        $this->forge->dropTable('notifications');
    }
}
