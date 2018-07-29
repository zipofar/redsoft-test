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
		var_dump($res);
		return new Response('Hello, World!');
	}

	public function getBySubStrName(Request $request, $attributes) {
		$name = $attributes['name'];
		$product = new MProduct();
		$res = $product->getBySubStrName($name);
		var_dump($res);
		return new Response('Hello, World!');
	}

	public function getByBrand(Request $request, $attributes) {
		$name = $attributes['name'];
		$product = new MProduct();
		$res = $product->getByBrand($name);
		var_dump($res);
		return new Response('Hello, World!');
	}

	public function getBySection(Request $request, $attributes) {
		$name = $attributes['name'];
		$product = new MProduct();
		$res = $product->getBySection($name);
		var_dump($res);
	}

	public function getBySections(Request $request, $attributes) {
		$name = $attributes['name'];
		$product = new MProduct();
		$res = $product->getBySections($name);
		var_dump($res);
	}
}