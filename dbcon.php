

<?php
	$host = 'localhost';
	$user = 'root';
	$password = '';
	$database = 'user_db';

	$con = mysqli_connect($host, $user, $password, $database);

	if (!$con){
		?>
			<script>alert("Connection Unsuccessful!!!");</script>
		<?php
	}
?>
