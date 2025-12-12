<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateMaterialsTableStructure extends Migration
{
    public function up()
    {
        // Add missing columns to existing materials table
        $fields = [
            'user_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true,
                'after' => 'course_id'
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'user_id'
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'title'
            ],
            'original_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'file_name'
            ],
            'file_size' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true,
                'after' => 'file_path'
            ],
            'file_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'file_size'
            ],
            'upload_date' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'file_type'
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'after' => 'upload_date'
            ],
            'download_count' => [
                'type' => 'INT',
                'unsigned' => true,
                'default' => 0,
                'after' => 'is_active'
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'created_at'
            ]
        ];

        $this->forge->addColumn('materials', $fields);

        // Add foreign key for user_id
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE', 'materials_user_id_foreign');
    }

    public function down()
    {
        // Remove added columns
        $this->forge->dropColumn('materials', [
            'user_id', 'title', 'description', 'original_name', 
            'file_size', 'file_type', 'upload_date', 'is_active', 
            'download_count', 'updated_at'
        ]);
    }
}
