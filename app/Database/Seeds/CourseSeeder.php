<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $data = [
            [
                'title'       => 'ITE 311 - Web Systems and Technologies',
                'description' => 'Covers client-side and server-side web development, including HTML, CSS, JavaScript, and PHP.',
                'user_id'     => 1, // Make sure user with id=1 exists in `users` table
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'title'       => 'ITE 312 - Database Management Systems',
                'description' => 'Teaches relational database concepts, SQL, normalization, and data modeling.',
                'user_id'     => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'title'       => 'ITE 313 - Systems Integration and Architecture',
                'description' => 'Focuses on enterprise architecture, middleware, and system integration techniques.',
                'user_id'     => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
        ];

        $this->db->table('courses')->insertBatch($data);
    }
}
