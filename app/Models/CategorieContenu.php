<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategorieContenu extends Model
{
    use HasFactory;

    protected $table = 'categories_contenus';

    protected $fillable = [
        'nom',
        'slug',
        'description',
        'icone',
    ];

    // Relations
    public function contenus(): HasMany
    {
        return $this->hasMany(Contenu::class, 'categorie_id');
    }

    public function videos(): HasMany
    {
        return $this->hasMany(Video::class, 'categorie_id');
    }
}
