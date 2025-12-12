<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TeacherCourseSeeder extends Seeder
{
    public function run()
    {
        // Get teacher user ID
        $teacherQuery = $this->db->query("SELECT id FROM users WHERE role = 'teacher' LIMIT 1");
        $teacher = $teacherQuery->getRow();
        
        if (!$teacher) {
            echo "❌ No teacher found. Creating teacher user first.\n";
            return;
        }
        
        $teacherId = $teacher->id;
        
        // Sample courses for the teacher
        $courses = [
            [
                'course_id' => 'ITE311',
                'course_name' => 'Web Systems and Technologies',
                'title' => 'Web Systems and Technologies',
                'course_code' => 'ITE311',
                'description' => 'Introduction to web development, HTML, CSS, JavaScript, and server-side programming.',
                'user_id' => $teacherId,
                'semester' => 'First Semester',
                'term' => '1st Term',
                'school_year' => '2024-2025',
                'class_schedule' => 'Monday,Wednesday,Friday',
                'time_start' => '08:00:00',
                'time_end' => '10:00:00',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'course_id' => 'CS101',
                'course_name' => 'Introduction to Programming',
                'title' => 'Introduction to Programming',
                'course_code' => 'CS101',
                'description' => 'Basic programming concepts using Python and problem-solving techniques.',
                'user_id' => $teacherId,
                'semester' => 'First Semester',
                'term' => '1st Term',
                'school_year' => '2024-2025',
                'class_schedule' => 'Tuesday,Thursday',
                'time_start' => '10:00:00',
                'time_end' => '12:00:00',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'course_id' => 'MATH201',
                'course_name' => 'Discrete Mathematics',
                'title' => 'Discrete Mathematics',
                'course_code' => 'MATH201',
                'description' => 'Mathematical foundations for computer science including logic, sets, and algorithms.',
                'user_id' => $teacherId,
                'semester' => 'Second Semester',
                'term' => '2nd Term',
                'school_year' => '2024-2025',
                'class_schedule' => 'Monday,Wednesday,Friday',
                'time_start' => '13:00:00',
                'time_end' => '15:00:00',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        ];
        
        // Insert new courses (update existing ones if they exist)
        foreach ($courses as $course) {
            $existing = $this->db->table('courses')->where('course_id', $course['course_id'])->get()->getRow();
            
            if ($existing) {
                // Update existing course to assign to teacher
                $this->db->table('courses')->where('course_id', $course['course_id'])->update($course);
            } else {
                // Insert new course
                $this->db->table('courses')->insert($course);
            }
        }
        
        echo "✅ Teacher courses seeded successfully!\n";
        echo "Created " . count($courses) . " courses assigned to teacher ID: $teacherId\n";
    }
}
