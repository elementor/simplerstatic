<?php
namespace SimplerStatic;
?>

<h1>Simpler Static &rsaquo; Diagnostics</h1>

<div class='wrap' id='diagnosticsPage'>

	<?php foreach ( $this->results as $title => $tests ) : ?>
		<table class='widefat striped'>
			<thead>
				<tr>
					<th colspan='2'><?php echo $title; ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $tests as $result ) : ?>
					<tr>
						<td class='label'><?php echo $result['label'] ?></td>
						<?php if ( $result['test'] ) : ?>
							<td class='test success'><?php echo $result['message'] ?></td>
						<?php else : ?>
							<td class='test error'><?php echo $result['message'] ?></td>
						<?php endif; ?>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endforeach; ?>

	<table class='widefat striped'>
		<thead>
			<tr>
				<th>Theme Name</th>
				<th>Theme URL</th>
				<th>Version</th>
				<th>Enabled</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $this->themes as $theme ) : ?>
				<tr>
					<td width='20%'><?php echo $theme->get( 'Name'); ?></td>
					<td width='60%'><a href='<?php echo $theme->get( 'ThemeURI'); ?>'><?php echo $theme->get( 'ThemeURI'); ?></a></td>
					<td width='10%'><?php echo $theme->get( 'Version'); ?></td>
					<?php if ( $theme->get( 'Name') === $this->current_theme_name ) : ?>
						<td width='10%' class='enabled'>Yes</td>
					<?php else : ?>
						<td width='10%' class='disabled'>No</td>
					<?php endif; ?>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<table class='widefat striped'>
		<thead>
			<tr>
				<th>Plugin Name</th>
				<th>Plugin URL</th>
				<th>Version</th>
				<th>Enabled</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $this->plugins as $plugin_path => $plugin_data ) : ?>
				<tr>
					<td width='20%'><?php echo $plugin_data[ 'Name' ]; ?></td>
					<td width='60%'><a href='<?php echo $plugin_data[ 'PluginURI' ]; ?>'><?php echo $plugin_data[ 'PluginURI' ]; ?></a></td>
					<td width='10%'><?php echo $plugin_data[ 'Version' ]; ?></td>
					<?php if ( is_plugin_active( $plugin_path ) ) : ?>
						<td width='10%' class='enabled'>Yes</td>
					<?php else : ?>
						<td width='10%' class='disabled'>No</td>
					<?php endif; ?>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<h3 style='margin-top: 50px;'>Debugging Options</h3>

	<form id='diagnosticsForm' method='post' action=''>

		<?php wp_nonce_field( 'simplerstatic_diagnostics' ) ?>
		<input type='hidden' name='_diagnostics' value='1' />

		<table class='form-table'>
			<tbody>
				<tr>
					<th>Debugging Mode</th>
					<td>
						<label>
							<input aria-describedby='enableDebuggingHelpBlock' name='debugging_mode' id='debuggingMode' value='1' type='checkbox' <?php Util::checked_if( $this->debugging_mode === '1' ); ?> />
							Enable debugging mode
						</label>
						<p id='enableDebuggingHelpBlock' class='help-block'>
							When enabled, a debug log will be created when generating static files.
						</p>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<p class='submit'>
							<input class='button button-primary' type='submit' name='save' value='Save Changes' />
						</p>
					</td>
				</tr>
			</tbody>
		</table>

	</form>

</div>
<!-- .wrap -->
