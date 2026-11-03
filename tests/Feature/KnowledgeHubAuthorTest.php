<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KnowledgeHubAuthorTest extends TestCase
{
    use RefreshDatabase;

    public function test_knowledge_hub_displays_author_profile_from_database(): void
    {
        $schoolUser = User::factory()->create([
            'name' => 'Akademi IT Nusantara',
            'role' => 'school',
            'status' => 'active',
        ]);

        School::create([
            'user_id' => $schoolUser->id,
            'npsn' => 'SMK-12345',
            'name' => 'SMK IT Nusantara',
            'institution_type' => 'SMK Vokasi IT',
            'logo_url' => 'https://example.com/logo-smk.png',
            'status' => 'Active',
        ]);

        Article::create([
            'user_id' => $schoolUser->id,
            'title' => 'Teknologi AI Terbaru di SMK',
            'category' => 'AI',
            'excerpt' => 'Ringkasan artikel AI di sekolah vokasi.',
            'status' => 'published',
        ]);

        $response = $this->get(route('knowledge.index'));

        $response->assertStatus(200);
        $response->assertSee('Teknologi AI Terbaru di SMK');
        $response->assertSee('SMK IT Nusantara');
        $response->assertSee('Sekolah');
        $response->assertSee('https://example.com/logo-smk.png');
    }

    public function test_article_detail_shows_author_profile_and_badge(): void
    {
        $user = User::factory()->create([
            'name' => 'Budi Santoso',
            'role' => 'user',
            'status' => 'active',
        ]);

        $article = Article::create([
            'user_id' => $user->id,
            'title' => 'Panduan Belajar Laravel untuk Pemula',
            'category' => 'Programming',
            'excerpt' => 'Belajar dasar-dasar framework Laravel.',
            'content' => 'Konten lengkap artikel belajar Laravel.',
            'status' => 'published',
        ]);

        $response = $this->get(route('knowledge.show', $article->id));

        $response->assertStatus(200);
        $response->assertSee('Panduan Belajar Laravel untuk Pemula');
        $response->assertSee('Budi Santoso');
        $response->assertSee('Member');
    }
}
