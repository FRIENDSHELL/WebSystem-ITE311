<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateEnrollmentsTable extends Migration
{
    public function up()
    {
        // Add new fields to enrollments table for detailed student information
        $fields = [
            'student_id' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
                'after' => 'course_id',
            ],
            'first_name' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
                'after' => 'student_id',
            ],
            'last_name' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
                'after' => 'first_name',
            ],
            'middle_name' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
                'after' => 'last_name',
            ],
            'age' => [
                'type' => 'INT',
                'constraint' => 3,
                'null' => true,
                'after' => 'middle_name',
            ],
            'birth_date' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'age',
            ],
            'gender' => [
                'type' => 'ENUM',
                'constraint' => ['Male', 'Female', 'Other'],
                'null' => true,
                'after' => 'birth_date',
            ],
            'contact_number' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
                'after' => 'gender',
            ],
            'email_address' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
                'after' => 'contact_number',
            ],
            'address' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'email_address',
            ],
            'guardian_name' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
                'after' => 'address',
            ],
            'guardian_contact' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
                'after' => 'guardian_name',
            ],
            'year_level' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
                'after' => 'guardian_contact',
            ],
            'program' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
                'after' => 'year_level',
            ],
            'enrollment_status' => [
                'type' => 'ENUM',
                'constraint' => ['Pending', 'Approved', 'Rejected', 'Enrolled'],
                'default' => 'Pending',
                'after' => 'program',
            ],
            'enrollment_date' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'enrollment_status',
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'enrollment_date',
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'notes',
            ],
        ];

        $this->forge->addColumn('enrollments', $fields);

        // Rename created_at to enrolled_at for clarity
        $this->forge->modifyColumn('enrollments', [
            'created_at' => [
                'name' => 'enrolled_at',
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        // Drop the added columns
        $this->forge->dropColumn('enrollments', [
            'student_id', 'first_name', 'last_name', 'middle_name', 'age', 'birth_date',
            'gender', 'contact_number', 'email_address', 'address', 'guardian_name',
            'guardian_contact', 'year_level', 'program', 'enrollment_status',
            'enrollment_date', 'notes', 'updated_at'
        ]);

        // Rename enrolled_at back to created_at
        $this->forge->modifyColumn('enrollments', [
            'enrolled_at' => [
                'name' => 'created_at',
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
    }
}