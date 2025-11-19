# Enrollment Procedure - Complete Flow

## ✅ Route Configuration

### Routes Defined:
```php
// Dashboard route (GET - shows dashboard)
$routes->get('dashboard', 'Auth::dashboard');

// Enrollment route (POST - handles enrollment)
$routes->post('dashboard/enroll', 'Auth::enroll');
```

## 📋 Complete Enrollment Flow

### 1. **User Interface (Frontend)**
- **File:** `app/Views/auth/dashboard.php`
- **Location:** Student dashboard section
- **Action:** User clicks "Enroll" button on available course

### 2. **JavaScript Handler**
```javascript
// When enroll button is clicked
$('.enroll-btn').click(function() {
    const courseId = $(this).data('course-id');
    
    // POST request to dashboard/enroll
    $.ajax({
        url: "<?= base_url('dashboard/enroll') ?>",
        type: "POST",
        data: { course_id: courseId }
    })
})
```

### 3. **Route Matching**
- **Route:** `POST /dashboard/enroll`
- **Controller:** `App\Controllers\Auth`
- **Method:** `enroll()`

### 4. **Controller Processing** (`Auth::enroll()`)

#### Step 1: Authentication Check
```php
if (!$session->get('logged_in') || !$session->get('id')) {
    return JSON error: "Please log in first."
}
```

#### Step 2: Input Validation
```php
$user_id   = (int) $session->get('id');
$course_id = (int) $this->request->getPost('course_id');

if (empty($course_id) || empty($user_id)) {
    return JSON error: "Invalid course or user."
}
```

#### Step 3: Duplicate Check
```php
$exists = $db->table('enrollments')
    ->where('user_id', $user_id)
    ->where('course_id', $course_id)
    ->countAllResults();

if ($exists > 0) {
    return JSON error: "You are already enrolled in this course."
}
```

#### Step 4: Database Insert
```php
$inserted = $db->table('enrollments')->insert([
    'user_id'    => $user_id,
    'course_id'  => $course_id,
    'created_at' => date('Y-m-d H:i:s'),
]);
```

#### Step 5: Response
```php
if ($inserted) {
    return JSON success: "You have successfully enrolled!"
} else {
    return JSON error: "Failed to enroll. Please try again."
}
```

### 5. **Frontend Response Handling**

#### Success Response:
```javascript
if (response.status === 'success') {
    // Show success message
    // Disable enroll button
    // Add course to enrolled courses list
    // Update UI
}
```

#### Error Response:
```javascript
else {
    // Show error message
    // Re-enable enroll button
}
```

## 🔄 Data Flow Diagram

```
User clicks "Enroll"
    ↓
JavaScript AJAX POST
    ↓
Route: POST /dashboard/enroll
    ↓
Controller: Auth::enroll()
    ↓
[Authentication Check]
    ↓
[Input Validation]
    ↓
[Duplicate Check]
    ↓
[Database Insert]
    ↓
[JSON Response]
    ↓
Frontend Updates UI
```

## 📊 Database Schema

### `enrollments` Table:
```sql
- id (PRIMARY KEY, AUTO_INCREMENT)
- user_id (INT, FOREIGN KEY → users.id)
- course_id (INT, FOREIGN KEY → courses.id)
- created_at (DATETIME)
```

## ✅ Validation Rules

1. **User must be logged in**
   - Session must have `logged_in = true`
   - Session must have `id` set

2. **Course ID must be valid**
   - Must be provided in POST data
   - Must be numeric/integer

3. **No duplicate enrollments**
   - Check if `(user_id, course_id)` combination already exists

4. **Database constraints**
   - Foreign key constraints ensure valid user and course

## 🛡️ Security Features

1. **Session-based authentication**
   - User ID from session (not from POST data)
   - Prevents user impersonation

2. **CSRF Protection**
   - CSRF token automatically injected via `ajaxSetup`
   - Token validated by CodeIgniter

3. **Input sanitization**
   - Integer casting: `(int) $course_id`
   - Prevents SQL injection

4. **Error handling**
   - Try-catch blocks for database errors
   - Graceful error messages

## 🎯 Response Format

### Success Response:
```json
{
    "status": "success",
    "message": "You have successfully enrolled!"
}
```

### Error Responses:
```json
{
    "status": "error",
    "message": "Error message here"
}
```

Possible error messages:
- "Please log in first."
- "Invalid course or user."
- "You are already enrolled in this course."
- "Failed to enroll. Please try again."
- "Database error: [error details]"

## 🔍 Testing Checklist

- [ ] User can enroll in a course
- [ ] Duplicate enrollment is prevented
- [ ] Error message shown if not logged in
- [ ] Error message shown if course_id is invalid
- [ ] Success message shown on successful enrollment
- [ ] UI updates correctly after enrollment
- [ ] Enroll button is disabled after enrollment
- [ ] Course appears in enrolled courses list

## 📝 Notes

1. **Alternative Route:** There's also `POST /course/enroll` → `Course::enroll()` which provides similar functionality but uses `EnrollmentModel`.

2. **Dashboard Method:** The `Auth::dashboard()` method previously handled enrollment via POST, but this has been separated into a dedicated `enroll()` method for cleaner code organization.

3. **Model Usage:** The current implementation uses direct database queries. Consider refactoring to use `EnrollmentModel` for consistency with other parts of the codebase.

