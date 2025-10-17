<?php namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AnnouncementModel;

class Announcement extends BaseController
{
    public function index()
    {
        $model = new AnnouncementModel();
        $announcements = $model->orderBy('created_at', 'DESC')->findAll();

        echo view('templates/header'); // if you use template header/footer
        echo view('announcements', ['announcements' => $announcements]);
        echo view('templates/footer');
    }
}
