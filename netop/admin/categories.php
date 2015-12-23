<?php
require("../config/settings.php");
require("blocks/header.php");
?>
<div class="main-content">
    <div class="page-title">
        <div class="title-env">
            <h1 class="title">Lista Categorii</h1>
            <p class="description">Adauga, editeaza sau sterge categorii.</p>
        </div> 
    </div>
    <?php
    if(isset($_POST['delete'])){
        $categories = new Category;
        $categories->delete($_POST['category_id']);
        $_SESSION['succes'] = "Categoria \"{$_POST['cat_name']}\" a fost stearsa!";
    }

    if(isset($_GET['action'])){
    	if($_GET['action'] == "add"){
            $action = "add";
            $title = "Adauga Categorie";
    		if (isset($_POST['send'])){
    			$data = array(
                    "category_name" => array(
                        "required" => "Introduceti numele categoriei!",
                        "check_db" =>  array(
                                "categories",
                                "category_name",
                                "Aceasta categorie exista in baza de date!"
                            )
                    )
                );
                try{
                    $errors = Functions::Validate($data, $_POST);
                    //var_dump($errors);
                    if (!$errors){
                        $category = new Category;
                        $category->category_name = $_POST['category_name'];
                        $value = null;
                        if ($category->save($value) == 1){
                            $_SESSION['succes'] = "Categoria \"{$category->category_name}\" a fost salvata!";
                            header("Location: categories.php");
                            // $url = "categories.php";
                            // echo "<META HTTP-EQUIV=\"refresh\" content=\"0; URL=".$url."\"> ";
                            // exit();
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
            $categories = new Category;
            $categories = $categories->getCategory($id);
            foreach ($categories as $category) {
                $category_id = $category->id;
                $category_name = $category->category_name;
            }
            if (isset($_POST['send'])){
                $data = array(
                    "category_name" => array("required" => "Introduceti numele categoriei!")
                );     
                try{
                    $errors = Functions::Validate($data, $_POST);
                    //var_dump($errors);
                    if (!$errors){
                        $category = new Category;
                        $id = $_GET['id'];
                        $category->category_name = $_POST['category_name'];
                        if ($category->save($_GET['id']) == 1){
                            $_SESSION['succes'] = "Categoria \"{$category->category_name}\" a fost editata!";
                            header("Location: categories.php");
                        }else{
                            header("Location: categories.php");
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
                        <form style="clear: both" class="form-default" action="categories.php?action=<?php echo $action; ?>" method="POST">
                            <div class="form-group">
                                <label class="control-label">Numele Categoriei</label>
                                <input type="text" class="form-control" name="category_name" id="category_name" value="<?php if($_GET['action'] == "edit"){echo $category_name;}?>" />
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
        <div class="panel-heading">Lista Categorii 
            <p style="margin-top: 15px">
                <a class="btn btn-primary btn-sm" href="categories.php?action=add"><i class="fa fa-plus" style="padding-right: 15px"></i>Adauga Categorii</a>
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
        $category = new Category();                  
        $categories = $category->getCategories();
        //var_dump($categories);
        if (count($categories) > 0){

        ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nume</th>
                        <th>Optiuni</th>
                    </tr>
                </thead>
                <?php
                $cnt = 0;
                $categories = Category::Paginate();
                foreach ($categories as $category){
                    $cnt++;    
                    echo "<tr>";
                    echo "<td>{$cnt}</td>";
                    echo "<td>{$category->category_name}</td>";
                    echo "
                        <td>
                            <a class=\"btn btn-icon btn-warning edit\" href=\"categories.php?action=edit&id={$category->id}\">
                                <i class=\"fa fa-wrench\"></i>
                            </a>";
                        ?>
                        <form id="delete" method="post" action="">
                            <input type="hidden" name="category_id" value="<?php echo $category->id; ?>"/> 
                            <input type="hidden" name="cat_name" value="<?php echo $category->category_name; ?>"/> 
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
            <?php echo Category::Links();   ?>
        </div>
    </div>
    <?php
        }else{
            echo "<div class=\"error\">Nu sunt categorii introduse in baza de date!</div>";
        }
    }
    require("blocks/footer.php");                 
    ?>
        

