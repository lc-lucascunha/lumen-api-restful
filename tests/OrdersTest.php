<?php

use App\Models\Client;
use App\Models\Product;
use App\Models\Order;

use Illuminate\Http\Response;

class OrdersTest extends TestCase
{
    public function testIndex()
    {
        $this->createOrder();

        $response = $this->get('/api/orders');
        $responseArray = getResponseArray($response->response);

        // Verifica o status 200
        $response->assertResponseStatus(Response::HTTP_OK);

        // Verifica se contém a chave 'data' no response
        $this->assertArrayHasKey('data', $responseArray);
    }

    public function testStore()
    {
        $client   = factory(Client::class)->create();
        $products = factory(Product::class, 2)->create();

        $data = [
            'client_id'   => $client->id,
            'product_ids' => $products->pluck('id')->toArray(),
        ];

        $response = $this->post('/api/orders', $data);
        $responseArray = getResponseArray($response->response);

        // Verifica o status 201
        $response->assertResponseStatus(Response::HTTP_CREATED);

        // Verifica se contém a chave 'id' no response
        $this->assertArrayHasKey('id', $responseArray);

        // Verifica se o 'id' do response existe na base de dados
        $this->seeInDatabase('orders', ['id' => $responseArray['id']]);
    }

    public function testShow()
    {
        $order = $this->createOrder();

        $response = $this->get('/api/orders/'.$order->id);
        $responseArray = getResponseArray($response->response);

        // Verifica o status 200
        $response->assertResponseStatus(Response::HTTP_OK);

        // Verifica se o id informado é o mesmo do response
        $this->assertEquals($order->id, $responseArray['id']);
    }

    public function testUpdate()
    {
        $order = $this->createOrder();

        $productsNew = factory(Product::class, 4)->create();

        $data = [
            'client_id'   => $order->client_id,
            'product_ids' => $productsNew->pluck('id')->toArray(),
        ];

        $response = $this->put('/api/orders/'.$order->id, $data);
        $responseArray = getResponseArray($response->response);

        // Verifica o status 200
        $response->assertResponseStatus(Response::HTTP_OK);

        // Verifica se o 'client_id' informado é o mesmo do response
        $this->assertEquals($data['client_id'], $responseArray['client_id']);

        // Verifica se o pedido com os produtos atualizados existe na base de dados
        $this->seeInDatabase('orders', ['id' => $order->id, 'client_id' => $data['client_id']]);

        // Verifica se a quantidade de produtos enviados é a mesma que foi salva na base de dados
        $this->assertCount(count($data['product_ids']), $responseArray['products']);
    }

    public function testDestroy()
    {
        $order = $this->createOrder();

        $response = $this->delete('api/orders/'.$order->id);

        // Verifica o status 200
        $response->assertResponseStatus(Response::HTTP_OK);

        // Verifica se o registro foi marcado como excluído (soft delete)
        $this->assertTrue($order->fresh()->trashed());

        // Verifica se todos os itens do pedido estão marcados como excluídos
        $items = $order->items()->withTrashed()->get();
        foreach ($items as $item) {
            $this->assertTrue($item->trashed());
        }
    }

    // Auxiliar para cria um pedido com dois produtos
    private function createOrder()
    {
        $client   = factory(Client::class)->create();
        $products = factory(Product::class, 2)->create();

        $order = new Order();
        $order = $order->create(['client_id' => $client->id]);
        $order->products()->sync($products->pluck('id')->toArray());

        return $order;
    }

}
