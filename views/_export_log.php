<?php
namespace SimplerStatic;

if ( is_array( $this->static_pages ) && count( $this->static_pages ) ) : ?>

	<?php $num_errors = count( array_filter( $this->static_pages, function($p) { return $p->error_message != false; } ) ); ?>

	<div class='tablenav top'>
		<?php include '_pagination.php'; ?>
	</div>

	<table class='widefat striped'>
		<thead>
			<tr>
				<th>Code</th>
				<th>URL</th>
				<th>Notes</th>
				<?php if ( $num_errors > 0 ) : ?>
				<th><?php echo sprintf( "Errors (%d)", $num_errors ); ?></th>
				<?php endif; ?>
			</tr>
		</thead>
		<tbody>

		<?php foreach ( $this->static_pages as $static_page ) : ?>
            <!-- clear skipped URLs from export log -->
            <?php // if ( $static_page->status_message !== 'Additional Dir; Do not save or follow' ) : ?>

                <tr>
                    <?php $processable = in_array( $static_page->http_status_code, Page::$processable_status_codes ); ?>
                    <td class='status-code <?php if ( ! $processable ) { echo 'unprocessable'; } ?>'>
                        <?php echo (int) $static_page->http_status_code === 666 ? 'skip' : $static_page->http_status_code; ?>
                    </td>
                    <td class='url'><a href='<?php echo $static_page->url; ?>'><?php echo $static_page->url; ?></a></td>
                    <td class='status-message'>
                        <?php
                            $msg = '';
                            $parent_static_page = $static_page->parent_static_page();
                            if ( $parent_static_page ) {
                                $display_url = Util::get_path_from_local_url( $parent_static_page->url );
                                $msg .= "<a href='" . $parent_static_page->url . "'>" .sprintf( 'Found on %s', $display_url ). "</a>";
                            }
                            if ( $msg !== '' && $static_page->status_message ) {
                                $msg .= '; ';
                            }
                            $msg .= $static_page->status_message;
                            echo $msg;
                        ?>
                    </td>
                    <?php if ( $num_errors > 0 ) : ?>
                    <td class='error-message'>
                        <?php echo $static_page->error_message; ?>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php // endif; ?>
		<?php endforeach; ?>
		</tbody>
	</table>

	<div class='tablenav bottom'>
		<?php include '_pagination.php'; ?>
	</div>

<?php endif ?>
