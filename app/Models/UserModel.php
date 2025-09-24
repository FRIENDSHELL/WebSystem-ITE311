<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';

    protected $allowedFields = ['name', 'email', 'password', 'role', 'created_at', 'updated_at'];

    protected $useTimestamps = true; // automatically fills created_at & updated_at

    public function createAccount(array $data)
    {
        // Hash password before saving
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        $this->insert($data);

        // Return the new user's ID
        return $this->getInsertID();
    }

    public function findUserByEmail($email)
    {
        return $this->where('email', $email)->first();
    }

    public function getDashboardStats($role, $userId)
    {
        // You can implement role-based stats later
        return [
            'total_users'        => 0,
            'total_projects'     => 0,
            'total_notifications'=> 0,
            'my_courses'         => 0,
            'my_notifications'   => 0,
        ];
    }
}
