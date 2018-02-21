<?php
/**
 * Slab Concatenator Manager
 *
 * @package Slab
 * @subpackage Concatenator
 * @author Eric
 */
namespace Slab\Concatenator;

class Manager
{
    /**
     * @var \Psr\SimpleCache\CacheInterface
     */
    private $cache;

    /**
     * Cache key
     *
     * @var string
     */
    private $cacheKey;

    /**
     * How long to cache the data for
     *
     * @var integer
     */
    private $cacheTime = 86400;

    /**
     * @var bool
     */
    private $cacheRefresh = false;

    /**
     * @var \Slab\Concatenator\Items\ItemInterface[]
     */
    private $objectList = [];

    /**
     * Actual output
     *
     * @var string
     */
    private $output = '';

    /**
     * @var string
     */
    private $source = 'undefined';

    /**
     * @var integer
     */
    private $lastModifiedTime;

    /**
     * @var array
     */
    private $fileSearchDirectories = [];

    /**
     * Set cache manager
     *
     * @param \Psr\SimpleCache\CacheInterface $cache
     * @param $key
     * @param $ttl
     * @return $this
     */
    public function setCache(\Psr\SimpleCache\CacheInterface $cache, $key, $ttl = 86400)
    {
        $this->cache = $cache;
        $this->cacheKey = $key;
        $this->cacheTime = $ttl;

        if (empty($this->cacheKey))
        {
            $this->cacheKey = 'concatenate-'.md5(serialize($this));
        }

        return $this;
    }

    /**
     * @param array $searchDirectories
     * @return $this
     */
    public function setFileSearchDirectories(array $searchDirectories)
    {
        $this->fileSearchDirectories = $searchDirectories;

        return $this;
    }

    /**
     * @param $cacheRefresh
     * @return $this
     */
    public function setCacheRefresh($cacheRefresh)
    {
        $this->cacheRefresh = $cacheRefresh;

        return $this;
    }

    /**
     * Get calculated source
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set object list
     *
     * @param $objectList
     * @return $this
     */
    public function setObjectList($objectList)
    {
        $this->objectList = $objectList;

        return $this;
    }

    /**
     * Add an object to the list
     *
     * @param $objectValue
     * @param $filters
     * @return $this
     */
    public function addObject($objectValue, $filters)
    {
        if (empty($objectValue)) {
            throw new \Exception("Failed to add empty object value!");
        }

        $object = null;
        if (strpos($objectValue, 'http') !== false) {
            /**
             * @var \Slab\Concatenator\Items\Base $object
             */
            $object = new Items\URL();
        } else {
            /**
             * @var \Slab\Concatenator\Items\Base $object
             */
            $object = new Items\File($this->fileSearchDirectories);
        }

        $object->initialize($objectValue, $filters);

        $this->objectList[] = $object;

        return $this;
    }

    /**
     * Conctenation output
     *
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Concatenate the object list
     *
     * @param $objects
     */
    public function concatenateObjectList()
    {
        //Short circuit if cache is disabled
        if (empty($this->cache) || empty($this->cacheKey) || empty($this->cacheTime)) {
            $this->source = 'raw';
            $this->output = $this->concatenateObjects();
            return;
        }

        //See if you can grab it from cache, unless cache refresh param is set
        if (!$this->cacheRefresh &&
            ($this->output = $this->cache->get($this->cacheKey)) !== null
        ) {
            $this->getActualLastModifiedTime();

            $lastCacheModifiedTime = $this->extractLastModifiedTime($this->output);

            if ($lastCacheModifiedTime == $this->lastModifiedTime) {
                //The cached timestamp is the same as what's in the cached copy so we can return it,
                //otherwise we can skip this and re-fetch since the source files are newer

                $this->source = 'cache';
                return;
            }
        }

        //Short circuit failed, get from cache failed, grab it and cache it
        $this->output = $this->concatenateObjects();

        $this->cache->set($this->cacheKey, $this->output, $this->cacheTime);

        $this->source = 'fetched';
    }

    /**
     * Gets actual last modified time of files to see if we need to bust cache
     */
    private function getActualLastModifiedTime()
    {
        if (empty($this->objectList)) return;

        foreach ($this->objectList as $object) {
            $modifiedTime = $object->getLastModifiedTimestamp();

            if (!empty($modifiedTime) && $modifiedTime > $this->lastModifiedTime) {
                $this->lastModifiedTime = $modifiedTime;
            }
        }
    }

    /**
     * Concatenate the actual files and set output
     */
    private function concatenateObjects()
    {
        $output = "";

        if (empty($this->objectList)) return $output;

        foreach ($this->objectList as $object) {
            if ($object->isValid()) {
                $output .= $object->getData();

                $modifiedTime = $object->getLastModifiedTimestamp();

                if (!empty($modifiedTime) && $modifiedTime > $this->lastModifiedTime) {
                    $this->lastModifiedTime = $modifiedTime;
                }
            } else {
                $output .= $this->handleInvalidFile($object->getValue());
            }
        }

        if (!empty($this->lastModifiedTime)) {
            $output = $this->formatLastModifiedTime() . $output;
        }

        return $output;
    }

    /**
     * Format last modified time and plug it into the output
     *
     * @return string
     */
    private function formatLastModifiedTime()
    {
        return '/* modified:' . date('c', $this->lastModifiedTime) . " */\n";
    }

    /**
     * Retrieve the last modified date from cached data
     *
     * @param $cacheData
     * @return int
     */
    private function extractLastModifiedTime($cacheData)
    {
        $matches = array();
        preg_match('#modified:([a-z0-9:-]*)#i', $cacheData, $matches);

        if (!empty($matches[1])) {
            return strtotime($matches[1]);
        }

        return 0;
    }

    /**
     * Return something for an invalid file, if you want
     *
     * @param string $filename
     */
    private function handleInvalidFile($filename)
    {
        return "/* Invalid file " . basename($filename) . " */";
    }

}