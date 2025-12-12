<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateCoursesTable extends Migration
{
    public function up()
    {
        // Add new fields to courses table
        $fields = [
            'semester_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'user_id',
            ],
            'term_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'semester_id',
            ],
            'school_year_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'term_id',
            ],
            'course_code' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
                'after' => 'title',
            ],
            'credits' => [
                'type' => 'INT',
                'constraint' => 3,
                'default' => 3,
                'after' => 'course_code',
            ],
        ];

        $this->forge->addColumn('courses', $fields);

        // Add foreign key constraints
        $this->forge->addForeignKey('semester_id', 'semesters', 'id', 'SET NULL', 'CASCADE', 'courses');
        $this->forge->addForeignKey('term_id', 'terms', 'id', 'SET NULL', 'CASCADE', 'courses');
        $this->forge->addForeignKey('school_year_id', 'school_years', 'id', 'SET NULL', 'CASCADE', 'courses');
    }

    public function down()
    {
        // Drop foreign keys first
        $this->forge->dropForeignKey('courses', 'courses_semester_id_foreign');
        $this->forge->dropForeignKey('courses', 'courses_term_id_foreign');
        $this->forge->dropForeignKey('courses', 'courses_school_year_id_foreign');

        // Drop columns
        $this->forge->dropColumn('courses', ['semester_id', 'term_id', 'school_year_id', 'course_code', 'credits']);
    }
}