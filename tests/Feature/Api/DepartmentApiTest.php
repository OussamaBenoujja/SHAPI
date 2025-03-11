<?php

namespace Tests\Feature\Api;

use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepartmentApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_list_all_departments()
    {
        
        Department::factory()->count(3)->create();

        
        $response = $this->getJson('/api/v1/departments');

        
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id', 'name', 'description', 'slug']
                     ]
                 ])
                 ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function it_can_create_a_department_when_authenticated()
    {
        
        $this->actingAs($this->user);

        
        $departmentData = [
            'name' => 'Electronics',
            'description' => 'Electronic devices and accessories'
        ];

        
        $response = $this->postJson('/api/v1/departments', $departmentData);

       
        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'data' => ['id', 'name', 'description', 'slug']
                 ])
                 ->assertJson([
                     'message' => 'Department created successfully',
                     'data' => $departmentData
                 ]);

        
        $this->assertDatabaseHas('departments', [
            'name' => 'Electronics',
            'slug' => 'electronics'
        ]);
    }

    /** @test */
    public function it_cannot_create_department_without_authentication()
    {
        $departmentData = [
            'name' => 'Electronics',
            'description' => 'Electronic devices and accessories'
        ];

        
        $response = $this->postJson('/api/v1/departments', $departmentData);

        
        $response->assertStatus(401);
    }

    /** @test */
    public function it_validates_department_creation()
    {
        
        $this->actingAs($this->user);

        
        $invalidData = [
            'description' => 'Some description'
        ];

        
        $response = $this->postJson('/api/v1/departments', $invalidData);

        
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name']);
    }
}