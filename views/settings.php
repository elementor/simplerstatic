<?php
namespace SimplerStatic;
?>

<h1>Simpler Static &rsaquo; Settings</h1>

<div class='wrap' id='settingsPage'>

	<h2 id='sistTabs' class='nav-tab-wrapper'>
		<a class='nav-tab' id='general-tab' href='#tab-general'>General</a>
		<a class='nav-tab' id='include-exclude-tab' href='#tab-include-exclude'>Include/Exclude</a>
		<a class='nav-tab' id='advanced-tab' href='#tab-advanced'>Advanced</a>
		<a class='nav-tab' id='reset-settings-tab' href='#tab-reset-settings'>Reset</a>
	</h2>

	<form id='optionsForm' method='post' action=''>

		<?php wp_nonce_field( 'simplerstatic_settings' ) ?>
		<input type='hidden' name='_settings' value='1' />

		<div id='general' class='tab-pane'>
			<table class='form-table'>
				<tbody>
					<tr>
						<th>
							<label>Destination URLs</label>
						</th>
						<td>
							<p>When exporting your static site, any links to your WordPress site will be replaced by one of the following: absolute URLs, relative URLs, or URLs contructed for offline use.</p>
						</td>
					</tr>
					<tr>
						<th></th>
						<td class='url-dest-option'>
							<span>
								<input type="radio" name="destination_url_type" value="absolute" <?php Util::checked_if( $this->destination_url_type === 'absolute' ); ?>>
							</span>
							<span>
								<p><label>Use absolute URLs</label></p>
								<select id='destinationScheme' class='scheme-entry' name='destination_scheme'>
									<?php foreach ( array( 'http://', 'https://', '//' ) as $scheme ) : ?>
									<option value='<?php echo $scheme; ?>' <?php Util::selected_if( $this->destination_scheme === $scheme ) ?>><?php echo $scheme; ?></option>
									<?php endforeach; ?>
								</select><!--
							 --><input aria-describedby='destinationHostHelpBlock' type='text' id='destinationHost' class='host-entry' name='destination_host' placeholder='www.example.com/' value='<?php echo trailingslashit( esc_attr( $this->destination_host ) ); ?>' size='50' />
								<p id='destinationHostHelpBlock' class='help-block'>Convert all URLs for your WordPress site to absolute URLs at the domain specified above.</p>
							</span>
						</td>
					</tr>
					<tr>
						<th></th>
						<td class='url-dest-option'>
							<span>
								<input type="radio" name="destination_url_type" value="relative" <?php Util::checked_if( $this->destination_url_type === 'relative' ); ?>>
							</span>
							<span>
								<p><label>Use relative URLs</label></p>
								<input aria-describedby='relativePathHelpBlock' type='text' id='relativePath' name='relative_path' placeholder='/' value='<?php echo trailingslashit( esc_attr( $this->relative_path ) ); ?>' size='50' />
								<div id='relativePathHelpBlock' class='help-block'>
									<p>Convert all URLs for your WordPress site to relative URLs that will work at any domain. Optionally specify a path above if you intend to place the files in a subdirectory.</p>
									<p>Example: enter <code>/path/</code> above if you wanted to serve your files at <code>www.example.com<b>/path/</b></code></p>
								</div>
							</span>
						</td>
					</tr>
					<tr>
						<th></th>
						<td class='url-dest-option'>
							<span>
								<input type="radio" name="destination_url_type" value="offline" <?php Util::checked_if( $this->destination_url_type === 'offline' ); ?>>
							</span>
							<span>
								<p><label>Save for offline use</label></p>
								<p class='help-block'>
									Convert all URLs for your WordPress site so that you can browse the site locally on your own computer without hosting it on a web server.
								</p>
							</span>
						</td>
					</tr>
					<tr>
						<th>
							<label for='deliveryMethod'>Delivery Method</label></th>
						<td>
							<select name='delivery_method' id='deliveryMethod'>
								<option value='zip' <?php Util::selected_if( $this->delivery_method === 'zip' ) ?>>ZIP Archive</option>
								<option value='local' <?php Util::selected_if( $this->delivery_method === 'local' ) ?>>Local Directory</option>
							</select>
						</td>
					</tr>
					<tr class='delivery-method zip'>
						<th></th>
						<td>
							<p>Saving your static files to a ZIP archive is Simpler Static's default delivery method. After generating your static files you will be provided with a link to download the ZIP archive.</p>
						</td>
					</tr>
					<tr class='delivery-method local'>
						<th></th>
						<td>
							<p>Saving your static files to a local directory is useful if you want to serve your static files from the same server as your WordPress installation. WordPress can live on a subdomain (e.g. wordpress.example.com) while your static files are served from your primary domain (e.g. www.example.com).</p>
						</td>
					</tr>
					<tr class='delivery-method local'>
						<th>
							<label for='local_dir'>Local Directory</label>
						</th>
						<td>
							<?php $example_local_dir = trailingslashit( untrailingslashit( get_home_path() ) . '_static' ); ?>
							<input aria-describedby='localDirHelpBlock' type='text' id='localDir' name='local_dir' value='<?php echo esc_attr( $this->local_dir ); ?>' class='widefat' />
							<div id='localDirHelpBlock' class='help-block'>
								<p>This is the directory where your static files will be saved. The directory must exist and be writeable by the webserver.</p>
								<p><?php echo sprintf( "Example: <code>%s</code>", $example_local_dir ); ?></p>
							</div>
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
		</div>

		<div id='include-exclude' class='tab-pane'>
			<table class='form-table'>
				<tbody>
					<tr>
						<th>
							<label for='additionalUrls'>Additional URLs</label>
						</th>
						<td>
							<textarea aria-describedby='additionalUrlsHelpBlock' class='widefat' name='additional_urls' id='additionalUrls' rows='5' cols='10'><?php echo esc_textarea( $this->additional_urls ); ?></textarea>
							<div id='additionalUrlsHelpBlock' class='help-block'>
								<p><?php echo sprintf( "Simpler Static will create a static copy of any page it can find a link to, starting at %s. If you want to create static copies of pages or files that <em>aren't</em> linked to, add the URLs here (one per line).", trailingslashit( Util::origin_url() ) ); ?></p>
								<p><?php echo sprintf( "Examples: <code>%s</code> or <code>%s</code>",
								Util::origin_url() . "/hidden-page/",
								Util::origin_url() . "/images/secret.jpg" ); ?></p>
							</div>
						</td>
					</tr>
					<tr>
						<th>
							<label for='additionalFiles'>Additional Files and Directories</label>
						</th>
						<td>
							<textarea aria-describedby='additionalFilesHelpBlock' class='widefat' name='additional_files' id='additionalFiles' rows='5' cols='10'><?php echo esc_textarea( $this->additional_files ); ?></textarea>
							<div id='additionalFilesHelpBlock' class='help-block'>
								<p>Sometimes you may want to include additional files (such as files referenced via AJAX) or directories. Add the paths to those files or directories here (one per line).</p>
								<p><?php echo sprintf( "Examples: <code>%s</code> or <code>%s</code>",
								get_home_path() .  "additional-directory/",
								trailingslashit( WP_CONTENT_DIR ) .  "fancy.pdf" ); ?></p>
							</div>
						</td>
					</tr>
					<tr>
						<th>
							<label for='excludeUrls'>URLs to Exclude</label>
						</th>
						<td>
							<?php
								$urls_to_exclude = $this->urls_to_exclude;
								array_unshift( $urls_to_exclude, array(
									'url' => '',
									'do_not_save' => '1',
									'do_not_follow' => '1'
								) );
							?>
							<div id="excludableUrlRows">
							<?php foreach ( $urls_to_exclude as $index => $url_to_exclude ) : ?>
								<div class='excludable-url-row' <?php if ( $index === 0 ) echo "id='excludableUrlRowTemplate'"; ?>>
									<input type='text' name='excludable[<?php echo $index; ?>][url]' value='<?php echo esc_attr( $url_to_exclude['url'] ); ?>' size='40' />

									<label>
										<input name='excludable[<?php echo $index; ?>][do_not_save]' value='0' type='hidden' />
										<input name='excludable[<?php echo $index; ?>][do_not_save]' value='1' type='checkbox' <?php Util::checked_if( $url_to_exclude['do_not_save'] === '1' ); ?> />
										Do not save
									</label>

									<label>
										<input name='excludable[<?php echo $index; ?>][do_not_follow]' value='0' type='hidden' />
										<input name='excludable[<?php echo $index; ?>][do_not_follow]' value='1' type='checkbox' <?php Util::checked_if( $url_to_exclude['do_not_follow'] === '1' ); ?> />
										Do not follow
									</label>

									<input class='button remove-excludable-url-row' type='button' name='remove' value='Remove' />
								</div>
							<?php endforeach; ?>
							</div>

							<div>
								<input class='button' type='button' name='add_url_to_exclude' id="AddUrlToExclude" value='Add URL to Exclude' />
							</div>

							<div id='excludeUrlsHelpBlock' class='help-block'>
									<p>In this section you can specify URLs, or parts of a URL, to exclude from Simpler Static's processing. You may also use regex to specify a pattern to match.</p>
									<p><b>Do not save</b>: do not save a static copy of the page/file &mdash; <b>Do not follow</b>: do not use this page to find additional URLs for processing</p>
									<p><?php echo sprintf( "Example: <code>%s</code> would exclude <code>%s</code> and other files containing <code>%s</code> from processing",
									".jpg",
									Util::origin_url() . "/wp-content/uploads/image.jpg",
									".jpg" ); ?></p>
							</div>
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
		</div>

		<div id='advanced' class='tab-pane'>
			<h2 class="title">Temporary Files</h2>
			<p>Your static files are temporarily saved to a directory before being copied to their destination or creating a ZIP.</p>
			<table class='form-table'>
				<tbody>
					<tr>
						<th>
							<label for='tempFilesDir'>Temporary Files Directory</label>
						</th>
						<td>
							<?php $example_temp_files_dir = trailingslashit( plugin_dir_path( dirname( __FILE__ ) ) . 'static-files' ); ?>
							<input aria-describedby='tempFilesDirHelpBlock' type='text' id='tempFilesDir' name='temp_files_dir' value='<?php echo esc_attr( $this->temp_files_dir ) ?>' class='widefat' />
							<div id='tempFilesDirHelpBlock' class='help-block'>
								<p>Specify the directory to save your temporary files. This directory must exist and be writeable.</p>
								<p><?php echo sprintf( "Default: <code>%s</code>", $example_temp_files_dir ); ?></p>
							</div>
						</td>
					</tr>
					<tr>
						<th>
							<label>Delete Temporary Files</label>
						</th>
						<td>
							<label>
								<input name='delete_temp_files' value='0' type='hidden' />
								<input aria-describedby='deleteTempFilesHelpBlock' name='delete_temp_files' id='deleteTempFiles' value='1' type='checkbox' <?php Util::checked_if( $this->delete_temp_files === '1' ); ?> />
								Delete temporary files at the end of the job
							</label>
						</td>
					</tr>
				</tbody>
			</table>

			<h2 class="title">HTTP Basic Authentication</h2>
			<p>If you've secured WordPress with HTTP Basic Auth you can specify the username and password to use below.</p>
			<?php if ( $this->http_basic_auth_digest != null ) : ?>
			<table class='form-table' id='basicAuthSet'>
				<tbody>
					<tr>
						<th>
							<label>Basic Auth</label>
						</th>
						<td>
							<p id='basicAuthCredentialsSaved'>Your basic auth credentials have been saved. To disable basic auth or set a new username/password, <a href='#'>click here</a>.</p>
						</td>
					</tr>
				</tbody>
			</table>
			<?php endif; ?>
			<table class='form-table <?php if ( $this->http_basic_auth_digest != null ) echo 'hide' ?>' id='basicAuthUserPass'>
				<tbody>
					<tr>
						<th>
							<label for='basicAuthUsername'>Basic Auth Username</label>
						</th>
						<td>
							<input type='text' id='basicAuthUsername' name='basic_auth_username' value='' <?php if ( $this->http_basic_auth_digest != null ) echo 'disabled' ?> />
						</td>
					</tr>
					<tr>
						<th>
							<label for='basicAuthPassword'>Basic Auth Password</label>
						</th>
						<td>
							<input type='text' id='basicAuthPassword' name='basic_auth_password' value='' <?php if ( $this->http_basic_auth_digest != null ) echo 'disabled' ?> />
						</td>
					</tr>
				</tbody>
			</table>

			<table class='form-table'>
				<tbody>
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
		</div>
	</form>

	<form id='resetForm' method='post' action=''>

		<?php wp_nonce_field( 'simplerstatic_reset' ) ?>
		<input type='hidden' name='_reset' value='1' />

		<div id='reset-settings' class='tab-pane'>
			<table class='form-table'>
				<tbody>
					<tr>
						<th>
							<label for='resetSettings'>Reset Plugin Settings</label>
						</th>
						<td>
							<input aria-describedby='resetSettingsHelpBlock' id='resetSettings' class='button button-destroy' type='submit' name='reset_settings' value='Reset Plugin Settings' />
							<p id='resetSettingsHelpBlock' class='help-block'>
								This will reset Simpler Static's settings back to their defaults.
							</p>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

	</form>

</div>
