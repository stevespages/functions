<?php

session_start();

include_once "index-admin.php";

$navigation_names = array("Home", "Say Omm", "The Bigger Picture");
$navigation_links = createLinks($navigation_names, 'admin.php');
$navigation_links['Main Site'] = 'index.php';
$navigation = createNavigation($navigation_links);

// detects if a session has started. If not $_SESSION['valid'] is assigned the value "No".
if(!isset($_SESSION['valid'])) {
	$_SESSION['valid'] = "No";
}

// if login.php was posted the correct password it will have set $_SESSION['valid']="Yes".
// if $_SESSION['valid']=="Yes" allow access to the Admin pages.
// if $_SESSION['valid']!="Yes" present the login page.
if($_SESSION['valid'] == "Yes") {
	// The value of $contrl is derived from navigation href values and form action values.
	// $contrl is used to select the appropriate controller .php file.
	// $contrl is used to append to the HTML title element.
	// $contrl is used to select the appropriate Smarty .tpl file.
	$controller = getControllerName($navigation_names);
	}	else {
			$controller = "login";
		}

// MODEL


// VIEW


// CONTROLLER

function login()
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST')	{
        if ($_POST['password'] == "1234")	{
            $_SESSION['valid'] = "Yes";
 		      header("Location: admin.php");
 		  }
    }

    if($_SESSION['valid'] != "Yes") {
        $content = array("<form method='post' action='admin.php'>
        <p><label>Password <input type='password' name='password'></label></p>
        <p><input type='submit'><p>
        </form></br>", '', '');
        return $content;
    }
}


// This, and functions/functions.php needs to be modified to change images
function home($pdo, $today_form_array)
{
	 $action = 'admin.php?page=home';
	 If (isset($_GET['id'])) {
        $id = htmlentities($_GET['id']); // this is necessary for editing and deleting a particular row
    }
    If ($_SERVER['REQUEST_METHOD'] == 'POST')	{
        $today_form_array = assignPostToFormArray($today_form_array);
        $today_form_array = validateFormArray($today_form_array, $pdo, 'quote');

        //if(!isset($_GET['id'])){
        		$today_form_array = assignFileUploadToFormArray($today_form_array);
		  //}

        $is_form_valid = isFormValid($today_form_array);


				echo 'line 75';
				echo '</br>';
				var_dump($is_form_valid);
				echo '<pre>';
				var_dump($_POST);
				echo '</pre>';
				echo '<pre>';
				var_dump($today_form_array);
				echo '</pre>';
				echo '<pre>';
				var_dump($_FILES);
				echo '</pre>';
				echo 'line 84';
				echo '</br>';
				exit;

        // do not move files if $is_form_valid != true
        if($is_form_valid) {
            $today_form_array = moveFiles($today_form_array, 'images/');
        }

        if($is_form_valid AND !isset($_GET['id'])) {
            save('quote', $today_form_array, $pdo); // saves post as a new row
				header('Location: admin.php');
        }
        // $_GET['id'] is set because the value of the form's action attribute was set...
	     // ...to ?id=.$id when the form was generated from clicking on the Edit hyperlink
	     if($is_form_valid === true AND isset($_GET['id'])) {
		      updateRow('quote', $today_form_array, $id, $pdo);  // updates a row from an edited post



		      header('Location: admin.php');
	     }
    }

    If ($_SERVER['REQUEST_METHOD'] != 'POST' AND isset($_GET['id']))	{
    	  // note that it does not make sense to repopulate the input...
    	  // type='file' values with the user's file names as these may have...
    	  // changed on the users's computer since (s)he uploaded them.
        if($_GET['action']=='edit') {
            $action .= "&id=".$id;
						$statement = getRowToEdit('quote', $id, $pdo); // gets a row to be edited using id of the row
						$row = $statement->fetchObject();
						foreach($today_form_array as $key => $array) {
							$today_form_array[$key]['value'] = $row->$key;
						}
        }

				echo 'line 121';
				echo '</br>';
				var_dump($is_form_valid);
				echo '<pre>';
				var_dump($today_form_array);
				echo '</pre>';
				echo '<pre>';
				var_dump($_FILES);
				echo '</pre>';
				echo 'line 130';
				echo '</br>';

        if($_GET['action']=='delete') {
            deleteRow('quote', $id, $pdo); // deletes a row using id of the row
            // need to call function to delete files
            header('Location: admin.php');
        }
    }

    // if this point has been reached as the result of an invalid post...
    // ...(as opposed to a fresh visit to the page) then it makes sense to...
    // ...repopulate the input type='file' values if that is possible. If...
    // ...not possible the names of the files could be supplied in an error...
    // ...message saying they need to be reselected. This only makes sense...
    // ...an invalid post as opposed to editing a post as in the former...
    // ...situation there has presumably not been time for files to change...
    // ...on the user's machine.
    $home_form = showForm($action, $today_form_array, true, 'admin.php');
    $statement = getAll('quote', $pdo);
    $home_table = createTable($statement, 'home', true, true);

    $content = array ($home_form, $home_table, '');

		echo 'line 145';
		echo '</br>';
		var_dump($is_form_valid);
		echo '<pre>';
		var_dump($today_form_array);
		echo '</pre>';
		echo '<pre>';
		var_dump($_FILES);
		echo '</pre>';
		echo 'line 154';
		echo '</br>';

    return $content;
}

function sayOmm()
{
    $content = array("<p>Ommmm !!</p>", '', '');
    return $content;
}

function theBiggerPicture()
{
    $content = array("<p>Lets investigate the bigger picture</p>", '', '');
    return $content;
}


if($controller == 'login') {
	$navigation = "";
}

$main_heading = 'Test Functions: Admin';

// the values of $controller relate to the elements in $navigation
switch ($controller) {
    case "home":
        $content = home($pdo, $today_form_array);
        break;
    case "login":
        $content = login();
        break;
    case "say-omm":
        $content = sayOmm();
        break;
    case "the-bigger-picture":
        $content = theBiggerPicture();
}

$sub_heading = ucwords(str_replace('-', ' ', $controller));
$title = 'Test Functions: Admin: '.$sub_heading;
$template = include_once 'template.php';
echo $template;
