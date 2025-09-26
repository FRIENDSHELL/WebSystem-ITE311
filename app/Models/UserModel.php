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
        // Hash password before saving (centralized dito na lang)
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

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
        // 🔹 Placeholder lang muna, depende sa role pwede mo dagdagan
        if ($role === 'admin') {
            return [
                'total_users'        => $this->countAll(),
                'total_projects'     => 5,
                'total_notifications'=> 3,
                'my_courses'         => 0,
                'my_notifications'   => 0,
            ];
        } elseif ($role === 'teachers') {
            return [
                'total_users'        => 0,
                'total_projects'     => 2,
                'total_notifications'=> 1,
                'my_courses'         => 3,
                'my_notifications'   => 2,
            ];
        } else { // student
            return [
                'total_users'        => 0,
                'total_projects'     => 0,
                'total_notifications'=> 0,
                'my_courses'         => 2,
                'my_notifications'   => 1,
            ];
        }
    }
}
