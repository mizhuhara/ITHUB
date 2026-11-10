<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\JobListing;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class JobListingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure Partner Users exist for official institutions
        $telkomUser = User::firstOrCreate(
            ['email' => 'corporate@telkom.co.id'],
            [
                'name' => 'PT. Telekomunikasi Indonesia Tbk',
                'password' => Hash::make('password123'),
                'role' => 'company',
                'status' => 'active',
                'is_premium_active' => true,
                'membership_status' => 'premium',
                'membership_plan' => 'enterprise_1_year',
                'membership_started_at' => now()->subMonths(2),
                'membership_expires_at' => now()->addMonths(10),
                'email_verified_at' => now(),
            ]
        );

        $briUser = User::firstOrCreate(
            ['email' => 'callcenter@bri.co.id'],
            [
                'name' => 'PT. Bank Rakyat Indonesia (Persero) Tbk',
                'password' => Hash::make('password123'),
                'role' => 'company',
                'status' => 'active',
                'is_premium_active' => true,
                'membership_status' => 'premium',
                'membership_plan' => 'enterprise_1_year',
                'membership_started_at' => now()->subMonths(1),
                'membership_expires_at' => now()->addMonths(11),
                'email_verified_at' => now(),
            ]
        );

        $gojekUser = User::firstOrCreate(
            ['email' => 'careers@gojek.com'],
            [
                'name' => 'PT. Gojek Indonesia',
                'password' => Hash::make('password123'),
                'role' => 'company',
                'status' => 'active',
                'is_premium_active' => true,
                'membership_status' => 'premium',
                'membership_plan' => 'enterprise_1_year',
                'membership_started_at' => now()->subMonths(3),
                'membership_expires_at' => now()->addMonths(9),
                'email_verified_at' => now(),
            ]
        );

        // Link companies to their users
        Company::where('name', 'like', '%Telekomunikasi Indonesia%')->update(['user_id' => $telkomUser->id]);
        Company::where('name', 'like', '%Bank Rakyat Indonesia%')->update(['user_id' => $briUser->id]);
        Company::where('name', 'like', '%Gojek Indonesia%')->update(['user_id' => $gojekUser->id]);

        $indoappsUser = User::where('email', 'adi@gmail.com')->first();
        if ($indoappsUser) {
            Company::firstOrCreate(
                ['user_id' => $indoappsUser->id],
                [
                    'name' => 'PT. Indoapps Solusindo Teknologi',
                    'type' => 'Swasta',
                    'industry' => 'Software Development & IT Solutions',
                    'city' => 'Denpasar',
                    'province' => 'Bali',
                    'location' => 'Denpasar, Bali',
                    'address' => 'Jl. Teuku Umar No. 88, Denpasar Barat, Bali',
                    'status' => 'Active',
                    'logo_url' => 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=160&h=160&q=80',
                    'logo_text' => 'INDOAPPS',
                    'logo_bg' => 'bg-blue-600',
                    'email' => 'adi@gmail.com',
                    'phone' => '092789273248',
                    'total_employees' => 85,
                    'founded' => 2019,
                ]
            );
        }

        $ijukUser = User::where('email', 'ijuk@gmail.com')->first();
        if ($ijukUser) {
            Company::firstOrCreate(
                ['user_id' => $ijukUser->id],
                [
                    'name' => 'PT. Ijuk Kreatif Digital',
                    'type' => 'Swasta',
                    'industry' => 'Creative Digital & UI/UX Agency',
                    'city' => 'Jakarta Selatan',
                    'province' => 'DKI Jakarta',
                    'location' => 'Jakarta Selatan, DKI Jakarta',
                    'status' => 'Active',
                    'logo_url' => 'https://images.unsplash.com/photo-1572044162444-ad60f128bdea?auto=format&fit=crop&w=160&h=160&q=80',
                    'logo_text' => 'IJUK',
                    'logo_bg' => 'bg-purple-600',
                    'email' => 'ijuk@gmail.com',
                    'total_employees' => 30,
                    'founded' => 2021,
                ]
            );
        }

        $baliItCompanyUser = User::where('email', 'baliit@academy.id')->first();
        if ($baliItCompanyUser) {
            Company::where('user_id', $baliItCompanyUser->id)->update([
                'name' => 'Bali IT Academys',
                'industry' => 'IT Training & Software Development',
                'city' => 'Denpasar',
                'province' => 'Bali',
                'location' => 'Denpasar, Bali',
                'logo_url' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=160&h=160&q=80',
                'status' => 'Active',
            ]);
        }

        $instikiUser = User::where('email', 'instiki@ac.id')->first();
        $bitacUser = User::where('email', 'bitac@academy.id')->first();

        // 2. Real Verified Partner Job Listings
        $jobs = [
            [
                'code' => 'JOB-9001',
                'user_id' => $indoappsUser?->id,
                'title' => 'Senior Full Stack Developer (Laravel & React)',
                'company' => 'PT. Indoapps Solusindo Teknologi',
                'company_industry' => 'Software Development & IT Solutions',
                'department' => 'Engineering',
                'type' => 'Full-time',
                'category' => 'jobs',
                'experience' => 'Senior',
                'salary_range' => '15–25jt',
                'location' => 'Denpasar, Bali (Hybrid)',
                'location_full' => 'Denpasar Barat, Kota Denpasar, Bali',
                'company_size' => '50-100 Karyawan',
                'skills' => ['Laravel', 'React', 'TypeScript', 'MySQL', 'Redis', 'Docker'],
                'tags' => ['Engineering', 'Laravel', 'Full-time'],
                'description' => 'Membangun arsitektur platform web aplikasi enterprise berbasis Laravel dan React TypeScript. Bekerja bersama tim engineer dalam mengimplementasikan fitur berkecepatan tinggi, optimasi query relasional, dan integrasi payment gateway.',
                'requirements' => "- Minimal 4+ tahun pengalaman profesional pengembangan backend Laravel dan frontend React.\n- Menguasai perancangan database MySQL, optimasi query, dan indexing.\n- Berpengalaman dengan Redis caching, RESTful API/GraphQL, dan Docker.\n- Memiliki komitmen tinggi terhadap clean code, testing, dan performa sistem.",
                'deadline' => '28 Des 2026',
                'status' => 'Active',
                'tab_status' => 'active',
                'is_active' => true,
                'total_views' => 348,
                'applicants_count' => 27,
                'logo_color' => '#2563eb',
                'logo_text' => 'IA',
                'logo_url' => 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=160&h=160&q=80',
                'image' => 'https://images.unsplash.com/photo-1551434678-e076c223a692?w=600&h=300&fit=crop',
            ],
            [
                'code' => 'JOB-9002',
                'user_id' => $baliItCompanyUser?->id,
                'title' => 'Mobile Application Developer (Flutter)',
                'company' => 'Bali IT Academys',
                'company_industry' => 'IT Training & Software Development',
                'department' => 'Mobile Development',
                'type' => 'Full-time',
                'category' => 'jobs',
                'experience' => 'Mid',
                'salary_range' => '8–15jt',
                'location' => 'Denpasar, Bali (On-site)',
                'location_full' => 'Denpasar Selatan, Kota Denpasar, Bali',
                'company_size' => '20-50 Karyawan',
                'skills' => ['Flutter', 'Dart', 'REST API', 'Firebase', 'Git', 'BLoC'],
                'tags' => ['Mobile', 'Flutter', 'Full-time'],
                'description' => 'Mengembangkan aplikasi mobile cross-platform (Android & iOS) berbasis Flutter untuk portal ekosistem pembelajaran dan sertifikasi IT. Berfokus pada fluid UX, offline-first sync, dan integrasi state management BLoC.',
                'requirements' => "- Minimal 2 tahun pengalaman membangun aplikasi mobile menggunakan Flutter dan Dart.\n- Memahami arsitektur state management (BLoC / Provider / Riverpod).\n- Pengalaman integrasi RESTful API, Firebase Cloud Messaging, dan local database.\n- Pernah merilis aplikasi di Google Play Store atau Apple App Store.",
                'deadline' => '20 Des 2026',
                'status' => 'Active',
                'tab_status' => 'active',
                'is_active' => true,
                'total_views' => 412,
                'applicants_count' => 35,
                'logo_color' => '#06b6d4',
                'logo_text' => 'BA',
                'logo_url' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=160&h=160&q=80',
                'image' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=600&h=300&fit=crop',
            ],
            [
                'code' => 'JOB-9003',
                'user_id' => $telkomUser->id,
                'title' => 'Cloud & DevOps Infrastructure Engineer',
                'company' => 'PT. Telekomunikasi Indonesia Tbk',
                'company_industry' => 'Telekomunikasi & Digital Network',
                'department' => 'Cloud Infrastructure',
                'type' => 'Full-time',
                'category' => 'jobs',
                'experience' => 'Senior',
                'salary_range' => '25jt+',
                'location' => 'Bandung / Jakarta (Hybrid)',
                'location_full' => 'Kota Bandung & Jakarta Selatan',
                'company_size' => '500+ Karyawan',
                'skills' => ['Docker', 'Kubernetes', 'AWS', 'CI/CD', 'Linux', 'Terraform'],
                'tags' => ['DevOps', 'Cloud', 'Kubernetes'],
                'description' => 'Mengelola infrastruktur cloud berskala nasional, pipeline otomasi CI/CD, dan container orchestration menggunakan Kubernetes. Memastikan reliabilitas, high availability, dan monitoring proaktif pada ekosistem telekomunikasi digital.',
                'requirements' => "- 4+ tahun pengalaman dalam DevOps, SRE, atau Cloud Engineering pada lingkungan skala enterprise.\n- Mahir mengoperasikan Kubernetes (EKS/GKE), Docker, dan Terraform (IaC).\n- Berpengalaman membangun CI/CD pipeline dengan GitLab CI atau GitHub Actions.\n- Pemahaman mendalam tentang networking, load balancing, SSL/TLS, dan Linux administration.",
                'deadline' => '15 Jan 2027',
                'status' => 'Active',
                'tab_status' => 'active',
                'is_active' => true,
                'total_views' => 580,
                'applicants_count' => 48,
                'logo_color' => '#dc2626',
                'logo_text' => 'TL',
                'logo_url' => 'https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&w=160&h=160&q=80',
                'image' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=600&h=300&fit=crop',
            ],
            [
                'code' => 'JOB-9004',
                'user_id' => $briUser->id,
                'title' => 'Cyber Security Analyst & Threat Hunter',
                'company' => 'PT. Bank Rakyat Indonesia (Persero) Tbk',
                'company_industry' => 'Perbankan & Financial Technology',
                'department' => 'Information Security',
                'type' => 'Full-time',
                'category' => 'jobs',
                'experience' => 'Mid',
                'salary_range' => '18–28jt',
                'location' => 'Jakarta Pusat (On-site)',
                'location_full' => 'Jl. Jend. Sudirman Kav. 44-46, Jakarta Pusat',
                'company_size' => '500+ Karyawan',
                'skills' => ['Cyber Security', 'SIEM', 'Network Security', 'Python', 'ISO 27001'],
                'tags' => ['Security', 'Fintech', 'Full-time'],
                'description' => 'Mengawasi sistem monitoring Security Operations Center (SOC), menganalisis indikator ancaman siber (IOC), menjalankan audit kerentanan, dan memperkuat postur keamanan perbankan digital nasional.',
                'requirements' => "- Minimal 3 tahun pengalaman di bidang Information Security, SOC Analyst, atau Penetration Testing.\n- Memahami framework MITRE ATT&CK, tools SIEM (Splunk/Elastic), dan WAF.\n- Sertifikasi keamanan seperti CEH, CompTIA Security+, atau OSCP merupakan nilai tambah.\n- Memahami standar keamanan data industri perbankan (PCI-DSS & ISO 27001).",
                'deadline' => '18 Jan 2027',
                'status' => 'Active',
                'tab_status' => 'active',
                'is_active' => true,
                'total_views' => 395,
                'applicants_count' => 29,
                'logo_color' => '#d97706',
                'logo_text' => 'BR',
                'logo_url' => 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?auto=format&fit=crop&w=160&h=160&q=80',
                'image' => 'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=600&h=300&fit=crop',
            ],
            [
                'code' => 'JOB-9005',
                'user_id' => $gojekUser->id,
                'title' => 'Backend Software Engineer (Golang & High-Throughput)',
                'company' => 'PT. Gojek Indonesia',
                'company_industry' => 'On-Demand & Payments Platform',
                'department' => 'Engineering - Core Platform',
                'type' => 'Full-time',
                'category' => 'jobs',
                'experience' => 'Senior',
                'salary_range' => '25jt+',
                'location' => 'Jakarta Selatan (Hybrid)',
                'location_full' => 'GoTo Tower, Pasar Minggu, Jakarta Selatan',
                'company_size' => '500+ Karyawan',
                'skills' => ['Go', 'Kafka', 'PostgreSQL', 'Redis', 'Docker', 'gRPC'],
                'tags' => ['Backend', 'Golang', 'Microservices'],
                'description' => 'Mengembangkan layanan microservices terdistribusi dengan Golang dan gRPC yang memproses jutaan transaksi real-time per detik. Mendesain pipeline event-driven dengan Apache Kafka dan arsitektur data cache yang tangguh.',
                'requirements' => "- 4+ tahun pengalaman backend engineering dengan bahasa Go (Golang) pada arsitektur microservices.\n- Terbiasa merancang sistem event-driven dengan Apache Kafka atau RabbitMQ.\n- Pengalaman mendalam dengan PostgreSQL, Redis caching, dan query performance profiling.\n- Berpengalaman dengan concurrency patterns, benchmarking, dan resilient distributed systems.",
                'deadline' => '10 Jan 2027',
                'status' => 'Active',
                'tab_status' => 'active',
                'is_active' => true,
                'total_views' => 630,
                'applicants_count' => 62,
                'logo_color' => '#16a34a',
                'logo_text' => 'GJ',
                'logo_url' => 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=160&h=160&q=80',
                'image' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=600&h=300&fit=crop',
            ],
            [
                'code' => 'JOB-9006',
                'user_id' => $ijukUser?->id,
                'title' => 'UI/UX & Product Designer',
                'company' => 'PT. Ijuk Kreatif Digital',
                'company_industry' => 'Creative Digital & UI/UX Agency',
                'department' => 'Product & Design',
                'type' => 'Full-time',
                'category' => 'remote',
                'experience' => 'Mid',
                'salary_range' => '10–18jt',
                'location' => 'Remote (Indonesia)',
                'location_full' => 'Kerja Remote dari Seluruh Indonesia',
                'company_size' => '10-30 Karyawan',
                'skills' => ['Figma', 'UI/UX', 'Design System', 'User Research', 'Prototyping'],
                'tags' => ['Design', 'UI/UX', 'Remote'],
                'description' => 'Memimpin proses riset kebutuhan pengguna, pembuatan wireframe, visual design, dan prototipe interaktif menggunakan Figma. Mengembangkan token desain dan komponen design system yang konsisten untuk aplikasi web dan mobile.',
                'requirements' => "- Minimal 2-3 tahun pengalaman sebagai UI/UX Designer atau Product Designer untuk aplikasi web & mobile.\n- Portofolio desain kuat yang menunjukkan alur riset pengguna, problem framing, dan visual design berkualitas tinggi.\n- Mahir menggunakan Figma (Auto-layout, Components, Variants, Variables/Tokens).\n- Kemampuan komunikasi efektif untuk berkolaborasi secara remote bersama engineer dan product owner.",
                'deadline' => '05 Jan 2027',
                'status' => 'Active',
                'tab_status' => 'active',
                'is_active' => true,
                'total_views' => 320,
                'applicants_count' => 44,
                'logo_color' => '#9333ea',
                'logo_text' => 'IJ',
                'logo_url' => 'https://images.unsplash.com/photo-1572044162444-ad60f128bdea?auto=format&fit=crop&w=160&h=160&q=80',
                'image' => 'https://images.unsplash.com/photo-1542744094-3a31f272c490?w=600&h=300&fit=crop',
            ],
            [
                'code' => 'JOB-9007',
                'user_id' => $instikiUser?->id,
                'title' => 'IT Lab Assistant & Web Programming Mentor',
                'company' => 'Institut bisnis dan teknologi indonesia',
                'company_industry' => 'Pendidikan Tinggi Teknologi',
                'department' => 'Fakultas Teknologi Informasi',
                'type' => 'Part-time',
                'category' => 'freelance',
                'experience' => 'Entry',
                'salary_range' => '< 8jt',
                'location' => 'Denpasar, Bali (On-site)',
                'location_full' => 'Kampus INSTIKI Denpasar, Bali',
                'company_size' => '100-300 Karyawan',
                'skills' => ['PHP', 'JavaScript', 'MySQL', 'Git', 'HTML/CSS'],
                'tags' => ['Education', 'Mentor', 'Part-time'],
                'description' => 'Mendampingi mahasiswa dalam praktikum pemrograman web, workshop teknologi, dan mentoring proyek akhir mahasiswa di lab komputer kampus inovasi INSTIKI.',
                'requirements' => "- Mahasiswa tingkat akhir atau lulusan S1 Sistem Informasi / Teknik Informatika.\n- Menguasai dasar pemrograman web (PHP, JavaScript, HTML5/CSS3) dan database MySQL.\n- Memiliki passion mengajar dan keterampilan komunikasi yang ramah serta sabar.",
                'deadline' => '30 Des 2026',
                'status' => 'Active',
                'tab_status' => 'active',
                'is_active' => true,
                'total_views' => 198,
                'applicants_count' => 15,
                'logo_color' => '#4f46e5',
                'logo_text' => 'IN',
                'logo_url' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=160&h=160&q=80',
                'image' => 'https://images.unsplash.com/photo-1531403009284-440f080d1e12?w=600&h=300&fit=crop',
            ],
            [
                'code' => 'JOB-9008',
                'user_id' => $bitacUser?->id,
                'title' => 'Junior Frontend Instructor (Fresh Graduate)',
                'company' => 'Bali It Academy',
                'company_industry' => 'Akademi & Bootcamp IT',
                'department' => 'Akademik & Pelatihan',
                'type' => 'Full-time',
                'category' => 'graduate',
                'experience' => 'Entry',
                'salary_range' => '8–12jt',
                'location' => 'Denpasar, Bali (Hybrid)',
                'location_full' => 'Bali IT Academy Kampus Utama',
                'company_size' => '20-50 Karyawan',
                'skills' => ['React', 'Node.js', 'Tailwind', 'REST API', 'Git'],
                'tags' => ['Instructor', 'Fresh Graduate', 'Full-time'],
                'description' => 'Program karir fresh graduate berpotensi tinggi untuk mengajar dan membangun kurikulum intensif bootcamp frontend development modern bersama para praktisi industri.',
                'requirements' => "- Lulusan baru (Fresh Graduate) S1/D4 jurusan rumpun informatika/ilmu komputer.\n- Menguasai ekosistem React.js modern, REST API, dan Tailwind CSS.\n- Komunikatif dan memiliki antusiasme dalam membimbing talenta digital muda.",
                'deadline' => '12 Jan 2027',
                'status' => 'Active',
                'tab_status' => 'active',
                'is_active' => true,
                'total_views' => 265,
                'applicants_count' => 22,
                'logo_color' => '#0891b2',
                'logo_text' => 'BT',
                'logo_url' => 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=160&h=160&q=80',
                'image' => 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?w=600&h=300&fit=crop',
            ],
            [
                'code' => 'JOB-9009',
                'user_id' => $telkomUser->id,
                'title' => 'Data Analyst & Business Intelligence Intern',
                'company' => 'PT. Telekomunikasi Indonesia Tbk',
                'company_industry' => 'Telekomunikasi & Digital Network',
                'department' => 'Big Data & Analytics',
                'type' => 'Internship',
                'category' => 'internship',
                'experience' => 'Entry',
                'salary_range' => '< 8jt',
                'location' => 'Jakarta (Hybrid)',
                'location_full' => 'Telkom Landmark Tower, Jakarta Selatan',
                'company_size' => '500+ Karyawan',
                'skills' => ['Python', 'SQL', 'Tableau', 'Power BI', 'Excel'],
                'tags' => ['Internship', 'Data', 'Hybrid'],
                'description' => 'Program magang 6 bulan berbayar di divisi Enterprise Big Data Telkom. Menganalisis pola perilaku pelanggan, membangun visualisasi dashboard interaktif, dan mempresentasikan insight bisnis kepada manajemen.',
                'requirements' => "- Mahasiswa tingkat akhir jurusan Statistika, Matematika, Sistem Informasi, atau Teknik Informatika.\n- Menguasai query SQL analitis dan visualisasi data (Tableau atau Power BI).\n- Memiliki kemampuan dasar Python untuk data manipulation (Pandas/Numpy).",
                'deadline' => '20 Jan 2027',
                'status' => 'Active',
                'tab_status' => 'active',
                'is_active' => true,
                'total_views' => 410,
                'applicants_count' => 53,
                'logo_color' => '#dc2626',
                'logo_text' => 'TL',
                'logo_url' => 'https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&w=160&h=160&q=80',
                'image' => 'https://images.unsplash.com/photo-1551836022-d5d88e9218df?w=600&h=300&fit=crop',
            ],
            [
                'code' => 'JOB-9010',
                'user_id' => $briUser->id,
                'title' => 'Fintech QA & Automated Testing Engineer',
                'company' => 'PT. Bank Rakyat Indonesia (Persero) Tbk',
                'company_industry' => 'Perbankan & Financial Technology',
                'department' => 'Quality Assurance',
                'type' => 'Full-time',
                'category' => 'jobs',
                'experience' => 'Mid',
                'salary_range' => '15–22jt',
                'location' => 'Jakarta Pusat (Hybrid)',
                'location_full' => 'Gedung BRI Sudirman, Jakarta Pusat',
                'company_size' => '500+ Karyawan',
                'skills' => ['Selenium', 'Postman', 'Cypress', 'Java', 'Git'],
                'tags' => ['QA', 'Testing', 'Full-time'],
                'description' => 'Merancang skenario automated end-to-end testing dan API performance testing pada modul perbankan digital BRImo. Memastikan zero defect pada rilis fitur transaksi finansial terkini.',
                'requirements' => "- Minimal 2-3 tahun pengalaman dalam Software Quality Assurance (QA Automation).\n- Menguasai tools automation seperti Cypress, Selenium, Appium, atau Playwright.\n- Pengalaman kuat dalam API testing dengan Postman, Newman, dan CI/CD integrasi.\n- Teliti, memiliki analytical thinking yang tajam, dan pemahaman security testing dasar.",
                'deadline' => '25 Jan 2027',
                'status' => 'Active',
                'tab_status' => 'active',
                'is_active' => true,
                'total_views' => 275,
                'applicants_count' => 19,
                'logo_color' => '#d97706',
                'logo_text' => 'BR',
                'logo_url' => 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?auto=format&fit=crop&w=160&h=160&q=80',
                'image' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=600&h=300&fit=crop',
            ],
        ];

        // Upsert by code so re-running seeds updates or inserts properly
        foreach ($jobs as $jobData) {
            JobListing::updateOrCreate(
                ['code' => $jobData['code']],
                $jobData
            );
        }

        // Also clean up or deactivate old dummy records (JOB-8921 to JOB-8910)
        JobListing::where('code', 'not like', 'JOB-90%')->update([
            'is_active' => false,
            'status' => 'Closed',
            'tab_status' => 'closed',
        ]);
    }
}
