<?php
require_once("../../includes/initialize.php");
// require_once("../../includes/functions.php");
// require_once("../../includes/session.php");
if (!$session->is_logged_in()) { redirect_to("login.php"); }
?>


<?php include_layout_template("admin_header.php"); ?>
	<?php
		
		// Create
		$user = new User();
		$user->username   = "vinnie";	
		$user->password   = "1234";
		$user->first_name = "vinnie";
		$user->last_name  = "marcaldo";
		$user->create();

		// Update
		// $user = User::find_by_id(4);
		// $user->username	  = "bingo";
		// $user->password   = "1234";
		// $user->first_name = "bbbingo";
		// $user->last_name  = "bbbongo";
		// $user->update();

		// Delete
		// $user = User::find_by_id(7);
		// $user->delete();

	?>
<?php include_layout_template("admin_footer.php"); ?>