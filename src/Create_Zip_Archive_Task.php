<?php
namespace SimplerStatic;

use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class Create_Zip_Archive_Task extends Task {

    /**
     * @var string
     */
    protected static $task_name = 'create_zip_archive';

    public function perform() {
        $download_url = $this->create_zip();
        if ( is_wp_error( $download_url ) ) {
            return $download_url;
        } else {
            $message = 'ZIP archive created: ';
            $message .= " <a href='$download_url'>Click here to download</a>";
            $this->save_status_message( $message );
            return true;
        }
    }

    /**
     * Create a ZIP file using the archive directory
     *
     * @return string|\WP_Error $temporary_zip The path to the archive zip file
     */
    public function create_zip() {
        $archive_dir = $this->options->get_archive_dir();

        $zip_filename = untrailingslashit( $archive_dir ) . '.zip';
        $zip_archive = new ZipArchive();
        $zip_archive->open( $zip_filename, ZipArchive::CREATE );

        Util::debug_log( 'Fetching list of files to include in zip' );
        $files = [];
        $iterator =
            new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(
                    $archive_dir,
                    RecursiveDirectoryIterator::SKIP_DOTS
                )
            );
        Util::debug_log( 'Creating zip archive' );

        foreach ( $iterator as $file_name => $file_object ) {
            if (
                ! $zip_archive->addFile( $file_object, str_replace( $archive_dir, '', $file_name ) )
            ) {
                return new \WP_Error( 'create_zip_failed', 'Unable to create ZIP archive' );
            }
        }

        $download_url = get_admin_url( null, 'admin.php' ) . '?' .
            Plugin::SLUG . '_zip_download=' . basename( $zip_filename );

        return $download_url;
    }

}
