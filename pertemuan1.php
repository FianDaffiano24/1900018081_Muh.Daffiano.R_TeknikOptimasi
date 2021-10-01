<?php 
class Catalogue{
	function createProductColumn($listOfRawProduct){
		foreach (array_keys($listOfRawProduct) as $listOfRawProductKey){
			$listOfRawProduct[$columns[$listOfRawProductKey]] = $listOfRawProduct[$listOfRawProductKey];
			unset($listOfRawProduct[$listOfRawProductKey]);
		}
		return $listOfRawProduct;
	}
	function product(){
		$collectionOfListProduct = [];
		$raw_data = file($parameters['file_name']);
		foreach ($raw_data as $listOfRawProduct) {
			$collectionOfListProduct[] = $this->createProductColumn($parameters['columns'], explode(",", $listOfRawProduct));
		}

		// foreach ($collectionOfListProduct as $listOfRawProduct) {
		// 	print_r($listOfRawProduct);
		// 	echo '<br>';
		// }

		//return $collectionOfListProduct;
		return [
			'product' => $collectionOfListProduct,
			'gen_lenght' => count($collectionOfListProduct)
		];
	}
}

class PopulationGenerator{
	function createIndividu($parameters){
		$catalogue = new Catalogue;
		$lengtOfGen = $catalogue->product($parameters)['gen_lenght'];
		for ($i = 0; $i <= $lengtOfGen; $i++){
			$ret[] = rand(0, 1);
		}
		return $ret;
	}

	function createPopulation(){
		//$individu = new Individu;
		for ($i=0; $i <= $parameters['population_size']; $i++){
			$ret[] = $this->createIndividu($parameters);
		}
		//print_r($ret);
		//return $ret;
		foreach ($ret as $key => $val){
			print_r($val);
			echo '<br>';
		}
	}
}

$parameters = [
	'file_name' => 'products.txt',
	'columns' => ['item', 'price'],
	'population_size' => 10
];

$katalog = new Catalogue;
$katalog->product($parameters);
//print_r($katalog->product($parameters));

$initialPopulation = new Population;
$initialPopulation->createRandomPopulation($parameters);
//$population = $initialPopulation->createRandomPopulation();
 ?>