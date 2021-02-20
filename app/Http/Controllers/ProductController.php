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

    public function postProduct(Request $request)
    {
        $this->validate($request, [
            'external_id' => 'required|int|unique:products',
            'name' => 'required|string',
            'tags.*' => 'required'
        ]);

        $result = $this->repository->createProduct($request->only(['external_id', 'name', 'tags']));

        return response()->json($result);
    }

    public function postMassProducts(Request $request)
    {
        // Validação
        $this->validate($request, [
            'products' => 'required|mimes:json,xml'
        ]);

        // Lida com os dados
        try {
            $this->repository->createMassProducts($request->file('products'));
        } catch (\Exception $exception) {
            return response()->json(['deu beyblade'], 422);
        }


        // Retorna a resposta
        return response()->json(['ok'], 201);
    }

    public function putProduct(Request $request, int $externalId)
    {
        $request->merge(['external_id' => $externalId]);

        $this->validate($request, [
            'external_id' => 'required|exists:products'
        ]);

        $result = $this->repository->updateProduct($externalId, $request->all());

        return response()->json($result);
    }

    public function deleteProduct(Request $request, int $externalId)
    {
        $request->merge(['external_id' => $externalId]);

        $this->validate($request, [
            'external_id' => 'required|exists:products'
        ]);

        $this->repository->deleteProduct($externalId);

        return response()->json();
    }

}
