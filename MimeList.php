<?php

/**
 * Class to guess file mime type from it's extension. Getting mime data from
 * apache.org and basing on it guesses mime type. Can cache mime data to PHP-
 * file.
 */
class MimeList
{
    const APACHE_MIMETYPES_URL = 'http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types';
    const USE_CACHE = true;
    const NOT_USE_CACHE = false;

    /**
     * File with absolute path to cache mime data.
     *
     * @var string
     */
    public $dataFile;

    /**
     * Defines whether or not to cache mime types data or keep it in memory.
     *
     * @var bool
     */
    public $cache;

    public function __construct($cache = static::USE_CACHE, $dataFile = "mimetypes.php")
    {
        $this->cache = $cache;
        $this->dataFile = $dataFile;
    }

    /**
     * Tries to guess mime type from given extension.
     *
     * @param string $extension file extension to match
     *
     * @return string|null result mime type or null if that extension is not
     *                     defined
     */
    public function guess($extension)
    {
        if ($this->cache) {
            $this->writeMimes($this->generateMimes());
        }

        $data = $this->cache ? require $this->dataFile : $this->generateMimes();

        return isset($data[$extension]) ? $data[$extension] : null;
    }

    protected function generateMimes()
    {
        $result = array();

        foreach (explode("\n", file_get_contents(static::APACHE_MIMETYPES_URL)) as $line) {
            if (
                isset($line[0])
                && $line[0] !== '#'
                && preg_match_all('#([^\s]+)#', $line, $out)
                && isset($out[1])
                && ($count = count($out[1])) > 1
            ) {
                for ($i = 1; $i < $count; $i++) {
                    $result[$out[1][$i]] = $out[1][0];
                }
            }
        }

        return $result;
    }

    protected function writeMimes($mimeList)
    {
        if (file_exists($this->dataFile)) {
            return;
        }

        ksort($mimeList);

        $fdata = fopen($this->dataFile, 'w');
        fwrite($fdata, "<?php\nreturn array(\n");

        foreach ($mimeList as $ext => $type) {
            fwrite($fdata, "    '".$ext."' => '".$type."',\n");
        }

        fwrite($fdata, "\n);");
        fclose($fdata);
    }
}
