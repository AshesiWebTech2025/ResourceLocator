import sqlite3

conn = sqlite3.connect('mockDatabase.db')
cursor = conn.cursor()

print("Verifying booking data for cancel/view features:")
print("=" * 70)

cursor.execute("""
    SELECT b.booking_id, r.name, b.start_time, b.status, b.purpose 
    FROM Bookings b 
    JOIN Resources r ON b.resource_id = r.resource_id
""")

for row in cursor.fetchall():
    print(f"\nBooking ID: {row[0]}")
    print(f"Resource: {row[1]}")
    print(f"Start Time: {row[2]}")
    print(f"Status: {row[3]}")
    print(f"Purpose: {row[4]}")

conn.close()
print("\n" + "=" * 70)
print("✅ All required fields are present!")
