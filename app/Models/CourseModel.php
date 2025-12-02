<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table      = 'courses';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'title',
        'description',
        'user_id',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;

    /**
     * Return courses filtered by the optional search term.
     */
    public function search(?string $term = null): array
    {
        $builder = $this->builder()
            ->orderBy('title', 'ASC');

        if (!empty($term)) {
            $builder->groupStart()
                ->like('title', $term)
                ->orLike('description', $term)
                ->groupEnd();
        }

        return $builder->get()->getResultArray();
    }
}

