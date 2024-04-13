<?php
namespace App\Models\Traits;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;

trait HandlesUuidKeys
{
    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;
    /**
     * Guard `id` and `uuid` from mass assignment.
     */
    protected $guarded = ['id', 'uuid'];

    /**
     * Boot the trait to handle automatic UUID generation, immutability checks, and dynamic relationship handling.
     */
    protected static function bootHandlesUuidKeys()
    {
        static::creating(function ($model) {
            // Automatically generate and set UUID if not already present
            if (Str::of($model->getAttribute($this->primaryKey))->trim()->isEmpty()) {
                // Generate UUID, convert to string, remove hyphens, and convert to binary
                $uuid = Str::uuid()->toString();
                $uuidBin = hex2bin(str_replace('-', '', $uuid));
                $model->setAttribute($this->primaryKey, $uuidBin);
            }
            // Merge casts when creating a new model instance
            $model->mergeCasts(['id' => 'integer', $this->primaryKey => 'string']);
        });

        static::updating(function ($model) {
            // Prevent modification of the UUID
            if ($model->isDirty($this->primaryKey)) {
                throw new RuntimeException('Attempting to modify an immutable UUID.');
            }
        });

        static::retrieved(function ($model) {
            // Merge casts when the model is retrieved from the database
            $model->mergeCasts(['id' => 'integer', $this->primaryKey => $this->keyType]);
        });
    }

    /**
     * Helper method to merge new casts into the model's existing casts.
     *
     * @param  array $casts
     */
    protected function mergeCasts($casts)
    {
        $this->casts = array_merge($this->casts, $casts);
    }

    /**
     * Accessor to convert binary UUID to string when retrieving it.
     */
    public function getUuidAttribute($value)
    {
        return $value ? bin2hex($value) : null;
    }
}