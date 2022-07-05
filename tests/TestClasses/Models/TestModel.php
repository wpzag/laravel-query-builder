<?php

    namespace Wpzag\QueryBuilder\Tests\TestClasses\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;
    use Illuminate\Database\Eloquent\Relations\BelongsToMany;
    use Illuminate\Database\Eloquent\Relations\HasMany;
    use Illuminate\Database\Eloquent\Relations\MorphMany;

    class TestModel extends Model
    {
        use HasFactory;

        protected $guarded = [];

        public function relatedModels(): HasMany
        {
            return $this->hasMany(RelatedModel::class);
        }

        public function getAppendedFieldAttribute(): string
        {
            return 'appended';
        }

        public function relatedModel(): BelongsTo
        {
            return $this->belongsTo(RelatedModel::class);
        }

        public function otherRelatedModels(): HasMany
        {
            return $this->hasMany(RelatedModel::class);
        }

        public function relatedThroughPivotModels(): BelongsToMany
        {
            return $this->belongsToMany(RelatedThroughPivotModel::class, 'pivot_models');
        }

        public function relatedThroughPivotModelsWithPivot(): BelongsToMany
        {
            return $this->belongsToMany(RelatedThroughPivotModel::class, 'pivot_models')
                ->withPivot(['location']);
        }

        public function morphModels(): MorphMany
        {
            return $this->morphMany(MorphModel::class, 'parent');
        }
    }
