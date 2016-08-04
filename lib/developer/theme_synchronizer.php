<?php
/**
 * Extend developer addon with theme path
 *
 * @author Daniel Weitenauer
 */

class theme_synchronizer extends rex_developer_synchronizer_default
{
    /**
     * @param string $dirname
     * @param string $table
     * @param array $files
     * @param array $metadata
     */
    public function __construct($dirname, $table, array $files, array $metadata = array())
    {
        parent::__construct($dirname, $table, $files, $metadata);

        // Overload target path
        $this->baseDir = rex_path::base($dirname.'/');
    }
}
