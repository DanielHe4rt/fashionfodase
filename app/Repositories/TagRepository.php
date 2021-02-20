<?php


namespace App\Repositories;


use App\Models\Tag;

class TagRepository
{
    /**
     * @var Tag
     */
    private $model;

    public function __construct()
    {
        $this->model = new Tag();
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
