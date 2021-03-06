<?php
include_once 'header.php';

$id = !empty($_GET['id']) ? intval($_GET['id']) : 0;
$action = !empty($_GET['action']) ? $_GET['action'] : 'add';

/*
$desc = $db->query('DESC movies')->fetchAll();
foreach($desc as $key => $field) {
	echo $field['Field'].'|'.$field['Null'].'|'.$field['Type'].'<br>';
}
*/

if ($action == 'delete' && !empty($id)) {

	$query = $db->prepare('DELETE FROM movies WHERE id = :id');
	$query->bindValue('id', $id, PDO::PARAM_INT);
	$query->execute();
	$result = $query->rowCount();

	if (empty($result)) {
		echo '<div class="alert alert-danger" role="danger">Une erreur est survenue</div>';
	} else {
		echo '<div class="alert alert-success" role="success">Le film a bien été supprimé</div>';
		echo redirectJs('movies.php');
	}
	goto end;
}


$fields = array(
	'slug' => 		 array('required' => false, 'type' => 'text',  		'maxlength' => 255),
	'title' => 		 array('required' => true, 'type' => 'textarea',  	'maxlength' => 0, 'error' => 'Title is mandatory and must be 255 length max'),
	'year' => 		 array('required' => true, 'type' => 'text',  		'maxlength' => 11),
	'genres' => 	 array('required' => true, 'type' => 'text',  		'maxlength' => 255),
	'synopsis' => 	 array('required' => true, 'type' => 'textarea',  	'maxlength' => 0),
	'directors' => 	 array('required' => false, 'type' => 'text',  		'maxlength' => 255),
	'actors' => 	 array('required' => false, 'type' => 'text',  		'maxlength' => 255),
	'writers' => 	 array('required' => false, 'type' => 'text',  		'maxlength' => 255),
	'runtime' => 	 array('required' => false, 'type' => 'text',  		'maxlength' => 11, 'label' => 'duration'),
	'mpaa' => 		 array('required' => false, 'type' => 'text',  		'maxlength' => 25),
	'rating' => 	 array('required' => false,  'type' => 'text',  		'maxlength' => 3, 'default' => 1),
	'popularity' =>  array('required' => false, 'type' => 'text',  		'maxlength' => 11),
	'poster_flag' => array('required' => false,  'type' => 'checkbox',  	'maxlength' => 1, 'default' => 1)
);


if ($action == 'update' && !empty($id)) {

	$query = $db->prepare('SELECT * FROM movies WHERE id = :id');
	$query->bindValue('id', $id, PDO::PARAM_INT);
	$query->execute();
	$movie = $query->fetch();

	if (empty($movie)) {
		exit('Undefined movie');
	}
}

foreach($fields as $field_name => $field_params) {
	$$field_name = !empty($_POST[$field_name]) ? $_POST[$field_name] : @$movie[$field_name];

	if (empty($$field_name) && !empty($field_params['default'])) {
		$$field_name = $field_params['default'];
	}
}

$errors = array();

if (!empty($_POST)) {

	foreach($fields as $field_name => $field_params) {

		if ($field_params['required'] !== false && empty($_POST[$field_name])) {

			$error_label = !empty($field_params['error']) ? $field_params['error'] : $field_name.' is mandatory';

			$errors[$field_name] = $error_label;
		}
	}

	if (empty($errors)) {

		if ($action == 'update') {
			$query = $db->prepare('UPDATE movies SET slug = :slug, title = :title, year = :year, genres = :genres, synopsis = :synopsis, directors = :directors, actors = :actors, writers = :writers, runtime = :runtime, mpaa = :mpaa, rating = :rating, popularity = :popularity, poster_flag = :poster_flag, modified = NOW() WHERE id = :id');
			$query->bindValue('id', $id, PDO::PARAM_INT);
		} else {
			$query = $db->prepare('INSERT INTO movies SET slug = :slug, title = :title, year = :year, genres = :genres, synopsis = :synopsis, directors = :directors, actors = :actors, writers = :writers, runtime = :runtime, mpaa = :mpaa, rating = :rating, popularity = :popularity, poster_flag = :poster_flag, modified = NOW(), created = NOW()');
		}

		$query->bindValue('slug', $slug);
		$query->bindValue('title', $title);
		$query->bindValue('year', $year);
		$query->bindValue('genres', $genres);
		$query->bindValue('synopsis', $synopsis);
		$query->bindValue('directors', $directors);
		$query->bindValue('actors', $actors);
		$query->bindValue('writers', $writers);
		$query->bindValue('runtime', $runtime);
		$query->bindValue('mpaa', $mpaa);
		$query->bindValue('rating', $rating);
		$query->bindValue('popularity', $popularity);
		$query->bindValue('poster_flag', $poster_flag);
		$query->execute();

		if ($action == 'update') {
			$result = $query !== false && empty(intval($query->errorCode()));
		} else {
			$result = $db->lastInsertId();
		}

		if (empty($result)) {
			echo '<div class="alert alert-danger" role="danger">Une erreur est survenue</div>';
		} else {
			echo '<div class="alert alert-success" role="success">Le film a bien été '.($action == 'update' ? 'modifié' : 'ajouté').'</div>';
			echo redirectJs('movies.php');
		}
		goto end;
	}
}

if (!empty($errors)) {
	echo '<div class="alert alert-danger" role="danger">';
	foreach($errors as $error) {
		echo $error.'<br>';
	}
	echo '</div>';
}
?>

<form class="form-horizontal" action="" method="POST" novalidate>

<?php
foreach($fields as $field_name => $field_params) {

	$required = $field_params['required'];
	$type = $field_params['type'];
	$maxlength = $field_params['maxlength'];
	$label = ucfirst(!empty($field_params['label']) ? $field_params['label'] : $field_name);

	echo PHP_EOL;

	if ($type == 'textarea') {
	?>
	<div class="form-group">
		<label for="<?= $field_name ?>" class="col-sm-2 control-label"><?= $label ?></label>
		<div class="col-sm-6">
			<textarea id="<?= $field_name ?>" name="<?= $field_name ?>" class="form-control" placeholder="<?= $label ?>" rows="5" style="resize: none;"><?= $$field_name ?></textarea>
		</div>
	</div>
	<?php } else if ($type == 'checkbox') { ?>
	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<div class="checkbox">
				<label for="<?= $label ?>">
					<input id="<?= $field_name ?>" type="checkbox" name="<?= $field_name ?>" value="1" <?= $$field_name ? 'checked' : '' ?>> <?= $label ?>
				</label>
			</div>
		</div>
	</div>
	<?php } else { ?>
	<div class="form-group">
		<label for="<?= $field_name ?>" class="col-sm-2 control-label"><?= $label ?></label>
		<div class="col-sm-6">
			<input type="text" id="<?= $field_name ?>" name="<?= $field_name ?>" class="form-control" placeholder="<?= $label ?>" value="<?= $$field_name ?>">
		</div>
	</div>
	<?php
	}
}
?>

	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<button type="submit" class="btn btn-default">Envoyer</button>
		</div>
	</div>

</form>

<?php
end:

include_once 'footer.php';