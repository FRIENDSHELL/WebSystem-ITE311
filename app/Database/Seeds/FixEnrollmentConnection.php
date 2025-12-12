<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FixEnrollmentConnection extends Seeder
{
    public function run()
    {
        echo "🔧 Fixing enrollment connections...\n\n";
        
        // Get the teacher account
        $teacherQuery = $this->db->query("SELECT id, name, email FROM users WHERE email = 'teacher@example.com'");
        $teacher = $teacherQuery->getRow();
        
        if (!$teacher) {
            echo "❌ Teacher account not found. Creating teacher account...\n";
            
            $teacherData = [
                'name' => 'Test Teacher',
                'email' => 'teacher@example.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'role' => 'teacher',
            ];
            
            $this->db->table('users')->insert($teacherData);
            
            $teacherQuery = $this->db->query("SELECT id, name, email FROM users WHERE email = 'teacher@example.com'");
            $teacher = $teacherQuery->getRow();
        }
        
        echo "✅ Teacher found: {$teacher->name} (ID: {$teacher->id})\n";
        
        // Update all courses to belong to this teacher
        $this->db->query("UPDATE courses SET user_id = ? WHERE user_id != ?", [$teacher->id, $teacher->id]);
        
        // Check courses owned by teacher
        $coursesQuery = $this->db->query("SELECT id, course_name FROM courses WHERE user_id = ?", [$teacher->id]);
        $courses = $coursesQuery->getResult();
        
        echo "✅ Courses assigned to teacher: " . count($courses) . "\n";
        foreach($courses as $course) {
            echo "   - {$course->course_name} (ID: {$course->id})\n";
        }
        
        if (empty($courses)) {
            echo "❌ No courses found. Creating a test course...\n";
            
            $courseData = [
                'course_id' => 'ITE313',
                'course_name' => 'Systems Integration and Architecture',
                'user_id' => $teacher->id,
                'description' => 'Test course for enrollment approval',
            ];
            
            $this->db->table('courses')->insert($courseData);
            
            $coursesQuery = $this->db->query("SELECT id, course_name FROM courses WHERE user_id = ?", [$teacher->id]);
            $courses = $coursesQuery->getResult();
            
            echo "✅ Created test course: {$courses[0]->course_name}\n";
        }
        
        // Get first course for enrollment
        $firstCourse = $courses[0];
        
        // Create or update student
        $studentQuery = $this->db->query("SELECT id FROM users WHERE email = 'john.doe@student.edu'");
        $student = $studentQuery->getRow();
        
        if (!$student) {
            $studentData = [
                'name' => 'John Michael Doe',
                'email' => 'john.doe@student.edu',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'role' => 'student',
            ];
            
            $this->db->table('users')->insert($studentData);
            
            $studentQuery = $this->db->query("SELECT id FROM users WHERE email = 'john.doe@student.edu'");
            $student = $studentQuery->getRow();
            
            echo "✅ Created student: john.doe@student.edu\n";
        } else {
            echo "✅ Student found: john.doe@student.edu (ID: {$student->id})\n";
        }
        
        // Clear existing test enrollments
        $this->db->query("DELETE FROM enrollments WHERE student_id IN ('TEST2025001', '2024001', '2024002', '2024003')");
        
        // Create fresh enrollment request
        $enrollmentData = [
            'user_id' => $student->id,
            'course_id' => $firstCourse->id,
            'student_id' => 'TEST2025001',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'middle_name' => 'Michael',
            'age' => 20,
            'birth_date' => '2004-03-15',
            'gender' => 'Male',
            'contact_number' => '+63 912 345 6789',
            'email_address' => 'john.doe@student.edu',
            'address' => '123 Main Street, Quezon City, Metro Manila',
            'guardian_name' => 'Robert Doe',
            'guardian_contact' => '+63 912 345 6790',
            'year_level' => '2nd Year',
            'program' => 'Bachelor of Science in Information Technology',
            'enrollment_status' => 'Pending',
            'enrollment_date' => date('Y-m-d H:i:s'),
        ];
        
        $this->db->table('enrollments')->insert($enrollmentData);
        
        echo "✅ Created enrollment request:\n";
        echo "   - Student: John Doe (john.doe@student.edu)\n";
        echo "   - Course: {$firstCourse->course_name} (ID: {$firstCourse->id})\n";
        echo "   - Teacher: {$teacher->name} (ID: {$teacher->id})\n";
        echo "   - Status: Pending\n\n";
        
        // Verify the connection
        $verifyQuery = $this->db->query("
            SELECT e.*, c.course_name, c.user_id as teacher_id, u.name as teacher_name
            FROM enrollments e
            JOIN courses c ON c.id = e.course_id
            JOIN users u ON u.id = c.user_id
            WHERE e.enrollment_status = 'Pending'
        ");
        
        $pendingEnrollments = $verifyQuery->getResult();
        
        echo "🔍 Verification - Pending enrollments visible to teacher:\n";
        foreach($pendingEnrollments as $enrollment) {
            echo "   - {$enrollment->first_name} {$enrollment->last_name} → {$enrollment->course_name} → Teacher: {$enrollment->teacher_name}\n";
        }
        
        echo "\n🎯 Ready for testing!\n";
        echo "1. Login as teacher: teacher@example.com / password123\n";
        echo "2. Go to: http://localhost/ITE311-EGARAN/teacher/dashboard\n";
        echo "3. You should see 'Pending Approvals: " . count($pendingEnrollments) . "'\n";
        echo "4. Click the baby pink section to approve enrollments\n";
    }
}