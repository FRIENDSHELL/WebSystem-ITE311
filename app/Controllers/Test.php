<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AnnouncementModel;

class Test extends BaseController
{
    public function announcements()
    {
        $model = new AnnouncementModel();
        $announcements = $model->orderBy('created_at', 'DESC')->findAll();
        
        echo "<h1>Debug: Announcements Test</h1>";
        echo "<p>Total announcements found: " . count($announcements) . "</p>";
        
        if (empty($announcements)) {
            echo "<p style='color: red;'>No announcements found in database!</p>";
        } else {
            echo "<h2>Announcements:</h2>";
            foreach ($announcements as $announcement) {
                echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
                echo "<h3>" . htmlspecialchars($announcement['title']) . "</h3>";
                echo "<p>" . nl2br(htmlspecialchars($announcement['content'])) . "</p>";
                echo "<small>Created: " . $announcement['created_at'] . "</small>";
                echo "</div>";
            }
        }
        
        echo "<hr>";
        echo "<p><a href='/announcements'>Go to Announcements Page</a></p>";
        echo "<p><a href='/login'>Go to Login</a></p>";
    }
}
