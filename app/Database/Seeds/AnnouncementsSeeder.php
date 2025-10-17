<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AnnouncementsSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'title' => 'Welcome to the New Academic Year!',
                'content' => 'We are excited to welcome all students, teachers, and staff to the new academic year. Please make sure to check your course schedules and familiarize yourself with the updated policies.',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Important: Library Hours Update',
                'content' => 'The library will now be open from 7:00 AM to 9:00 PM on weekdays and 8:00 AM to 6:00 PM on weekends. Please note that the library will be closed on national holidays.',
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
            ],
        ];

        $this->db->table('announcements')->insertBatch($data);
    }
}
