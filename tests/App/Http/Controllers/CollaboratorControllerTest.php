<?php

namespace Tests\App\Http\Controllers;

use App\Classes\Services\CollaboratorService;
use App\Http\Controllers\CollaboratorController;
use App\Models\Collaborator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CollaboratorControllerTest extends TestCase
{
    use RefreshDatabase;
    protected CollaboratorController $collaboratorsController;

    protected function setUp(): void
    {
        parent::setUp();
        $collaboratorsService = new CollaboratorService();
        $this->collaboratorsController = new CollaboratorController($collaboratorsService);
    }

    public function testListCollaborators()
    {
        Collaborator::factory()->count(3)->create();

        $response = $this->collaboratorsController->list();
        $response = $response->getData();

        $this->assertCount(3, $response->collaborators);
    }

    public function testShowCollaborator()
    {
        $collaborator = Collaborator::factory()->create();

        $response = $this->collaboratorsController->show($collaborator->id);
        $response = $response->getData();

        $this->assertTrue(isset($response->collaborator));
        $this->assertEquals($collaborator->id, $response->collaborator->id);
    }

    public function testDestroyCollaborator()
    {
        $collaborator = Collaborator::factory()->create();

        $response = $this->collaboratorsController->destroy($collaborator->id);
        $response = $response->getData();

        $this->assertEquals('success', $response->status);
    }
}
