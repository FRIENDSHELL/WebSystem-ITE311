<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'name'       => 'Admin User',
                'email'      => 'admin@test.com',
                'password'   => password_hash('1234', PASSWORD_DEFAULT),
                'role'       => 'admin',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Instructor One',
                'email'      => 'instructor@test.com',
                'password'   => password_hash('1234', PASSWORD_DEFAULT),
                'role'       => 'instructor',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Student One',
                'email'      => 'student@test.com',
                'password'   => password_hash('1234', PASSWORD_DEFAULT),
                'role'       => 'student',
                'created_at' => date('Y-m-d H:i:s'),
            ],
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

        echo "✅ User seeding completed with roles: admin, instructor, student.";
    }
}
