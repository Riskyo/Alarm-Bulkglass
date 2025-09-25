import pandas as pd

# ==== 1. Load Excel ====
file_path = "alarm BG 2.xlsx"   # ganti sesuai lokasi file
df = pd.read_excel(file_path, sheet_name=0, header=None)

# ==== 2. Cari baris header yang ada "Code Alarm" ====
header_row = None
for i in range(len(df)):
    row = df.iloc[i].astype(str).str.strip().str.lower()
    if any("code alarm" in cell for cell in row):
        header_row = i
        break

if header_row is None:
    raise ValueError("❌ Tidak ditemukan header 'Code Alarm' di file Excel")

# Ambil data mulai setelah baris header
data = df.iloc[header_row+1:].reset_index(drop=True)

# ==== 3. Tentukan kolom index ====
code_col = None
desc_col = None
action_col = None

header = df.iloc[header_row].astype(str).str.strip().str.lower()
for i, col in enumerate(header):
    if "code alarm" in col:
        code_col = i
    elif "description" in col:
        if desc_col is None:  # pakai description pertama yang ketemu
            desc_col = i
    elif "action" in col:
        action_col = i

if code_col is None or desc_col is None:
    raise ValueError("❌ Tidak bisa menemukan kolom Code Alarm atau Description")

# ==== 4. Buat SQL Insert ====
alarm_inserts = []
action_inserts = []

for idx, row in data.iterrows():
    code_alarm = str(row[code_col]).strip()
    description = str(row[desc_col]).strip() if pd.notna(row[desc_col]) else ""
    action_text = str(row[action_col]).strip() if (action_col is not None and pd.notna(row[action_col])) else ""

    # Skip baris kosong
    if not code_alarm.isdigit():
        continue

    # Pastikan 3 digit
    code_alarm = code_alarm.zfill(3)

    # Insert Alarm (tanpa id)
    alarm_sql = (
        "INSERT INTO alarms (code_alarm, description, created_at, updated_at) "
        f"VALUES ('{code_alarm}', '{description.replace("'", "''")}', NOW(), NOW());"
    )
    alarm_inserts.append(alarm_sql)

    # Insert Actions (tanpa id, foreign key ambil pakai LAST_INSERT_ID())
    if action_text:
        actions = [a.strip() for a in action_text.replace("\n", "|").split("|") if a.strip()]
        for act in actions:
            action_sql = (
                "INSERT INTO actions (alarm_id, action_text, created_at, updated_at) "
                f"VALUES (LAST_INSERT_ID(), '{act.replace("'", "''")}', NOW(), NOW());"
            )
            action_inserts.append(action_sql)

# ==== 5. Gabungkan ====
# Gunakan format: insert alarm lalu langsung insert action-nya
sql_lines = []
for a, acts in zip(alarm_inserts, action_inserts):
    sql_lines.append(a)
    sql_lines.append(acts)

sql_script = "\n".join(alarm_inserts + [""] + action_inserts)

# ==== 6. Simpan ke file ====
output_path = "alarms_data.sql"
with open(output_path, "w", encoding="utf-8") as f:
    f.write(sql_script)

print(f"✅ File SQL berhasil dibuat: {output_path}")
