<?php

namespace App\Models;

use Database\Factories\PostCategoryFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostCategory extends Model
{
    use HasFactory;

    protected $table = 'post_categories';

    /**
     * Создает новый экземпляр фабрики для модели
     *
     * @return Factory
     */
    protected static function newFactory(): Factory
    {
        return PostCategoryFactory::new();
    }
}
