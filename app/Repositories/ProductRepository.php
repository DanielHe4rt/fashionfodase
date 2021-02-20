<?php


namespace App\Repositories;


use App\Models\Product;
use App\Models\Tag;
use Exception;
use Illuminate\Http\UploadedFile;

class ProductRepository
{
    /**
     * @var Product
     */
    private $model;

    public function __construct()
    {
        $this->model = new Product();
    }

    public function createProduct(array $data): Product
    {
        return $this->newProduct($data);
    }

    public function createMassProducts(UploadedFile $file): void
    {
        $mime = $file->getClientOriginalExtension();

        if ($mime == "json") {
            $this->handleJsonData($file);
        } else if ($mime == "xml") {
            $this->handleXMLData($file);
        } else {
            throw new Exception();
        }
    }

    protected function handleJsonData(UploadedFile $file): void
    {
        $products = json_decode($file->get(), true)['products'];
        $this->handleProducts($products);
    }

    protected function handleXMLData(UploadedFile $file): void
    {
        $xml = simplexml_load_string($file->get(), "SimpleXMLElement", LIBXML_NOCDATA);
        $json = json_encode($xml);
        $products = json_decode($json, true)['element'];

        foreach ($products as $key => $product) {
            if ($product['tags']['element']) {
                $products[$key]['tags'] = $product['tags']['element'];
            }
        }

        $this->handleProducts($products);

    }

    private function handleProducts(array $products): void
    {
        foreach ($products as $product) {
            $productExists = $this->model->where('external_id', $product['id'])->first();

            if ($productExists) {
                continue;
            }

            $this->newProduct($product);
        }
    }

    public function newProduct(array $product): Product
    {
        $model = $this->model->create([
            'name' => $product['name'],
            'external_id' => $product['id'] ?? $product['external_id']
        ]);

        $tagIds = app(TagRepository::class)->getTagIds($product['tags']);
        $model->tags()->sync($tagIds);

        return $model;
    }



    public function updateProduct(int $externalId, array $data): Product
    {
        $model = $this->model->where('external_id', $externalId)->first();
        $model->update($data);
        return $model;
    }

    public function deleteProduct(int $externalId)
    {
        $this->model->where('external_id', $externalId)->delete();
    }
}
