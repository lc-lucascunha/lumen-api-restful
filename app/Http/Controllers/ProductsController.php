<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class ProductsController extends Controller
{

    /**
     * @var Product
     */
    private $product;

    public function __construct(
        Product $product
    ){
        $this->product = $product;
    }

    // Listar produtos por página
    public function index(Request $request)
    {
        try{
            $perPage = $request->query('per_page', 10);
            $page    = $request->query('page', 1);

            $products = $this->product->paginate($perPage, ['*'], 'page', $page);

            return response()->json($products, Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json(formatException($e->getMessage()), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Validar e salvar um novo produto
    public function store(Request $request)
    {
        try {
            $request = $request->all();

            $validate = Validator::make($request, [
                'name'  => 'required|string|min:3|max:100|unique:products,name',
                'price' => 'required|numeric|between:0,999999.99',
                'photo' => 'nullable|string',
            ]);

            if($validate->fails()){
                return response()->json(formatValidate($validate->errors()), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $product = $this->product->create($request);

            return response()->json($product, Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return response()->json(formatException($e->getMessage()), Response::HTTP_BAD_REQUEST);
        }
    }

    // Exibir um produto específico
    public function show($id)
    {
        try{
            $product = $this->product->find($id);

            if(!$product){
                return response()->json(['Product not found.'], Response::HTTP_NOT_FOUND);
            }

            return response()->json($product, Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json(formatException($e->getMessage()), Response::HTTP_BAD_REQUEST);
        }
    }

    // Validar e atualizar um produto específico
    public function update(Request $request, $id)
    {
        try {
            $product = $this->product->find($id);

            if(!$product){
                return response()->json(['Product not found.'], Response::HTTP_NOT_FOUND);
            }

            $request = $request->all();

            $validate = Validator::make($request, [
                'name'  => 'required|string|min:3|max:100|unique:products,name,'.$product->id,
                'price' => 'required|numeric|between:0,999999.99',
                'photo' => 'nullable|string',
            ]);

            if($validate->fails()){
                return response()->json(formatValidate($validate->errors()), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $product->update($request);

            return response()->json($product, Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json(formatException($e->getMessage()), Response::HTTP_BAD_REQUEST);
        }
    }

    // Excluir um produto específico
    public function destroy($id)
    {
        try{
            $product = $this->product->find($id);

            if(!$product){
                return response()->json(['Product not found.'], Response::HTTP_NOT_FOUND);
            }

            $product->delete();

            return response()->json(['Product successfully removed.'], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json(formatException($e->getMessage()), Response::HTTP_BAD_REQUEST);
        }
    }
}
