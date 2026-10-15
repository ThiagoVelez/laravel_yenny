<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroArtistico extends Model
{
    use HasFactory;

    // Campos permitidos para inserción masiva
    protected $fillable = [
        'disciplina_artistica',
        'nivel_artistico',
    ];
}
