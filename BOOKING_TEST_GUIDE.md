# 🎉 Booking System - Ready to Test!

## ✅ What Was Fixed:

1. **Created `create_booking.php`** - Proper backend that handles form submissions
   - Validates all input fields
   - Checks for booking conflicts
   - Inserts into correct `Bookings` table
   - Shows success/error messages

2. **Fixed `fetch_bookings.php`** - Now queries correct table with proper columns
   - Uses `Bookings` table (not `bookings`)
   - Uses `booking_id` (not `id`)
   - Joins with `Resources` table for resource names

3. **Updated `add_booking.php`** - Fixed table names and structure

4. **Updated `bookings.php`** - Added session-based success/error messages

5. **Added sample data** - Database now has:
   - 1 test user (user_id=1)
   - 5 resource types
   - 6 bookable resources
   - 1 sample booking (for tomorrow at 10:00 AM)

---

## 🚀 How to Test:

### Step 1: Start Your PHP Server
```powershell
cd "c:\Users\Cecilia\Desktop\Web Technologies\group-project\ResourceLocator\frontend"
php -S localhost:8000
```

### Step 2: Open the Bookings Page
Open your browser and go to:
```
http://localhost:8000/bookings.php
```

### Step 3: You Should See:
- ✅ One existing booking (Study Room B1, tomorrow at 10:00-12:00)
- ✅ "Book New Resource" button in the header
- ✅ Summary showing: 1 total booking, 1 upcoming, 0 past

### Step 4: Test Creating a New Booking:
1. Click "Book New Resource" button
2. Select a resource (e.g., "Room A101" or "Study Room B2")
3. Pick today's date or a future date
4. Choose start time (e.g., 14:00)
5. Choose end time (e.g., 16:00)
6. Enter a purpose (e.g., "Team meeting")
7. Click "Book Resource"
8. ✅ You should see a green success message
9. ✅ The new booking should appear in your bookings list

### Step 5: Test Conflict Detection:
1. Try to book the same resource at the same time again
2. ✅ You should see a red error: "Resource is already booked during the selected time slot"

---

## 📊 Available Resources in Database:
- Room A101 (Classroom, capacity 30)
- Study Room B1 (Study Room, capacity 8)
- Study Room B2 (Study Room, capacity 6)
- Computer Lab C1 (Laboratory, capacity 40)
- Conference Room D1 (Conference Room, capacity 15)

---

## 🔍 Troubleshooting:

If you get errors:
1. Check that PHP server is running
2. Check browser console for JavaScript errors
3. Check PHP errors in the terminal where server is running
4. Verify database path in `backend/dbConnector.php`

---

## 📝 Notes:
- Currently using **hardcoded user_id=1** (will need login system later)
- All bookings are for the logged-in user
- Status options: Confirmed, Cancelled, Completed
- Conflict detection prevents double-booking

---

## 🎯 Next Steps (Optional):
- Implement cancel booking functionality
- Add user authentication/login
- Add booking details modal
- Add booking edit functionality
- Filter bookings by status (upcoming/past)
