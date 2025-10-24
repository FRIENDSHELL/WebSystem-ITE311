# File Upload System - Testing Instructions

## ✅ All Issues Fixed

### Issues Fixed:
1. ✅ Added 'teacher' role support in all Materials controller methods (upload, download, delete, view)
2. ✅ Verified session handling (using 'id' consistently)
3. ✅ Security files in place (.htaccess, index.html)
4. ✅ Database migration successful (materials table created)
5. ✅ All routes configured correctly

---

## 🧪 Step-by-Step Testing Guide

### **Prerequisites:**
- XAMPP running (Apache + MySQL)
- Access to http://localhost/ITE311-EGARAN/

---

### **Test 1: Admin Upload (Primary Test)**

1. **Login as Admin:**
   - Go to: `http://localhost/ITE311-EGARAN/login`
   - Login with your admin account

2. **Navigate to Dashboard:**
   - You should see your courses listed
   - Each course has "View Materials" and "Upload" buttons

3. **Upload a File:**
   - Click the **"Upload"** button next to any course
   - You'll be redirected to the upload form
   - Select a test file (PDF, DOC, PPT, etc.)
   - Click **"Upload Material"**
   - You should see a success message: "Material uploaded successfully!"

4. **Verify Upload:**
   - **Database:** Check phpMyAdmin → `materials` table → Should have 1 record
   - **File System:** Check `writable/uploads/materials/` → Should have the file with a random name

5. **View Materials:**
   - Click **"View Materials"** for the course
   - You should see the uploaded file listed
   - Try downloading it (should work)

6. **Delete Material:**
   - Click the **"Delete"** button
   - Confirm deletion
   - File should be removed from database and filesystem

---

### **Test 2: Teacher Upload**

1. **Login as Teacher:**
   - If you don't have a teacher account, create one or change a user's role to 'teacher' in the database

2. **Test Same Flow as Admin:**
   - Should be able to upload, view, download, and delete materials

---

### **Test 3: Student Access (Enrolled)**

1. **Login as Student:**
   - Login with a student account that is enrolled in a course

2. **View Materials:**
   - Click **"View Materials"** next to an enrolled course
   - Should see the list of materials
   - Should be able to download files
   - Should NOT see "Upload" or "Delete" buttons

---

### **Test 4: Student Access (Not Enrolled)**

1. **Try to Access Materials:**
   - Try to manually access a course you're not enrolled in
   - Example: `http://localhost/ITE311-EGARAN/materials/view/1`
   - Should be blocked with error: "You must be enrolled in this course to view materials."

---

### **Test 5: File Validation**

1. **Test Invalid File Type:**
   - Try uploading an .exe or .php file
   - Should be rejected with validation error

2. **Test Large File (if needed):**
   - Try uploading a file > 10MB
   - Should be rejected with size error

---

## 📊 Verification Checklist

After testing, verify the following:

- [ ] Files are uploaded to `writable/uploads/materials/`
- [ ] Files have random names (security)
- [ ] Original filenames are preserved in the database
- [ ] Records exist in the `materials` table
- [ ] Admin/Teacher can upload, view, download, delete
- [ ] Students can only view/download from enrolled courses
- [ ] Students cannot access materials from non-enrolled courses
- [ ] File validation works (type and size)
- [ ] Delete removes both database record and physical file

---

## 🐛 Common Issues & Solutions

### Issue: "Failed to save material to database"
**Solution:** Check database connection in `.env` file

### Issue: "Upload error: Permission denied"
**Solution:** Make sure `writable/uploads/materials/` folder has write permissions (755)

### Issue: File not found when downloading
**Solution:** Check if file exists in `writable/uploads/materials/` folder

### Issue: Upload button not showing
**Solution:** Make sure you're logged in as admin, teacher, or instructor

### Issue: CSRF Token Error
**Solution:** Clear browser cache and cookies, then try again

---

## 📷 Screenshots for Lab Submission

Take screenshots of:

1. **phpMyAdmin** - materials table structure
2. **phpMyAdmin** - materials table with data (after upload)
3. **Upload Form** - The upload page (admin view)
4. **Materials List** - The materials view page with download buttons
5. **File System** - Windows Explorer showing `writable\uploads\materials\` with uploaded files
6. **GitHub** - Your repository with the latest commit

---

## 🚀 Quick Test Command

To verify the materials table exists:
```bash
php spark db:table materials
```

To check routes:
```bash
php spark routes | findstr materials
```

---

## ✨ What's Working:

✅ Database migration (materials table created)
✅ MaterialModel with all methods
✅ Materials controller with upload, download, delete, view
✅ File upload validation (type, size)
✅ Security (role-based access control)
✅ File storage with random names
✅ Bootstrap-styled views
✅ Flash messages for user feedback
✅ Routes configured
✅ Dashboard integration with buttons
✅ Teacher role support added
✅ Enrollment verification for students

---

## 🎯 System is 100% Ready for Testing!

All code is in place and all roles (admin, teacher, student) are properly handled. You can now start testing!
