<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Dashboard</title>
</head>
<body>
    <div class="container mt-5">
    <div class="card shadow-sm" style="background-color: #ffe6f0; border-radius: 15px;">
        <div class="card-body text-center">
            <h2 class="mb-3" style="color:#cc6699;"> Dashboard</h2>
            <p>Welcome, <strong><?= session()->get('name')?> </stong> </p>

            <?php if(session()->get('role') == "student"):?>

                
                   
                        
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <div class="card mb-3 shadow-sm" style="background-color: #fff0f5; border-radius: 12px;">
                                        <div class="card-body">
                                            <h5 class="card-title">Subject</h5>
                                            <p class="card-text">Manage your courses and lessons.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card mb-3 shadow-sm" style="background-color: #fff0f5; border-radius: 12px;">
                                        <div class="card-body">
                                            <h5 class="card-title">Quizzes</h5>
                                            <p class="card-text">Track your quizzwes</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            
                    
            

                
            <?php elseif(session()->get('role')== "teacher"):?>
                
               

                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <div class="card mb-3 shadow-sm" style="background-color: #fff0f5; border-radius: 12px;">
                                        <div class="card-body">
                                            <h5 class="card-title">My Courses</h5>
                                            <p class="card-text">View and access your couses na gina tudluan.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card mb-3 shadow-sm" style="background-color: #fff0f5; border-radius: 12px;">
                                        <div class="card-body">
                                            <h5 class="card-title">Notifications</h5>
                                            <p class="card-text">wait sa announcements.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            

            <?php elseif(session()->get('role') == "admin"):?>
                <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card mb-3 shadow-sm" style="background-color: #fff0f5; border-radius: 12px;">
                        <div class="card-body">
                            <h5 class="card-title">Manage Users</h5>
                            <p class="card-text">View and manage all system users.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mb-3 shadow-sm" style="background-color: #fff0f5; border-radius: 12px;">
                        <div class="card-body">
                            <h5 class="card-title">Reports</h5>
                            <p class="card-text">Access system-wide reports and logs.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mb-3 shadow-sm" style="background-color: #fff0f5; border-radius: 12px;">
                        <div class="card-body">
                            <h5 class="card-title">Settings</h5>
                            <p class="card-text">kung may problem ka .</p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif;?>
            <a href="<?= site_url('logout') ?>" 
               class="btn mt-3" 
               style="background-color:#cc6699; color:white; border-radius:10px;">
               Logout
            </a>
        </div>
    </div>
</div>



</body>
</html>