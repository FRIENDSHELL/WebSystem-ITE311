<?php

namespace App\Models;
use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table = 'notifications';
    protected $allowedFields = ['user_id', 'message', 'is_read', 'created_at'];

    public function getUnreadCount($userId)
    {
        return $this->where('user_id', $userId)
                    ->where('is_read', 0)
                    ->countAllResults();
    }

    public function getNotificationsForUser($userId)
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll(5);
    }

    public function markAsRead($id, $userId = null)
    {
        $conditions = ['id' => $id];

        if ($userId !== null) {
            $conditions['user_id'] = $userId;
        }

        return $this->where($conditions)
                    ->set(['is_read' => 1])
                    ->update();
    }

    public function markAsUnread($id, $userId = null)
    {
        $conditions = ['id' => $id];

        if ($userId !== null) {
            $conditions['user_id'] = $userId;
        }

        return $this->where($conditions)
                    ->set(['is_read' => 0])
                    ->update();
    }
}
