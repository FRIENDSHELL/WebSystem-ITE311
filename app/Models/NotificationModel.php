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

    /**
     * Create a single notification entry.
     */
    public function createNotification(int $userId, string $message): bool
    {
        return (bool) $this->insert([
            'user_id'    => $userId,
            'message'    => $message,
            'is_read'    => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Create multiple notifications for the same message.
     */
    public function createNotifications(array $userIds, string $message): bool
    {
        $userIds = array_filter(array_unique(array_map('intval', $userIds)));

        if (empty($userIds)) {
            return true;
        }

        $timestamp = date('Y-m-d H:i:s');

        $batch = array_map(function ($userId) use ($message, $timestamp) {
            return [
                'user_id'    => $userId,
                'message'    => $message,
                'is_read'    => 0,
                'created_at' => $timestamp,
            ];
        }, $userIds);

        return (bool) $this->insertBatch($batch);
    }
}
