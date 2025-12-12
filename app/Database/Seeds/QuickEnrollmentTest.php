<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class QuickEnrollmentTest extends Seeder
{
    public function run()
    {
        // Get teacher and course
        $teacherQuery = $this->db->query("SELECT id FROM users WHERE email = 'teacher@example.com'");
        $teacher = $teacherQuery->getRow();
        
        if (!$teacher) {
            echo "❌ Teacher not found. Please ensure teacher@example.com exists.\n";
            return;
        }
        
        $courseQuery = $this->db->query("SELECT id FROM courses WHERE user_id = ? LIMIT 1", [$teacher->id]);
        $course = $courseQuery->getRow();
        
        if (!$course) {
            echo "❌ No courses found for teacher.\n";
            return;
        }
        
        // Get or create student
        $studentQuery = $this->db->query("SELECT id FROM users WHERE email = 'test.student@example.com'");
        $student = $studentQuery->getRow();
        
        if (!$student) {
            // Create test student
            $studentData = [
                'name' => 'Test Student',
                'email' => 'test.student@example.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'role' => 'student',
            ];
            $this->db->table('users')->insert($studentData);
            
            $studentQuery = $this->db->query("SELECT id FROM users WHERE email = 'test.student@example.com'");
            $student = $studentQuery->getRow();
        }
        
        // Create enrollment request
        $enrollmentData = [
            'user_id' => $student->id,
            'course_id' => $course->id,
            'student_id' => 'TEST2025001',
            'first_name' => 'Test',
            'last_name' => 'Student',
            'middle_name' => 'Demo',
            'age' => 20,
            'birth_date' => '2004-01-15',
            'gender' => 'Male',
            'contact_number' => '+63 912 345 6789',
            'email_address' => 'test.student@example.com',
            'address' => '123 Test Street, Test City',
            'guardian_name' => 'Test Guardian',
            'guardian_contact' => '+63 912 345 6790',
            'year_level' => '2nd Year',
            'program' => 'Bachelor of Science in Information Technology',
            'enrollment_status' => 'Pending',
            'enrollment_date' => date('Y-m-d H:i:s'),
        ];
        
        // Clear existing test enrollment
        $this->db->query("DELETE FROM enrollments WHERE student_id = 'TEST2025001'");
        
        // Insert new enrollment
        $this->db->table('enrollments')->insert($enrollmentData);
        
        echo "✅ Test enrollment request created successfully!\n";
        echo "Student: Test Student (test.student@example.com)\n";
        echo "Status: Pending\n";
        echo "Teacher can now approve this request in their dashboard.\n";
        echo "\nTo test:\n";
        echo "1. Login as teacher: teacher@example.com / password123\n";
        echo "2. Go to teacher dashboard\n";
        echo "3. Click 'Pending Approvals' to see the request\n";
        echo "4. Click 'Approve' to approve the enrollment\n";
    }
}