<div class='alignleft'>
	<div class='http-status'>
        HTTP status codes:
        1xx Informational: <b><?php echo $this->http_status_codes['1']; ?></b> |
		2xx Success: <b><?php echo $this->http_status_codes['2']; ?></b> |
		3xx Redirection: <b><?php echo $this->http_status_codes['3']; ?></b> |
		4xx Client Error: <b><?php echo $this->http_status_codes['4']; ?></b> |
		5xx Server Error: <b><?php echo $this->http_status_codes['5']; ?></b> |
		Skipped: <b><?php echo $this->http_status_codes['6']; ?></b>
    </div>
</div>

<div class='tablenav-pages'>
	<span class='displaying-num'><?php echo sprintf( "%d URLs", $this->total_static_pages );?></span>
	<?php
		$args = array(
			'format' => '?page=%#%',
			'total' => $this->total_pages,
			'current' => $this->current_page,
			'prev_text' => '&lsaquo;',
			'next_text' => '&rsaquo;'
		);
		echo paginate_links( $args );
	?>
</div>
