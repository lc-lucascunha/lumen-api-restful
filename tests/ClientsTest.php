<?php

use App\Models\Client;
use Illuminate\Http\Response;

class ClientsTest extends TestCase
{
    public function testIndex()
    {
        factory(Client::class)->create();

        $response = $this->get('/api/clients');
        $responseArray = getResponseArray($response->response);

        // Verifica o status 200
        $response->assertResponseStatus(Response::HTTP_OK);

        // Verifica se contém a chave 'data' no response
        $this->assertArrayHasKey('data', $responseArray);
    }

    public function testStore()
    {
        $data = factory(Client::class)->make()->toArray();

        $response = $this->post('/api/clients', $data);
        $responseArray = getResponseArray($response->response);

        // Verifica o status 201
        $response->assertResponseStatus(Response::HTTP_CREATED);

        // Verifica se contém a chave 'id' no response
        $this->assertArrayHasKey('id', $responseArray);

        // Verifica se o 'id' do response existe na base de dados
        $this->seeInDatabase('clients', ['id' => $responseArray['id']]);
    }

    public function testShow()
    {
        $client = factory(Client::class)->create();

        $response = $this->get('/api/clients/' . $client->id);
        $responseArray = getResponseArray($response->response);

        // Verifica o status 200
        $response->assertResponseStatus(Response::HTTP_OK);

        // Verifica se o id informado é o mesmo do response
        $this->assertEquals($client->id, $responseArray['id']);
    }

    public function testUpdate()
    {
        $client = factory(Client::class)->create();
        $data   = factory(Client::class)->make()->toArray();

        $response = $this->put('/api/clients/'.$client->id, $data);
        $responseArray = getResponseArray($response->response);

        // Verifica o status 200
        $response->assertResponseStatus(Response::HTTP_OK);

        // Verifica se o 'nome' informado é o mesmo do response
        $this->assertEquals($data['name'], $responseArray['name']);

        // Verifica se o cliente com o nome atualizado existe na base de dados
        $this->seeInDatabase('clients', ['id' => $client->id, 'name' => $data['name']]);
    }

    public function testDestroy()
    {
        $client = factory(Client::class)->create();

        $response = $this->delete('api/clients/'.$client->id);

        // Verifica o status 200
        $response->assertResponseStatus(Response::HTTP_OK);

        // Verifica se o registro foi marcado como excluído (soft delete)
        $this->assertTrue($client->fresh()->trashed());
    }
}
