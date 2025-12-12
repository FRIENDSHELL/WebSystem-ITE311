<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ForceShowEnrollments extends Seeder
{
    public function run()
    {
        echo "🔍 Checking enrollment visibility...\n\n";
        
        // Get teacher
        $teacherQuery = $this->db->query("SELECT id, name, email FROM users WHERE email = 'teacher@example.com'");
        $teacher = $teacherQuery->getRow();
        
        if (!$teacher) {
            echo "❌ Teacher not found!\n";
            return;
        }
        
        echo "✅ Teacher: {$teacher->name} (ID: {$teacher->id})\n";
        
        // Check teacher's courses
        $coursesQuery = $this->db->query("SELECT id, course_name FROM courses WHERE user_id = ?", [$teacher->id]);
        $courses = $coursesQuery->getResult();
        
        echo "✅ Teacher's courses: " . count($courses) . "\n";
        
        // Check all pending enrollments
        $allPendingQuery = $this->db->query("SELECT COUNT(*) as count FROM enrollments WHERE enrollment_status = 'Pending'");
        $allPendingCount = $allPendingQuery->getRow()->count;
        
        echo "✅ Total pending enrollments in system: {$allPendingCount}\n";
        
        // Check enrollments for teacher's courses
        if (!empty($courses)) {
            $courseIds = array_column($courses, 'id');
            $courseIdsStr = implode(',', $courseIds);
            
            $teacherPendingQuery = $this->db->query("
                SELECT COUNT(*) as count 
                FROM enrollments 
                WHERE enrollment_status = 'Pending' 
                AND course_id IN ({$courseIdsStr})
            ");
            $teacherPendingCount = $teacherPendingQuery->getRow()->count;
            
            echo "✅ Pending enrollments for teacher's courses: {$teacherPendingCount}\n";
        }
        
        // Create a fresh enrollment that definitely belongs to teacher
        if (!empty($courses)) {
            $firstCourse = $courses[0];
            
            // Get or create student
            $studentQuery = $this->db->query("SELECT id FROM users WHERE email = 'demo.student@test.com'");
            $student = $studentQuery->getRow();
            
            if (!$student) {
                $studentData = [
                    'name' => 'Demo Student',
                    'email' => 'demo.student@test.com',
                    'password' => password_hash('password123', PASSWORD_DEFAULT),
                    'role' => 'student',
                ];
                
                $this->db->table('users')->insert($studentData);
                
                $studentQuery = $this->db->query("SELECT id FROM users WHERE email = 'demo.student@test.com'");
                $student = $studentQuery->getRow();
            }
            
            // Clear existing demo enrollment
            $this->db->query("DELETE FROM enrollments WHERE student_id = 'DEMO2025'");
            
            // Create fresh enrollment
            $enrollmentData = [
                'user_id' => $student->id,
                'course_id' => $firstCourse->id,
                'student_id' => 'DEMO2025',
                'first_name' => 'Demo',
                'last_name' => 'Student',
                'middle_name' => 'Test',
                'age' => 21,
                'birth_date' => '2003-05-10',
                'gender' => 'Female',
                'contact_number' => '+63 912 345 6789',
                'email_address' => 'demo.student@test.com',
                'address' => '456 Demo Street, Test City',
                'guardian_name' => 'Demo Guardian',
                'guardian_contact' => '+63 912 345 6790',
                'year_level' => '3rd Year',
                'program' => 'Bachelor of Science in Computer Science',
                'enrollment_status' => 'Pending',
                'enrollment_date' => date('Y-m-d H:i:s'),
            ];
            
            $this->db->table('enrollments')->insert($enrollmentData);
            
            echo "✅ Created fresh enrollment:\n";
            echo "   - Student: Demo Student\n";
            echo "   - Course: {$firstCourse->course_name} (ID: {$firstCourse->id})\n";
            echo "   - Teacher: {$teacher->name}\n";
            echo "   - Status: Pending\n";
        }
        
        // Final verification
        $finalQuery = $this->db->query("
            SELECT e.first_name, e.last_name, c.course_name, u.name as teacher_name
            FROM enrollments e
            JOIN courses c ON c.id = e.course_id
            JOIN users u ON u.id = c.user_id
            WHERE e.enrollment_status = 'Pending'
            ORDER BY e.enrollment_date DESC
        ");
        
        $finalEnrollments = $finalQuery->getResult();
        
        echo "\n🎯 All pending enrollments with teacher assignments:\n";
        foreach($finalEnrollments as $enrollment) {
            echo "   - {$enrollment->first_name} {$enrollment->last_name} → {$enrollment->course_name} → Teacher: {$enrollment->teacher_name}\n";
        }
        
        echo "\n✅ Ready! Now login as teacher and check dashboard.\n";
        echo "URL: http://localhost/ITE311-EGARAN/teacher/dashboard\n";
        echo "Login: teacher@example.com / password123\n";
    }
}