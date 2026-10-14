<?php

namespace Tests\Feature;

use App\Models\Achievement;
use App\Models\News;
use App\Models\Room;
use App\Models\SchoolProfile;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PerformanceOptimizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_school_profile_memoizes_and_caches_queries(): void
    {
        SchoolProfile::clearCurrentCache();

        SchoolProfile::create([
            'school_name' => 'SMK Negeri 2 Karanganyar',
            'email'       => 'info@smkn2kra.sch.id',
        ]);

        SchoolProfile::clearCurrentCache();

        // Track DB query count
        DB::flushQueryLog();
        DB::enableQueryLog();

        // 1st call hits DB and populates memoization & cache
        $profile1 = SchoolProfile::current();
        $this->assertNotNull($profile1);
        $this->assertEquals('SMK Negeri 2 Karanganyar', $profile1->school_name);

        $initialQueryCount = count(DB::getQueryLog());
        $this->assertGreaterThan(0, $initialQueryCount);

        // Subsequent 5 calls must hit memoization with ZERO additional DB queries
        for ($i = 0; $i < 5; $i++) {
            $subsequent = SchoolProfile::current();
            $this->assertEquals('SMK Negeri 2 Karanganyar', $subsequent->school_name);
        }

        $subsequentQueryCount = count(DB::getQueryLog());
        $this->assertSame($initialQueryCount, $subsequentQueryCount, 'SchoolProfile::current() did not memoize queries in-memory');
    }

    public function test_homepage_caches_aggregated_statistics(): void
    {
        Cache::flush();
        SchoolProfile::clearCurrentCache();

        $response = $this->get('/');
        $response->assertOk();

        // Assert stats cache key is populated
        $this->assertTrue(Cache::has('home_stats'));
        $stats = Cache::get('home_stats');
        $this->assertArrayHasKey('achievements', $stats);
        $this->assertArrayHasKey('news', $stats);
        $this->assertArrayHasKey('teachers', $stats);
        $this->assertArrayHasKey('rooms', $stats);
    }

    public function test_sarpras_room_index_query_filtering(): void
    {
        Room::create([
            'name'      => 'Lab Komputer 1',
            'code'      => 'LAB-01',
            'location'  => 'Lantai 2',
            'condition' => 'baik',
            'is_active' => true,
        ]);

        Room::create([
            'name'      => 'Gudang Nonaktif',
            'code'      => 'GDG-01',
            'location'  => 'Lantai 1',
            'condition' => 'rusak_berat',
            'is_active' => false,
        ]);

        // Standard request
        $response = $this->get('/sarpras');
        $response->assertOk();
        $response->assertSee('Lab Komputer 1');
        $response->assertDontSee('Gudang Nonaktif');

        // Search request
        $searchResponse = $this->get('/sarpras?search=LAB');
        $searchResponse->assertOk();
        $searchResponse->assertSee('Lab Komputer 1');
    }

    public function test_sarpras_map_aggregation_query(): void
    {
        Room::create([
            'name'      => 'Ruang Teori A',
            'code'      => 'R-A',
            'condition' => 'baik',
            'map_x'     => 10.5,
            'map_y'     => 20.5,
            'map_floor' => '1',
            'is_active' => true,
        ]);

        Room::create([
            'name'      => 'Ruang Teori B',
            'code'      => 'R-B',
            'condition' => 'rusak_ringan',
            'map_x'     => 15.0,
            'map_y'     => 25.0,
            'map_floor' => '1',
            'is_active' => true,
        ]);

        $response = $this->get('/sarpras/peta');
        $response->assertOk();
        $response->assertViewHas('stats', function ($stats) {
            return $stats['total'] === 2 && $stats['baik'] === 1 && $stats['rusak_ringan'] === 1 && $stats['rusak_berat'] === 0;
        });
    }
}
