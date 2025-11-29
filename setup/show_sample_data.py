import sqlite3
from datetime import datetime

db_path = r'c:\Users\Cecilia\Desktop\Web Technologies\group-project\ResourceLocator\setup\mockDatabase.db'
conn = sqlite3.connect(db_path)
cursor = conn.cursor()

print("\n" + "=" * 70)
print("DATABASE CONTENTS - Ready for Testing!")
print("=" * 70)

# Show Users
print("\n📋 USERS:")
print("-" * 70)
cursor.execute("SELECT user_id, name, ashesi_email, role FROM Users")
for row in cursor.fetchall():
    print(f"  ID: {row[0]} | {row[1]} | {row[2]} | Role: {row[3]}")

# Show Resource Types
print("\n📦 RESOURCE TYPES:")
print("-" * 70)
cursor.execute("SELECT type_id, type_name FROM Resource_Types")
for row in cursor.fetchall():
    print(f"  ID: {row[0]} | {row[1]}")

# Show Resources
print("\n🏢 BOOKABLE RESOURCES:")
print("-" * 70)
cursor.execute("""
    SELECT r.resource_id, r.name, rt.type_name, r.capacity, r.description
    FROM Resources r
    JOIN Resource_Types rt ON r.type_id = rt.type_id
    WHERE r.is_bookable = 1
    ORDER BY rt.type_name, r.name
""")
for row in cursor.fetchall():
    print(f"  ID: {row[0]} | {row[1]}")
    print(f"       Type: {row[2]} | Capacity: {row[3]}")
    print(f"       {row[4]}")
    print()

# Show Bookings
print("📅 CURRENT BOOKINGS:")
print("-" * 70)
cursor.execute("""
    SELECT b.booking_id, r.name, b.start_time, b.end_time, b.purpose, b.status
    FROM Bookings b
    JOIN Resources r ON b.resource_id = r.resource_id
    ORDER BY b.start_time
""")
bookings = cursor.fetchall()
if bookings:
    for row in bookings:
        start = datetime.strptime(row[2], '%Y-%m-%d %H:%M:%S')
        end = datetime.strptime(row[3], '%Y-%m-%d %H:%M:%S')
        print(f"  Booking #{row[0]} | {row[1]}")
        print(f"       When: {start.strftime('%b %d, %Y at %I:%M %p')} - {end.strftime('%I:%M %p')}")
        print(f"       Purpose: {row[4]}")
        print(f"       Status: {row[5]}")
        print()
else:
    print("  No bookings yet")

print("=" * 70)
print("✅ Database is ready! Follow BOOKING_TEST_GUIDE.md to test.")
print("=" * 70 + "\n")

conn.close()
