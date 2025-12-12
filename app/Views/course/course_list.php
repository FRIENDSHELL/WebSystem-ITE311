<div class="container mt-4">

    <div class="card shadow-sm border-0 mx-auto" style="max-width: 900px; border-top: 5px solid #ff9eb6; border-radius: 15px;">
        <div class="card-body" style="background:#fff7fa; border-radius: 0 0 15px 15px;">

            <!-- Title -->
            <h4 class="text-center mb-4" style="color:#ff6699; font-weight:700;">
                Course List
            </h4>

            <!-- Search + Filter Form -->
            <form method="GET" action="/course" class="row g-3 justify-content-center mb-4">

                <div class="col-md-5">
                    <input type="text" name="search" class="form-control"
                        placeholder="Search course name..."
                        style="border-radius:10px; border:1px solid #ffc7d8;"
                        value="<?= esc($_GET['search'] ?? '') ?>">
                </div>

                <div class="col-md-4">
                    <input type="text" name="year" class="form-control"
                        placeholder="School Year (ex. 2024-2025)"
                        style="border-radius:10px; border:1px solid #ffc7d8;"
                        value="<?= esc($_GET['year'] ?? '') ?>">
                </div>

                <div class="col-md-3 d-grid">
                    <button class="btn text-white" 
                        style="background:#ff9eb6; border-radius:10px;">
                        Search
                    </button>
                </div>

            </form>

            <div class="text-center mb-3">
                <a href="/course/create" class="btn text-white"
                    style="background:#ff9eb6; border-radius:10px;">
                    + Add New Course
                </a>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle"
                    style="border-radius: 10px; overflow:hidden;">
                    <thead style="background:#ff9eb6; color:white;">
                        <tr>
                            <th>#</th>
                            <th>Course Name</th>
                            <th>Semester</th>
                            <th>Term</th>
                            <th>School Year</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody style="background:white;">
                        <?php if (!empty($courses)) : ?>
                            <?php $i = 1; foreach ($courses as $course) : ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= esc($course['course_name']) ?></td>
                                <td><?= esc($course['semester']) ?></td>
                                <td><?= esc($course['term']) ?></td>
                                <td><?= esc($course['school_year']) ?></td>
                                <td class="text-start"><?= esc($course['course_description']) ?></td>
                                <td>
                                    <a href="/course/edit/<?= $course['id'] ?>" class="btn btn-warning btn-sm" style="border-radius:8px;">Edit</a>
                                    <a href="/course/delete/<?= $course['id'] ?>"
                                        onclick="return confirm('Delete this course?');"
                                        class="btn btn-danger btn-sm" style="border-radius:8px;">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="7" class="text-muted">No courses found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>

                </table>
            </div>

        </div>
    </div>

</div>
