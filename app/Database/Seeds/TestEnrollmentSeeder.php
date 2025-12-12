<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TestEnrollmentSeeder extends Seeder
{
    public function run()
    {
        // Get teacher and course IDs (use the teacher we created)
        $teacherQuery = $this->db->query("SELECT id FROM users WHERE email = 'teacher@example.com'");
        $teacher = $teacherQuery->getRow();
        
        if (!$teacher) {
            echo "❌ No teacher found. Please ensure a teacher user exists.\n";
            return;
        }
        
        $courseQuery = $this->db->query("SELECT id FROM courses WHERE user_id = ? LIMIT 1", [$teacher->id]);
        $course = $courseQuery->getRow();
        
        if (!$course) {
            echo "❌ No courses found for teacher. Please ensure courses are assigned to teacher.\n";
            return;
        }
        
        // Create test student users first
        $studentUsers = [
            [
                'name' => 'John Michael Doe',
                'email' => 'john.doe@student.edu',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'role' => 'student',
            ],
            [
                'name' => 'Jane Anne Smith',
                'email' => 'jane.smith@student.edu',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'role' => 'student',
            ],
            [
                'name' => 'Mark David Johnson',
                'email' => 'mark.johnson@student.edu',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'role' => 'student',
            ]
        ];
        
        // Check if student users already exist, if not create them
        $studentIds = [];
        foreach ($studentUsers as $student) {
            $userQuery = $this->db->query("SELECT id FROM users WHERE email = ?", [$student['email']]);
            $user = $userQuery->getRow();
            if ($user) {
                $studentIds[] = $user->id;
                echo "✓ Student user already exists: {$student['email']}\n";
            } else {
                // Insert new student user
                $this->db->table('users')->insert($student);
                $newUserQuery = $this->db->query("SELECT id FROM users WHERE email = ?", [$student['email']]);
                $newUser = $newUserQuery->getRow();
                if ($newUser) {
                    $studentIds[] = $newUser->id;
                    echo "✓ Created new student user: {$student['email']}\n";
                }
            }
        }
        
        if (count($studentIds) < 3) {
            echo "❌ Failed to create all student users. Created " . count($studentIds) . " out of 3.\n";
            return;
        }
        
        // Sample enrollment requests
        $enrollments = [
            [
                'user_id' => $studentIds[0],
                'course_id' => $course->id,
                'student_id' => '2024001',
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
                'enrollment_date' => date('Y-m-d H:i:s', strtotime('-2 days')),
            ],
            [
                'user_id' => $studentIds[1],
                'course_id' => $course->id,
                'student_id' => '2024002',
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'middle_name' => 'Anne',
                'age' => 19,
                'birth_date' => '2005-07-22',
                'gender' => 'Female',
                'contact_number' => '+63 912 345 6791',
                'email_address' => 'jane.smith@student.edu',
                'address' => '456 Oak Avenue, Makati City, Metro Manila',
                'guardian_name' => 'Mary Smith',
                'guardian_contact' => '+63 912 345 6792',
                'year_level' => '1st Year',
                'program' => 'Bachelor of Science in Computer Science',
                'enrollment_status' => 'Pending',
                'enrollment_date' => date('Y-m-d H:i:s', strtotime('-1 day')),
            ],
            [
                'user_id' => $studentIds[2],
                'course_id' => $course->id,
                'student_id' => '2024003',
                'first_name' => 'Mark',
                'last_name' => 'Johnson',
                'middle_name' => 'David',
                'age' => 21,
                'birth_date' => '2003-11-08',
                'gender' => 'Male',
                'contact_number' => '+63 912 345 6793',
                'email_address' => 'mark.johnson@student.edu',
                'address' => '789 Pine Street, Pasig City, Metro Manila',
                'guardian_name' => 'James Johnson',
                'guardian_contact' => '+63 912 345 6794',
                'year_level' => '3rd Year',
                'program' => 'Bachelor of Science in Information Technology',
                'enrollment_status' => 'Pending',
                'enrollment_date' => date('Y-m-d H:i:s', strtotime('-3 hours')),
            ]
        ];
        
        // Clear existing test enrollments first to avoid duplicates
        $this->db->query("DELETE FROM enrollments WHERE student_id IN ('2024001', '2024002', '2024003')");
        
        // Insert test enrollments
        $this->db->table('enrollments')->insertBatch($enrollments);
        
        echo "✅ Test enrollment requests created successfully!\n";
        echo "Created " . count($studentUsers) . " student users and " . count($enrollments) . " pending enrollment requests.\n";
        echo "Teacher can now see these in the 'Pending Approvals' section.\n";
        echo "Student login credentials: email (john.doe@student.edu, jane.smith@student.edu, mark.johnson@student.edu) / password: password123\n";
    }
}
