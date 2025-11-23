<?php 

namespace App\Repositories;
use Illuminate\Database\Eloquent\Builder;
interface BaseInterface {

	public function filterQuery(array $parameters):self;
	public function setParameters(array $parameters):self;

	public function get();
	public function columns();
	public function with();
	public function relation();
	public function like(string $column, string $value): self;
	public function first();

	/**
	 * GLOBAL METHODS
	 */
	public function pregSplit(string $pattern, string $value):array;

}