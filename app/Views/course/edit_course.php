<!DOCTYPE html>
<html>
<head>
    <title>Edit Course</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #ffeaf4; /* soft baby pink */
        }
        .card {
            border-radius: 18px;
            border: none;
        }
        .card-header {
            background-color: #ffb6d9;
            color: white;
            border-radius: 18px 18px 0 0;
        }
        .btn-pink {
            background-color: #ff7fbf;
            color: white;
            border-radius: 10px;
        }
        .btn-pink:hover {
            background-color: #ff5fae;
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
            <h3 class="m-0">🌸 Edit Course</h3>
        </div>

        <div class="card-body p-4">

            <form action="/course/update/<?= $course['id'] ?>" method="POST">
                <?= csrf_field() ?>

                <!-- Course Name -->
                <div class="mb-3">
                    <label class="form-label">Course Name</label>
                    <input type="text" name="course_name" class="form-control"
                        value="<?= esc($course['course_name']) ?>" required>
                </div>

                <!-- Semester -->
                <div class="mb-3">
                    <label class="form-label">Semester</label>
                    <select name="semester" class="form-select">
                        <option <?= $course['semester'] == '1st Semester' ? 'selected' : '' ?>>1st Semester</option>
                        <option <?= $course['semester'] == '2nd Semester' ? 'selected' : '' ?>>2nd Semester</option>
                        <option <?= $course['semester'] == 'Summer' ? 'selected' : '' ?>>Summer</option>
                    </select>
                </div>

                <!-- Term -->
                <div class="mb-3">
                    <label class="form-label">Term</label>
                    <select name="term" class="form-select">
                        <option <?= $course['term'] == 'Prelim' ? 'selected' : '' ?>>Prelim</option>
                        <option <?= $course['term'] == 'Midterm' ? 'selected' : '' ?>>Midterm</option>
                        <option <?= $course['term'] == 'Finals' ? 'selected' : '' ?>>Finals</option>
                    </select>
                </div>

                <!-- School Year -->
                <div class="mb-3">
                    <label class="form-label">School Year</label>
                    <input type="text" name="school_year" class="form-control"
                        value="<?= esc($course['school_year']) ?>" placeholder="2024-2025">
                </div>

                <!-- Description -->
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="course_description" class="form-control" rows="3"><?= esc($course['course_description']) ?></textarea>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-pink px-4">Update Course</button>
                    <a href="/course" class="btn btn-secondary px-4">Back</a>
                </div>

            </form>

        </div>
    </div>
</div>

</body>
</html>
