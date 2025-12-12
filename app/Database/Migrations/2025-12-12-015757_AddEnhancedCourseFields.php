<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEnhancedCourseFields extends Migration
{
    public function up()
    {
        // Add new fields to courses table for enhanced course creation
        $fields = [
            'course_id' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
                'after' => 'id',
            ],
            'course_name' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
                'after' => 'course_id',
            ],
            'semester' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
                'after' => 'school_year_id',
            ],
            'term' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
                'after' => 'semester',
            ],
            'school_year' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
                'after' => 'term',
            ],
            'class_schedule' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'school_year',
            ],
            'time_start' => [
                'type' => 'TIME',
                'null' => true,
                'after' => 'class_schedule',
            ],
            'time_end' => [
                'type' => 'TIME',
                'null' => true,
                'after' => 'time_start',
            ],
        ];

        $this->forge->addColumn('courses', $fields);
    }

    public function down()
    {
        // Drop the added columns
        $this->forge->dropColumn('courses', [
            'course_id', 
            'course_name', 
            'semester', 
            'term', 
            'school_year', 
            'class_schedule', 
            'time_start', 
            'time_end'
        ]);
    }
}
