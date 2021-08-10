<?php

namespace Database\Factories;

use Faker\Generator;
use Illuminate\Cache\RedisStore;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\App;

abstract class RedisFactory
{
    use WithFaker;

    /** @var Generator */
    protected $faker;
    protected string $model;

    private RedisStore $store;

    /** @var self[] */
    private array $has = [];
    private array $state = [];
    private int $count = 1;

    abstract public function definition(): array;

    abstract protected function getKey(): string;

    public function __construct()
    {
        $this->store = App::make(RedisStore::class);
        $this->setUpFaker();
    }

    public static function new(): self
    {
        return new static();
    }

    public function state(array $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function has(self $factory, string $relation): self
    {
        $this->has[$relation][] = $factory;

        return $this;
    }

    public function count(int $count): self
    {
        if ($count < 1) {
            return $this;
        }

        $this->count = $count;

        return $this;
    }

    public function configure(): self
    {
        return $this;
    }

    /**
     * @param array $attributes
     * @return array|object
     */
    public function create(array $attributes = [])
    {
        $models = $this->make($attributes);
        if (!is_array($models)) {
            $models = [$models];
        }

        foreach ($models as $model) {
            foreach ($this->has as $relation => $factories) {
                foreach ($factories as $factory) {
                    $model->{$relation}[] = $factory->make();
                }
            }

            $this->store->forever($this->getKey().$model->{$this->getIdentifier()}, $model);
        }

        if (count($models) > 1) {
            return $models;
        }

        return $models[0];
    }

    /**
     * @param array $attributes
     * @return array|object
     */
    public function make(array $attributes = [])
    {
        $models = [];

        $attributes = array_merge($this->definition(), $this->state, $attributes);
        for ($i = 0; $i < $this->count; ++$i) {
            $model = new $this->model();
            foreach ($attributes as $name => $value) {
                $model->{$name} = $value;
            }

            $models[] = $model;
        }

        if (count($models) > 1) {
            return $models;
        }

        return $models[0];
    }

    /**
     * @return string|int
     */
    protected function getIdentifier()
    {
        return '';
    }
}
