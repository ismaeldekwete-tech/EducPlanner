import re

file_path = r"C:\Users\user\Documents\EducPlanner\resources\views\livewire\timetable-manager.blade.php"

with open(file_path, "r", encoding="utf-8") as f:
    lines = f.readlines()

for i, line in enumerate(lines):
    if "print" in line.lower() or "télécharger" in line.lower() or "download" in line.lower() or "pdf" in line.lower():
        print(f"Line {i+1}: {line.strip()}")
