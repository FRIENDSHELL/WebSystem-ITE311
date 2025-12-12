<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CreateTeacherUser extends Seeder
{
    public function run()
    {
        // Check if teacher user already exists
        $existingTeacher = $this->db->query("SELECT id FROM users WHERE email = 'teacher@example.com'")->getRow();
        
        if ($existingTeacher) {
            echo "✓ Teacher user already exists with ID: {$existingTeacher->id}\n";
            return;
        }
        
        // Create teacher user
        $teacherData = [
            'name' => 'Test Teacher',
            'email' => 'teacher@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'role' => 'teacher',
        ];
        
        $this->db->table('users')->insert($teacherData);
        
        // Get the created teacher ID
        $teacherQuery = $this->db->query("SELECT id FROM users WHERE email = 'teacher@example.com'");
        $teacher = $teacherQuery->getRow();
        
        if ($teacher) {
            echo "✅ Teacher user created successfully!\n";
            echo "Email: teacher@example.com\n";
            echo "Password: password123\n";
            echo "Teacher ID: {$teacher->id}\n";
            echo "You can now login as teacher and test the enrollment system.\n";
        } else {
            echo "❌ Failed to create teacher user.\n";
        }
    }
}