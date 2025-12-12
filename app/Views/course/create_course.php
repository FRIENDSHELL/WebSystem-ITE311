<!DOCTYPE html>
<html>
<head>
    <title>Create Course</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #ffe6f2; /* soft pink */
        }
        .card {
            border-radius: 15px;
            border: none;
        }
        .card-header {
            background-color: #ff66a3; /* darker pink */
            color: white;
            border-radius: 15px 15px 0 0;
        }
        .btn-pink {
            background-color: #ff66a3;
            color: white;
        }
        .btn-pink:hover {
            background-color: #ff3385;
            color: white;
        }
        .form-control, .form-select {
            border-radius: 10px;
        }
    </style>
</head>

<body>

<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-header text-center">
            <h3 class="m-0">🎀 Create New Course</h3>
        </div>

        <div class="card-body p-4">
            
            <form action="/course/store" method="POST">
                <?= csrf_field() ?>

                <!-- Course Name -->
                <div class="mb-3">
                    <label class="form-label">Course Name</label>
                    <input type="text" name="course_name" class="form-control" required placeholder="Enter course name">
                </div>

                <!-- Semester -->
                <div class="mb-3">
                    <label class="form-label">Semester</label>
                    <select name="semester" class="form-select">
                        <option>1st Semester</option>
                        <option>2nd Semester</option>
                        <option>Summer</option>
                    </select>
                </div>

                <!-- Term -->
                <div class="mb-3">
                    <label class="form-label">Term</label>
                    <select name="term" class="form-select">
                        <option>Prelim</option>
                        <option>Midterm</option>
                        <option>Finals</option>
                    </select>
                </div>

                <!-- School Year -->
                <div class="mb-3">
                    <label class="form-label">School Year</label>
                    <input type="text" name="school_year" class="form-control" placeholder="2024-2025">
                </div>

                <!-- Description -->
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="course_description" class="form-control" rows="3" placeholder="Short course description"></textarea>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-pink px-4">Save Course</button>
                    <a href="/course" class="btn btn-secondary px-4">Back</a>
                </div>

            </form>

        </div>
    </div>
</div>

</body>
</html>
