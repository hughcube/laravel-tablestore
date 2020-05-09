<?php

namespace HughCube\TableStore\Eloquent;

use HughCube\TableStore\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Support\Str;

/**
 * Class Model
 * @package HughCube\TableStore\Eloquent
 *
 * @method array getKeyType()
 * @method array getKeyName()
 * @method Builder newQuery()
 */
abstract class Model extends BaseModel
{
    use HybridRelations, EmbedsRelations;

    /**
     * The primary key for the model.
     * @var array
     */
    protected $primaryKey = ['id'];

    /**
     * The primary key type.
     * @var array
     */
    protected $keyType = ['id' => 'int'];

    /**
     * @var string 增量的key
     */
    protected $incrPrimaryKey = null;

    /**
     * Model constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->incrementing = false;
        if (null != $this->incrPrimaryKey) {
            $this->incrementing = true;
        }

        parent::__construct($attributes);
    }

    /**
     * @inheritdoc
     */
    public function getQualifiedKeyName()
    {
        return $this->getKeyName();
    }

    /**
     * @inheritdoc
     */
    public function getDateFormat()
    {
        return $this->dateFormat ?: 'Y-m-d H:i:s';
    }

    /**
     * @inheritdoc
     */
    public function getCasts()
    {
        return array_merge([$this->getKeyType()], $this->casts);
    }

    /**
     * @inheritDoc
     * @return array
     */
    public function getKey()
    {
        $keys = [];
        foreach ($this->getKeyName() as $name) {
            $keys[$name] = $this->getAttribute($name);
        }

        return $keys;
    }

    /**
     * @inheritdoc
     */
    public function getForeignKey()
    {
        return Str::snake(class_basename($this)) . '_' . implode('_', $this->getKeyName());
    }

    /**
     * @inheritdoc
     */
    public function newEloquentBuilder($query)
    {
        return new Builder($query);
    }

    /**
     * @inheritdoc
     */
    protected function newBaseQueryBuilder()
    {
        $connection = $this->getConnection();

        return new QueryBuilder($connection);
    }
}
