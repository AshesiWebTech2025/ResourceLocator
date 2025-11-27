import sqlite3
from datetime import datetime

db_path = r'c:\Users\Cecilia\Desktop\Web Technologies\group-project\ResourceLocator\setup\mockDatabase.db'
conn = sqlite3.connect(db_path)
cursor = conn.cursor()

print("Adding actual Ashesi University resources...")
print("=" * 70)

# Clear existing data
cursor.execute("DELETE FROM Bookings")
cursor.execute("DELETE FROM Resources WHERE resource_id > 0")
cursor.execute("DELETE FROM Resource_Types WHERE type_id > 0")
print("✓ Cleared existing data")

# Add resource types and get their IDs
resource_type_ids = {}
resource_types = ['Laboratory', 'Classroom', 'Conference Room']

for type_name in resource_types:
    cursor.execute("INSERT INTO Resource_Types (type_name) VALUES (?)", (type_name,))
    type_id = cursor.lastrowid
    resource_type_ids[type_name] = type_id
    print(f"✓ Added resource type: {type_name} (ID: {type_id})")

# Add actual Ashesi resources
# Format: (type_name, name, capacity, description, latitude, longitude, is_bookable)

resources = [
    # LABORATORIES
    ('Laboratory', 'Bio Lab', 30, 'Biology laboratory with modern equipment', 5.759500, -0.220500, 1),
    ('Laboratory', 'EE Lab', 25, 'Electrical Engineering laboratory', 5.759600, -0.220600, 1),
    ('Laboratory', 'Fab Lab 203', 20, 'Fabrication laboratory room 203', 5.759700, -0.220700, 1),
    ('Laboratory', 'Fab Lab 303', 20, 'Fabrication laboratory room 303', 5.759800, -0.220800, 1),
    ('Laboratory', 'Jackson Lab 221', 30, 'Jackson building laboratory room 221', 5.759900, -0.220900, 1),
    ('Laboratory', 'Jackson Lab 222', 30, 'Jackson building laboratory room 222', 5.760000, -0.221000, 1),
    ('Laboratory', 'King Eng. Lab 102', 35, 'King Engineering laboratory room 102', 5.760100, -0.221100, 1),
    ('Laboratory', 'ME Lab', 25, 'Mechanical Engineering laboratory', 5.760200, -0.221200, 1),
    ('Laboratory', 'Science Lab', 30, 'General science laboratory', 5.760300, -0.221300, 1),
    
    # CLASSROOMS / STUDY ROOMS
    ('Classroom', 'Apt Hall 216', 40, 'Classroom in Apt Hall, room 216', 5.760400, -0.221400, 1),
    ('Classroom', 'Apt Hall 217', 40, 'Classroom in Apt Hall, room 217', 5.760500, -0.221500, 1),
    ('Classroom', 'Databank Foundation Hall 218', 45, 'Databank Foundation Hall classroom, room 218', 5.760600, -0.221600, 1),
    ('Classroom', 'Jackson Hall 115', 35, 'Jackson Hall classroom, room 115', 5.760700, -0.221700, 1),
    ('Classroom', 'Jackson Hall 116', 35, 'Jackson Hall classroom, room 116', 5.760800, -0.221800, 1),
    ('Classroom', 'Nutor Hall 100', 50, 'Nutor Hall large classroom, room 100', 5.760900, -0.221900, 1),
    ('Classroom', 'Nutor Hall 115', 35, 'Nutor Hall classroom, room 115', 5.761000, -0.222000, 1),
    ('Classroom', 'Nutor Hall 216', 35, 'Nutor Hall classroom, room 216', 5.761100, -0.222100, 1),
    ('Classroom', 'Radichel MPR', 100, 'Radichel Multi-Purpose Room for large gatherings', 5.761200, -0.222200, 1),
    
    # CONFERENCE ROOMS
    ('Conference Room', 'The Hive', 20, 'Conference and collaboration space', 5.761300, -0.222300, 1),
    ('Conference Room', 'Norton-Motulsky 207A', 15, 'Norton-Motulsky building conference room 207A', 5.761400, -0.222400, 1),
    ('Conference Room', 'Norton-Motulsky 207B', 15, 'Norton-Motulsky building conference room 207B', 5.761500, -0.222500, 1),
]

for resource in resources:
    type_name = resource[0]
    type_id = resource_type_ids[type_name]
    cursor.execute("""
        INSERT INTO Resources (type_id, name, capacity, description, latitude, longitude, is_bookable)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    """, (type_id, resource[1], resource[2], resource[3], resource[4], resource[5], resource[6]))
    print(f"✓ Added: {resource[1]}")

# Commit all changes
conn.commit()
print("\n" + "=" * 70)
print("Ashesi University resources added successfully!")
print("=" * 70)

# Display summary by category
print(f"\n📊 SUMMARY BY CATEGORY:")
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

# Show all resources by category
print("\n" + "=" * 70)
print("FULL RESOURCE LIST:")
print("=" * 70)

cursor.execute("""
    SELECT rt.type_name, r.name, r.capacity
    FROM Resources r
    JOIN Resource_Types rt ON r.type_id = rt.type_id
    ORDER BY rt.type_name, r.name
""")

current_type = ""
for row in cursor.fetchall():
    if row[0] != current_type:
        print(f"\n{row[0].upper()}S:")
        print("-" * 70)
        current_type = row[0]
    print(f"  • {row[1]} (Capacity: {row[2]})")

conn.close()
print("\n" + "=" * 70)
print("✅ Database ready with actual Ashesi resources!")
print("=" * 70)
