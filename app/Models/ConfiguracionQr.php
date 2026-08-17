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

    /**
     * Obtener la URL de la imagen
     */
    public function getImagenUrlAttribute()
    {
        if ($this->imagen_qr) {
            $path = storage_path('app/public/' . $this->imagen_qr);
            if (file_exists($path)) {
                return asset('storage/' . $this->imagen_qr);
            }
        }
        return null;
    }

    /**
     * Obtener la URL del QR (imagen o URL externa)
     */
    public function getQrUrlAttribute()
    {
        // Prioridad: imagen sobre URL
        if ($this->imagen_qr && $this->imagen_url) {
            return $this->imagen_url;
        }
        
        if ($this->url_qr) {
            return $this->url_qr;
        }
        
        return null;
    }

    /**
     * Verificar si tiene QR configurado
     */
    public function tieneQr(): bool
    {
        // Verificar si tiene imagen y existe físicamente
        if ($this->imagen_qr) {
            $path = storage_path('app/public/' . $this->imagen_qr);
            if (file_exists($path)) {
                return true;
            }
        }
        
        // Verificar si tiene URL
        if (!empty($this->url_qr)) {
            return true;
        }
        
        return false;
    }
}