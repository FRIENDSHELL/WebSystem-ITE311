<?php

namespace App\Controllers;

use App\Models\MaterialModel;
use App\Models\EnrollmentModel;
use App\Models\NotificationModel;
use CodeIgniter\Controller;

class Materials extends BaseController
{
    protected $helpers = ['form', 'url', 'filesystem'];

    /**
     * 🔹 Upload Material for a Course
     */
    public function upload($course_id = null)
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please log in first.');
        }

        // Only admin/instructor/teacher can upload
        $userRole = session()->get('role');
        if ($userRole !== 'admin' && $userRole !== 'instructor' && $userRole !== 'teacher') {
            return redirect()->to('/dashboard')->with('error', 'You do not have permission to upload materials.');
        }

        // Validate course_id
        if (empty($course_id)) {
            return redirect()->to('/dashboard')->with('error', 'Invalid course.');
        }

        $db = \Config\Database::connect();
        $course = $db->table('courses')
            ->where('id', $course_id)
            ->get()
            ->getRowArray();

        if (!$course) {
            return redirect()->to('/dashboard')->with('error', 'Course not found.');
        }

        // GET request: Show upload form
        if ($this->request->getMethod() !== 'POST') {
            return view('materials/upload', [
                'course_id' => $course_id,
                'course_title' => $course['title']
            ]);
        }

        // POST request: Handle file upload
        $validationRule = [
            'file' => [
                'label' => 'File',
                'rules' => [
                    'uploaded[file]',
                    'max_size[file,10240]', // 10MB max
                    'ext_in[file,pdf,doc,docx,ppt,pptx,txt,zip,rar]',
                ],
            ],
        ];

        if (!$this->validate($validationRule)) {
            return redirect()->back()
                ->with('error', 'File validation failed: ' . implode(', ', $this->validator->getErrors()))
                ->withInput();
        }

        $file = $this->request->getFile('file');

        if (!$file->isValid()) {
            return redirect()->back()
                ->with('error', 'File upload error: ' . $file->getErrorString())
                ->withInput();
        }

        // Create upload directory if it doesn't exist
        $uploadPath = WRITEPATH . 'uploads/materials/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Generate unique filename
        $newName = $file->getRandomName();
        
        try {
            // Move file to upload directory
            $file->move($uploadPath, $newName);

            // Save to database
            $materialModel = new MaterialModel();
            $data = [
                'course_id' => $course_id,
                'file_name' => $file->getClientName(),
                'file_path' => $newName,
            ];

            if ($materialModel->insertMaterial($data)) {
                $this->notifyEnrolledStudents($course['id'], $course['title'], $file->getClientName());
                return redirect()->to('/dashboard')
                    ->with('success', 'Material uploaded successfully!');
            } else {
                // Delete uploaded file if database insert fails
                unlink($uploadPath . $newName);
                return redirect()->back()
                    ->with('error', 'Failed to save material to database.')
                    ->withInput();
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Upload error: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * 🔹 Download Material
     */
    public function download($material_id = null)
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please log in first.');
        }

        if (empty($material_id)) {
            return redirect()->to('/dashboard')->with('error', 'Invalid material.');
        }

        $materialModel = new MaterialModel();
        $material = $materialModel->getMaterialById($material_id);

        if (!$material) {
            return redirect()->to('/dashboard')->with('error', 'Material not found.');
        }

        // Check if user is enrolled in the course (or is admin/instructor/teacher)
        $userId = session()->get('id');
        $userRole = session()->get('role');

        if ($userRole !== 'admin' && $userRole !== 'instructor' && $userRole !== 'teacher') {
            // Check enrollment
            $enrollmentModel = new EnrollmentModel();
            if (!$enrollmentModel->isAlreadyEnrolled($userId, $material['course_id'])) {
                return redirect()->to('/dashboard')
                    ->with('error', 'You must be enrolled in this course to download materials.');
            }
        }

        // Download the file
        $filePath = WRITEPATH . 'uploads/materials/' . $material['file_path'];

        if (!file_exists($filePath)) {
            return redirect()->to('/dashboard')
                ->with('error', 'File not found on server.');
        }

        return $this->response->download($filePath, null)
            ->setFileName($material['file_name']);
    }

    /**
     * 🔹 Delete Material
     */
    public function delete($material_id = null)
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please log in first.');
        }

        // Only admin/instructor/teacher can delete
        $userRole = session()->get('role');
        if ($userRole !== 'admin' && $userRole !== 'instructor' && $userRole !== 'teacher') {
            return redirect()->to('/dashboard')->with('error', 'You do not have permission to delete materials.');
        }

        if (empty($material_id)) {
            return redirect()->to('/dashboard')->with('error', 'Invalid material.');
        }

        $materialModel = new MaterialModel();
        $material = $materialModel->getMaterialById($material_id);

        if (!$material) {
            return redirect()->to('/dashboard')->with('error', 'Material not found.');
        }

        // Delete file from filesystem
        $filePath = WRITEPATH . 'uploads/materials/' . $material['file_path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Delete from database
        if ($materialModel->deleteMaterial($material_id)) {
            return redirect()->to('/dashboard')
                ->with('success', 'Material deleted successfully!');
        } else {
            return redirect()->to('/dashboard')
                ->with('error', 'Failed to delete material.');
        }
    }

    /**
     * 🔹 View Materials for a Course (Student View)
     */
    public function view($course_id = null)
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please log in first.');
        }

        if (empty($course_id)) {
            return redirect()->to('/dashboard')->with('error', 'Invalid course.');
        }

        $userId = session()->get('id');
        $userRole = session()->get('role');

        // Check if user is enrolled (or is admin/instructor/teacher)
        if ($userRole !== 'admin' && $userRole !== 'instructor' && $userRole !== 'teacher') {
            $enrollmentModel = new EnrollmentModel();
            if (!$enrollmentModel->isAlreadyEnrolled($userId, $course_id)) {
                return redirect()->to('/dashboard')
                    ->with('error', 'You must be enrolled in this course to view materials.');
            }
        }

        // Get course info
        $db = \Config\Database::connect();
        $course = $db->table('courses')
            ->where('id', $course_id)
            ->get()
            ->getRowArray();

        if (!$course) {
            return redirect()->to('/dashboard')->with('error', 'Course not found.');
        }

        // Get materials
        $materialModel = new MaterialModel();
        $materials = $materialModel->getMaterialsByCourse($course_id);

        return view('materials/view', [
            'course' => $course,
            'materials' => $materials,
            'user_role' => $userRole
        ]);
    }

    /**
     * Notify enrolled students that a new material is available.
     */
    protected function notifyEnrolledStudents(int $courseId, string $courseTitle, string $fileName): void
    {
        $enrollmentModel = new EnrollmentModel();
        $userIds = $enrollmentModel->getUserIdsByCourse($courseId);

        if (empty($userIds)) {
            return;
        }

        $notificationModel = new NotificationModel();
        $message = sprintf(
            "New material \"%s\" has been uploaded for %s.",
            $fileName,
            $courseTitle
        );

        $notificationModel->createNotifications($userIds, $message);
    }
}
