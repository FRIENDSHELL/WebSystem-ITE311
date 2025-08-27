<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My CodeIgniter System</title>


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<head>
    <style>
        .navbar-pink {
            background-color: pink;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-pink">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">LMS</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav"> 
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= base_url('index')?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('about')?>">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?=base_url('contact')?>">Contact</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

</body>
</html>
