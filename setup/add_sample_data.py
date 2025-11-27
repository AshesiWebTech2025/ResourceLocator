import sqlite3
from datetime import datetime, timedelta

db_path = r'c:\Users\Cecilia\Desktop\Web Technologies\group-project\ResourceLocator\setup\mockDatabase.db'
conn = sqlite3.connect(db_path)
cursor = conn.cursor()

print("Adding sample data to mockDatabase.db...")
print("=" * 50)

# 1. Add a test user (if not exists)
try:
    cursor.execute("""
        INSERT INTO Users (user_id, ashesi_email, name, role, password_hash, is_active)
        VALUES (1, 'test.student@ashesi.edu.gh', 'Test Student', 'Student', 'hashed_password_here', 1)
    """)
    print("✓ Added test user (user_id=1)")
except sqlite3.IntegrityError:
    print("✓ Test user already exists")

# 2. Add resource types (if not exists)
resource_types = [
    ('Classroom',),
    ('Study Room',),
    ('Laboratory',),
    ('Conference Room',),
]

for type_name in resource_types:
    try:
        cursor.execute("INSERT INTO Resource_Types (type_name) VALUES (?)", type_name)
        print(f"✓ Added resource type: {type_name[0]}")
    except sqlite3.IntegrityError:
        print(f"✓ Resource type already exists: {type_name[0]}")

# 3. Add bookable resources
resources = [
    (1, 'Room A101', 30, 'Main lecture hall with projector', 5.760000, -0.220000, 1),
    (2, 'Study Room B1', 8, 'Quiet study room for group work', 5.761000, -0.221000, 1),
    (2, 'Study Room B2', 6, 'Small study room with whiteboard', 5.761500, -0.221500, 1),
    (3, 'Computer Lab C1', 40, 'Computer lab with 40 workstations', 5.762000, -0.222000, 1),
    (4, 'Conference Room D1', 15, 'Conference room with video conferencing', 5.763000, -0.223000, 1),
]

cursor.execute("DELETE FROM Resources WHERE resource_id > 1")  # Clear sample data except first one
for resource in resources:
    try:
        cursor.execute("""
            INSERT INTO Resources (type_id, name, capacity, description, latitude, longitude, is_bookable)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        """, resource)
        print(f"✓ Added resource: {resource[1]}")
    except sqlite3.IntegrityError as e:
        print(f"  Skip: {resource[1]} - {e}")

# 4. Add a sample booking for demonstration
tomorrow = datetime.now() + timedelta(days=1)
start_time = tomorrow.replace(hour=10, minute=0, second=0, microsecond=0)
end_time = start_time + timedelta(hours=2)

cursor.execute("DELETE FROM Bookings")  # Clear any existing bookings

sample_booking = (
    2,  # resource_id (Study Room B1)
    1,  # user_id (Test Student)
    start_time.strftime('%Y-%m-%d %H:%M:%S'),
    end_time.strftime('%Y-%m-%d %H:%M:%S'),
    'Group project meeting',
    'Confirmed'
)

cursor.execute("""
    INSERT INTO Bookings (resource_id, user_id, start_time, end_time, purpose, status)
    VALUES (?, ?, ?, ?, ?, ?)
""", sample_booking)
print(f"✓ Added sample booking for tomorrow at {start_time.strftime('%H:%M')}")

# Commit all changes
conn.commit()
print("\n" + "=" * 50)
print("Sample data added successfully!")
print("=" * 50)

# Display summary
cursor.execute("SELECT COUNT(*) FROM Users")
user_count = cursor.fetchone()[0]

cursor.execute("SELECT COUNT(*) FROM Resource_Types")
type_count = cursor.fetchone()[0]

cursor.execute("SELECT COUNT(*) FROM Resources WHERE is_bookable = 1")
resource_count = cursor.fetchone()[0]

cursor.execute("SELECT COUNT(*) FROM Bookings")
booking_count = cursor.fetchone()[0]

print(f"\nDatabase Summary:")
print(f"  Users: {user_count}")
print(f"  Resource Types: {type_count}")
print(f"  Bookable Resources: {resource_count}")
print(f"  Bookings: {booking_count}")

conn.close()
print("\nYou can now test the booking system!")
