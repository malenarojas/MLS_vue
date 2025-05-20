<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Propiedad extends Model
{
    protected $table = 'propiedades';

    protected $primaryKey = 'MLS_ID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'Departamento',
        'Nombre_Oficina',
        'Nombre_Agente_Asociado',
        'Titulo_a_Enseñar',
        'MLS_ID',
        'Integrador_de_ID',
        'Moneda',
        'Precio',
        'Ciudad',
        'Hora_Local',
        'Dirección',
        'Numero_en_la_Calle',
        'Código_Postal',
        'Status_de_Captación',
        'Tipo_de_Transacción',
        'Tipo_de_Transacción.1',
        'Categoría_de_propiedad',
        'Tipo_de_Propiedad',
        'Borrar',
        'Fecha_Captación',
        'Fecha_Cargado_a_la_Web',
        'Ultima_Actualización',
        'Fecha_Cancelado',
        'CancellationReason',
        'Fecha_de_Venta/Alquiler',
        'Fecha_de_Vencimiento',
        'Listing_Percentage',
        'Porcentaje_de_Venta',
        'Precio_Venta/Alquiler',
        'Comisión_Total',
        'Nombre_del_dueño',
        'Id_del_Contacto',
        'Email_del_dueño',
        'Cell_del_dueño',
        'Casa_del_dueño',
        'Días_en_el_Mercado',
        'Transferir_Nombre_Agente_Asociado',
        'Transferir_MLSID',
        'Transferir_Date',
        'key',
        'status',
    ];
}
