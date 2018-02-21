<?php
/**
 * Base File Object
 *
 * @package Slab
 * @subpackage Concatenator
 * @author Eric
 */
namespace Slab\Concatenator\Items;

class File extends \Slab\Concatenator\Items\Base
{
    /**
     * @var string
     */
    private $actualFilePath;

    /**
     * @var array
     */
    private $fileSearchDirectories = [];

    /**
     * File constructor.
     * @param $fileSearchDirectories
     */
    public function __construct($fileSearchDirectories)
    {
        $this->fileSearchDirectories = $fileSearchDirectories;
    }

    /**
     * Constructor
     *
     * @param $value
     * @param array $filters
     */
    public function initialize($value, $filters = [])
    {
        parent::initialize($value, $filters);

        foreach ($this->fileSearchDirectories as $directory)
        {
            $filename = $directory . DIRECTORY_SEPARATOR . $value;
            if (is_file($filename))
            {
                $this->actualFilePath = $filename;
                return;
            }
        }
    }

    /**
     * Return the value
     */
    public function fetchData()
    {
        $this->output = @file_get_contents($this->actualFilePath);
    }

    /**
     * Return the last modified timestamp
     *
     * @return mixed|void
     */
    public function getLastModifiedTimestamp()
    {
        return @filemtime($this->actualFilePath);
    }

    /**
     * Is Valid
     *
     * @return bool
     */
    public function isValid()
    {
        return !empty($this->actualFilePath) && @is_readable($this->actualFilePath);
    }
}