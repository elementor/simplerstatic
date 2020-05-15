<?php
namespace SimplerStatic;
?>

<?php foreach ( $this->flashes as $flash ) : ?>
	<div class="fade <?php echo $flash['type']; ?>">
		<p><strong>
			<?php echo $flash['message']; ?>
		</strong></p>
	</div>
<?php endforeach; ?>

<div class="wrap">
	<div id="sistContainer">

		<div id="sistContent">
			<?php include $this->template; ?>
		</div>
		<!-- .sist-content -->

		<div id="sistSidebar">
			<div class="sidebar-container metabox-holder">
				<div class="postbox">
					<h3 class="wp-ui-primary">Like this plugin?</h3>
					<div class="inside">
						<div class="main">
                            <p>Removed newsletter signup chunk</p>

						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- .sist-sidebar -->

	</div>
	<!-- .sist-container -->
</div>
<!-- .wrap -->
