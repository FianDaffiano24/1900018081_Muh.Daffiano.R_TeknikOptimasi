<?php 

class Parameters {
	const FILE_NAME = 'products.txt';
	const COLUMNS = ['item', 'price'];
	const POPULATION_SIZE = 30;
	const BUDGET = 280000;
	const STOPPING_VALUE = 100000;
	const CROSOVER_RATE = 0.8;
}

class Catalogue {
	function createProductColumn($listOfRawProduct){
		foreach (array_keys($listOfRawProduct) as $listOfRawProductKey){
			$listOfRawProduct[Parameters::COLUMNS[$listOfRawProductKey]] = $listOfRawProduct[$listOfRawProductKey];
			unset($listOfRawProduct[$listOfRawProductKey]);
		}
		return $listOfRawProduct;
	}

	function product(){
		$collectionOfListProduct = [];
		$raw_data = file(Parameters::FILE_NAME);
		foreach ($raw_data as $listOfRawProduct) {
			$collectionOfListProduct[] = $this->createProductColumn(explode(",", $listOfRawProduct));
		}

		return $collectionOfListProduct; 

		/*foreach ($collectionOfListProduct as $listOfRawProduct) {
			print_r($listOfRawProduct);
			echo '<br>';
		}*/

		/*return [
			'product' => $collectionOfListProduct,
			'gen_lenght' => count($collectionOfListProduct)
		];*/
	}
}

class Individu{
	function countNumberOfGen(){
		$catalogue = new Catalogue;
		return count($catalogue->product());
	}

	function createRandomIndividu(){
		for ($i=0; $i<=$this->countNumberOfGen()-1; $i++){
			$ret[] = rand(0, 1);
		}
		return $ret;
	}
}

class Population{
	/*function createIndividu($parameters){
		$catalogue = new Catalogue;
		$lengtOfGen = $catalogue->($parameters)['gen_lenght'];
		for ($i = 0; $ <= $lengtOfGen; $i++){
			$ret[] = rand(0, 1);
		}
		return $ret;
	}*/

	function createRandomPopulation(){
		$individu = new Individu;
		for ($i=0; $i <= Parameters::POPULATION_SIZE-1; $i++){
			$ret[] = $individu->createRandomIndividu();
		}
		return $ret;
		/*foreach ($ret as $key => $val){
			print_r($val);
			echo '<br>';
		}*/
	}
}

class Fitness{
	function selectingItem(){
		$catalogue = new Catalogue;
		$individu = new Individu;
		foreach ($individu as $individuKey => $binaryGen){
			if ($binaryGen === 1){
				$ret[] = [
					'selectedKey' => $individuKey,
					'selectedPrice' => $catalogue->product()[$individuKey]['price']
				];
			}
		}
		return $ret;
	}

	function calculateFitnessValue($individu){
		array_sum(array_column($this->selectingItem($individu), 'selectedPrice'));
		// return array_sum(array_column($this->selectingItem($individu), 'selectedPrice'));
		//$this->selectingItem($individu);
	}

	function countSelectedItem($individu){
		return count($this->selectingItem($individu));
	}

	function searchBestIndividu($fits, $maxItem, $numberOfIndividuHasMaxItem){
		if ($numberOfIndividuHasMaxItem === 1){
			$index = array_search($maxItem, array_column($fits, 'numberOfSelectedItem'));
			return $fits[$index];
		} else {
			foreach ($fits as $key => $val){
				if ($val['numberOfSelectedItem'] === $maxItem){
					echo $key. ' '. $val['fitnessValue']. '<br>';
					$ret[] = [
						'individuKey' => $key,
						'fitnessValue' => $val['fitnessValue']
					];
				}
			}
			if (count(array_unique(array_column($ret, 'fitnessValue'))) ===1 ){
				$index = rand(0, count($ret) - 1);
			} else {
				$max = max(array_column($ret, 'fitnessValue'));
				$index = array_search($max, array_column($ret, 'fitnessValue'));
			}
			echo ('hasil');
			print_r($ret[$index]);
			return $ret[$index];
		}
	}

	function isFound($fits){
		$countedMaxItems = array_count_values(array_column($fits, 'numberOfSelectedItem'));
		print_r($countedMaxItems);
		echo'<br>';
		$maxItem = (array_keys($countedMaxItems));
		echo $maxItem;
		echo '<br>';
		echo $countedMaxItems[$maxItem];
		$numberOfIndividuHasMaxItem = $countedMaxItems[$maxItem];

		$bestFitnessValue = $this->searchBestIndividu($fits, $maxItem, $numberOfIndividuHasMaxItem);
		echo('<br>');
		print_r($bestFitnessValue['fitnessValue']);
		echo '<br> Best fitness value'. $bestFitnessValue;

		$residual = Parameters::BUDGET - $bestFitnessValue;
		echo 'Residual : '. $residual;

		if ($residual <= Parameters::STOPPING_VALUE && $residual > 0){
			return TRUE;
		}
	}

	function isfit($fitnessValue){
		if ($fitnessValue <= Parameters::BUDGET){
			return TRUE;
		}
	}

	function fitnessEvaluation($population){
		$catalogue = new Catalogue;
		foreach ($population as $listOfIndividuKey => $listOfIndividu){
			echo 'Individu-'. $listOfIndividuKey. '<br>';
			foreach ($listOfIndividu as $individuKey => $binaryGen){
				echo $binaryGen. '&nbsp;&nbsp;';
				print_r($catalogue->product()[$individuKey]);
				echo '<br>';
			}
			$fitnessValue = $this->calculateFitnessValue($listOfIndividu);
			$numberOfSelectedItem = $this->countSelectedItem($listOfIndividu);
			echo 'Max. Item : '. $numberOfSelectedItem;
			echo 'Fitness value : '. $fitnessValue;
			if ($this->isfit($fitnessValue)) {
				echo ' (Fit)';
				$fits[] = [
					'selectedIndividuKey' => $listOfIndividuKey,
					'numberOfSelectedItem' => $numberOfSelectedItem,
					'fitnessValue => $fitnessValue'
				];
				print_r($fits);
			} else {
				echo ' (Not Fit)';
			}
			echo '<p>';
		}
		if ($this->isFound($fits)){
			echo 'found';
		} else {
			echo ' >> Next generation';
		}
	}
}

class Crossover
{
	public $population;
	function __construct($population)
	{
		$this->populations = $population;
	}

	function randomZeroToOne()
	{
		return (float) rand() / (float) getrandmax();
	}

	function generateCrossover()
	{
		for ($i=0; $i<=Parameters::POPULATION_SIZE-1; $i++){
			$randomZeroToOne = $this->randomZeroToOne();
			if ($randomZeroToOne < Parameters::CROSOVER_RATE) {
				$parents[$i] = $randomZeroToOne;
			}
		}
		foreach (array_keys($parents) as $key){
			foreach (array_keys($parents) as $subkey){
				if ($key !== $subkey) {
					$ret[] = [$key, $subkey];
				}
			}
			array_shift($parents);
		}
		return $ret;
	}

	function offspring($parent1, $parent2, $cutPointIndex, $offspring){
		$lengtOfGen = new Individu;
		if ($offspring === 1) {
			for ($i=0; $i <= $lengtOfGen->countNumberOfGen()-1; $i++){
				if ($i <= $cutPointIndex){
					$ret[] = $parent1[$i];
				}
				if ($i > $cutPointIndex){
					$ret[] = $parent2[$i];
				}
			}

		}

		if ($offspring === 2) {
			for ($i=0; $i <= $lengtOfGen->countNumberOfGen()-1; $i++){
				if ($i <= $cutPointIndex) {
					$ret[] = $parent2[$i];
				}
				if ($i > $cutPointIndex){
					$ret[] = $parent1[$i];
				}
			}
		}
		return $ret;
	}

	function cutPointRandom()
	{
		$lengtOfGen = new Individu;
		return rand(0, $lengtOfGen->countNumberOfGen()-1);
	}

	function crossover()
	{
		$cutPointIndex = $this->cutPointRandom();
		//echo $cutPointIndex;
		foreach ($this->generateCrossover() as $listOfCrossover){
			$parent1 = $this->populations[$listOfCrossover[0]];
			$parent2 = $this->populations[$listOfCrossover[1]];
			// echo '<p></p>';
			// echo 'parents :<br>';
			// foreach ($parent1 as $gen){
			// 	echo $gen;
			// }
			// echo ' >< ';
			// foreach ($parent2 as $gen){
			// 	echo $gen;
			// }
			// echo '<br>';

			// echo 'offspring<br>';
			$offspring1 = $this->offspring($parent1, $parent2, $cutPointIndex, 1);
			$offspring2 = $this->offspring($parent1, $parent2, $cutPointIndex, 2);
			// foreach ($offspring1 as $gen){
			// 	echo $gen;
			// }
			// echo ' >< ';
			// foreach ($offspring2 as $gen){
			// 	echo $gen;
			// }
			$offspring[] = $offspring1;
			$offspring[] = $offspring2;
		}
		return $offspring;
	}
}

class Randomizer
{
	static function getRandomIndexofGen(){
		return rand(0, (new Individu())->countNumberOfGen() - 1);
	}

	static function getRandomIndexofIndividu(){
		return rand(0, Parameters::POPULATION_SIZE - 1);
	}
}

class Mutation 
{
	function __construct($population)
	{
		$this->population = $population;
	}

	function calculateMutationRate()
	{
		return 1 / (new Individu())->countNumberOfGen();
	}

	function calculateNumofMutation()
	{
		return round($this->calculateMutationRate() * Parameters::POPULATION_SIZE);
	}

	function isMutation()
	{
		if ($this->calculateNumofMutation() > 0){
			return true;
		}
	}

	function generateMutation($valueofGen)
	{
		if ($valueofGen === 0){
			return 1;
		} else{
			return 0;
		}
	}

	function mutation()
	{ 
		if ($this->isMutation()){
			for ($i = 0; $i <= $this->calculateMutationRate()-1; $i++){
				$indexofIndividu = Randomizer::getRandomIndexofIndividu();
				$indexofGen = Randomizer::getRandomIndexofGen();
				$selectedIndividu = $this->population[$indexofIndividu];

				echo 'Before mutation : ';
				print_r($selectedIndividu);
				echo '<br>';
				$valueofGen = $selectedIndividu[$indexofGen];
				$mutatedGen = $this->generateMutation($valueofGen);
				$selectedIndividu[$indexofGen] = $mutatedGen;
				echo 'After Mutation : ';
				print_r($selectedIndividu);
				$ret[] = $selectedIndividu;
			}
			return $ret;
			return $selectedIndividu;
		}
	}
}

class Selection 
{
	function __construct($population, $combinedoffsprings){
		$this->population = $population;
		$this->combinedoffsprings = $combinedoffsprings;
	}

	function createTemporarypopulation()
	{
		//echo 'base population : '. count($this->population).'&nbsp;';
		foreach ($this->combinedoffsprings as $offspring){
			$this->population[] = $offspring;
		}
		//echo 'offspring : '. count($this->combinedoffsprings).'Temporary : '.count($this->population);
		return $this->population;
	}

	function getVariableValue($basePopulation, $fitTemporarypopulation)
	{
		foreach ($fitTemporarypopulation as $val){
			$ret[] = $basePopulation[$val[1]];
		}
		return $ret;
	}

	function sortFitTemporaryPopulation()
	{
		$tempPopulation = $this->createTemporarypopulation();
		$fitness = new Fitness;
		foreach ($tempPopulation as $key => $individu){
			$this->fitnessValue = $fitness->calculateFitnessValue($individu);
			if ($fitness->isfit($fitnessValue)){
				$fitTemporarypopulation[] = [
					$fitnessValue,
					$key
				];
			}
		}
		rsort($fitTemporarypopulation);
		$fitTemporarypopulation = array_slice($fitTemporarypopulation, 0, Parameters::POPULATION_SIZE);
		return $this->getVariableValue($tempPopulation, $fitTemporarypopulation);
		}

	function selectingIndividus()
	{
		$selected = $this->sortFitTemporaryPopulation();
		echo '<p></p>';
		print_r($selected);
	}
}


$initialPopulation = new Population;
$population = $initialPopulation->createRandomPopulation();

$fitness = new Fitness;
$fitness->fitnessEvaluation($population);

$crossover = new Crossover($population);
$crossoveroffsprings = $crossover->crossover();

echo '<br>';
echo 'Crossover offsprings:<br>';
print_r($crossoveroffsprings);

echo '<p></p>';
(new Mutation($population))->mutation();
$mutation = new Mutation($population);
if ($mutation->mutation()){
	$mutationoffsprings = $mutation->mutation();
	echo 'Mutation offsprings<br>';
	print_r($mutationoffsprings);
	echo '<p></p>';
	foreach ($mutationoffsprings as $mutationoffspring){
		$crossoveroffsprings[] = $mutationoffspring;
	}
}
echo 'Mutation offsprings <br>';
print_r($crossoveroffsprings);
$fitness->fitnessEvaluation($crossoveroffsprings);

$selection = new Selection($population, $crossoveroffsprings);
$selection->selectingIndividus();

// $individu = new Individu;
// print_r($individu->createRandomIndividu());

 ?>