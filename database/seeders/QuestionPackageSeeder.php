<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\QuestionPackage;
use Illuminate\Database\Seeder;

class QuestionPackageSeeder extends Seeder {
    public function run(): void {
        // Ambil teacher dari database, atau buat beberapa jika belum ada atau kurang dari 3
        $teachers = User::where('role', 'teacher')->get();
        
        if ($teachers->count() < 3) {
            $teacherData = [
                ['name' => 'Budi Santoso, S.Pd.', 'email' => 'budi@example.com'],
                ['name' => 'Siti Aminah, M.Pd.', 'email' => 'siti@example.com'],
                ['name' => 'Dr. Ahmad Hidayat', 'email' => 'ahmad@example.com'],
            ];

            foreach ($teacherData as $data) {
                // Gunakan updateOrCreate agar tidak duplikat jika email sudah ada (misal dari RoleAndPermissionSeeder)
                $teachers->push(User::updateOrCreate(
                    ['email' => $data['email']],
                    [
                        'name' => $data['name'],
                        'password' => \Illuminate\Support\Facades\Hash::make('password'),
                        'role' => User::ROLE_TEACHER,
                        'is_approved' => true
                    ]
                ));
            }
            
            // Refresh collection to remove duplicates (if any) and ensure uniqueness by ID
            $teachers = $teachers->unique('id');
        }

        $topics = [
            [
                'name' => 'Matematika Dasar',
                'desc' => 'Ujian penguasaan matematika dasar meliputi aritmatika dan geometri.',
                'mc' => [
                    ['text' => 'Berapakah hasil dari 5 + 7 x 2?', 'options' => ['A' => '24', 'B' => '19', 'C' => '17', 'D' => '14', 'E' => '12'], 'correct' => 'B'],
                    ['text' => 'Berapa luas persegi panjang dengan panjang 10 dan lebar 5?', 'options' => ['A' => '15', 'B' => '50', 'C' => '20', 'D' => '100', 'E' => '30'], 'correct' => 'B']
                ],
                'essay' => [
                    ['text' => 'Sebutkan 3 bilangan prima pertama!', 'correct' => '2, 3, dan 5'],
                    ['text' => 'Jelaskan apa yang dimaksud dengan bilangan genap!', 'correct' => 'Bilangan yang habis dibagi dua.']
                ]
            ],
            [
                'name' => 'Pancasila',
                'desc' => 'Evaluasi pemahaman nilai-nilai dasar Pancasila.',
                'mc' => [
                    ['text' => 'Sila ke-3 Pancasila berbunyi?', 'options' => ['A' => 'Ketuhanan Yang Maha Esa', 'B' => 'Kemanusiaan yang adil dan beradab', 'C' => 'Persatuan Indonesia', 'D' => 'Keadilan sosial', 'E' => 'Kerakyatan yang dipimpin'], 'correct' => 'C'],
                    ['text' => 'Lambang sila pertama adalah?', 'options' => ['A' => 'Rantai', 'B' => 'Pohon Beringin', 'C' => 'Bintang', 'D' => 'Kepala Banteng', 'E' => 'Padi dan Kapas'], 'correct' => 'C']
                ],
                'essay' => [
                    ['text' => 'Berikan 2 contoh penerapan sila pertama di masyarakat!', 'correct' => 'Saling menghormati antar umat beragama dan tidak memaksakan kehendak beragama.'],
                    ['text' => 'Apa lambang sila ke-4?', 'correct' => 'Kepala Banteng']
                ]
            ],
            [
                'name' => 'UUD 1945',
                'desc' => 'Ujian pemahaman konstitusi negara RI.',
                'mc' => [
                    ['text' => 'UUD 1945 disahkan pada tanggal?', 'options' => ['A' => '17 Agustus 1945', 'B' => '18 Agustus 1945', 'C' => '1 Juni 1945', 'D' => '22 Juni 1945', 'E' => '20 Agustus 1945'], 'correct' => 'B'],
                    ['text' => 'Pasal berapakah yang mengatur tentang kebebasan beragama?', 'options' => ['A' => 'Pasal 27', 'B' => 'Pasal 28', 'C' => 'Pasal 29', 'D' => 'Pasal 30', 'E' => 'Pasal 31'], 'correct' => 'C']
                ],
                'essay' => [
                    ['text' => 'Sebutkan 2 lembaga negara baru hasil amandemen UUD 1945!', 'correct' => 'Mahkamah Konstitusi (MK) dan Komisi Yudisial (KY).'],
                    ['text' => 'Berapa kali UUD 1945 diamandemen?', 'correct' => 'Empat kali.']
                ]
            ],
            [
                'name' => 'Sejarah Islam',
                'desc' => 'Materi tentang peradaban dan sejarah Islam.',
                'mc' => [
                    ['text' => 'Siapakah khalifah pertama setelah wafatnya Nabi Muhammad SAW?', 'options' => ['A' => 'Umar bin Khattab', 'B' => 'Ali bin Abi Thalib', 'C' => 'Abu Bakar Ash-Shiddiq', 'D' => 'Utsman bin Affan', 'E' => 'Muawiyah'], 'correct' => 'C'],
                    ['text' => 'Perang Badar terjadi pada tahun ke berapa Hijriah?', 'options' => ['A' => '1 H', 'B' => '2 H', 'C' => '3 H', 'D' => '4 H', 'E' => '5 H'], 'correct' => 'B']
                ],
                'essay' => [
                    ['text' => 'Sebutkan nama 4 Khulafaur Rasyidin!', 'correct' => 'Abu Bakar Ash-Shiddiq, Umar bin Khattab, Utsman bin Affan, Ali bin Abi Thalib.'],
                    ['text' => 'Di kota manakah Nabi Muhammad SAW dilahirkan?', 'correct' => 'Kota Mekkah.']
                ]
            ],
            [
                'name' => 'Sejarah Indonesia',
                'desc' => 'Materi sejarah pergerakan dan kemerdekaan Indonesia.',
                'mc' => [
                    ['text' => 'Siapakah presiden pertama Republik Indonesia?', 'options' => ['A' => 'Soeharto', 'B' => 'B.J. Habibie', 'C' => 'Soekarno', 'D' => 'Megawati', 'E' => 'Gus Dur'], 'correct' => 'C'],
                    ['text' => 'Kapan hari Sumpah Pemuda diperingati?', 'options' => ['A' => '28 Oktober', 'B' => '17 Agustus', 'C' => '10 November', 'D' => '20 Mei', 'E' => '1 Juni'], 'correct' => 'A']
                ],
                'essay' => [
                    ['text' => 'Apa makna peristiwa Rengasdengklok?', 'correct' => 'Peristiwa penculikan Soekarno-Hatta oleh golongan muda untuk mempercepat proklamasi.'],
                    ['text' => 'Sebutkan 3 tokoh proklamator Indonesia!', 'correct' => 'Soekarno, Mohammad Hatta, Ahmad Soebardjo.']
                ]
            ],
            [
                'name' => 'Geografi Nasional',
                'desc' => 'Pemahaman bentang alam dan wilayah Nusantara.',
                'mc' => [
                    ['text' => 'Pulau terbesar di Indonesia adalah?', 'options' => ['A' => 'Jawa', 'B' => 'Sumatera', 'C' => 'Kalimantan', 'D' => 'Papua', 'E' => 'Sulawesi'], 'correct' => 'D'],
                    ['text' => 'Ibukota provinsi Jawa Tengah adalah?', 'options' => ['A' => 'Surabaya', 'B' => 'Semarang', 'C' => 'Bandung', 'D' => 'Yogyakarta', 'E' => 'Solo'], 'correct' => 'B']
                ],
                'essay' => [
                    ['text' => 'Sebutkan 3 gunung berapi aktif di pulau Jawa!', 'correct' => 'Gunung Merapi, Gunung Semeru, dan Gunung Kelud.'],
                    ['text' => 'Apa nama selat yang memisahkan pulau Sumatera dan Jawa?', 'correct' => 'Selat Sunda.']
                ]
            ],
            [
                'name' => 'Biologi Terapan',
                'desc' => 'Ujian ilmu hayati dan makhluk hidup.',
                'mc' => [
                    ['text' => 'Proses tumbuhan membuat makanan sendiri disebut?', 'options' => ['A' => 'Respirasi', 'B' => 'Fotosintesis', 'C' => 'Transpirasi', 'D' => 'Ekskresi', 'E' => 'Pencernaan'], 'correct' => 'B'],
                    ['text' => 'Organ pernapasan utama pada ikan adalah?', 'options' => ['A' => 'Paru-paru', 'B' => 'Insang', 'C' => 'Kulit', 'D' => 'Trakea', 'E' => 'Stomata'], 'correct' => 'B']
                ],
                'essay' => [
                    ['text' => 'Apa perbedaan antara hewan karnivora dan herbivora?', 'correct' => 'Karnivora memakan daging, sedangkan herbivora memakan tumbuhan.'],
                    ['text' => 'Sebutkan fungsi sel darah merah!', 'correct' => 'Mengikat dan mengangkut oksigen ke seluruh tubuh.']
                ]
            ],
            [
                'name' => 'Fisika Dasar',
                'desc' => 'Hukum alam, mekanika, and dasar ilmu fisika.',
                'mc' => [
                    ['text' => 'Satuan standar internasional untuk gaya adalah?', 'options' => ['A' => 'Joule', 'B' => 'Watt', 'C' => 'Newton', 'D' => 'Pascal', 'E' => 'Volt'], 'correct' => 'C'],
                    ['text' => 'Penemu gaya gravitasi adalah?', 'options' => ['A' => 'Albert Einstein', 'B' => 'Isaac Newton', 'C' => 'Nikola Tesla', 'D' => 'Galileo Galilei', 'E' => 'Thomas Edison'], 'correct' => 'B']
                ],
                'essay' => [
                    ['text' => 'Jelaskan Hukum Newton 1!', 'correct' => 'Benda yang diam akan tetap diam, dan benda yang bergerak lurus beraturan akan tetap bergerak lurus beraturan jika resultan gaya bernilai nol.'],
                    ['text' => 'Apa yang dimaksud dengan energi kinetik?', 'correct' => 'Energi yang dimiliki oleh suatu benda karena gerakannya.']
                ]
            ],
            [
                'name' => 'Tata Bahasa Indonesia',
                'desc' => 'Ujian kebahasaan dan struktur bahasa Indonesia.',
                'mc' => [
                    ['text' => 'Manakah kata baku di bawah ini?', 'options' => ['A' => 'Apotik', 'B' => 'Jadual', 'C' => 'Kwalitas', 'D' => 'Apotek', 'E' => 'Nasehat'], 'correct' => 'D'],
                    ['text' => 'Sinonim dari kata "Eksklusif" adalah?', 'options' => ['A' => 'Terbuka', 'B' => 'Khusus', 'C' => 'Umum', 'D' => 'Mewah', 'E' => 'Murah'], 'correct' => 'B']
                ],
                'essay' => [
                    ['text' => 'Buatlah 1 kalimat menggunakan majas personifikasi!', 'correct' => 'Angin malam berbisik pelan di telingaku.'],
                    ['text' => 'Apa yang dimaksud dengan kalimat pasif?', 'correct' => 'Kalimat yang subjeknya dikenai pekerjaan.']
                ]
            ],
            [
                'name' => 'Kewarganegaraan',
                'desc' => 'Pendidikan bela negara dan hak asasi.',
                'mc' => [
                    ['text' => 'Sistem pemerintahan yang dianut Indonesia adalah?', 'options' => ['A' => 'Monarki', 'B' => 'Parlementer', 'C' => 'Presidensial', 'D' => 'Komunis', 'E' => 'Oligarki'], 'correct' => 'C'],
                    ['text' => 'Hak Asasi Manusia (HAM) dilindungi secara hukum. Hal ini menunjukkan Indonesia sebagai negara?', 'options' => ['A' => 'Negara Hukum', 'B' => 'Negara Kekuasaan', 'C' => 'Negara Agama', 'D' => 'Negara Otoriter', 'E' => 'Negara Tradisional'], 'correct' => 'A']
                ],
                'essay' => [
                    ['text' => 'Jelaskan apa yang dimaksud dengan otonomi daerah!', 'correct' => 'Hak, wewenang, dan kewajiban daerah otonom untuk mengatur dan mengurus sendiri urusan pemerintahan dan kepentingan masyarakat.'],
                    ['text' => 'Sebutkan 3 hak warga negara yang dijamin konstitusi!', 'correct' => 'Hak memeluk agama, hak mendapat pendidikan, dan hak atas penghidupan yang layak.']
                ]
            ]
        ];

        $types = ['multiple_choice', 'essay', 'mixed'];
        
        foreach ($types as $type) {
            foreach ($topics as $index => $topic) {
                // Assign teacher randomly or by index
                $teacher = $teachers[$index % $teachers->count()];
                
                $typeName = '';
                if ($type === 'multiple_choice') $typeName = 'Pilihan Ganda';
                if ($type === 'essay') $typeName = 'Esai';
                if ($type === 'mixed') $typeName = 'Campuran';
                
                $package = QuestionPackage::create([
                    'user_id' => $teacher->id,
                    'name' => "Paket {$typeName}: {$topic['name']}",
                    'description' => $topic['desc'],
                    'package_type' => $type,
                    'duration_minutes' => 60,
                    'passing_score' => 70,
                    'attempt_limit' => 2,
                    'shuffle_questions' => true,
                    'shuffle_answers' => true,
                    'is_published' => true,
                    'total_questions_count' => 0
                ]);

                $questionsCount = 0;
                $order = 1;

                if (in_array($type, ['multiple_choice', 'mixed'])) {
                    foreach ($topic['mc'] as $mc) {
                        $question = Question::create([
                            'question_package_id' => $package->id,
                            'question_type' => 'multiple_choice',
                            'question_text' => $mc['text'],
                            'correct_answer' => $mc['correct'],
                            'difficulty_level' => 'medium',
                            'order' => $order++,
                            'is_active' => true,
                        ]);
                        
                        foreach ($mc['options'] as $label => $text) {
                            QuestionOption::create([
                                'question_id' => $question->id,
                                'option_label' => $label,
                                'option_text' => $text,
                            ]);
                        }
                        $questionsCount++;
                    }
                }

                if (in_array($type, ['essay', 'mixed'])) {
                    foreach ($topic['essay'] as $es) {
                        Question::create([
                            'question_package_id' => $package->id,
                            'question_type' => 'essay',
                            'question_text' => $es['text'],
                            'correct_answer' => $es['correct'],
                            'difficulty_level' => 'medium',
                            'order' => $order++,
                            'is_active' => true,
                        ]);
                        $questionsCount++;
                    }
                }

                $package->update(['total_questions_count' => $questionsCount]);
            }
        }

        echo "✅ QuestionPackageSeeder berhasil dijalankan!\n";
    }
}
