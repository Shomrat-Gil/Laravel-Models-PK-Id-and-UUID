# Laravel-Models-PK-Id-and-UUID
UUID and Primary Key in Laravel Models

# Laravel UUID Handling

This guide details how to manage UUIDs in your Laravel models using the `HandlesUuidKeys` trait and the `UuidBinaryCast` custom cast. This setup is designed to efficiently manage UUIDs stored as binary data for optimized database performance and application integrity.

## Prerequisites

- Laravel 8.x or higher
- PHP 7.4 or higher

## Installation

To start, make sure you have a Laravel project set up. The following classes should be directly incorporated into your Laravel application.

## Using the `HandlesUuidKeys` Trait

The `HandlesUuidKeys` trait automates the handling of UUIDs, making them immutable and managing their conversion to and from binary data.

### Step 1: Add the Trait to Your Model

Include the trait in your Laravel model. This setup assumes you have a UUID field named `uuid` which is used in your model.

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HandlesUuidKeys;

class ExampleModel extends Model
{
    use HandlesUuidKeys;

    protected $fillable = ['name', 'other_field'];
}
```

## Step 2: Database Migration

Create and update a migration for the model to include a UUID field stored as binary:

```
php artisan make:migration create_example_models_table


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExampleModelsTable extends Migration
{
    public function up()
    {
        Schema::create('example_models', function (Blueprint $table) {
            $table->id();
            $table->binary('uuid', 16)->unique(); // UUID stored as binary
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('example_models');
    }
}
```

### Run the migration:
```
php artisan migrate
```

### Using UUID in a relationship
```
public function relatedModels()
{
    return $this->hasMany(OtherModel::class, 'foreign_key_uuid', 'uuid');
}
```
### Using the UuidBinaryCast Custom Cast
This custom cast class ensures UUIDs are automatically converted to and from binary format when they are retrieved or stored in the database.
```
class OtherModel extends Model
{
    protected $casts = [
        'foreign_key_uuid' => UuidBinaryCast::class,
    ];
}
```
## Conclusion
Using the HandlesUuidKeys trait and UuidBinaryCast custom cast in your Laravel application will efficiently manage UUIDs stored as binary data. This approach optimizes database storage, enhances performance, and maintains the usability of UUIDs across your application.

## Development Note
This feature is currently under ongoing development and will be refined and updated as issues arise or improvements are identified. We welcome contributions and feedback to enhance its functionality and performance.

## Notes
* Ensure that the paths and namespaces correspond accurately to your Laravel application structure.
* This README provides a comprehensive guide to setting up, integrating, and utilizing these functionalities within a Laravel application, improving data handling and performance.
