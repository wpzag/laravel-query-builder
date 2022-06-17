<?php
	
	namespace Wpzag\QueryBuilder\Database\Factories;
	
	use Illuminate\Database\Eloquent\Factories\Factory;
	use Wpzag\QueryBuilder\Tests\TestClasses\Models\TestModel;
	
	class TestModelFactory extends Factory
	{
		protected $model = TestModel::class;
		
		public function definition() : array
		{
			return [
				'name' => $this->faker->name,
				'age' => $this->faker->numberBetween(13, 60),
				'is_visible' => $this->faker->boolean,
			];
		}
	}
