<?php

namespace Tests\Feature\Clients;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\Helpers\CpfGenerator;
use Symfony\Component\Uid\Ulid;

class ClientsControllerTest extends TestCase
{
    use RefreshDatabase;
    use CpfGenerator;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create and authenticate a test user
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function testIndexReturnsInertiaResponse(): void
    {
        $response = $this->get('/clients');

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testStoreCreatesClientSuccessfully(): void
    {
        $clientData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'document_number' => '42603972065',
            'street' => '123 Main St',
            'city' => 'Anytown',
            'state' => 'CA',
            'zip_code' => '12345',
            'phone' => '(555) 123-4567',
            'observations' => 'VIP client',
        ];

        $response = $this->post('/clients', $clientData);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'message' => 'Client created successfully',
            ])
            ->assertJsonStructure([
                'client_id',
            ]);

        $this->assertDatabaseHas('clients', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'document_number' => '42603972065',
        ]);
    }

    public function testStoreValidatesRequiredFields(): void
    {
        $response = $this->postJson('/clients', []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
                'name',
                'email',
                'document_number',
                'phone',
            ]);
    }

    public function testStoreValidatesEmailFormat(): void
    {
        $clientData = [
            'name' => 'John Doe',
            'email' => 'invalid-email',
            'document_number' => '67887286077',
            'phone' => '(555) 123-4567',
        ];

        $response = $this->postJson('/clients', $clientData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['email']);
    }



    public function testShowByIdReturnsClientSuccessfully(): void
    {
        $clientId = (new Ulid())->toString();
        Client::create([
            'id' => $clientId,
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'document_number' => '98765432100',
        ]);

        $response = $this->get("/clients/{$clientId}");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'client' => [
                    'name',
                    'email',
                    'document_number',
                ],
            ]);
    }

    public function testShowByIdReturnsNotFoundForInvalidId(): void
    {
        $invalidId = (new Ulid())->toString();

        $response = $this->get("/clients/{$invalidId}");

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testUpdateModifiesClientSuccessfully(): void
    {
        $clientId = (new Ulid())->toString();
        Client::create([
            'id' => $clientId,
            'name' => 'Original Name',
            'email' => 'original@example.com',
            'document_number' => '65373965065',
        ]);

        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'document_number' => '65373965065',
            'street' => '456 Oak Ave',
            'city' => 'Newtown',
            'state' => 'NY',
            'zip_code' => '54321',
            'phone' => '(555) 987-6543',
            'observations' => 'Updated observations',
        ];

        $response = $this->put("/clients/{$clientId}", $updateData);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Client updated successfully',
            ]);

        $this->assertDatabaseHas('clients', [
            'id' => $clientId,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
    }

    public function testDeleteRemovesClientSuccessfully(): void
    {
        $clientId = (new Ulid())->toString();
        Client::create([
            'id' => $clientId,
            'name' => 'Delete Me',
            'email' => 'delete@example.com',
            'document_number' => '65373965065',
        ]);

        $response = $this->delete("/clients/{$clientId}");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Client deleted successfully',
            ]);

        $this->assertSoftDeleted('clients', [
            'id' => $clientId,
        ]);
    }

    public function testDeleteReturnsNotFoundForInvalidId(): void
    {
        $invalidId = (new Ulid())->toString();

        $response = $this->delete("/clients/{$invalidId}");

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testFindByFiltersReturnsPaginatedResults(): void
    {
        // Generate valid CPFs for each client
        $validCpfs = $this->generateMultipleValidCpfs(5);

        // Create multiple clients
        for ($i = 0; $i < 5; $i++) {
            Client::create([
                'id' => (new Ulid())->toString(),
                'name' => "Client {$i}",
                'email' => "client{$i}@example.com",
                'document_number' => $validCpfs[$i],
            ]);
        }

        $response = $this->get('/clients/filters?' . http_build_query([
            'page' => 1,
            'per_page' => 3,
            'sort_direction' => 'asc',
            'sort_by' => 'name',
            'search' => '',
        ]));

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'clients' => [
                    'total_items',
                    'items',
                ],
            ]);
    }

    public function testFindByFiltersWithSearchTerm(): void
    {
        // Generate valid CPFs for each client
        $validCpfs = $this->generateMultipleValidCpfs(3);

        // Create clients with different names
        Client::create([
            'id' => (new Ulid())->toString(),
            'name' => 'Alice Johnson',
            'email' => 'alice@example.com',
            'document_number' => $validCpfs[0],
        ]);

        Client::create([
            'id' => (new Ulid())->toString(),
            'name' => 'Bob Smith',
            'email' => 'bob@example.com',
            'document_number' => $validCpfs[1],
        ]);

        Client::create([
            'id' => (new Ulid())->toString(),
            'name' => 'Charlie Johnson',
            'email' => 'charlie@example.com',
            'document_number' => $validCpfs[2],
        ]);

        $response = $this->get('/clients/filters?' . http_build_query([
            'page' => 1,
            'per_page' => 10,
            'sort_direction' => 'asc',
            'sort_by' => 'name',
            'search' => 'Johnson',
        ]));

        $response->assertStatus(Response::HTTP_OK);

        $clients = $response->json('clients.items');
        $this->assertCount(2, $clients);
    }



    public function testFindByFiltersWithColumnSearch(): void
    {
        $this->actingAs($this->user);

        $cpfs = $this->generateMultipleValidCpfs(2);

        Client::create([
            'id' => (new Ulid())->toString(),
            'name' => 'Test Client',
            'email' => 'specific@example.com',
            'document_number' => $cpfs[0],
        ]);

        Client::create([
            'id' => (new Ulid())->toString(),
            'name' => 'Another Client',
            'email' => 'other@example.com',
            'document_number' => $cpfs[1],
        ]);

        $response = $this->get('/clients/filters?' . http_build_query([
            'page' => 1,
            'per_page' => 10,
            'sort_direction' => 'asc',
            'sort_by' => 'name',
            'search' => '',
            'email' => 'specific@example.com',
        ]));

        $response->assertStatus(Response::HTTP_OK);

        $clients = $response->json('clients.items');
        $this->assertCount(1, $clients);
        $this->assertEquals('specific@example.com', $clients[0]['email']);
    }

    public function testStoreReturnsExistingClientWhenDocumentExists(): void
    {
        // Create existing client
        $existingClientId = (new Ulid())->toString();
        Client::create([
            'id' => $existingClientId,
            'name' => 'Existing Client',
            'email' => 'existing@example.com',
            'document_number' => '64361235000191',
        ]);

        // Try to create client with same document
        $clientData = [
            'name' => 'New Client',
            'email' => 'new@example.com',
            'document_number' => '64361235000191',
            'phone' => '(555) 123-4567',
        ];

        $response = $this->post('/clients', $clientData);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Client already exists',
                'client_id' => $existingClientId,
            ]);

        // Verify no new client was created
        $this->assertDatabaseCount('clients', 1);
    }
}
