<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Site;
use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitesControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_create_sites(): void
    {
        $this->withoutExceptionHandling();

        $user = UserFactory::new()->create();

        $response = $this->followingRedirects()
            ->actingAs($user)
            ->post(route('sites.store'),
                [
                    'name' => 'Google',
                    'url' => 'https://google.com'
                ]
            );

        $site = Site::first();
        $this->assertEquals(1, Site::count());
        $this->assertEquals('Google', $site->name);
        $this->assertEquals('https://google.com', $site->url);
        $this->assertNull($site->is_online);
        $this->assertEquals($user->id, $site->user->id);

        $response->assertSeeText('Google');
        $this->assertEquals(route('sites.show', $site),
            url()->current());

    }
}
