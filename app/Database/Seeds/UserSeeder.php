<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            
            [
                 'id'    => 2,
                'name'       => 'Teacher One',
                'email'      => 'teacher@test.com',
                'password'   => password_hash('1234', PASSWORD_DEFAULT),
                'role'       => 'teacher',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id'    => 3,
                'name'       => 'Student One',
                'email'      => 'student@test.com',
                'password'   => password_hash('1234', PASSWORD_DEFAULT),
                'role'       => 'student',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id'    => 4,
                'name'       => 'admin',
                'email'      => 'admin@example.com',
                'password'   => password_hash('admin123', PASSWORD_DEFAULT),
                'role'       => 'admin',
                'created_at' => date('Y-m-d H:i:s'),
            ]
        ];

        $builder = $this->db->table('users');

        foreach ($users as $user) {
            // Check if email exists
            $existing = $builder->where('email', $user['email'])->get()->getRowArray();

            if ($existing) {
                // Prevent overwriting created_at
                unset($user['created_at']);
                $builder->where('email', $existing['email'])->update($user);
            } else {
                $builder->insert($user);
            }
        }

        echo "✅ User seeding completed with roles: admin, teacher, student.";
    }
}
