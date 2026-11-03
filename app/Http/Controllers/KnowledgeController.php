<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Company;
use App\Models\School;
use App\Models\University;
use Illuminate\Http\Request;

class KnowledgeController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::published()->with('user');

        // Category filter
        $category = $request->query('category', 'All');
        if (strtolower($category) !== 'all' && strtolower($category) !== 'articles') {
            $query->where('category', $category);
        }

        // Search keyword
        if ($request->filled('q')) {
            $q = trim($request->query('q'));
            $query->where(function ($sq) use ($q) {
                $sq->where('title', 'like', "%{$q}%")
                    ->orWhere('author_name', 'like', "%{$q}%")
                    ->orWhere('category', 'like', "%{$q}%")
                    ->orWhere('excerpt', 'like', "%{$q}%");
            });
        }

        $perPage = 6;
        $currentPage = max(1, (int) $request->query('page', 1));
        $total = $query->count();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $currentPage = min($currentPage, $totalPages);

        $articles = $query->orderByDesc('published_at')
            ->orderByDesc('id')
            ->skip(($currentPage - 1) * $perPage)
            ->take($perPage)
            ->get()
            ->map(fn (Article $a) => $this->articleToArray($a))
            ->all();

        $categories = [
            'All', 'Articles', 'Technology', 'Programming', 'Web Development',
            'Mobile Development', 'AI', 'Cybersecurity', 'Networking',
            'Cloud', 'Database', 'UI/UX', 'Career', 'Education',
        ];

        return view('knowladge.index', [
            'articles' => $articles,
            'categories' => $categories,
            'activeCategory' => $category,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
        ]);
    }

    public function ai(Request $request)
    {
        $aiTools = collect($this->getAiTools());

        // Category filter (pills or dropdown)
        $category = $request->query('category', 'Semua');
        if (strtolower($category) !== 'semua' && strtolower($category) !== 'semua bidang') {
            $aiTools = $aiTools->filter(function ($tool) use ($category) {
                return strtolower($tool['category']) === strtolower($category);
            });
        }

        if ($request->filled('field') && strtolower($request->query('field')) !== 'semua bidang') {
            $field = strtolower($request->query('field'));
            $aiTools = $aiTools->filter(function ($tool) use ($field) {
                return strtolower($tool['category']) === $field;
            });
        }

        // Search filter
        if ($request->filled('q')) {
            $q = strtolower($request->query('q'));
            $aiTools = $aiTools->filter(function ($tool) use ($q) {
                return str_contains(strtolower($tool['name']), $q)
                    || str_contains(strtolower($tool['description']), $q)
                    || str_contains(strtolower($tool['category']), $q);
            });
        }

        $categories = ['Semua', 'Pemrograman', 'Kesehatan', 'Pertanian'];

        return view('knowladge.ai', [
            'aiTools' => $aiTools->values()->all(),
            'categories' => $categories,
            'activeCategory' => $category,
            'activeField' => $request->query('field', 'Semua Bidang'),
        ]);
    }

    public function aiDetail($id)
    {
        $aiTools = collect($this->getAiTools());
        $tool = $aiTools->firstWhere('id', (int) $id);

        if (! $tool) {
            $tool = $aiTools->firstWhere('id', 1);
        }

        $relatedTools = $aiTools->where('id', '!=', $tool['id'])->take(3)->values()->all();

        return view('knowladge.ai_detail', [
            'tool' => $tool,
            'relatedTools' => $relatedTools,
        ]);
    }

    public function show($id)
    {
        $article = Article::published()->with('user')->findOrFail($id);
        $article->increment('views');

        $articleArray = $this->articleToArray($article);

        // Ensure rich content structure exists
        if (empty($article->content)) {
            $articleArray['content'] = [
                'intro' => $article->excerpt ?? '',
                'section1_title' => 'Perkembangan & Inovasi',
                'section1_body' => 'Artikel ini belum memiliki konten lengkap.',
                'bullets' => [],
                'callout' => '',
                'section2_title' => '',
                'section2_body' => '',
            ];
        } else {
            $articleArray['content'] = $article->content;
        }

        $relatedArticles = Article::published()
            ->with('user')
            ->where('id', '!=', $article->id)
            ->inRandomOrder()
            ->take(3)
            ->get()
            ->map(fn (Article $a) => $this->articleToArray($a))
            ->all();

        return view('knowladge.show', [
            'article' => $articleArray,
            'relatedArticles' => $relatedArticles,
        ]);
    }

    /**
     * Convert an Article model to the array shape expected by the Knowledge Hub views.
     *
     * @return array<string, mixed>
     */
    private function articleToArray(Article $article): array
    {
        $user = $article->user;
        $name = null;
        $role = null;
        $avatar = $article->author_avatar;
        $badge = null;
        $badgeClass = 'bg-slate-100 text-slate-700';
        $authorUrl = null;

        if ($user) {
            switch ($user->role) {
                case 'school':
                    $school = School::where('user_id', $user->id)->first();
                    $name = $school?->name ?: $user->name;
                    $role = $school?->institution_type ?: 'Sekolah Vokasi';
                    $avatar = $school?->logo_url ?: ($user->avatar_url ?: $avatar);
                    $badge = 'Sekolah';
                    $badgeClass = 'bg-blue-50 text-blue-700 border border-blue-200/60';
                    $authorUrl = $school ? route('education.show', $school->id) : null;
                    break;
                case 'university':
                    $univ = University::where('user_id', $user->id)->first();
                    $name = $univ?->name ?: $user->name;
                    $role = $univ?->type ?: 'Perguruan Tinggi';
                    $avatar = $univ?->logo_url ?: ($user->avatar_url ?: $avatar);
                    $badge = 'Kampus';
                    $badgeClass = 'bg-indigo-50 text-indigo-700 border border-indigo-200/60';
                    $authorUrl = $univ ? route('campus.show', $univ->id) : null;
                    break;
                case 'company':
                    $comp = Company::where('user_id', $user->id)->first();
                    $name = $comp?->name ?: $user->name;
                    $role = $comp?->industry ?: 'Perusahaan Mitra';
                    $avatar = $comp?->logo_url ?: ($user->avatar_url ?: $avatar);
                    $badge = 'Industri';
                    $badgeClass = 'bg-emerald-50 text-emerald-700 border border-emerald-200/60';
                    $authorUrl = route('industry', ['q' => $comp?->name ?? '']);
                    break;
                case 'super_admin':
                case 'admin':
                    $name = $user->name ?: 'Admin IndoTech';
                    $role = 'Admin IndoTech';
                    $avatar = $user->avatar_url ?: $avatar;
                    $badge = 'Admin';
                    $badgeClass = 'bg-amber-50 text-amber-700 border border-amber-200/60';
                    break;
                default:
                    $name = $user->name;
                    $role = 'Member IndoTech';
                    $avatar = $user->avatar_url ?: $avatar;
                    $badge = 'Member';
                    $badgeClass = 'bg-slate-100 text-slate-700 border border-slate-200';
                    break;
            }
        }

        if (empty($name)) {
            $name = ! empty($article->author_name) ? $article->author_name : 'Admin IndoTech';
        }
        if (empty($role)) {
            $role = ! empty($article->author_role) ? $article->author_role : 'Kontributor';
        }

        // Format avatar URL properly
        if (! empty($avatar)) {
            if (! str_starts_with($avatar, 'http://') && ! str_starts_with($avatar, 'https://')) {
                $avatar = asset(ltrim($avatar, '/'));
            }
        } else {
            $avatar = null;
        }

        // Initials & deterministic background color for avatar fallback
        $words = preg_split('/\s+/', trim($name));
        $initials = '';
        foreach (array_slice($words, 0, 2) as $w) {
            $initials .= strtoupper(substr($w, 0, 1));
        }
        $initials = ! empty($initials) ? $initials : 'IT';

        $colors = ['#2563eb', '#4f46e5', '#7c3aed', '#059669', '#d97706', '#dc2626', '#0891b2', '#0284c7'];
        $bg = $colors[abs(crc32($name)) % count($colors)];

        return [
            'id' => $article->id,
            'title' => $article->title,
            'category' => $article->category,
            'category_tag' => $article->category,
            'excerpt' => $article->excerpt ?? '',
            'image' => $article->image ?? 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=800&h=500&fit=crop',
            'image_caption' => $article->image_caption ?? null,
            'author_name' => $name,
            'author_avatar' => $avatar,
            'author_initials' => $initials,
            'author_bg' => $bg,
            'author_role' => $role,
            'author_badge' => $badge,
            'author_badge_class' => $badgeClass,
            'author_url' => $authorUrl,
            'date' => $article->formatted_date,
            'read_time' => $article->read_time ?? '5 min read',
            'tags' => $article->tags ?? [],
            'content' => $article->content ?? '',
        ];
    }

    private function getArticles(): array
    {
        return [
            [
                'id' => 1,
                'title' => 'GitHub Copilot: Cara Kerja & Manfaat AI Pair Programmer',
                'category' => 'AI',
                'category_tag' => 'Pemrograman',
                'excerpt' => 'Pelajari bagaimana GitHub Copilot menggunakan model AI canggih untuk membantu pengembang menulis kode 55% lebih cepat langsung dari IDE favorit.',
                'image' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=800&h=500&fit=crop',
                'author_name' => 'Tim IndoTech AI',
                'author_avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=120&h=120&fit=crop',
                'author_role' => 'AI Research Lead',
                'date' => 'Oct 24, 2024',
                'read_time' => '5 min read',
                'ai_tool_id' => 1,
                'tags' => ['ArtificialIntelligence', 'Programming', 'GitHubCopilot', 'DeveloperTools'],
            ],
            [
                'id' => 2,
                'title' => 'Cursor Editor: Membedah Fitur & Keunggulan IDE Berbasis AI',
                'category' => 'Programming',
                'category_tag' => 'Pemrograman',
                'excerpt' => 'Mengenal Cursor, editor kode yang dirancang khusus dari awal dengan integrasi kecerdasan buatan untuk refactoring dan penulisan kode skala besar.',
                'image' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800&h=500&fit=crop',
                'author_name' => 'Budi Santoso',
                'author_avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=120&h=120&fit=crop',
                'author_role' => 'Senior Software Architect',
                'date' => 'Oct 12, 2024',
                'read_time' => '8 min read',
                'ai_tool_id' => 2,
                'tags' => ['CursorIDE', 'SoftwareEngineering', 'Refactoring', 'CodeEditor'],
            ],
            [
                'id' => 3,
                'title' => 'IBM Watson Health: Peran Komputasi Kognitif dalam Dunia Medis',
                'category' => 'Cloud',
                'category_tag' => 'Kesehatan',
                'excerpt' => 'Bagaimana sistem AI pemrosesan bahasa alami membantu dokter menganalisis ribuan rekam medis dan data uji klinis secara akurat.',
                'image' => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=800&h=500&fit=crop',
                'author_name' => 'Siti Rahma',
                'author_avatar' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=120&h=120&fit=crop',
                'author_role' => 'Medical Informatics Specialist',
                'date' => 'Oct 10, 2024',
                'read_time' => '6 min read',
                'ai_tool_id' => 3,
                'tags' => ['HealthTech', 'IBMwatson', 'MedicalAI', 'DataAnalytics'],
            ],
            [
                'id' => 4,
                'title' => 'Zebra Medical Vision: Analisis AI untuk Radiologi & Diagnostik Dini',
                'category' => 'UI/UX',
                'category_tag' => 'Kesehatan',
                'excerpt' => 'Pemanfaatan deep learning untuk mendeteksi anomali radiologis pada pemindaian X-Ray dan CT scan medis secara presisi.',
                'image' => 'https://images.unsplash.com/photo-1516549655169-df83a0774514?w=800&h=500&fit=crop',
                'author_name' => 'Arif Wijaya',
                'author_avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=120&h=120&fit=crop',
                'author_role' => 'Radiology Tech Researcher',
                'date' => 'Oct 08, 2024',
                'read_time' => '6 min read',
                'ai_tool_id' => 4,
                'tags' => ['Radiology', 'DeepLearning', 'Healthcare', 'MedicalImaging'],
            ],
            [
                'id' => 5,
                'title' => 'Prospera Agritech: Visi Komputer untuk Pemantauan Kesehatan Tanaman',
                'category' => 'Technology',
                'category_tag' => 'Pertanian',
                'excerpt' => 'Transformasi sektor pertanian berbasis data dan visi komputer untuk mengoptimalkan penggunaan pupuk dan hasil panen.',
                'image' => 'https://images.unsplash.com/photo-1500937386664-56d1dfef3854?w=800&h=500&fit=crop',
                'author_name' => 'Hendra Wijaya',
                'author_avatar' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=120&h=120&fit=crop',
                'author_role' => 'AgriTech Innovation Lead',
                'date' => 'Oct 18, 2024',
                'read_time' => '5 min read',
                'ai_tool_id' => 5,
                'tags' => ['AgriTech', 'ComputerVision', 'SmartFarming', 'Sustainability'],
            ],
            [
                'id' => 6,
                'title' => 'Taranis: Analitik Udara Berpresisi Tinggi Hingga Tingkat Daun',
                'category' => 'Education',
                'category_tag' => 'Pertanian',
                'excerpt' => 'Penggunaan citra udara beresolusi tinggi dan kecerdasan buatan untuk mendeteksi hama dan kekurangan nutrisi tanaman sejak dini.',
                'image' => 'https://images.unsplash.com/photo-1625246333195-78d9c38ad449?w=800&h=500&fit=crop',
                'author_name' => 'Dr. Budi Santoso',
                'author_avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=120&h=120&fit=crop',
                'author_role' => 'Agronomist & Data Scientist',
                'date' => 'Oct 15, 2024',
                'read_time' => '7 min read',
                'ai_tool_id' => 6,
                'tags' => ['PrecisionAgriculture', 'DroneAnalytics', 'CropScience', 'AgriAI'],
            ],
            [
                'id' => 7,
                'title' => 'Strategi Keamanan Siber (Cybersecurity) bagi Organisasi Modern',
                'category' => 'Cybersecurity',
                'category_tag' => 'Keamanan Siber',
                'excerpt' => 'Menghadapi era serangan ransomware dan zero-day exploit dengan pendekatan arsitektur Zero Trust dan mitigasi proaktif.',
                'image' => 'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=800&h=500&fit=crop',
                'author_name' => 'Rian Pratama',
                'author_avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=120&h=120&fit=crop',
                'author_role' => 'Cyber Security Consultant',
                'date' => 'Nov 02, 2024',
                'read_time' => '6 min read',
                'ai_tool_id' => null,
                'tags' => ['Cybersecurity', 'ZeroTrust', 'InfoSec', 'DataProtection'],
            ],
            [
                'id' => 8,
                'title' => 'Trend Arsitektur Microservices & Cloud Native di Indonesia',
                'category' => 'Cloud',
                'category_tag' => 'Teknologi Informasi',
                'excerpt' => 'Bagaimana kontainerisasi dengan Docker & Kubernetes membantu perusahaan rintisan mengelola skala aplikasi berkinerja tinggi.',
                'image' => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=800&h=500&fit=crop',
                'author_name' => 'Maya Indah',
                'author_avatar' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=120&h=120&fit=crop',
                'author_role' => 'Cloud Solutions Architect',
                'date' => 'Nov 05, 2024',
                'read_time' => '7 min read',
                'ai_tool_id' => null,
                'tags' => ['CloudNative', 'Kubernetes', 'Microservices', 'DevOps'],
            ],
        ];
    }

    private function getAiTools(): array
    {
        return [
            [
                'id' => 1,
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
                'id' => 2,
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
                'id' => 3,
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
                'id' => 4,
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
                'id' => 5,
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
                'id' => 6,
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
    }
}
