import mysql.connector
import random
from tabulate import tabulate
from openpyxl import Workbook

# Fungsi untuk menghubungkan ke database
def connect_db():
    try:
        conn = mysql.connector.connect(
            host='localhost',
            user='root',
            password='',
            database='penjadwalan2'
        )
        return conn
    except mysql.connector.Error as e:
        print(f"Error connecting to MySQL: {e}")
        exit()

# Fungsi untuk mengambil data dosen, matakuliah, prodi, kelas, waktu, dan ruangan
def get_data():
    conn = connect_db()
    cursor = conn.cursor()

    try:
        # Ambil data dosen, matakuliah, prodi, dan jurusan dari tabel dosen_mk
        cursor.execute(
            """
            SELECT 
                dmk.dosen_id, d.nama_dosen, dmk.matkul_id, mk.nama_matkul, 
                dmk.prodi_id, p.nama_prodi, dmk.prodi_id, j.jurusan
            FROM dosen_mk dmk
            JOIN dosen d ON dmk.dosen_id = d.dosen_id
            JOIN matakuliah mk ON dmk.matkul_id = mk.matkul_id
            JOIN prodi p ON dmk.prodi_id = p.prodi_id
            JOIN jurusan j ON dmk.prodi_id = j.prodi_id
            """
        )
        dosen_mk = cursor.fetchall()

        # Ambil data kelas
        cursor.execute("SELECT kelas_id, nama_kelas FROM kelas")
        kelas = cursor.fetchall()

        # Ambil data waktu (hari, jam mulai, jam selesai)
        cursor.execute("SELECT waktu_id, hari, jam_mulai, jam_selesai FROM waktu")
        waktu = cursor.fetchall()

        # Ambil data ruangan
        cursor.execute("SELECT ruangan_id, nama_ruangan FROM ruangan")
        ruangan = cursor.fetchall()
    finally:
        conn.close()

    return dosen_mk, kelas, waktu, ruangan

# Fungsi crossover
def crossover(parent1, parent2):
    crossover_point = random.randint(1, len(parent1) - 2)
    child1 = parent1[:crossover_point] + parent2[crossover_point:]
    child2 = parent2[:crossover_point] + parent1[crossover_point:]
    return child1, child2

# Fungsi mutasi
def mutate(chromosome, waktu, ruangan):
    mutation_probability = 0.1  # 10% peluang mutasi
    for gene in chromosome:
        if random.random() < mutation_probability:
            gene["waktu_id"] = random.choice(waktu)[0]
            gene["ruangan_id"] = random.choice(ruangan)[0]

# Fungsi untuk menghitung fitness dari sebuah kromosom
def fitness_function(chromosome):
    conflicts = 0
    for i, gene in enumerate(chromosome):
        for other_gene in chromosome[i + 1:]:
            if (
                gene["waktu_id"] == other_gene["waktu_id"] and (
                    gene["kelas_id"] == other_gene["kelas_id"] or
                    gene["ruangan_id"] == other_gene["ruangan_id"] or
                    gene["dosen_id"] == other_gene["dosen_id"]
                )
            ):
                conflicts += 1
    return 1 / (1 + conflicts)

# Fungsi utama algoritma genetika
def genetic_algorithm(tasks, dosen_mk, waktu, kelas, ruangan, generations=100, population_size=50):
    population = []
    for _ in range(population_size):
        chromosome = []
        for task in tasks:
            matkul_id, dosen_id, prodi_id, kelas_id = task
            gene = {
                "matkul_id": matkul_id,
                "dosen_id": dosen_id,
                "kelas_id": kelas_id,
                "waktu_id": random.choice(waktu)[0],
                "ruangan_id": random.choice(ruangan)[0],
            }
            chromosome.append(gene)
        population.append(chromosome)

    for generation in range(generations):
        fitness_scores = [(fitness_function(chromosome), chromosome) for chromosome in population]
        fitness_scores.sort(reverse=True, key=lambda x: x[0])
        next_generation = [chromosome for _, chromosome in fitness_scores[:population_size // 2]]

        for i in range(0, len(next_generation), 2):
            if i + 1 < len(next_generation):
                child1, child2 = crossover(next_generation[i], next_generation[i + 1])
                next_generation.extend([child1, child2])

        for chromosome in next_generation:
            mutate(chromosome, waktu, ruangan)

        population = next_generation

    best_chromosome = max(population, key=lambda chromosome: fitness_function(chromosome))
    return best_chromosome

# Fungsi untuk mencetak jadwal dalam format matriks menggunakan tabulate
def print_schedule_matrix(schedule_matrix):
    headers = ["Waktu", "Ruangan", "Mata Kuliah", "Dosen", "Kelas"]
    rows = []
    for waktu, slot in schedule_matrix.items():
        for ruangan, details in slot.items():
            rows.append([waktu, ruangan] + details)

    print(tabulate(rows, headers=headers, tablefmt="grid"))

# Fungsi untuk menyimpan jadwal ke dalam Excel
def save_schedule_to_excel(schedule_matrix, filename="jadwal.xlsx"):
    wb = Workbook()
    ws = wb.active
    ws.title = "Jadwal"

    headers = ["Waktu", "Ruangan", "Mata Kuliah", "Dosen", "Kelas"]
    ws.append(headers)

    for waktu, slot in schedule_matrix.items():
        for ruangan, details in slot.items():
            ws.append([waktu, ruangan] + details)

    wb.save(filename)
    print(f"Jadwal berhasil disimpan dalam file {filename}")

# Fungsi utama untuk menjalankan penjadwalan
def schedule_roster():
    dosen_mk, kelas, waktu, ruangan = get_data()

    if not (dosen_mk and kelas and waktu and ruangan):
        print("Data tidak lengkap. Pastikan semua tabel memiliki data.")
        return

    # Membuat tugas berdasarkan dosen, matkul, dan kelas
    tasks = [(dmk[2], dmk[0], dmk[4], random.choice(kelas)[0]) for dmk in dosen_mk]

    # Menjalankan algoritma genetika
    result = genetic_algorithm(tasks, dosen_mk, waktu, kelas, ruangan)

    # Membuat matriks jadwal
    schedule_matrix = {}
    for gene in result:
        waktu_entry = next(w for w in waktu if w[0] == gene["waktu_id"])
        waktu_str = f"{waktu_entry[1]} {waktu_entry[2]}-{waktu_entry[3]}"

        ruangan_name = next(r[1] for r in ruangan if r[0] == gene["ruangan_id"])
        matkul_name = next(m[3] for m in dosen_mk if m[2] == gene["matkul_id"])
        dosen_name = next(d[1] for d in dosen_mk if d[0] == gene["dosen_id"])
        kelas_name = next(k[1] for k in kelas if k[0] == gene["kelas_id"])

        if waktu_str not in schedule_matrix:
            schedule_matrix[waktu_str] = {}

        schedule_matrix[waktu_str][ruangan_name] = [matkul_name, dosen_name, kelas_name]

    # Cetak jadwal
    print_schedule_matrix(schedule_matrix)

    # Simpan jadwal ke file Excel
    save_schedule_to_excel(schedule_matrix)

# Jalankan
schedule_roster()
