<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ClientsController extends Controller
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(
        Client $client
    ){
        $this->client = $client;
    }

    // Listar clientes por página
    public function index(Request $request)
    {
        try{
            $perPage = $request->query('per_page', 10);
            $page    = $request->query('page', 1);

            $clients = $this->client->paginate($perPage, ['*'], 'page', $page);

            return response()->json($clients, Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json(formatException($e->getMessage()), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Validar e salvar um novo cliente
    public function store(Request $request)
    {
        try {
            $request = $request->all();

            $validate = Validator::make($request, [
                'name'       => 'required|string|min:5|max:50',
                'email'      => 'required|string|min:5|max:50|email|unique:clients,email',
                'phone'      => 'required|string|size:11',
                'birthdate'  => 'required|string|size:10|date',
                'zip_code'   => 'required|string|size:8',
                'address'    => 'required|string|min:5|max:100',
                'province'   => 'required|string|min:5|max:50',
                'complement' => 'nullable|string|min:3|max:50',
            ]);

            if($validate->fails()){
                return response()->json(formatValidate($validate->errors()), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $client = $this->client->create($request);

            return response()->json($client, Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return response()->json(formatException($e->getMessage()), Response::HTTP_BAD_REQUEST);
        }
    }

    // Exibir um cliente específico
    public function show($id)
    {
        try{
            $client = $this->client->find($id);

            if(!$client){
                return response()->json(['Client not found.'], Response::HTTP_NOT_FOUND);
            }

            return response()->json($client, Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json(formatException($e->getMessage()), Response::HTTP_BAD_REQUEST);
        }
    }

    // Validar e atualizar um cliente específico
    public function update(Request $request, $id)
    {
        try {
            $client = $this->client->find($id);

            if(!$client){
                return response()->json(['Client not found.'], Response::HTTP_NOT_FOUND);
            }

            $request = $request->all();

            $validate = Validator::make($request, [
                'name'       => 'required|string|min:5|max:50',
                'email'      => 'required|string|min:5|max:50|email|unique:clients,email,'.$client->id,
                'phone'      => 'required|string|size:11',
                'birthdate'  => 'required|string|size:10|date',
                'zip_code'   => 'required|string|size:8',
                'address'    => 'required|string|min:5|max:100',
                'province'   => 'required|string|min:5|max:50',
                'complement' => 'nullable|string|min:3|max:50',
            ]);

            if($validate->fails()){
                return response()->json(formatValidate($validate->errors()), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $client->update($request);

            return response()->json($client, Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json(formatException($e->getMessage()), Response::HTTP_BAD_REQUEST);
        }
    }

    // Excluir um cliente específico
    public function destroy($id)
    {
        try{
            DB::beginTransaction();

            $client = $this->client->find($id);

            if(!$client){
                return response()->json(['Client not found.'], Response::HTTP_NOT_FOUND);
            }

            // Exclui os itens de produtos associados aos pedidos do cliente
            foreach ($client->orders as $order) {
                $order->items()->delete();
            }

            // Exclui os pedidos associados ao cliente
            $client->orders()->delete();

            // Exclui o cliente
            $client->delete();

            DB::commit();

            return response()->json(['Client successfully removed.'], Response::HTTP_OK);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(formatException($e->getMessage()), Response::HTTP_BAD_REQUEST);
        }
    }

    // Exibir os pedidos de um cliente específico
    public function showOrders($id)
    {
        try{
            $client = $this->client->with('orders.products')->find($id);

            if(!$client){
                return response()->json(['Client not found.'], Response::HTTP_NOT_FOUND);
            }

            return response()->json($client, Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json(formatException($e->getMessage()), Response::HTTP_BAD_REQUEST);
        }
    }
}
