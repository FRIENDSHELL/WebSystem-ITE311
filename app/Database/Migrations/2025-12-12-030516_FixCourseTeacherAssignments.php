<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixCourseTeacherAssignments extends Migration
{
    public function up()
    {
        // Get the teacher user ID
        $teacherQuery = $this->db->query("SELECT id FROM users WHERE role = 'teacher' LIMIT 1");
        $teacher = $teacherQuery->getRow();
        
        if ($teacher) {
            $teacherId = $teacher->id;
            
            // Update all courses to be assigned to the teacher instead of admin
            $this->db->query("UPDATE courses SET user_id = ? WHERE user_id IS NULL OR user_id IN (SELECT id FROM users WHERE role = 'admin')", [$teacherId]);
            
            echo "✅ All courses are now assigned to teacher (ID: $teacherId)\n";
        } else {
            echo "❌ No teacher user found. Please ensure a teacher user exists.\n";
        }
    }

    public function down()
    {
        // This migration cannot be easily reversed
        // Manual intervention would be required to restore original assignments
    }
}
