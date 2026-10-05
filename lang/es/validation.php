<?php

return [
    'regex' => 'El formato del campo :attribute no es válido.',
    'required' => 'El campo :attribute es obligatorio.',
    'unique' => 'El valor del campo :attribute ya está en uso.',
    'max' => [
        'string' => 'El campo :attribute no debe tener más de :max caracteres.',
    ],
    'min' => [
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
    ],
    'email' => 'El campo :attribute debe ser una dirección de correo válida.',
    'numeric' => 'El campo :attribute debe ser un número.',
    'integer' => 'El campo :attribute debe ser un número entero.',
    'string' => 'El campo :attribute debe ser una cadena de caracteres.',
    'boolean' => 'El campo :attribute debe ser verdadero o falso.',
    'confirmed' => 'La confirmación de :attribute no coincide.',
    'date' => 'El campo :attribute no corresponde con una fecha válida.',
    'date_format' => 'El campo :attribute no coincide con el formato :format.',
    'after' => 'El campo :attribute debe ser una fecha posterior a :date.',
    'exists' => 'El valor seleccionado de :attribute no es válido.',
    'in' => 'El valor seleccionado de :attribute no es válido.',
    'between' => [
        'numeric' => 'El campo :attribute debe estar entre :min y :max.',
    ],
    'image' => 'El campo :attribute debe ser una imagen.',
    'url' => 'El formato del campo :attribute no es válido.',

    'custom' => [
        'nombre_completo' => [
            'regex' => 'El nombre solo puede contener letras, espacios, apóstrofes y guiones.',
        ],
        'telefono' => [
            'regex' => 'El teléfono solo puede contener números, +, -, espacios y paréntesis.',
        ],
        'licencia' => [
            'regex' => 'La licencia solo puede contener letras, números y guiones.',
        ],
        'placa' => [
            'regex' => 'La placa solo puede contener letras, números y guiones.',
        ],
        'marca' => [
            'regex' => 'La marca solo puede contener letras, espacios, apóstrofes y guiones.',
        ],
        'modelo' => [
            'regex' => 'El modelo solo puede contener letras, números, espacios y guiones.',
        ],
        'dispositivo_id' => [
            'regex' => 'El ID del dispositivo solo puede contener letras, números, guiones y guiones bajos.',
        ],
    ],

    'attributes' => [
        'name' => 'nombre',
        'email' => 'correo electrónico',
        'password' => 'contraseña',
        'password_confirmation' => 'confirmación de contraseña',
        'current_password' => 'contraseña actual',
        'role' => 'rol',
        'nombre_completo' => 'nombre completo',
        'telefono' => 'teléfono',
        'licencia' => 'licencia',
        'placa' => 'placa',
        'marca' => 'marca',
        'modelo' => 'modelo',
        'tipo' => 'tipo',
        'capacidad_kg' => 'capacidad (kg)',
        'observaciones' => 'observaciones',
        'direccion' => 'dirección',
        'latitud' => 'latitud',
        'longitud' => 'longitud',
        'foto_casa' => 'foto de la casa',
        'origen' => 'origen',
        'destino' => 'destino',
        'fecha_servicio' => 'fecha del servicio',
        'hora_inicio' => 'hora de inicio',
        'hora_fin' => 'hora de fin',
        'cantidad_ayudantes' => 'cantidad de ayudantes',
        'numero_pisos' => 'número de pisos',
        'es_callejon' => 'es callejón',
        'distancia_km' => 'distancia (km)',
        'metodo_pago' => 'método de pago',
        'monto' => 'monto',
        'dispositivo_id' => 'ID del dispositivo',
        'chofer_id' => 'chofer',
        'vehiculo_id' => 'vehículo',
        'cliente_id' => 'cliente',
    ],
];