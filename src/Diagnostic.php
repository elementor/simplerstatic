<?php
namespace SimplerStatic;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Simpler Static Diagnostic class
 *
 * Checks to ensure that the user's server and WP installation meet a set of
 * minimum requirements.
 */
class Diagnostic {

    /**
     * @var mixed[]
     */
    protected static $min_version = [
        'php' => '7.3.0',
        'curl' => '7.15.0',
    ];

    /**
     * Assoc. array of categories, and then functions to check
     *
     * @var mixed[]
     */
    protected $description = [
        'URLs' => [],
        'Filesystem' => [
            [ 'function' => 'is_temp_files_dir_readable' ],
            [ 'function' => 'is_temp_files_dir_writeable' ],
        ],
        'WordPress' => [
            [ 'function' => 'is_permalink_structure_set' ],
            [ 'function' => 'can_wp_make_requests_to_itself' ],
        ],
        'MySQL' => [
            [ 'function' => 'user_can_delete' ],
            [ 'function' => 'user_can_insert' ],
            [ 'function' => 'user_can_select' ],
            [ 'function' => 'user_can_create' ],
            [ 'function' => 'user_can_alter' ],
            [ 'function' => 'user_can_drop' ],
        ],
        'PHP' => [
            [ 'function' => 'php_version' ],
            [ 'function' => 'has_curl' ],
        ],
    ];

    /**
     * Assoc. array of results of the diagnostic check
     *
     * @var mixed[]
     */
    public $results = [];

    /**
     * An instance of the options structure containing all options for this plugin
     *
     * @var Options
     */
    protected $options = null;

    public function __construct() {
        $this->options = Options::instance();

        if ( $this->options->get( 'destination_url_type' ) == 'absolute' ) {
            $this->description['URLs'][] = [
                'function' => 'is_destination_host_a_valid_url',
            ];
        }

        if ( $this->options->get( 'delivery_method' ) == 'local' ) {
            $this->description['Filesystem'][] = [
                'function' => 'is_local_dir_writeable',
            ];
        }

        $additional_urls = Util::string_to_array( $this->options->get( 'additional_urls' ) );
        foreach ( $additional_urls as $url ) {
            $this->description['URLs'][] = [
                'function' => 'is_additional_url_valid',
                'param' => $url,
            ];
        }

        $additional_files = Util::string_to_array( $this->options->get( 'additional_files' ) );
        foreach ( $additional_files as $file ) {
            $this->description['Filesystem'][] = [
                'function' => 'is_additional_file_valid',
                'param' => $file,
            ];
        }

        foreach ( $this->description as $title => $tests ) {
            $this->results[ $title ] = [];
            foreach ( $tests as $test ) {
                $param = isset( $test['param'] ) ? $test['param'] : null;
                $result = $this->{$test['function']}( $param );

                if ( ! isset( $result['message'] ) ) {
                    $result['message'] = $result['test'] ? __( 'OK', 'simplerstatic' ) : __( 'FAIL', 'simplerstatic' );
                }

                $this->results[ $title ][] = $result;
            }
        }
    }

    /**
     * @return mixed[]
     */
    public function is_destination_host_a_valid_url() {
        $destination_scheme = $this->options->get( 'destination_scheme' );
        $destination_host = $this->options->get( 'destination_host' );
        $destination_url = $destination_scheme . $destination_host;
        $label = sprintf( __( 'Checking if Destination URL <code>%s</code> is valid', 'simplerstatic' ), $destination_url );
        return [
            'label' => $label,
            'test' => filter_var( $destination_url, FILTER_VALIDATE_URL ) !== false,
        ];
    }

    /**
     * @return mixed[]
     */
    public function is_additional_url_valid( string $url ) {
        $label = sprintf( 'Checking if Additional URL <code>%s</code> is valid', $url );
        if ( filter_var( $url, FILTER_VALIDATE_URL ) === false ) {
            $test = false;
            $message = 'Not a valid URL';
        } elseif ( ! Util::is_local_url( $url ) ) {
            $test = false;
            $message = 'Not a local URL';
        } else {
            $test = true;
            $message = null;
        }

        return [
            'label' => $label,
            'test' => $test,
            'message' => $message,
        ];
    }

    /**
     * @return mixed[]
     */
    public function is_additional_file_valid( string $file ) {
        $label = sprintf( 'Checking if Additional File/Dir <code>%s</code> is valid', $file );
        if ( stripos( $file, get_home_path() ) !== 0 && stripos( $file, WP_PLUGIN_DIR ) !== 0 && stripos( $file, WP_CONTENT_DIR ) !== 0 ) {
            $test = false;
            $message = 'Not a valid path';
        } elseif ( ! is_readable( $file ) ) {
            $test = false;
            $message = 'Not readable';
        } else {
            $test = true;
            $message = null;
        }

        return [
            'label' => $label,
            'test' => $test,
            'message' => $message,
        ];
    }

    /**
     * @return mixed[]
     */
    public function is_permalink_structure_set() {
        $label = 'Checking if WordPress permalink structure is set';
        return [
            'label' => $label,
            'test' => strlen( get_option( 'permalink_structure' ) ) !== 0,
        ];
    }

    /**
     * @return mixed[]
     */
    public function can_wp_make_requests_to_itself() {
        $ip_address = getHostByName( (string) getHostName() );
        $label = sprintf( 'Checking if WordPress can make requests to itself from <code>%s</code>', $ip_address );

        $url = Util::origin_url();
        $response = Url_Fetcher::remote_get( $url );

        if ( is_wp_error( $response ) ) {
            $test = false;
            $message = null;
        } else {
            $code = $response['response']['code'];
            if ( $code == 200 ) {
                $test = true;
                $message = $code;
            } elseif ( in_array( $code, Page::$processable_status_codes ) ) {
                $test = false;
                $message = sprintf( 'Received a %s response. This might indicate a problem.', $code );
            } else {
                $test = false;
                $message = sprintf( 'Received a %s response.', $code );

            }
        }

        return [
            'label' => $label,
            'test' => $test,
            'message' => $message,
        ];
    }

    /**
     * @return mixed[]
     */
    public function is_temp_files_dir_readable() {
        $temp_files_dir = $this->options->get( 'temp_files_dir' );
        $label = sprintf( __( 'Checking if web server can read from Temp Files Directory: <code>%s</code>', 'simplerstatic' ), $temp_files_dir );
        return [
            'label' => $label,
            'test' => is_readable( $temp_files_dir ),
        ];
    }

    /**
     * @return mixed[]
     */
    public function is_temp_files_dir_writeable() {
        $temp_files_dir = $this->options->get( 'temp_files_dir' );
        $label = sprintf( __( 'Checking if web server can write to Temp Files Directory: <code>%s</code>', 'simplerstatic' ), $temp_files_dir );
        return [
            'label' => $label,
            'test' => is_writable( $temp_files_dir ),
        ];
    }

    /**
     * @return mixed[]
     */
    public function is_local_dir_writeable() {
        $local_dir = $this->options->get( 'local_dir' );
        $label = sprintf( __( 'Checking if web server can write to Local Directory: <code>%s</code>', 'simplerstatic' ), $local_dir );
        return [
            'label' => $label,
            'test' => is_writable( $local_dir ),
        ];
    }

    /**
     * @return mixed[]
     */
    public function user_can_delete() {
        $label = __( 'Checking if MySQL user has <code>DELETE</code> privilege', 'simplerstatic' );
        return [
            'label' => $label,
            'test' => Sql_Permissions::instance()->can( 'delete' ),
        ];
    }

    /**
     * @return mixed[]
     */
    public function user_can_insert() {
        $label = __( 'Checking if MySQL user has <code>INSERT</code> privilege', 'simplerstatic' );
        return [
            'label' => $label,
            'test' => Sql_Permissions::instance()->can( 'insert' ),
        ];
    }

    /**
     * @return mixed[]
     */
    public function user_can_select() {
        $label = __( 'Checking if MySQL user has <code>SELECT</code> privilege', 'simplerstatic' );
        return [
            'label' => $label,
            'test' => Sql_Permissions::instance()->can( 'select' ),
        ];
    }

    /**
     * @return mixed[]
     */
    public function user_can_create() {
        $label = __( 'Checking if MySQL user has <code>CREATE</code> privilege', 'simplerstatic' );
        return [
            'label' => $label,
            'test' => Sql_Permissions::instance()->can( 'create' ),
        ];
    }

    /**
     * @return mixed[]
     */
    public function user_can_alter() {
        $label = __( 'Checking if MySQL user has <code>ALTER</code> privilege', 'simplerstatic' );
        return [
            'label' => $label,
            'test' => Sql_Permissions::instance()->can( 'alter' ),
        ];
    }

    /**
     * @return mixed[]
     */
    public function user_can_drop() {
        $label = __( 'Checking if MySQL user has <code>DROP</code> privilege', 'simplerstatic' );
        return [
            'label' => $label,
            'test' => Sql_Permissions::instance()->can( 'drop' ),
        ];
    }

    /**
     * @return mixed[]
     */
    public function php_version() {
        $label = sprintf( 'Checking if PHP version >= %s', self::$min_version['php'] );
        return [
            'label' => $label,
            'test' => version_compare( (string) phpversion(), self::$min_version['php'], '>=' ),
            'message'  => phpversion(),
        ];
    }

    /**
     * @return mixed[]
     */
    public function has_curl() {
        $label = __( 'Checking for cURL support', 'simplerstatic' );

        if ( function_exists('curl_version') ) {
            $version = curl_version();
            $test = version_compare( $version['version'], self::$min_version['curl'], '>=' );
            $message = $version['version'];
        } else {
            $test = false;
            $message = null;
        }

        return [
            'label' => $label,
            'test' => $test,
            'message'  => $message,
        ];
    }

}
