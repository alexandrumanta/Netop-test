<?php
require("config/settings.php");
$book = new Books();                  
        $books = $book->getBooks();
foreach ($books as $book) {
	$category = new Category(); 
            $category = $category->getCategory($book->category_id);
	$data[] = array(
				'id' => $book->id,
				'name' => $book->name,
				'author' => $book->author,
				'image' => $book->image ,
				'category' => $category[0]->name,
				'price' => $book->price
				);
}
$data = json_encode($data, true);
echo $data;

// if(isset($_GET['keyword']) && (!empty($_GET['keyword'])) ){
// 	try{
// 		$value = $_GET['keyword'];
// 		$book = new Books;
// 		$books = $book->searchKeyword($value);
// 		foreach ($books as $book) {
// 			$category = new Category(); 
//             $category = $category->getCategory($book->category_id);
// 			$data[] = array(
// 				'name' => 'Nume:' . $book->name,
// 				'author' => 'Autor: ' . $book->author,
// 				'image' => '<img src ="' .$book->image . '"/>',
// 				'category' => 'Categorie: ' . $category[0]->name,
// 				'error' => ''
// 				);
// 		}
// 	}catch (Exception $e){
// 		$data[] = array(
// 			'error' => $e->getMessage(),
// 			'name' => '',
// 			'author' => '',
// 			'image' => '',
// 			'category' => ''
// 			);
// 	}
// 	echo json_encode($data);
// }else{
// 	$errors[] = "Eroare la conexiune!";;
// 	echo json_encode($data);
// }

?>