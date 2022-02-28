<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeTopic extends Model
{
    protected $fillable = ['title', 'url', 'description', 'images', 'status'];

    protected $table = 'knowledge_topics';
}
