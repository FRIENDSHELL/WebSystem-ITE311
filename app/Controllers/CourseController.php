<?php

namespace App\Controllers;

use App\Models\CourseModel;
use CodeIgniter\Controller;

class CourseController extends Controller
{
    /* ============================================
       COURSE LIST + SEARCH FILTER
    ============================================ */
    public function index()
    {
        $search = $this->request->getGet('search');
        $year   = $this->request->getGet('year');

        $courseModel = new CourseModel();
        $builder = $courseModel->builder();

        // Search by course name
        if (!empty($search)) {
            $builder->like('course_name', $search);
        }

        // Filter by school year
        if (!empty($year)) {
            $builder->like('school_year', $year);
        }

        $data['courses'] = $builder->get()->getResultArray();

        return view('course/course_list', $data);
    }

    /* ============================================
       SHOW CREATE COURSE FORM
    ============================================ */
    public function create()
    {
        return view('course/create_course');
    }

    /* ============================================
       STORE COURSE DATA
    ============================================ */
    public function store()
    {
        helper(['form', 'url']);

        $validation = [
            'course_name'  => 'required|min_length[2]|max_length[100]',
            'semester'     => 'required',
            'term'         => 'required',
            'school_year'  => 'required',
        ];

        if (!$this->validate($validation)) {
            return view('course/create_course', [
                'validation' => $this->validator
            ]);
        }

        $courseModel = new CourseModel();

        $data = [
            'course_name'        => $this->request->getPost('course_name'),
            'semester'           => $this->request->getPost('semester'),
            'term'               => $this->request->getPost('term'),
            'school_year'        => $this->request->getPost('school_year'),
            'course_description' => $this->request->getPost('course_description')
        ];

        $courseModel->insert($data);

        return redirect()->to('/course')->with('success', 'Course added successfully!');
    }

    /* ============================================
       SHOW EDIT FORM
    ============================================ */
    public function edit($id)
    {
        $courseModel = new CourseModel();
        $data['course'] = $courseModel->find($id);

        return view('course/edit_course', $data);
    }

    /* ============================================
       UPDATE COURSE DATA
    ============================================ */
    public function update($id)
    {
        helper(['form', 'url']);

        $courseModel = new CourseModel();

        $data = [
            'course_name'        => $this->request->getPost('course_name'),
            'semester'           => $this->request->getPost('semester'),
            'term'               => $this->request->getPost('term'),
            'school_year'        => $this->request->getPost('school_year'),
            'course_description' => $this->request->getPost('course_description')
        ];

        $courseModel->update($id, $data);

        return redirect()->to('/course')->with('success', 'Course updated successfully!');
    }

    /* ============================================
       DELETE COURSE
    ============================================ */
    public function delete($id)
    {
        $courseModel = new CourseModel();
        $courseModel->delete($id);

        return redirect()->to('/course')->with('success', 'Course deleted successfully!');
    }
}
