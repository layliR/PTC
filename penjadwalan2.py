import pandas as pd
import random
from sqlalchemy import create_engine, text

def connect_db():
    try:
        engine = create_engine('mysql+mysqlconnector://root:@localhost/penjadwalan2')
        return engine
    except Exception as e:
        print(f"Error connecting to MySQL: {e}")
        exit()

# Fungsi untuk memuat data dari tabel database
def load_data_from_db():
    engine = connect_db()
    query = '''
    SELECT dm.*, d.nama_dosen, m.nama_matkul, p.nama_prodi, p.jurusan
    FROM dosen_mk dm
    JOIN dosen d ON dm.dosen_id = d.dosen_id
    JOIN matakuliah m ON dm.matkul_id = m.matkul_id
    JOIN prodi p ON dm.prodi_id = p.prodi_id
    '''
    dosen_mk = pd.read_sql_query(query, engine)
    waktu = pd.read_sql_query('SELECT * FROM waktu', engine)
    kelas = pd.read_sql_query('SELECT * FROM kelas', engine)
    ruangan = pd.read_sql_query('SELECT * FROM ruangan', engine)
    return dosen_mk, waktu, kelas, ruangan

# Fungsi untuk membuat populasi awal secara random
def initialize_population(dosen_mk, waktu, kelas, ruangan, population_size=100):
    population = []
    for _ in range(population_size):
        individual = []
        for _, row in dosen_mk.iterrows():
            individual.append({
                'nama_dosen': row['nama_dosen'],
                'nama_matkul': row['nama_matkul'],
                'nama_prodi': row['nama_prodi'],
                'jurusan': row['jurusan'],
                'waktu': waktu.sample(1).iloc[0].to_dict(),
                'kelas': kelas.sample(1).iloc[0].to_dict(),
                'ruangan': ruangan.sample(1).iloc[0].to_dict(),
            })
        population.append(individual)
    return population

# Fungsi untuk menghitung fitness (menghindari bentrok)
def calculate_fitness(individual):
    conflicts = 0
    schedule = []

    for entry in individual:
        waktu = entry['waktu']
        kelas = entry['kelas']
        ruangan = entry['ruangan']

        # Bentrok waktu, ruangan, atau kelas
        for s in schedule:
            if (s['waktu'] == waktu and (s['ruangan'] == ruangan or s['kelas'] == kelas)):
                conflicts += 1
        schedule.append({
            'waktu': waktu,
            'kelas': kelas,
            'ruangan': ruangan,
        })

    return 1 / (1 + conflicts)  # Nilai fitness semakin tinggi jika konflik semakin sedikit

# Fungsi seleksi berdasarkan fitness
def selection(population):
    population = sorted(population, key=lambda ind: calculate_fitness(ind), reverse=True)
    return population[:len(population)//2]  # Ambil separuh terbaik

# Fungsi crossover untuk menghasilkan keturunan baru
def crossover(parent1, parent2):
    point = random.randint(0, len(parent1) - 1)
    child1 = parent1[:point] + parent2[point:]
    child2 = parent2[:point] + parent1[point:]
    return child1, child2

# Fungsi mutasi untuk memperkenalkan variasi
def mutate(individual, waktu, kelas, ruangan, mutation_rate=0.1):
    for entry in individual:
        if random.random() < mutation_rate:
            entry['waktu'] = waktu.sample(1).iloc[0].to_dict()
        if random.random() < mutation_rate:
            entry['kelas'] = kelas.sample(1).iloc[0].to_dict()
        if random.random() < mutation_rate:
            entry['ruangan'] = ruangan.sample(1).iloc[0].to_dict()

# Algoritma genetika utama
def genetic_algorithm(dosen_mk, waktu, kelas, ruangan, generations=100, population_size=100):
    population = initialize_population(dosen_mk, waktu, kelas, ruangan, population_size)

    for generation in range(generations):
        # Hitung fitness dan lakukan seleksi
        population = selection(population)

        # Crossover untuk menghasilkan keturunan baru
        next_generation = []
        while len(next_generation) < population_size:
            parent1, parent2 = random.sample(population, 2)
            child1, child2 = crossover(parent1, parent2)
            next_generation.extend([child1, child2])

        # Mutasi
        for individual in next_generation:
            mutate(individual, waktu, kelas, ruangan)

        population = next_generation

        # Cetak fitness terbaik di setiap generasi
        best_fitness = max(calculate_fitness(ind) for ind in population)
        print(f'Generation {generation + 1}: Best Fitness = {best_fitness}')

    # Ambil individu terbaik
    best_individual = max(population, key=lambda ind: calculate_fitness(ind))
    return best_individual

def save_schedule_to_db(schedule, engine):
    # Persiapkan data yang akan dimasukkan
    data_to_insert = []
    
    for entry in schedule:
        waktu = entry['waktu']
        kelas = entry['kelas']
        ruangan = entry['ruangan']
        
        # Ambil jam_mulai dan jam_selesai dari kolom waktu
        jam_mulai = waktu['jam_mulai']
        jam_selesai = waktu['jam_selesai']
        
        print(f"Inserting: {entry['nama_dosen']}, {entry['nama_matkul']}, {waktu['hari']}, {jam_mulai}, {jam_selesai}, {kelas['nama_kelas']}, {ruangan['nama_ruangan']}")
        
        # Kumpulkan setiap baris data sebagai dictionary
        data_to_insert.append({
            'nama_dosen': entry['nama_dosen'],
            'nama_matkul': entry['nama_matkul'],
            'hari': waktu['hari'],
            'jam_mulai': jam_mulai,
            'jam_selesai': jam_selesai,
            'nama_kelas': kelas['nama_kelas'],
            'nama_ruangan': ruangan['nama_ruangan']
        })
    
    try:
        # Pastikan koneksi ditutup setelah operasi selesai
        with engine.connect() as conn:
            # Gunakan text() agar query SQL dapat dieksekusi
            query = text('''
                INSERT INTO jadwal (nama_dosen, nama_matkul, hari, jam_mulai, jam_selesai, nama_kelas, nama_ruangan)
                VALUES (:nama_dosen, :nama_matkul, :hari, :jam_mulai, :jam_selesai, :nama_kelas, :nama_ruangan)
            ''')

            # Melakukan batch insertion dengan metode executemany
            conn.execute(query, data_to_insert)
            conn.commit()  # Pastikan commit untuk menyimpan perubahan
            print("Data successfully inserted into database.")
    except Exception as e:
        print(f"Error saat menyimpan jadwal: {e}")

# Main program
def main():
    dosen_mk, waktu, kelas, ruangan = load_data_from_db()
    best_schedule = genetic_algorithm(dosen_mk, waktu, kelas, ruangan)
    
    # Menyimpan jadwal terbaik ke dalam database
    save_schedule_to_db(best_schedule, connect_db())

if __name__ == '__main__':
    main()
