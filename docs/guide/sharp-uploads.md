# Sharp built-in solution for uploads

Uploads are painful.

Sharp provide a very opinionated and totally optional solution to handle if you are using Eloquent and the `WithSharpFormEloquentUpdater` trait (see [related documentation](building-form.md)).

The proposal is to use a special Sharp Model for all your uploads, and to link them to your Models with Eloquent's Morph relationships.

## Use `SharpUploadModel`

The base Model class is `Code16\Sharp\Form\Eloquent\Uploads\SharpUploadModel`. Just create your own Model class and make it extends this base class.

You'll have to define the Eloquent `$table` attribute to indicate the table name. So for instance, let's say your Model name choice is `Media`, here's the class code:

```php
use Code16\Sharp\Form\Eloquent\Uploads\SharpUploadModel;

class Media extends SharpUploadModel
{
    protected $table = 'medias';
}
```

### Generator

```bash
php artisan sharp:make:media <model_name> --table=<table_name>
```

## Create the migration

Sharp provides an artisan command for that:
`sharp:create_uploads_migration <table_name>`

Pass your specific table name in the `table_name` argument ("medias" in our example).

This command will create a migration file like this one:

```php
class CreateMediasTable extends Migration
{
    public function up()
    {
        Schema::create('medias', function (Blueprint $table) {
            $table->increments('id');
            $table->morphs('model');
            $table->string('model_key')->nullable();
            $table->string('file_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->string('disk')->default('local')->nullable();
            $table->unsignedInteger('size')->nullable();
            $table->text('custom_properties')->nullable();
            $table->unsignedInteger('order')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('medias');
    }
}
```

## Link to your Models

Now, you need to define the relationships. Let's say you have a Book model, and you want the user to be able to upload its cover and PDF version.

```php
class Book extends Model
{
	public function cover()
    {
        return $this->morphOne(Media::class, 'model')
            ->where('model_key', 'cover');
    }

    public function pdf()
    {
        return $this->morphOne(Media::class, 'model')
            ->where('model_key', 'pdf');
    }
}
```

## Use it!

Let's pretend you already have data in this new table, here how to handle it.

### Properties

By default, you can get the `file_name`, but also `mime_type` and file's `size`.


### Custom properties

You can add whatever property you need through custom properties, by setting it:

```php
$book->cover->author = 'Tomi Ungerer';
```

Custom properties will be stored in the `custom_properties` column, as JSON.

You can retrieve the value the same way:

```php
$author = $book->cover->author;
```

### Thumbnails

Thumbnail creation is built-in. You must first define the thumbnail directory, in Sharp's config:

```php
// config/sharp.php

'uploads' => [
    'thumbnails_dir' => 'thumbnails',
],
```

This path is relative to the `public` directory.

Then you can create a thumbnail using the `thumbnail` method directly on the upload model:

```php
thumbnail(int $width = null, int $height = null, array $modifiers = []);
```

For instance, you can display a 150px width thumbnail in a view like this:

```php
<img src="{{ $book->cover->thumbnail(150) }}" alt="My picture">
```

Another option is to use the fluent API, calling `thumbnail()` without parameters:

```php
$thumb = $book->cover->thumnail()->setQuality(60)->toJpeg()->make(150);
```

Available methods are:

- `setQuality(int $quality)`: set the quality of the thumbnail used by some encoders (default to 90).

- `toWebp()`, `toPng()`, `toJpeg()`, `toGif()`, `toAvif()`: force the use of a specific encoder for the thumbnail.

- `setAppendTimestamp(bool $appendTimestamp = true)`: append a timestamp to the thumbnail URL (useful for browser cache).

- `setAfterClosure(Closure $closure)`: set a closure to be executed after the thumbnail creation. Intended to be used like this:

```php
$book->cover
    ->thumbnail()
    ->setAfterClosure(function ($wasCreated, $thumbnailPath, $thumbnailDisk) {
        // Do something...
    })
    ->make(150);
```

- `addModifier(ThumbnailModifier $modifier)`: apply an image modifier (see below).

- `make(int $width = null, int $height = null)`: create the thumbnail, with the given size. Must be called last.

#### Modifiers

You can specify Modifiers to perform image processing on the fly. A Modifier must extend the `Code16\Sharp\Form\Eloquent\Uploads\Thumbnails\ThumbnailModifier` class:

```php
class MyModifier extends ThumbnailModifier
{
    public function apply(ImageInterface $image): ImageInterface
    {
        // Do something...
    }
}
```

The following modifiers are available out of the box:

- `GreyscaleModifier`
- `FitModifier`:  will center-fit the image with a constraints set via `->setSize($width, $height)`.

You can provide a custom Modifier; you’ll need to create a class that extends `Code16\Sharp\Form\Eloquent\Uploads\Thumbnails\ThumbnailModifier`, implementing:

- `function apply(ImageInterface $image): ImageInterface`: apply your filter, using the great [Intervention API](https://image.intervention.io/v3).
- `function resized(): bool`: must return true if the resize is part of the `apply()` code (optional, default to false).

## Update with Sharp

The best part is this: Sharp will take care of everything related to update and store.

First declare your upload, like usual:

```php
function buildFormFields()
{
    $this->addField(
        SharpFormUploadField::make('cover')
            ->setLabel('Cover')
            ->setFileFilterImages()
            ->setCropRatio('1:1')
            ->setStorageDisk('local')
            ->setStorageBasePath('data/Books')
    );
}
```

Then add a customTransformer:

```php
function find($id): array
{
    return $this
        ->setCustomTransformer(
            'cover',
            new SharpUploadModelFormAttributeTransformer()
        )
        ->transform(
            Book::with('cover')->findOrFail($id)
        );
}
```

The full path of this transformer is `Code16\Sharp\Form\Eloquent\Uploads\Transformers\SharpUploadModelFormAttributeTransformer`.

And finally, and this is a sad exception to the "don't touch the applicative code for Sharp", add this in your Model that declares an upload relationship (Book, in our example):

```php
public function getDefaultAttributesFor($attribute)
{
    return in_array($attribute, ['cover'])
        ? ['model_key' => $attribute]
        : [];
}
```

This will tell SharpEloquentUpdater to add the necessary `model_key`attribute when creating a new upload.

And... voilà! From there, Sharp will handle the rest.

### Updating custom attributes

So we want to add an `author` custom attribute to our cover field: for this we add the field in the Sharp Entity Form, using the `:` separator to designate a related attribute:

```php
$this->addField(
    SharpFormTextField::make('cover:author')
        ->setLabel('Author')
);
```

Here we intend to update the `author` attribute of the `cover` relation.

## What about upload lists?

So let's say we want to add pictures of inner pages, for our Book. It can be easily done by creating a `morphMany` relation in the Book Model:

```php
public function pictures()
{
    return $this->morphMany(Media::class, 'model')
        ->where('model_key', 'pictures')
        ->orderBy('order');
}
```

And then add the field in the Sharp Entity Form:

```php
$this->addField(
    SharpFormListField::make('pictures')
        ->setLabel('Additional pictures')
        ->setAddable()->setAddText('Add a picture')
        ->setRemovable()
        ->setSortable()
        ->setOrderAttribute('order')
        ->addItemField(
            SharpFormUploadField::make('file')
                ->setFileFilterImages()
                ->setStorageDisk('local')
                ->setStorageBasePath('data/Books/Pictures')
        )
);
```

Note that we use the special `file` key for the SharpFormUploadField in the item.

You'll have next to update your Model special `getDefaultAttributesFor()` function:

```php
public function getDefaultAttributesFor($attribute)
{
    return in_array($attribute, ['cover','pictures'])
        ? ['model_key' => $attribute]
        : [];
}
```

All set.

#### Updating custom attributes in upload lists

```php
$this->addField(
    SharpFormListField::make('pictures')
        [...]
        ->addItemField(
            SharpFormUploadField::make('file')
        )
        ->addItemField(
            SharpFormTextField::make('legend')
        )
);
```

In this code, the `legend` designates a custom attribute.
