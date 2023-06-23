<?php

namespace App\Http\Controllers;

use App\Mail\OrderConfirmationEmail;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class OrdersController extends Controller
{

    /**
     * @var Order
     */
    private $order;

    public function __construct(
        Order $order
    ){
        $this->order = $order;
    }

    // Listar pedidos por página
    public function index(Request $request)
    {
        try{
            $perPage = $request->query('per_page', 10);
            $page    = $request->query('page', 1);

            $orders = $this->order->with('products')->paginate($perPage, ['*'], 'page', $page);

            return response()->json($orders, Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json(formatException($e->getMessage()), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Validar e salvar um novo pedido
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $request = $request->all();

            // Realiza a validação verificando se o ID do cliente
            // e de cada produto existe, e não está excluído
            $validate = Validator::make($request, [
                'client_id'     => 'required|numeric|exists:clients,id,deleted_at,NULL',
                'product_ids'   => 'required|array',
                'product_ids.*' => 'required|numeric|exists:products,id,deleted_at,NULL',
            ]);

            if($validate->fails()){
                return response()->json(formatValidate($validate->errors()), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Cria o pedido
            $order = $this->order->create($request);

            // Sincroniza os produtos associados ao pedido
            $order->products()->sync($request['product_ids']);

            // Disparar email para o cliente com os detalhes do pedido
            Mail::to($order->client->email)->send(new OrderConfirmationEmail($order));

            DB::commit();

            return response()->json($order->load('products'), Response::HTTP_CREATED);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(formatException($e->getMessage()), Response::HTTP_BAD_REQUEST);
        }
    }

    // Exibir um pedido específico
    public function show($id)
    {
        try{
            $order = $this->order->with('products')->find($id);

            if(!$order){
                return response()->json(['Order not found.'], Response::HTTP_NOT_FOUND);
            }

            return response()->json($order, Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json(formatException($e->getMessage()), Response::HTTP_BAD_REQUEST);
        }
    }

    // Validar e atualizar um pedido específico
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $order = $this->order->find($id);

            if(!$order){
                return response()->json(['Order not found.'], Response::HTTP_NOT_FOUND);
            }

            $request = $request->all();

            // Realiza a validação verificando se o ID do cliente
            // e de cada produto existe, e não está excluído
            $validate = Validator::make($request, [
                'client_id'     => 'required|numeric|exists:clients,id,deleted_at,NULL',
                'product_ids'   => 'required|array',
                'product_ids.*' => 'required|numeric|exists:products,id,deleted_at,NULL',
            ]);

            if($validate->fails()){
                return response()->json(formatValidate($validate->errors()), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Atualiza o pedido
            $order->update($request);

            // Sincroniza os produtos associados ao pedido
            $order->products()->sync($request['product_ids']);

            DB::commit();

            return response()->json($order->load('products'), Response::HTTP_OK);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(formatException($e->getMessage()), Response::HTTP_BAD_REQUEST);
        }
    }

    // Excluir um pedido específico
    public function destroy($id)
    {
        try{
            DB::beginTransaction();

            $order = $this->order->find($id);

            if(!$order){
                return response()->json(['Order not found.'], Response::HTTP_NOT_FOUND);
            }

            // Exclui os itens de produtos associados ao pedido
            $order->items()->delete();

            // Exclui o pedido
            $order->delete();

            DB::commit();

            return response()->json(['Order successfully removed.'], Response::HTTP_OK);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(formatException($e->getMessage()), Response::HTTP_BAD_REQUEST);
        }
    }
}
