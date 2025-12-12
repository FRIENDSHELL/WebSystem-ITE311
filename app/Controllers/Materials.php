<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MaterialModel;
use App\Models\CourseModel;
use App\Models\EnrollmentModel;

class Materials extends BaseController
{
    protected $helpers = ['form', 'url'];

    /**
     * Display materials list with search
     */
    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to(site_url('login'))->with('error', 'Please log in to access materials.');
        }

        $materialModel = new MaterialModel();
        $userId = session()->get('id');
        $role = session()->get('role');
        $search = $this->request->getGet('search');

        // Get materials based on search or all materials
        if ($search) {
            $materials = $materialModel->searchMaterials($search, $userId, $role);
        } else {
            $materials = $materialModel->getMaterialsWithDetails($userId, $role);
        }

        // Get statistics
        $stats = $materialModel->getMaterialStats($userId, $role);

        $data = [
            'title' => 'Course Materials',
            'materials' => $materials,
            'search' => $search,
            'stats' => $stats,
            'user_name' => session()->get('name'),
            'user_role' => session()->get('role'),
        ];

        return view('materials/index', $data);
    }

    /**
     * Show upload form
     */
    public function upload($courseId = null)
    {
        if (!session()->get('logged_in') || !in_array(session()->get('role'), ['teacher', 'admin'])) {
            return redirect()->to(site_url('login'))->with('error', 'Access denied.');
        }

        $courseModel = new CourseModel();
        $userId = session()->get('id');
        $role = session()->get('role');

        // Get courses based on role with additional information
        if ($role === 'teacher') {
            $courses = $courseModel->select('courses.*, school_years.year as year_label, semesters.name as semester_name, terms.name as term_name')
                ->join('school_years', 'school_years.id = courses.school_year_id', 'left')
                ->join('semesters', 'semesters.id = courses.semester_id', 'left')
                ->join('terms', 'terms.id = courses.term_id', 'left')
                ->where('courses.user_id', $userId)
                ->orderBy('courses.course_name', 'ASC')
                ->findAll();
        } else {
            $courses = $courseModel->select('courses.*, school_years.year as year_label, semesters.name as semester_name, terms.name as term_name, users.name as teacher_name')
                ->join('school_years', 'school_years.id = courses.school_year_id', 'left')
                ->join('semesters', 'semesters.id = courses.semester_id', 'left')
                ->join('terms', 'terms.id = courses.term_id', 'left')
                ->join('users', 'users.id = courses.user_id', 'left')
                ->orderBy('courses.course_name', 'ASC')
                ->findAll();
        }

        $data = [
            'title' => 'Upload Course Material',
            'courses' => $courses,
            'selected_course_id' => $courseId,
            'user_name' => session()->get('name'),
            'user_role' => session()->get('role'),
        ];

        return view('materials/upload', $data);
    }

    /**
     * Process file upload
     */
    public function store()
    {
        if (!session()->get('logged_in') || !in_array(session()->get('role'), ['teacher', 'admin'])) {
            return redirect()->to(site_url('login'))->with('error', 'Access denied.');
        }

        // Log upload attempt
        log_message('info', 'Material upload attempt by user: ' . session()->get('id') . ' (' . session()->get('role') . ')');

        // Validation rules
        $validationRules = [
            'course_id' => [
                'label' => 'Course',
                'rules' => 'required|integer',
                'errors' => [
                    'required' => 'Please select a course.',
                    'integer' => 'Invalid course selection.'
                ]
            ],
            'title' => [
                'label' => 'Title',
                'rules' => 'required|min_length[3]|max_length[255]',
                'errors' => [
                    'required' => 'Material title is required.',
                    'min_length' => 'Title must be at least 3 characters.',
                    'max_length' => 'Title cannot exceed 255 characters.'
                ]
            ],
            'description' => [
                'label' => 'Description',
                'rules' => 'permit_empty|max_length[1000]',
                'errors' => [
                    'max_length' => 'Description cannot exceed 1000 characters.'
                ]
            ],
            'material_file' => [
                'label' => 'File',
                'rules' => 'uploaded[material_file]|max_size[material_file,10240]|ext_in[material_file,pdf,doc,docx,ppt,pptx,xls,xlsx,txt,zip,rar,jpg,jpeg,png,gif,mp4,avi,mov]',
                'errors' => [
                    'uploaded' => 'Please select a file to upload.',
                    'max_size' => 'File size cannot exceed 10MB.',
                    'ext_in' => 'File type not allowed. Allowed types: PDF, Word, PowerPoint, Excel, Text, ZIP/RAR, Images, Videos.'
                ]
            ]
        ];

        if (!$this->validate($validationRules)) {
            $errors = $this->validator->getErrors();
            $errorMessages = [];
            foreach ($errors as $field => $message) {
                $errorMessages[] = ucfirst($field) . ': ' . $message;
            }
            return redirect()->back()
                ->withInput()
                ->with('errors', $errorMessages)
                ->with('error', 'Please fix the errors below and try again.');
        }

        $file = $this->request->getFile('material_file');
        
        if (!$file) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'No file was uploaded.');
        }
        
        if ($file->isValid() && !$file->hasMoved()) {
            // Create upload directory if it doesn't exist
            $uploadPath = WRITEPATH . 'uploads/materials/';
            if (!is_dir($uploadPath)) {
                if (!mkdir($uploadPath, 0755, true)) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Failed to create upload directory. Please check permissions.');
                }
            }

            // Generate unique filename
            $newName = $file->getRandomName();
            
            // Move file to upload directory
            if ($file->move($uploadPath, $newName)) {
                $materialModel = new MaterialModel();
                
                // Prepare material data
                $materialData = [
                    'course_id' => $this->request->getPost('course_id'),
                    'user_id' => session()->get('id'),
                    'title' => $this->request->getPost('title'),
                    'description' => $this->request->getPost('description'),
                    'file_name' => $newName,
                    'original_name' => $file->getClientName(),
                    'file_path' => $uploadPath . $newName,
                    'file_size' => $file->getSize(),
                    'file_type' => $file->getClientMimeType(),
                    'upload_date' => date('Y-m-d H:i:s'),
                    'is_active' => 1,
                    'download_count' => 0
                ];

                if ($materialModel->insert($materialData)) {
                    log_message('info', 'Material uploaded successfully: ' . $newName . ' by user: ' . session()->get('id'));
                    return redirect()->to(site_url('materials'))
                        ->with('success', 'Material uploaded successfully!');
                } else {
                    // Delete uploaded file if database insert fails
                    if (file_exists($uploadPath . $newName)) {
                        unlink($uploadPath . $newName);
                    }
                    $dbError = $materialModel->errors();
                    log_message('error', 'Failed to save material to database: ' . json_encode($dbError));
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Failed to save material information. Please try again. Error: ' . json_encode($dbError));
                }
            } else {
                $errors = $file->getErrorString();
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Failed to upload file: ' . $errors);
            }
        } else {
            $errors = $file->getErrorString();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Invalid file or file upload error: ' . $errors);
        }
    }

    /**
     * Download material
     */
    public function download($materialId)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to(site_url('login'))->with('error', 'Please log in to download materials.');
        }

        $materialModel = new MaterialModel();
        $material = $materialModel->find($materialId);

        if (!$material) {
            return redirect()->back()->with('error', 'Material not found.');
        }

        if (!$material['is_active']) {
            return redirect()->back()->with('error', 'Material is not available for download.');
        }

        $userRole = session()->get('role');
        $userId = session()->get('id');

        // For students, verify they are enrolled in the course
        if ($userRole === 'student') {
            $enrollmentModel = new EnrollmentModel();
            $enrollment = $enrollmentModel->where('user_id', $userId)
                ->where('course_id', $material['course_id'])
                ->whereIn('enrollment_status', ['Approved', 'Enrolled'])
                ->first();
            
            if (!$enrollment) {
                return redirect()->back()->with('error', 'You must be enrolled in this course to download materials.');
            }
        }

        // Check if file exists
        if (empty($material['file_path']) || !file_exists($material['file_path'])) {
            return redirect()->back()->with('error', 'File not found on server. Please contact the administrator.');
        }

        // Check if file is readable
        if (!is_readable($material['file_path'])) {
            return redirect()->back()->with('error', 'File is not accessible. Please check file permissions.');
        }

        // Increment download count
        $materialModel->incrementDownloadCount($materialId);

        // Force download with proper headers
        return $this->response->download($material['file_path'], null)
                             ->setFileName($material['original_name']);
    }

    /**
     * View material details
     */
    public function view($materialId)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to(site_url('login'))->with('error', 'Please log in to view materials.');
        }

        $materialModel = new MaterialModel();
        $material = $materialModel->select('materials.*, courses.course_name, courses.course_id as course_code, users.name as uploaded_by')
                                  ->join('courses', 'courses.id = materials.course_id', 'left')
                                  ->join('users', 'users.id = materials.user_id', 'left')
                                  ->where('materials.id', $materialId)
                                  ->first();

        if (!$material || !$material['is_active']) {
            return redirect()->back()->with('error', 'Material not found or not available.');
        }

        $data = [
            'title' => 'Material Details',
            'material' => $material,
            'user_name' => session()->get('name'),
            'user_role' => session()->get('role'),
        ];

        return view('materials/view', $data);
    }

    /**
     * Delete material (Admin/Teacher only)
     */
    public function delete($materialId)
    {
        if (!session()->get('logged_in') || !in_array(session()->get('role'), ['teacher', 'admin'])) {
            return redirect()->to(site_url('login'))->with('error', 'Access denied.');
        }

        $materialModel = new MaterialModel();
        $material = $materialModel->find($materialId);

        if (!$material) {
            return redirect()->back()->with('error', 'Material not found.');
        }

        // Check if teacher owns the material
        if (session()->get('role') === 'teacher' && $material['user_id'] != session()->get('id')) {
            return redirect()->back()->with('error', 'You can only delete your own materials.');
        }

        // Soft delete (set is_active to 0)
        if ($materialModel->update($materialId, ['is_active' => 0])) {
            return redirect()->back()->with('success', 'Material deleted successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to delete material.');
        }
    }
}