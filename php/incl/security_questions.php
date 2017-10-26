<?php
//Array of security questions 
//  Used to keep database reference integer associated with the correct question

$security_questions_array = array(
	1 => "What was the last name of your second grade teacher?",
	2 => "Where was your first kiss?",
	//Add more security questions when possible
);

//Example of how to use for user input
/*
<select name="selectName">
	<?php
	foreach($secuirty_questions_array as $key => $question){
		echo '<option value='.$key.'>'.$question.'</option>';
	}
	?>
</select>
*/
?>