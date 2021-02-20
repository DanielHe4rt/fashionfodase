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

            $model = $this->model->create([
                'name' => $product['name'],
                'external_id' => $product['id']
            ]);

            $tagIds = $this->getTagIds($product['tags']);
            $model->tags()->sync($tagIds);
        }
    }

    public function getTagIds(array $tagNames): array
    {
        $result = [];
        foreach ($tagNames as $tag) {
            $model = Tag::firstOrCreate(['name' => $tag]);
            $result[] = $model->id;
        }
        return $result;
    }
}
