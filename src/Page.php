<?php
namespace SimplerStatic;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Simpler Static Page class, for tracking the status of pages / static files
 */
class Page extends Model {
    // TODO: not able to set these props without issues
    // /**
    // * @var string
    // */
    // private $url;
    // /**
    // * @var int
    // */
    // private $http_status_code;

    /*
        Note: seems a conflict between the ORM.
        for now, set to private vars and ignore warnings
        when working with instance vars
    */
    /**
     * @var string
     */
    private $found_on_id;
    /**
     * @var string
     */
    private $content_hash;
    /**
     * @var string
     */
    private $last_checked_at;
    /**
     * @var string
     */
    private $last_modified_at;
    /**
     * @var string
     */
    public $content_type;
    /**
     * @var string
     */
    public $redirect_url;
    /**
     * @var string|null
     */
    public $file_path;
    /**
     * @var string
     */
    private $status_message;
    /**
     * @var string
     */
    private $error_message;

    /**
     * @var int[]
     */
    public static $processable_status_codes = [
        200,
        301,
        302,
        303,
        307,
        308,
    ];

    /** @const */
    protected static $table_name = 'pages';

    /**
     * @var mixed[]
     */
    protected static $columns = [
        'id'                  => 'BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT',
        'found_on_id'         => 'BIGINT(20) UNSIGNED NULL',
        'url'                 => 'VARCHAR(255) NOT NULL',
        'redirect_url'        => 'TEXT NULL',
        'file_path'           => 'VARCHAR(255) NULL',
        'http_status_code'    => 'SMALLINT(20) NULL',
        'content_type'        => 'VARCHAR(255) NULL',
        'content_hash'        => 'BINARY(20) NULL',
        'error_message'       => 'VARCHAR(255) NULL',
        'status_message'      => 'VARCHAR(255) NULL',
        'last_checked_at'     => "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
        'last_modified_at'    => "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
        'last_transferred_at' => "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
        'created_at'          => "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
        'updated_at'          => "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
    ];

    /**
     * @var string[]
     */
    protected static $indexes = [
        'PRIMARY KEY  (id)',
        'KEY url (url)',
        'KEY last_checked_at (last_checked_at)',
        'KEY last_modified_at (last_modified_at)',
        'KEY last_transferred_at (last_transferred_at)',
    ];

    /** @const */
    protected static $primary_key = 'id';

    /**
     * Get the number of pages for each group of status codes, e.g. 1xx, 2xx, 3xx
     *
     * @return array<int, int> Array of status code to number of pages.
     */
    public static function get_http_status_codes_summary() {
        global $wpdb;

        $query = 'SELECT LEFT(http_status_code, 1) AS code, COUNT(*) AS count';
        $query .= ' FROM ' . self::table_name();
        $query .= ' GROUP BY code';

        $rows = array_column( $wpdb->get_results( $query, \ARRAY_A ), 'count', 'code' );

        $http_status_codes = array_fill( 1, 8, 0 );

        array_walk(
            $http_status_codes,
            function ( $count, $code ) use ( $rows ) {
                return $rows[ (string) $code ] ?? $count;
            }
        );

        return $http_status_codes;
    }

    /**
     * Return the static page that this page belongs to (if any)
     *
     * @return Query|null The parent Page
     */
    public function parent_static_page() {
        return self::query()->find_by( 'id', $this->found_on_id );
    }

    /**
     * Check if the hash for the content matches the prior hash for the page
     *
     * @param  string  $sha1 The content of the page/file
     * @return bool          Is the hash a match?
     */
    public function is_content_identical( $sha1 ) {
        return $sha1 === $this->content_hash;
    }

    /**
     * Set the hash for the content and update the last_modified_at value
     */
    public function set_content_hash( string $sha1 ) : void {
        $this->content_hash = $sha1;
        $this->last_modified_at = (string) Util::formatted_datetime();
    }

    /**
     * Sets or appends an error message
     *
     * An error indicates that something bad happened when fetching the page, or
     * saving the page, or during some other activity related to the page.
     */
    public function set_error_message( string $message ) : void {
        if ( $this->error_message ) {
            $this->error_message = $this->error_message . '; ' . $message;
        } else {
            $this->error_message = $message;
        }
    }

    /**
     * Sets or appends a status message
     *
     * A status message is used to indicate things that happened to the page
     * that weren't errors, such as not following links or not saving the page.
     */
    public function set_status_message( string $message ) : void {
        if ( $this->status_message ) {
            $this->status_message = $this->status_message . '; ' . $message;
        } else {
            $this->status_message = $message;
        }
    }

    public function is_type( string $content_type ) : bool {
        return stripos( $this->content_type, $content_type ) !== false;
    }
}
