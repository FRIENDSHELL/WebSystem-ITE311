<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UpdateCourseOwnership extends Seeder
{
    public function run()
    {
        // Get the teacher user we just created
        $teacherQuery = $this->db->query("SELECT id FROM users WHERE email = 'teacher@example.com'");
        $teacher = $teacherQuery->getRow();
        
        if (!$teacher) {
            echo "❌ Teacher user not found. Please run CreateTeacherUser seeder first.\n";
            return;
        }
        
        // Update all courses to be owned by this teacher
        $updateResult = $this->db->query("UPDATE courses SET user_id = ? WHERE user_id != ?", [$teacher->id, $teacher->id]);
        
        // Get count of updated courses
        $courseCount = $this->db->query("SELECT COUNT(*) as count FROM courses WHERE user_id = ?", [$teacher->id])->getRow()->count;
        
        echo "✅ Updated course ownership successfully!\n";
        echo "Teacher ID: {$teacher->id}\n";
        echo "Courses assigned to teacher: {$courseCount}\n";
        echo "Now the teacher should see pending enrollment requests.\n";
    }
}