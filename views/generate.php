<?php
namespace SimplerStatic;
?>

<h1>Simpler Static &rsaquo; Generate</h1>

<div class='wrap' id='generatePage'>

	<?php wp_nonce_field( 'simplerstatic_generate' ) ?>

	<div class='actions'>
		<input id='generate' class='button button-primary button-hero <?php if ( ! $this->archive_generation_done ) { echo 'hide'; } ?>' type='submit' name='generate' value='Generate Static Files' />

		<input id='cancel' class='button button-cancel button-hero <?php if ( $this->archive_generation_done ) { echo 'hide'; } ?>' type='submit' name='cancel' value='Cancel' />

		<span class='spinner <?php if ( ! $this->archive_generation_done ) { echo 'is-active'; } ?>'></span>
	</div>

	<h3>Activity Log</h3>
	<div id='activityLog'>
		<?php echo $this->activity_log; ?>
	</div>

	<h3>Export Log</h3>
	<div id='exportLog'>
		<?php echo $this->export_log; ?>
	</div>

</div>
