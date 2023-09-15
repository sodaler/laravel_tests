<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Site;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitesControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
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

        // make sure the site exists in the db
        $site = Site::first();
        $this->assertEquals(1, Site::count());
        $this->assertEquals('Google', $site->name);
        $this->assertEquals('https://google.com', $site->url);
        $this->assertNull($site->is_online);
        $this->assertEquals($user->id, $site->user->id);

        // check routes page
        $response->assertSeeText('Google');
        $this->assertEquals(route('sites.show', $site),
            url()->current());

    }

    /** @test */
    public function it_only_allows_authenticated_users_to_create_sites()
    {
        // make a post request to a route to create a site
        $response = $this->followingRedirects()
            ->post(route('sites.store'),
                [
                    'name' => 'Google',
                    'url' => 'https://google.com'
                ]
            );

        // make sure no site exists in the db
        $this->assertEquals(0, Site::count());

        // redirect ro login page
        $response->assertSeeText('Laravel');
        $this->assertEquals(route('login'), url()->current());
    }
}
