import sqlite3

db_path = r'c:\Users\Cecilia\Desktop\Web Technologies\group-project\ResourceLocator\setup\mockDatabase.db'
conn = sqlite3.connect(db_path)
cursor = conn.cursor()

print("\n" + "=" * 70)
print("ASHESI UNIVERSITY RESOURCES - DATABASE")
print("=" * 70)

# Show Resources by Category
cursor.execute("""
    SELECT rt.type_name, r.name, r.capacity, r.description
    FROM Resources r
    JOIN Resource_Types rt ON r.type_id = rt.type_id
    WHERE r.is_bookable = 1
    ORDER BY rt.type_name, r.name
""")

resources = cursor.fetchall()
current_type = ""

for row in resources:
    if row[0] != current_type:
        if current_type != "":
            print()
        print(f"\n{'=' * 70}")
        print(f"📚 {row[0].upper()}S")
        print('=' * 70)
        current_type = row[0]
    
    print(f"\n  🏫 {row[1]}")
    print(f"     Capacity: {row[2]} people")
    print(f"     {row[3]}")

# Summary
print("\n" + "=" * 70)
print("📊 SUMMARY")
print("=" * 70)

cursor.execute("""
    SELECT rt.type_name, COUNT(*) as count
    FROM Resources r
    JOIN Resource_Types rt ON r.type_id = rt.type_id
    GROUP BY rt.type_name
    ORDER BY rt.type_name
""")

for row in cursor.fetchall():
    print(f"  {row[0]}: {row[1]} locations")

cursor.execute("SELECT COUNT(*) FROM Resources WHERE is_bookable = 1")
total = cursor.fetchone()[0]
print(f"\n  Total Bookable Resources: {total}")

print("\n" + "=" * 70)
print("✅ All Ashesi resources loaded and ready for booking!")
print("=" * 70 + "\n")

conn.close()
