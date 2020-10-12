<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class BrowseProjectsTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function a_guest_cannot_browse_projects()
    {
        $this->withExceptionHandling()
            ->get('/')
            ->assertRedirect('/login');
    }

    /**
     * @test
     */
    public function signed_in_user_can_see_projects()
    {
        $this->signIn();

        $project = create('App\Project', ['user_id' => auth()->id()]);

        $this->get('/')
            ->assertSee($project->title);
    }

    /**
     * @test
     */
    public function signed_in_user_should_see_only_his_projects()
    {
        $this->signIn();

        $usersProject = create('App\Project', ['user_id' => auth()->id()]);
        $foreignProject = create('App\Project');

        $this->get('/')
            ->assertSee($usersProject->title)
            ->assertDontSee($foreignProject->title);
    }
}
