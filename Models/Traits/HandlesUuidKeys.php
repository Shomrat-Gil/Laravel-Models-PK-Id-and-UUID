<?php
namespace App\Models\Traits;

use Illuminate\Support\Str;
use RuntimeException;

trait HandlesUuidKeys
{
    
    public function getKeyName(): string
    {
        return 'uuid';
    }

    public function getKeyType(): string
    {
        return 'string';
    }

    public function getIncrementing(): bool
    {
        return false;
    }

    /**
     * Boot the trait to handle automatic UUID generation, immutability checks, and dynamic relationship handling.
     */
    protected static function bootHandlesUuidKeys(): void
    {
        static::creating(function ($model) {
            // Automatically generate and set UUID if not already present
            if (Str::of($model->getAttribute($model->getKeyName()))->trim()->isEmpty()) {
                // Generate UUID, convert to string, remove hyphens, and convert to binary
                $uuid = Str::uuid()->toString();
                $uuidBin = hex2bin(str_replace('-', '', $uuid));
                $model->setAttribute($model->getKeyName(), $uuidBin);
            }
            // Merge casts when creating a new model instance
            $model->mergeCasts(['id' => 'integer', $model->getKeyName() => 'string']);
        });

        static::updating(function ($model) {
            // Prevent modification of the UUID
            if ($model->isDirty($model->getKeyName())) {
                throw new RuntimeException('Attempting to modify an immutable UUID.');
            }
        });

        static::retrieved(function ($model) {
            // Merge casts when the model is retrieved from the database
            $model->mergeCasts(['id' => 'integer', $model->getKeyName() => $model->getKeyType()]);
        });
    }

    /**
     * Merge new casts with existing casts on the model.
     *
     * @param  array  $casts
     * @return $this
     */
    public function mergeCasts($casts): static
    {
        $casts = $this->ensureCastsAreStringValues($casts);

        $this->casts = array_merge($this->casts, $casts);

        return $this;
    }


    /**
     * Accessor to convert binary UUID to string when retrieving it.
     */
    public function getUuidAttribute($value): ?string
    {
        return $value ? bin2hex($value) : null;
    }
}