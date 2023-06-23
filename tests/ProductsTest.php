<?php

use App\Models\Product;
use Illuminate\Http\Response;

class ProductsTest extends TestCase
{
    public function testIndex()
    {
        factory(Product::class)->create();

        $response = $this->get('/api/products');
        $responseArray = getResponseArray($response->response);

        // Verifica o status 200
        $response->assertResponseStatus(Response::HTTP_OK);

        // Verifica se contém a chave 'data' no response
        $this->assertArrayHasKey('data', $responseArray);
    }

    public function testStore()
    {
        $data = factory(Product::class)->make()->toArray();

        $response = $this->post('/api/products', $data);
        $responseArray = getResponseArray($response->response);

        // Verifica o status 201
        $response->assertResponseStatus(Response::HTTP_CREATED);

        // Verifica se contém a chave 'id' no response
        $this->assertArrayHasKey('id', $responseArray);

        // Verifica se o 'id' do response existe na base de dados
        $this->seeInDatabase('products', ['id' => $responseArray['id']]);
    }

    public function testShow()
    {
        $product = factory(Product::class)->create();

        $response = $this->get('/api/products/' . $product->id);
        $responseArray = getResponseArray($response->response);

        // Verifica o status 200
        $response->assertResponseStatus(Response::HTTP_OK);

        // Verifica se o id informado é o mesmo do response
        $this->assertEquals($product->id, $responseArray['id']);
    }

    public function testUpdate()
    {
        $product = factory(Product::class)->create();
        $data    = factory(Product::class)->make()->toArray();

        $response = $this->put('/api/products/'.$product->id, $data);
        $responseArray = getResponseArray($response->response);

        // Verifica o status 200
        $response->assertResponseStatus(Response::HTTP_OK);

        // Verifica se o 'nome' informado é o mesmo do response
        $this->assertEquals($data['name'], $responseArray['name']);

        // Verifica se o cliente com o nome atualizado existe na base de dados
        $this->seeInDatabase('products', ['id' => $product->id, 'name' => $data['name']]);
    }

    public function testDestroy()
    {
        $product = factory(Product::class)->create();

        $response = $this->delete('api/products/'.$product->id);

        // Verifica o status 200
        $response->assertResponseStatus(Response::HTTP_OK);

        // Verifica se o registro foi marcado como excluído (soft delete)
        $this->assertTrue($product->fresh()->trashed());
    }
}
