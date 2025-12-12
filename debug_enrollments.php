<?php
require_once 'vendor/autoload.php';

// Load CodeIgniter
$app = \Config\Services::codeigniter();
$app->initialize();

// Get database connection
$db = \Config\Database::connect();

echo "=== DEBUGGING ENROLLMENT DATA ===\n\n";

// Check enrollments table
echo "1. All Enrollments:\n";
$enrollments = $db->query("SELECT * FROM enrollments")->getResultArray();
echo "Total enrollments: " . count($enrollments) . "\n";
foreach($enrollments as $enrollment) {
    echo "ID: {$enrollment['id']}, Student: {$enrollment['first_name']} {$enrollment['last_name']}, Status: {$enrollment['enrollment_status']}, Course ID: {$enrollment['course_id']}\n";
}

echo "\n2. All Courses:\n";
$courses = $db->query("SELECT * FROM courses")->getResultArray();
echo "Total courses: " . count($courses) . "\n";
foreach($courses as $course) {
    echo "ID: {$course['id']}, Name: {$course['course_name']}, Teacher ID: {$course['user_id']}\n";
}

echo "\n3. All Users (Teachers):\n";
$teachers = $db->query("SELECT * FROM users WHERE role = 'teacher'")->getResultArray();
echo "Total teachers: " . count($teachers) . "\n";
foreach($teachers as $teacher) {
    echo "ID: {$teacher['id']}, Name: {$teacher['name']}, Email: {$teacher['email']}\n";
}

echo "\n4. Pending Enrollments with Course Details:\n";
$pendingWithCourses = $db->query("
    SELECT e.*, c.course_name, c.user_id as course_teacher_id, u.name as teacher_name 
    FROM enrollments e 
    JOIN courses c ON c.id = e.course_id 
    JOIN users u ON u.id = c.user_id 
    WHERE e.enrollment_status = 'Pending'
")->getResultArray();
echo "Total pending with course details: " . count($pendingWithCourses) . "\n";
foreach($pendingWithCourses as $pending) {
    echo "Student: {$pending['first_name']} {$pending['last_name']}, Course: {$pending['course_name']}, Teacher: {$pending['teacher_name']}\n";
}

echo "\n=== END DEBUG ===\n";
?>