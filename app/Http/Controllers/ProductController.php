<?php


namespace App\Http\Controllers;


use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * @var ProductRepository
     */
    private $repository;

    public function __construct(ProductRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getProducts()
    {
        $product = Product::with('tags')->paginate(15);

        return response()->json($product);
    }

    public function postProducts(Request $request)
    {
        // Validação
        $this->validate($request, [
            'products' => 'required|mimes:json,xml'
        ]);

        // Lida com os dados
        try {
            $this->repository->createMassProducts($request->file('products'));
        } catch(\Exception $exception) {
            return response()->json(['deu beyblade'], 422);
        }


        // Retorna a resposta
        return response()->json(['ok'], 201);
    }

}
