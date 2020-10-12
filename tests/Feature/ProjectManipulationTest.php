<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use App\Project;

class ProjectManipulationTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test 
     */
    public function a_user_can_create_project()
    {   
        $this->signIn()
            ->makeProject()
            ->json('POST', '/projects', $this->project->toArray())
            ->assertStatus(201)
            ->assertJson($this->project->toArray());
            
        $this->assertEquals($this->project->title, Project::owned()->first()->title);
    }

    /**
     * @test 
     */
    public function a_project_requires_a_title()
    {
        $this->withExceptionHandling()
            ->signIn()
            ->createProject(['title' => ''])
            ->json('POST', '/projects', $this->project->toArray())
            ->assertStatus(422)
            ->assertJson(['title' => ['The title field is required.']]);
    }

    /**
     * @test 
     */
    public function a_user_can_delete_a_project()
    {
        $this->signIn()
            ->createProject()
            ->call('DELETE', "/projects/{$this->project->id}")
            ->assertStatus(204);

        $this->assertEmpty(Project::find($this->project->id));
    }
    
    /**
     * @test 
     */
    public function a_user_can_update_a_project()
    {
        $this->signIn()
            ->createProject()
            ->editProject(['title' => 'Foo Bar Baz'])
            ->json('PUT', "/projects/{$this->project->id}", $this->project->toArray())
            ->assertStatus(200)
            ->assertJson($this->project->toArray());

        $this->assertEquals($this->project->title, Project::find($this->project->id)->title);
    }

    /**
     * @test 
     */
    public function a_project_cannot_be_updated_without_a_title()
    {
        $this->withExceptionHandling()
            ->signIn()
            ->createProject()
            ->editProject(['title' => ''])
            ->json('PUT', "/projects/{$this->project->id}", $this->project->toArray())
            ->assertStatus(422)
            ->assertJson(['title' => ['The title field is required.']]);
    }

    protected function makeProject($attributes = [])
    {
        $this->project = make('App\Project', array_merge(
            ['user_id' => auth()->id()],
            $attributes
        ));

        return $this;
    }
    
    protected function createProject($attributes = [])
    {
        $this->project = create('App\Project', array_merge(
            ['user_id' => auth()->id()],
            $attributes
        ));

        return $this;
    }

    protected function editProject($attributes = [])
    {
        foreach ($attributes as $attribute => $value) {
            $this->project->$attribute = $value;
        }

        return $this;
    }
}
