<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeCategory extends Model
{
    protected $fillable = ['title', 'url', 'description', 'status'];

    protected $table = 'knowledge_categories';
}
