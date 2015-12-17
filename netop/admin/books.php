<?php
require("blocks/header.php");
?>
<div class="main-content">
	<div class="page-title">
		<div class="title-env">
			<h1 class="title">Lista Carti</h1>
			<p class="description">Adauga, editeaza sau sterge carti.</p>
		</div> 
	</div>
	<?php
    if(isset($_POST['delete'])){
        $books = new Books;
        $books->delete($_POST['book_id']);
        $_SESSION['succes'] = "Cartea \"{$_POST['book_name']}\" a fost stearsa!";
        unlink("../" . $_POST['book_image']);
    }
    if(isset($_GET['action'])){
    	if($_GET['action'] == "add"){
            $action = "add";
            $title = "Adauga Carte";
    		if (isset($_POST['send'])){
    			$data = array(
                    "name" => array(
                        "required" => "Introduceti numele cartii!",
                        "check_db" =>  array(
                                "books",
                                "name",
                                "Aceasta carte exista in baza de date!"
                            )
                    )
                );
                try{
                    $errors = Functions::Validate($data, $_POST);
                    if (!$errors){
	                    //verific fisierul
	                    //print_r($_FILES);
                    	if ($_FILES['image']['error'] == 0){
                    		$ext = Functions::GetExtension($_FILES['image']['name']);
                    		$target = "assets/images/books/";
                    		//$target .= str_replace(' ', '_', $_POST['name']);
                    		$target .= preg_replace('/[^A-Za-z0-9\-]/', '_', $_POST['name']) . "_banner." . $ext;
                    		if (move_uploaded_file($_FILES['image']['tmp_name'], DOC_ROOT . $target)){
                            	//fisierul s-a transferat ok
                    		}else{
                            	//fisierul nu a fost salvat
                    			$errors[] = "Imaginea nu a fost salvata";
                    		}
                    	}
                    }

                    if (!$errors){
                        $book = new Books;
                        $book->name = $_POST['name'];
                     	$book->category_id = $_POST['category'];
                        $book->author = $_POST['author'];
                        $book->price = $_POST['price'];
                        if (isset($target)){
                        	$book->image = $target;
                    	}
                        $value = null;
                        if ($book->save($value) == 1){
                            $_SESSION['succes'] = "Categoria \"{$book->name}\" a fost salvata!";
                            header("Location: books.php");
                        }else{
                            $errors[] = "Categoria nu a fost salvata!";
                        }
                    }
                }
                catch (Exception $e){
                    $errors[] = $e->getMessage();
                }
            }
        }
     	if($_GET['action'] == 'edit'){
            $id = $_GET['id'];
            $action = "edit&id={$id}";
            $title = "Editeaza Categorie";
            $books = new Books;
            $books = $books->getBook($id);
            foreach ($books as $book) {
                $book_id = $book->id;
                $name = $book->name;
                $category = $book->category_id;
                $author = $book->author;
                $image = $book->image;
                $price = $book->price;
            }
            if (isset($_POST['send'])){
                $data = array(
                    "name" => array("required" => "Introduceti numele cartii!")
                );     
                try{
                    $errors = Functions::Validate($data, $_POST);
                    if (!$errors){
	                    if ($_FILES['image']['error'] == 0){
                    		$ext = Functions::GetExtension($_FILES['image']['name']);
                    		$target = "assets/images/books/";
                    		$target .= str_replace(' ', '_', $_POST['name']) . "_banner." . $ext;
                    		if (move_uploaded_file($_FILES['image']['tmp_name'], DOC_ROOT . $target)){
                    			unlink($_GET['image']);
                    		}else{
                    			$errors[] = "Imaginea nu a fost salvata";
                    		}
                    	}
	                }
                    if (!$errors){
                        $book = new Books;
                        $book->name = $_POST['name'];
                        $book->category_id = $_POST['category'];
                        $book->author = $_POST['author'];
                        $book->price = $_POST['price'];
                        if (isset($target)){
                        	$book->image = $target;
                    	}
                        if ($book->save($_GET['id']) == 1){
                            $_SESSION['succes'] = "Cartea \"{$book->name}\" a fost editata!";
                            header("Location: books.php");
                        }else{
                            header("Location: books.php");
                        }
                    }
                }
                catch (Exception $e){
                    $errors[] = $e->getMessage();
                }
            }
        }
    ?>
	<div class="panel panel-default">
        <div class="panel-heading"><?php echo $title; ?></div>   
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12">
                    <?php
                    if ((isset($errors)) and (is_array($errors))){
                        echo"
                            <div class=\"alert alert-danger\">
                                <button type=\"button\" class=\"close\" data-dismiss=\"alert\">
                                    <span aria-hidden=\"true\">×</span>
                                    <span class=\"sr-only\">Close</span>
                                </button>";
                                 foreach ($errors as $error){
                                    echo $error;
                                }
                        echo "</div>";
                    }
                    ?>
                        <form enctype="multipart/form-data" style="clear: both" class="form-default" action="books.php?action=<?php echo $action; ?>" method="POST" >
                            <div class="form-group">
                                <label class="control-label">Numele Cartii</label>
                                <input type="text" class="form-control" name="name" id="name" value="<?php if($_GET['action'] == "edit"){echo $name;}?>" />
                            </div>
                            <div class="form-group">
	                            <label for="category">Categorie</label>
	                            <select class="form-control" name="category" id="category" style="width:300px">
	                            	<option value="">(Selecteaza categoria)</option>
	                            	<?php
	                            	$categories = Category::All(Category::$order_field);
	                            	foreach ($categories as $category){
	                            		if(($_GET['action'] == "edit") and ($category == $category->id)) {
	                            			$selected='selected';
	                            			echo "\t\t<option value=\"" . $category->id . "\" " . $selected . ">" . $category->name . "</option>\n"; 
	                            		}else{ 
	                            			echo "\t\t<option value=\"" . $category->id . "\" >" . $category->name . "</option>\n"; 
	                            		}
	                            	}      

	                            	?>  
	                            </select>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Autorul Cartii</label>
                                <input type="text" class="form-control" name="author" id="author" value="<?php if($_GET['action'] == "edit"){echo $author;}?>" />
                            </div>
                            <div class="form-group">
                            	<?php 
                            	if(($_GET['action'] == 'edit') and (!empty($image))){
                            		echo "<img src=\"".HOST."{$image}\" width=\"150\" >"."<br>";
                            	}else{
                            		echo "<img src=\"".HOST."assets/images/no-picture.jpg\" width=\"150\" >"."<br>";
                            	}
                            	?>
                            	<label for="image">Imagine</label>
                            	<input type="file" name="image" id="image" />
                            </div>
                            <div class="form-group">
                                <label class="control-label">Pretul Cartii</label>
                                <input style="width:300px" type="text" class="form-control" name="price" id="price" value="<?php if($_GET['action'] == "edit"){echo $price;}?>" />
                            </div>
                            <div class="form-group">
                                <input type="hidden" name="send" value="true"/>
                                <button class="btn btn-primary text-left" type="submit" value="Save">
                                    <i class="fa fa-save"></i>Salveaza
                                </button>
                            </div>
                        </form>
                    </div>
                </div>                    
            </div> 
        </div> 
    <?php
    }else{
	?>
	<div class="panel panel-default">
        <div class="panel-heading">Lista Carti 
            <p style="margin-top: 15px">
                <a class="btn btn-primary btn-sm" href="books.php?action=add"><i class="fa fa-plus" style="padding-right: 15px"></i>Adauga Carti</a>
            </p>
        </div>
        <div class="panel-body">
        <?php
        if (isset($_SESSION['succes'])){
            echo "
            <div class=\"alert alert-success\">
                <button type=\"button\" class=\"close\" data-dismiss=\"alert\">
                    <span aria-hidden=\"true\">×</span>
                    <span class=\"sr-only\">Close</span>
                </button>
                
                {$_SESSION['succes']}
            </div>";
            unset($_SESSION['succes']);
        }
        $book = new Books();                  
        $books = $book->getBooks();
        if (count($books) > 0){

        ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nume</th>
                        <th>Caetgorie</th>
                        <th>Autor</th>
                        <th>Imagine</th>
                        <th>Pret</th>
                        <th>Optiuni</th>
                    </tr>
                </thead>
                <?php
                $cnt = 0;
                $books = Books::Paginate();
                foreach ($books as $book){
                    $cnt++;    
                    echo "<tr>";
                    echo "<td>{$cnt}</td>";
                    echo "<td>{$book->name}</td>";
                    $category = new Category(); 
                    $category = $category->getCategory($book->category_id);
                    echo "<td>{$category[0]->name}</td>";
                    echo "<td>{$book->author}</td>";
                    echo "<td>";
                    $url = HOST . $book->image;
                    if ($book->image){
                        echo "<img src=\"". $url ."\" class=\"img-small\">";
                    }elseif(!is_array(@getimagesize($url))){
                        echo "<img src=\"" .HOST . "layout/assets/img/no-picture.jpg\" class=\"no-picture\">";  
                    };
                    echo "</td>";
                    echo "<td>" . Functions::FormatPrice($book->price) . " $</td>";
                    echo "
                        <td style=\"width:11.6%\">
                            <a class=\"btn btn-icon btn-warning edit\" href=\"books.php?action=edit&id={$book->id}&image={$book->image}\">
                                <i class=\"fa fa-wrench\"></i>
                            </a>";
                        ?>
                        <form id="delete" method="post" action="">
                            <input type="hidden" name="book_id" value="<?php echo $book->id; ?>"/> 
                            <input type="hidden" name="book_name" value="<?php echo $book->name; ?>"/> 
                            <input type="hidden" name="book_image" value="<?php echo $book->image; ?>"/> 
                            <input type="hidden" name="delete" value="true"/>
                            <button id="remove" type="submit" value="Delete" class="btn btn-icon btn-red delete" onclick="return ConfirmDelete()">
                                <i class="fa fa-trash"></i>
                            </button>
                        </form>
                        <?php
                    echo "</td>";
                    echo "</tr>"; 
                }
            ?>
            </table>
            <?php echo Books::Links();   ?>
        </div>
    </div>
    <?php
        }else{
            echo "<div class=\"error\">Nu sunt categorii introduse in baza de date!</div>";
        }
    }
    require("blocks/footer.php");                 
    ?>