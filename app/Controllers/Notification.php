<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\NotificationModel;

class Notifications extends BaseController
{
    public function get()
    {
        $userId = session()->get('id');

        if (!$userId) {
            return $this->response->setStatusCode(401)->setJSON([
                'message' => 'Unauthorized'
            ]);
        }

        $notif = new NotificationModel();

        return $this->response->setJSON([
            'unread' => $notif->getUnreadCount($userId),
            'list'   => $notif->getNotificationsForUser($userId)
        ]);
    }

    public function mark_as_read($id)
    {
        $userId = session()->get('id');

        if (!$userId) {
            return $this->response->setStatusCode(401)->setJSON([
                'message' => 'Unauthorized'
            ]);
        }

        $notif = new NotificationModel();
        $notif->markAsRead($id, $userId);

        return $this->response->setJSON([
            'status'    => 'success',
            'csrfToken' => csrf_hash()
        ]);
    }
}
