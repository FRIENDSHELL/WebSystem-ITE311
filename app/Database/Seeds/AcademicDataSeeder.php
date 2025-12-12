<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AcademicDataSeeder extends Seeder
{
    public function run()
    {
        // Seed Semesters
        $semesterData = [
            [
                'name' => 'Fall 2025',
                'description' => 'Fall semester for academic year 2025-2026',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Spring 2026',
                'description' => 'Spring semester for academic year 2025-2026',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Summer 2026',
                'description' => 'Summer semester for academic year 2025-2026',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('semesters')->insertBatch($semesterData);

        // Seed Terms
        $termData = [
            [
                'name' => 'Midterm',
                'description' => 'Mid-semester examination period',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Finals',
                'description' => 'Final examination period',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Quarter 1',
                'description' => 'First quarter of the semester',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Quarter 2',
                'description' => 'Second quarter of the semester',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('terms')->insertBatch($termData);

        // Seed School Years
        $schoolYearData = [
            [
                'year' => '2025-2026',
                'start_date' => '2025-08-15',
                'end_date' => '2026-05-30',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'year' => '2024-2025',
                'start_date' => '2024-08-15',
                'end_date' => '2025-05-30',
                'is_active' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'year' => '2026-2027',
                'start_date' => '2026-08-15',
                'end_date' => '2027-05-30',
                'is_active' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('school_years')->insertBatch($schoolYearData);

        echo "Academic data seeded successfully!\n";
    }
}