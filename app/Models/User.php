<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory; // Importa el trait HasFactory
use Illuminate\Foundation\Auth\User as Authenticatable; // Cambia Model por Authenticatable
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


/**
 * Class User
 * 
 * @property int $id
 * @property string $username
 * @property string|null $api_token
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Task[] $tasks
 *
 * @package App\Models
 */
class User extends Authenticatable
{
	use HasApiTokens, HasFactory, Notifiable;
	
	protected $table = 'users';

	protected $casts = [
		'email_verified_at' => 'datetime'
	];

	protected $hidden = [
		'api_token',
		'password',
		'remember_token'
	];

	protected $fillable = [
		'username',
		'api_token',
		'name',
		'email',
		'email_verified_at',
		'password',
		'remember_token'
	];

	public function tasks()
	{
		return $this->hasMany(Task::class);
	}
}
