<?php 

include('qalib.php');

echo "Testing qalib<br>";




$api = new MyClass('admin','pass');
 
echo "<pre><br>";

/*

	//example login

	//print_r($api->login('admin','pass'));

*/

/*

//example add and remove discussion

	$json = $api->addDiscussion(1,'esto es una prueba','aquí viene el texto de la pregunta');
	echo $json;
	$array = json_decode($json);

	echo "<br>";
	$json = $api->removeDiscussion(1,$array->DiscussionID);
	echo $json;

*/

/*

//example add and remove discussion

	$json = $api->addComment(54,1,'comentario de pruebas');
	echo $json;
	$array = json_decode($json);

	echo "<br>";
	$json = $api->removeComment(1,$array->CommentID);
	echo $json;

*/




?>