<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProjectsTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function only_authenticated_users_can_create_project(){

        //$this->withoutExceptionHandling();

        //use the ProjectFactory to create a project but not persist it to the db
        //raw returns tha attributes as an array
        $attributes = factory('App\Project')->raw();

        $this->post('/projects', $attributes)->assertRedirect('login');
    }

    /**
     * @test
     */
    public function a_user_can_create_a_project()
    {
        //$this->withoutExceptionHandling();
        $user = factory('App\User')->create();
        $this->actingAs($user);

        $attributes = [
            'title' => $this->faker->sentence,  // рандомная фраза
            'description' => $this->faker->paragraph,    // рандомная фраза
        ];

        $this->actingAs($user)->post('/projects', $attributes)->assertRedirect('/projects');

        $this->assertDatabaseHas('projects',  $attributes);

        $this->get('/projects')->assertSee($attributes['title']);
    }

    /** @test */
    public function a_user_can_view_a_project(){

        //$this->withoutExceptionHandling();
       $project =  factory('App\Project')->create();

       $this->get($project->path())->assertSee($project->title)->assertSee($project->descrption);
    }

    /** @test */
    public function a_project_requires_a_title(){

        //$this->withoutExceptionHandling();

        $this->actingAs(factory('App\User')->create());
        $attributes = factory('App\Project')->raw(['title' => '']);

        $this->post('/projects', $attributes)->assertSessionHasErrors('title');
    }

    /** @test */
    public function a_project_requires_a_description(){
        //$this->withoutExceptionHandling();
        
        $this->actingAs(factory('App\User')->create());
        $attributes = factory('App\Project')->raw(['description' => '']);

        $this->post('/projects', $attributes)->assertSessionHasErrors('description');
    }

    
}
