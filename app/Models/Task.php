<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Task
 * 
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string $status
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property User $user
 *
 * @package App\Models
 */
class Task extends Model
{
	use HasFactory;
	
	protected $table = 'tasks';

	protected $casts = [
		'user_id' => 'int'
	];

	protected $fillable = [
		'title',
		'description',
		'status',
		'user_id'
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
