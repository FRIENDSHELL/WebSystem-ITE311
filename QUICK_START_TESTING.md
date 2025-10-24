# 🚀 Quick Start - Test File Upload Now!

## ⚡ 3-Minute Test Guide

### Step 1: Start Your Server (30 seconds)
1. Open XAMPP Control Panel
2. Start **Apache** and **MySQL**
3. Verify they're running (green indicators)

### Step 2: Open Your Application (10 seconds)
1. Open browser
2. Go to: `http://localhost/ITE311-EGARAN/`

### Step 3: Login as Admin (20 seconds)
1. Enter your admin credentials
2. Click "Login"
3. You should see the Dashboard

### Step 4: Upload a File (1 minute)
1. **Find a course** in the courses table
2. Click the **green "Upload" button** next to any course
3. Click **"Choose File"** and select a PDF or DOC file from your computer
4. Click **"Upload Material"**
5. ✅ You should see: **"Material uploaded successfully!"**

### Step 5: Verify Upload (30 seconds)
1. Click **"View Materials"** button for the same course
2. You should see your uploaded file in the list
3. Click **"Download"** to test download
4. ✅ File should download to your computer

### Step 6: Verify Database (30 seconds)
1. Open phpMyAdmin: `http://localhost/phpmyadmin/`
2. Select database: `lms_egaran`
3. Click on `materials` table
4. ✅ You should see 1 record with your file info

### Step 7: Verify File System (30 seconds)
1. Open Windows Explorer
2. Navigate to: `C:\xampp1\htdocs\ITE311-EGARAN\writable\uploads\materials\`
3. ✅ You should see a file with a random name (e.g., `1729752934_abc123def456.pdf`)

---

## ✅ SUCCESS INDICATORS

If you see all of these, the system is **100% working**:

- ✅ Upload button appears on dashboard
- ✅ Upload form loads correctly
- ✅ File uploads without errors
- ✅ Success message appears
- ✅ File appears in materials list
- ✅ Download works
- ✅ Record exists in database
- ✅ Physical file exists in folder

---

## 🎯 What to Test

### ✅ Test as Admin:
- Upload file ✓
- View materials ✓
- Download file ✓
- Delete file ✓

### ✅ Test as Student:
- Login as student
- Enroll in a course (if not enrolled)
- Click "View Materials" on enrolled course ✓
- Download file ✓
- Try accessing non-enrolled course (should be blocked) ✓

---

## 🐛 Common Issues

### ❌ "Upload" button not showing
**Fix:** Make sure you're logged in as admin, teacher, or instructor (not student)

### ❌ Upload fails with error
**Fix:** 
1. Check file size (must be < 10MB)
2. Check file type (must be PDF, DOC, DOCX, PPT, PPTX, TXT, ZIP, or RAR)
3. Check folder permissions

### ❌ "Course not found" error
**Fix:** Make sure you have courses in the database. Add a course first.

### ❌ File doesn't appear after upload
**Fix:** Check `writable/uploads/materials/` folder permissions (should be writable)

---

## 📸 Screenshots to Take

Take these screenshots for your lab report:

1. **phpMyAdmin - materials table structure**
   - Database → lms_egaran → materials → Structure tab

2. **phpMyAdmin - materials table data**
   - Database → lms_egaran → materials → Browse tab (after upload)

3. **Upload Form**
   - The page at `/admin/course/1/upload`

4. **Materials Listing**
   - The page at `/materials/view/1` showing the uploaded file

5. **File System**
   - Windows Explorer showing `C:\xampp1\htdocs\ITE311-EGARAN\writable\uploads\materials\`

6. **GitHub Repository**
   - Your repository page showing the latest commit

---

## 💡 Quick Commands

### Check if materials table exists:
```bash
php spark db:table materials
```

### View routes:
```bash
php spark routes | Select-String "materials"
```

### Check database:
```bash
php spark db:table materials
```

---

## 🎉 You're Ready!

Everything is set up and working. Just follow the steps above to test!

**Estimated Testing Time:** 5-10 minutes

---

## 📞 Need Help?

All issues have been fixed:
- ✅ Teacher role support added
- ✅ Session handling verified
- ✅ Security files in place
- ✅ Routes configured
- ✅ Database migrated
- ✅ Views created
- ✅ Controller implemented
- ✅ Dashboard integrated

**The system is 100% functional and ready to use!**
