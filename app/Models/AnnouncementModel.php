<?php namespace App\Models;

use CodeIgniter\Model;

class AnnouncementModel extends Model
{
    protected $table      = 'announcements';
    protected $primaryKey = 'id';
    protected $allowedFields = ['title', 'content', 'created_at'];
    public    $useTimestamps = false; // we will set created_at manually in seeder/controller
}
