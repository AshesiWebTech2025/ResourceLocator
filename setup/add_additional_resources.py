import sqlite3

db_path = r'c:\Users\Cecilia\Desktop\Web Technologies\group-project\ResourceLocator\setup\mockDatabase.db'
conn = sqlite3.connect(db_path)
cursor = conn.cursor()

print("Adding additional Ashesi resources...")
print("=" * 70)

# Get the type IDs for Conference Room and Classroom
cursor.execute("SELECT type_id FROM Resource_Types WHERE type_name = 'Conference Room'")
conference_type_id = cursor.fetchone()[0]

cursor.execute("SELECT type_id FROM Resource_Types WHERE type_name = 'Classroom'")
classroom_type_id = cursor.fetchone()[0]

# Add new conference rooms
new_conference_rooms = [
    (conference_type_id, 'Joseph and Miyuki Dadzi Seminar Room', 20, 'Dedicated seminar room for meetings and discussions', 5.761600, -0.222600, 1),
    (conference_type_id, 'Catherine and Patrick Awuah SNR. Seminar Room', 20, 'Senior seminar room for executive meetings', 5.761700, -0.222700, 1),
]

# Add new classroom/study room
new_classroom = [
    (classroom_type_id, 'Green Lounge', 25, 'Comfortable study lounge with collaborative seating', 5.761800, -0.222800, 1),
]

for resource in new_conference_rooms:
    cursor.execute("""
        INSERT INTO Resources (type_id, name, capacity, description, latitude, longitude, is_bookable)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    """, resource)
    print(f"✓ Added Conference Room: {resource[1]}")

for resource in new_classroom:
    cursor.execute("""
        INSERT INTO Resources (type_id, name, capacity, description, latitude, longitude, is_bookable)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    """, resource)
    print(f"✓ Added Classroom: {resource[1]}")

conn.commit()

print("\n" + "=" * 70)
print("✅ Additional resources added successfully!")
print("=" * 70)

# Show updated summary
print("\n📊 UPDATED SUMMARY:")
print("-" * 70)

cursor.execute("""
    SELECT rt.type_name, COUNT(*) as count, SUM(r.capacity) as total_capacity
    FROM Resources r
    JOIN Resource_Types rt ON r.type_id = rt.type_id
    GROUP BY rt.type_name
    ORDER BY rt.type_name
""")

for row in cursor.fetchall():
    print(f"{row[0]}: {row[1]} rooms, Total capacity: {row[2]}")

cursor.execute("SELECT COUNT(*) FROM Resources WHERE is_bookable = 1")
total = cursor.fetchone()[0]
print(f"\nTotal Bookable Resources: {total}")

print("\n" + "=" * 70)
print("NEW RESOURCES:")
print("=" * 70)
print("\nCONFERENCE ROOMS:")
print("  • Joseph and Miyuki Dadzi Seminar Room")
print("  • Catherine and Patrick Awuah SNR. Seminar Room")
print("\nCLASSROOMS/STUDY ROOMS:")
print("  • Green Lounge")

conn.close()
