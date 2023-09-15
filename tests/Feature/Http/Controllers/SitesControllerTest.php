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

    /** @test */
    public function it_requires_all_fields_to_be_present()
    {
        // create a user
        $user = UserFactory::new()->create();

        // make a post request to a route to create a site
        $response = $this->actingAs($user)
            ->post(route('sites.store'),
                [
                    'name' => '',
                    'url' => ''
                ]
            );

        // make sure no site exists in the db
        $this->assertEquals(0, Site::count());

        $response->assertSessionHasErrors(['name', 'url']);
    }

    /** @test */
    public function it_requires_the_url_to_have_a_valid_protocol()
    {
        // create a user
        $user = UserFactory::new()->create();

        // make a post request to a route to create a site
        $response = $this->actingAs($user)
            ->post(route('sites.store'),
                [
                    'name' => 'Google',
                    'url' => 'google.com'
                ]
            );

        // make sure no site exists in the db
        $this->assertEquals(0, Site::count());

        $response->assertSessionHasErrors(['url']);
    }

    /** @test */
    public function it_only_allows_authenticated_users_to_create_sites()
    {
        $this->withoutExceptionHandling();

        // create a user
        $user = UserFactory::new()->create();

        // make a post request to a route to create a site
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
}
