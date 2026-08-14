<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfiguracionQr extends Model
{
    use HasFactory;

    protected $table = 'configuracion_qr';

    protected $fillable = [
        'imagen_qr',
        'url_qr',
        'fecha_actualizacion'
    ];

    protected $casts = [
        'fecha_actualizacion' => 'datetime'
    ];

    public function getImagenUrlAttribute()
    {
        if ($this->imagen_qr) {
            return asset('storage/' . $this->imagen_qr);
        }
        return null;
    }

    public function tieneQr(): bool
    {
        return !empty($this->imagen_qr) || !empty($this->url_qr);
    }
}