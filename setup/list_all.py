import sqlite3

conn = sqlite3.connect('mockDatabase.db')
cursor = conn.cursor()

print("\n" + "=" * 70)
print("ASHESI UNIVERSITY - ALL RESOURCES")
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

print("\n" + "=" * 70)
conn.close()
