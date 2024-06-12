<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peramalan extends Model
{
    use HasFactory;

    protected $table = "peramalans";
    protected $primaryKey = "id";
    protected $fillable = ['id','user_id', 'tanggal', 'nilai_aktual', 'nilai_peramalan', 'nilai_error'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
 