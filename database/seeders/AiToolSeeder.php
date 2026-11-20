<?php

namespace Database\Seeders;

use App\Models\AiTool;
use Illuminate\Database\Seeder;

class AiToolSeeder extends Seeder
{
    public function run(): void
    {
        if (AiTool::count() > 0) {
            return;
        }

        $tools = [
            [
                'name' => 'GitHub Copilot',
                'category' => 'Pemrograman',
                'icon_type' => 'code',
                'icon_bg' => 'bg-blue-50 text-blue-600',
                'tagline' => 'Asisten Pemrograman AI Berbasis OpenAI Codex & GPT-4',
                'description' => 'Asisten AI berbasis cloud yang menyediakan penyelesaian kode otomatis dan saran arsitektur langsung di editor Anda, mempercepat siklus pengembangan perangkat lunak secara signifikan.',
                'overview' => 'GitHub Copilot adalah alat pasangan pemrogram berbasis AI (AI Pair Programmer) yang dikembangkan oleh GitHub bersama OpenAI. Alat ini terintegrasi langsung ke dalam lingkungan pengembangan (IDE) seperti VS Code, Visual Studio, Neovim, dan JetBrains untuk membantu pengembang menulis kode lebih cepat dan efisien.',
                'what_it_does' => 'Mengkonversi instruksi bahasa alami menjadi kode program executable, menyarankan penyelesaian baris demi baris, membuat pengujian otomatis (unit test), dan menjelaskan alur algoritma yang rumit secara instan.',
                'capabilities' => [
                    [
                        'title' => 'Penyelesaian Kode Otomatis (Autocompletion)',
                        'desc' => 'Menyediakan saran penyelesaian baris atau seluruh blok fungsi secara real-time saat Anda mengetik kode.',
                    ],
                    [
                        'title' => 'Chat & Refactoring di IDE (Copilot Chat)',
                        'desc' => 'Memungkinkan Anda bertanya tentang kode, mendeteksi bug, serta melakukan refactoring langsung di editor.',
                    ],
                    [
                        'title' => 'Pembuatan Dokumen & Unit Test',
                        'desc' => 'Dapat membuat skenario pengujian unit (Unit Testing) dan dokumentasi fungsi secara otomatis.',
                    ],
                    [
                        'title' => 'Dukungan Multi-Bahasa Pemrograman',
                        'desc' => 'Mendukung berbagai bahasa utama seperti Python, JavaScript, TypeScript, PHP, Go, C++, Ruby, dan HTML/CSS.',
                    ],
                ],
                'use_cases' => [
                    'Mempercepat pembuatan REST API dan komponen UI.',
                    'Memahami repositori kode legacy yang besar.',
                    'Mencari rujukan sintaks dan algoritma tanpa harus meninggalkan IDE.',
                ],
                'target_audience' => 'Pengembang Perangkat Lunak, Student Developer, Data Engineer, & Enterprise Tech Team.',
                'url' => 'https://github.com/features/copilot',
                'action_type' => 'detail',
                'pricing' => 'Freemium / Berlangganan',
            ],
            [
                'name' => 'Cursor',
                'category' => 'Pemrograman',
                'icon_type' => 'editor',
                'icon_bg' => 'bg-indigo-50 text-indigo-600',
                'tagline' => 'Editor Kode Generasi Baru dengan Integrasi AI Mendalam',
                'description' => 'Editor kode cerdas yang dibangun khusus dengan integrasi AI tingkat lanjut, memungkinkan modifikasi basis kode skala besar dan refactoring kode secara natural dan intuitif.',
                'overview' => 'Cursor adalah IDE turunan VS Code yang dirancang secara khusus mengutamakan kecerdasan buatan (AI-first). Cursor memungkinkan pengembang melakukan pengeditan basis kode skala besar, bertanya pada seluruh codebase, dan mengedit beberapa file sekaligus menggunakan instruksi bahasa alami.',
                'what_it_does' => 'Cursor menghubungkan kecerdasan LLM (seperti Claude 3.5 Sonnet & GPT-4o) dengan basis kode lokal Anda, sehingga dapat mengedit banyak file dalam satu perintah, memperbaiki error terminal secara otomatis, dan menjawab pertanyaan seputar repositori.',
                'capabilities' => [
                    [
                        'title' => 'Codebase Chat & Context Indexing',
                        'desc' => 'Dapat merujuk pada seluruh struktur proyek Anda (@codebase) untuk menjawab pertanyaan arsitektur secara akurat.',
                    ],
                    [
                        'title' => 'Multi-File Editing (Cmd+K)',
                        'desc' => 'Mengubah dan memperbarui beberapa file sekaligus berdasarkan deskripsi tugas yang Anda berikan.',
                    ],
                    [
                        'title' => 'Terminal Agent & Auto-Fix Error',
                        'desc' => 'Menganalisis pesan kesalahan terminal dan menawarkan perbaikan satu-klik.',
                    ],
                    [
                        'title' => 'Kustomisasi Model AI',
                        'desc' => 'Bebas memilih model LLM terbaik seperti Claude 3.5 Sonnet, GPT-4o, atau model khusus lainnya.',
                    ],
                ],
                'use_cases' => [
                    'Refactoring basis kode monolitik menjadi microservices.',
                    'Pembangunan proyek aplikasi dari awal dalam waktu singkat.',
                    'Perbaikan bug kompleks lintas file.',
                ],
                'target_audience' => 'Fullstack Developer, Lead Engineers, Tech Startup Founders.',
                'url' => 'https://cursor.com',
                'action_type' => 'detail',
                'pricing' => 'Gratis dengan Opsi Pro',
            ],
            [
                'name' => 'IBM Watson Health',
                'category' => 'Kesehatan',
                'icon_type' => 'health',
                'icon_bg' => 'bg-blue-50 text-blue-600',
                'tagline' => 'Sistem Komputasi Kognitif Analisis Data Medis & Klinis',
                'description' => 'Sistem komputasi kognitif yang memproses volume besar data medis untuk membantu profesional klinis dalam diagnosis presisi, perencanaan perawatan, dan penelitian medis mutakhir.',
                'overview' => 'IBM Watson Health memanfaatkan pemrosesan bahasa alami (NLP) dan pembelajaran mesin untuk membantu profesional kesehatan memproses data medis terstruktur dan tidak terstruktur dalam jumlah masif.',
                'what_it_does' => 'Membantu rumah sakit dan lembaga medis dalam menganalisis data rekam medis elektronik, hasil riset ilmiah terbaru, dan citra medis guna mendukung keputusan klinis yang lebih cepat dan tepat.',
                'capabilities' => [
                    [
                        'title' => 'Analisis Dokumen Medis & NLP',
                        'desc' => 'Membaca dan mengidentifikasi informasi penting dari ribuan halaman jurnal medis dan catatan dokter.',
                    ],
                    [
                        'title' => 'Rekomendasi Perawatan Presisi',
                        'desc' => 'Mencocokkan kondisi spesifik pasien dengan opsi terapi dan uji klinis medis terbaru.',
                    ],
                    [
                        'title' => 'Optimalisasi Alur Kerja Klinis',
                        'desc' => 'Meningkatkan efisiensi pengelolaan data kesehatan dan alokasi sumber daya medis.',
                    ],
                ],
                'use_cases' => [
                    'Riset klinis obat baru dan pengobatan kanker.',
                    'Pengolahan data rekam medis terpadu di rumah sakit.',
                    'Dukungan keputusan diagnostik dokter spesialis.',
                ],
                'target_audience' => 'Dokter, Peneliti Medis, Rumah Sakit, & Institusi Kesehatan.',
                'url' => 'https://www.ibm.com/watson-health',
                'action_type' => 'visit',
                'pricing' => 'Lisensi Enterprise Medis',
            ],
            [
                'name' => 'Zebra Medical Vision',
                'category' => 'Kesehatan',
                'icon_type' => 'medical_plus',
                'icon_bg' => 'bg-sky-50 text-sky-600',
                'tagline' => 'Platform Analisis Gambar Medis & Diagnostik Radiologi AI',
                'description' => 'Platform analisis gambar medis yang memanfaatkan deep learning untuk mengidentifikasi anomali radiologis secara otomatis, meningkatkan akurasi deteksi dini penyakit kronis.',
                'overview' => 'Zebra Medical Vision (sekarang bagian dari Nanox) menggunakan algoritma deep learning untuk menganalisis gambar CT scan, X-Ray, dan Mammografi guna mendeteksi kondisi medis serius lebih awal.',
                'what_it_does' => 'Pemindaian gambar medis secara otomatis untuk mendeteksi tanda awal pendarahan otak, penyakit tulang, massa tumor, dan penyakit kardiovaskular.',
                'capabilities' => [
                    [
                        'title' => 'Deteksi Anomali Radiologis Otomatis',
                        'desc' => 'Memberikan indikasi awal dan anotasi visual pada hasil Rontgen dan CT scan pasien.',
                    ],
                    [
                        'title' => 'Integrasi Sistem PACS/DICOM Rumah Sakit',
                        'desc' => 'Terhubung langsung dengan alur kerja departemen radiologi tanpa mengganggu sistem operasional.',
                    ],
                    [
                        'title' => 'Skor Risiko Kesehatan Dini',
                        'desc' => 'Menghitung tingkat keparahan anomali untuk menentukan prioritas penanganan medis.',
                    ],
                ],
                'use_cases' => [
                    'Screening kesehatan massal di laboratorium radiologi.',
                    'Deteksi dini penyakit kronis seperti osteoporosis dan osteoartritis.',
                    'Triase darurat pasien trauma di UGD.',
                ],
                'target_audience' => 'Dokter Radiologi, Laboratorium Klinis, Rumah Sakit.',
                'url' => 'https://www.nanox.vision',
                'action_type' => 'visit',
                'pricing' => 'Platform Berlangganan Klinis',
            ],
            [
                'name' => 'Prospera',
                'category' => 'Pertanian',
                'icon_type' => 'agri',
                'icon_bg' => 'bg-cyan-50 text-cyan-600',
                'tagline' => 'Solusi Agritech Cerdas Pemantauan Kesehatan Tanaman',
                'description' => 'Solusi agritech cerdas yang menggabungkan visi komputer dan machine learning untuk memantau kesehatan tanaman harian, memprediksi hasil panen, dan mengoptimalkan penggunaan sumber daya.',
                'overview' => 'Prospera adalah platform teknologi pertanian (agritech) yang menggunakan penginderaan jarak jauh, AI, dan visi komputer untuk membantu petani membuat keputusan berbasis data di lapangan.',
                'what_it_does' => 'Mengidentifikasi penyakit tanaman, serangan hama, dan kekurangan air/pupuk secara real-time dari data visual lapangan.',
                'capabilities' => [
                    [
                        'title' => 'Pemantauan Visual Lahan Harian',
                        'desc' => 'Menganalisis perkembangan kesehatan dedaunan dan buah tanaman menggunakan kamera sensor.',
                    ],
                    [
                        'title' => 'Prediksi Hasil Panen & Tonase',
                        'desc' => 'Memberikan estimasi kuantitas hasil panen berdasarkan tren pertumbuhan harian.',
                    ],
                    [
                        'title' => 'Efisiensi Pupuk & Irigasi',
                        'desc' => 'Menyarankan jadwal dan dosis penyiraman serta pemupukan yang paling tepat.',
                    ],
                ],
                'use_cases' => [
                    'Manajemen perkebunan skala sedang hingga industri.',
                    'Pencegahan penyakit tanaman pangan sebelum menyebar luas.',
                    'Optimasi penggunaan air irigasi di daerah kering.',
                ],
                'target_audience' => 'Petani Modern, Agronomis, Pengelola Perkebunan Industri.',
                'url' => 'https://prospera.ag',
                'action_type' => 'detail',
                'pricing' => 'Paket Layanan Agritech',
            ],
            [
                'name' => 'Taranis',
                'category' => 'Pertanian',
                'icon_type' => 'leaf',
                'icon_bg' => 'bg-teal-50 text-teal-600',
                'tagline' => 'Analisis Pertanian Presisi Tingkat Daun Berbasis Citra Udara',
                'description' => 'Layanan analitik pertanian presisi yang menggunakan citra resolusi sangat tinggi dari udara untuk mendeteksi ancaman hama, gulma, dan kekurangan nutrisi hingga tingkat daun individu.',
                'overview' => 'Taranis menyediakan platform intelijen tanaman yang memanfaatkan citra udara beresolusi ultra-tinggi (diambil dari drone dan pesawat) untuk mendeteksi ancaman di lahan pertanian hingga ke tingkat detail daun individu.',
                'what_it_does' => 'Memberikan laporan analisis kondisi fisik tanaman secara menyeluruh untuk mencegah gagal panen.',
                'capabilities' => [
                    [
                        'title' => 'Sub-Millimeter Aerial Imaging',
                        'desc' => 'Kamera udara resolusi tinggi yang mampu memotret detail spesies serangga atau bintik daun.',
                    ],
                    [
                        'title' => 'Klasifikasi Gulma & Hama Otomatis',
                        'desc' => 'Jaringan AI mengenali jenis gulma spesifik untuk menentukan jenis pestisida yang tepat.',
                    ],
                    [
                        'title' => 'Peta Panas (Heatmap) Kesehatan Lahan',
                        'desc' => 'Visualisasi bagian lahan mana yang membutuhkan perhatian ekstra dari agronomis.',
                    ],
                ],
                'use_cases' => [
                    'Survei ketersediaan nutrisi tanah dan tanaman.',
                    'Deteksi serangan hama mendadak di lahan seluas puluhan hektar.',
                    'Perencanaan pemeliharaan tanaman berkala.',
                ],
                'target_audience' => 'Agronomis, Konsultan Pertanian, Pemilik Lahan Agribisnis.',
                'url' => 'https://taranis.ag',
                'action_type' => 'detail',
                'pricing' => 'Layanan Analisis Per Hektar',
            ],
        ];

        foreach ($tools as $tool) {
            AiTool::create($tool);
        }
    }
}
