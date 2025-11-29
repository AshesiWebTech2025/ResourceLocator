import sqlite3

conn = sqlite3.connect('mockDatabase.db')
cursor = conn.cursor()

print("Resource Types:")
cursor.execute("SELECT * FROM Resource_Types")
for row in cursor.fetchall():
    print(f"  ID: {row[0]}, Name: {row[1]}")

print("\nResources:")
cursor.execute("SELECT resource_id, type_id, name FROM Resources LIMIT 5")
for row in cursor.fetchall():
    print(f"  ID: {row[0]}, Type: {row[1]}, Name: {row[2]}")

conn.close()
