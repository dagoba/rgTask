<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use App\Project;

class ProjectTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test 
     */
    public function project_has_owned_scope()
    {
        $this->signIn();

        $ownedProjects = create('App\Project', ['user_id' => auth()->id()], 3);
        $foreignProjects = create('App\Project', [], 1);

        $projects = Project::owned()->get();

        $this->assertEmpty($ownedProjects->diff($projects));
        $this->assertEmpty($foreignProjects->intersect($projects));
    }
    
    /**
     * @test 
     */
    public function cannot_get_any_owned_projects_for_guest()
    {
        create('App\Project', ['user_id' => 1], 3);

        $projects = Project::owned()->get();

        $this->assertEmpty($projects);
    }

    /** 
     * @test
     */
    public function project_has_tasks()
    {
        $this->signIn();
        
        $project = create('App\Project', ['user_id' => auth()->id()]);

        $this->assertInstanceOf(
            'Illuminate\Database\Eloquent\Collection',
            $project->tasks
        ); 
    }
}
