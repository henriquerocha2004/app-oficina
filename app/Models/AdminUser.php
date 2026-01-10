<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Database\Factories\AdminUserFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotificationCollection;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property bool $is_active
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static AdminUserFactory factory($count = null, $state = [])
 * @method static Builder<static>|AdminUser newModelQuery()
 * @method static Builder<static>|AdminUser newQuery()
 * @method static Builder<static>|AdminUser query()
 * @method static Builder<static>|AdminUser whereCreatedAt($value)
 * @method static Builder<static>|AdminUser whereEmail($value)
 * @method static Builder<static>|AdminUser whereEmailVerifiedAt($value)
 * @method static Builder<static>|AdminUser whereId($value)
 * @method static Builder<static>|AdminUser whereIsActive($value)
 * @method static Builder<static>|AdminUser whereName($value)
 * @method static Builder<static>|AdminUser wherePassword($value)
 * @method static Builder<static>|AdminUser whereRememberToken($value)
 * @method static Builder<static>|AdminUser whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AdminUser extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected $connection = 'central';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }
}
