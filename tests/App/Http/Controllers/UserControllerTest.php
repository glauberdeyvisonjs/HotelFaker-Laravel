<?php

namespace Tests\App\Http\Controllers;

use App\Classes\Services\UserService;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;
    protected UserController $userController;

    protected function setUp(): void
    {
        parent::setUp();
        $userService = new UserService();
        $this->userController = new UserController($userService);
    }

    public function testListUsers()
    {
        User::factory()->count(3)->create();

        $response = $this->userController->list();
        $response = $response->getData();

        $this->assertCount(3, $response->users);
    }

    public function testShowUser()
    {
        $user = User::factory()->create();

        $response = $this->userController->show($user->id);
        $response = $response->getData();

        $this->assertEquals($user->id, $response->user->id);
    }

    public function testUpdateUser()
    {
        $user = User::factory()->create();

        $data = [
            'name' => 'Novo nome',
            'email' => 'novoemail@teste.com',
            'cpf' => '12345678901',
            'flag_collaborator' => 1,
        ];

        $request = new Request($data);

        $response = $this->userController->update($request, $user->id);
        $response = $response->getData();

        $this->assertEquals('success', $response->status);
    }

    public function testDestroyUser()
    {
        $user = User::factory()->create();

        $response = $this->userController->destroy($user->id);
        $response = $response->getData();

        $this->assertEquals('success', $response->status);
    }

    public function testSendMail()
    {
        $user = User::factory()->create();

        $response = $this->userController->sendMail($user);

        $this->assertTrue($response);
    }
}
