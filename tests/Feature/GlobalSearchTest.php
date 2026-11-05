<?php

namespace Tests\Feature;

use App\Models\Community;
use App\Models\Event;
use App\Models\School;
use App\Models\University;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GlobalSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_page_renders_successfully(): void
    {
        $response = $this->get(route('search'));

        $response->assertOk();
        $response->assertSee('Mulai Pencarian Anda');
    }

    public function test_welcome_page_search_form_targets_search_route(): void
    {
        $response = $this->get(route('welcome'));

        $response->assertOk();
        $response->assertSee(route('search'));
    }

    public function test_search_finds_campus_across_platform(): void
    {
        University::create([
            'name' => 'Universitas Indonesia Tech',
            'city' => 'Depok',
            'status' => 'Active',
            'npsn' => '12345678',
        ]);

        $response = $this->get(route('search', ['q' => 'Indonesia Tech']));

        $response->assertOk();
        $response->assertSee('Universitas Indonesia Tech');
        $response->assertSee('Kampus &amp; Perguruan Tinggi', false);
    }

    public function test_search_finds_school_across_platform(): void
    {
        School::create([
            'name' => 'SMK Telkom Nusantara',
            'city' => 'Bandung',
            'status' => 'Active',
            'npsn' => '87654321',
        ]);

        $response = $this->get(route('search', ['q' => 'Telkom Nusantara']));

        $response->assertOk();
        $response->assertSee('SMK Telkom Nusantara');
        $response->assertSee('Sekolah Vokasi &amp; SMK', false);
    }

    public function test_search_finds_event_across_platform(): void
    {
        Event::create([
            'title' => 'Webinar AI for Developers',
            'organizer' => 'IndoTech Community',
            'category' => 'Webinar',
            'status' => 'Approved',
            'is_verified' => true,
            'date' => '2026-12-01',
        ]);

        $response = $this->get(route('search', ['q' => 'AI for Developers']));

        $response->assertOk();
        $response->assertSee('Webinar AI for Developers');
        $response->assertSee('Agenda &amp; Event IT', false);
    }

    public function test_search_finds_community_across_platform(): void
    {
        Community::create([
            'name' => 'Komunitas Flutter Bali',
            'category' => 'Technology',
            'status' => 'active',
            'approval_status' => 'approved',
            'members' => 15,
        ]);

        $response = $this->get(route('search', ['q' => 'Flutter Bali']));

        $response->assertOk();
        $response->assertSee('Komunitas Flutter Bali');
        $response->assertSee('Komunitas Teknologi');
    }

    public function test_search_filters_by_specific_category(): void
    {
        University::create([
            'name' => 'Institut Teknologi Digital',
            'city' => 'Surabaya',
            'status' => 'Active',
            'npsn' => '99999999',
        ]);

        $response = $this->get(route('search', ['q' => 'Teknologi Digital', 'type' => 'campus']));

        $response->assertOk();
        $response->assertSee('Institut Teknologi Digital');
    }
}
