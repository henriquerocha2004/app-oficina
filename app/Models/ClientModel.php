<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientModel extends Model
{
    use SoftDeletes;
    use HasFactory;
    use HasUlids;

    protected $table = 'clients';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
        'email',
        'document_number',
        'street',
        'city',
        'state',
        'zip_code',
        'phone',
        'observations',
    ];

    public function cars(): HasMany
    {
        return $this->hasMany(VehicleModel::class, 'client_id', 'id');
    }
}
