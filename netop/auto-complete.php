<?php
require("config/settings.php");

if(isset($_GET['keyword']) && (!empty($_GET['keyword'])) ){
	try{
		$value = $_GET['keyword'];
		$book = new Books;
		$books = $book->searchKeyword($value);
		//var_dump($books);
		foreach ($books as $book) {
			$data[] = array(
				'name' => 'Nume:' . $book->name,
				'author' => 'Autor: ' . $book->author,
				'image' => '<img src ="' .$book->image . '"/>',
				'category' => 'Categorie: ' . $book->category_name,
				'price' => Functions::FormatPrice($book->price) . " $",
				'error' => ''
				);
		}
	}catch (Exception $e){
		$data[] = array(
			'error' => $e->getMessage(),
			'name' => '',
			'author' => '',
			'image' => '',
			'price' => '',
			'category' => ''
			);
	}
	echo json_encode($data);
}else{
	$data[] = "Eroare la conexiune!";;
	echo json_encode($data);
}

?>