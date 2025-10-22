<?php

namespace AugustPermana\HypervelMetaGenerator\Models;

use Hypervel\Database\Eloquent\Model;

/**
 * Base model for metadata tables.
 */
class MetaModel extends Model
{
    // Define fillable fields for mass assignment
    protected array $fillable = ['parent_id', 'key', 'type', 'value'];

    /**
     * Define a polymorphic relationship to the parent model.
     *
     * @return \Hypervel\Database\Eloquent\Relations\MorphTo
     */
    public function parent()
    {
        return $this->morphTo('parent');
    }
}
