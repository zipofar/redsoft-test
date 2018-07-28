<?php

namespace Zipofar;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Zipofar\Model\Product as MProduct;

class Product
{
	public function getById(Request $request, $attributes) {
		$id = $attributes['id'];
		$product = new MProduct();
		$res = $product->getById($id);
		return new Response('Hello, World!');
	}

	public function getBySubStrName(Request $request, $attributes) {
		$name = $attributes['name'];
		$product = new MProduct();
		$res = $product->getBySubStrName($name);
		return new Response('Hello, World!');
	}

	public function getByBrand(Request $request, $attributes) {
		var_dump($attributes);
		return new Response('Hello, World!');
	}

	public function getBySection(Request $request, $attributes) {
		var_dump($attributes);
		return new Response('Hello, World!');
	}

	public function getBySections(Request $request, $attributes) {
		var_dump($attributes);
		return new Response('Hello, World!');
	}
}