<?php
/**
 * Concatenator Item Base Abstract Object
 *
 * @package Slab
 * @subpackage Controllers
 * @author Eric
 */
namespace Slab\Concatenator\Items;

abstract class Base implements \Slab\Concatenator\Items\ItemInterface
{
    /**
     * @var string
     */
    protected $value;

    /**
     * @var array
     */
    protected $filters = [];

    /**
     * @var string
     */
    protected $output;

    /**
     * Constructor must have the value and filters
     *
     * @param $value
     * @param array $filters
     */
    public function initialize($value, $filters = [])
    {
        $this->value = $value;

        if (empty($filters)) return;

        //If a comma separated string is passed in, we can explode on it
        if (is_string($filters)) {
            $this->filters = explode(',', $filters);
        } else if (!is_array($filters)) {
            $this->filters = [$this->filters];
        } else {
            $this->filters = $filters;
        }
    }

    /**
     * Get the value
     *
     * @return mixed|string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Return the value
     *
     * @return string
     */
    abstract protected function fetchData();

    /**
     * Get data but supply filters
     *
     * @return mixed|void
     */
    public function getData()
    {
        $this->fetchData();

        $this->preProcessData();

        $this->applyFilters();

        $this->postProcessData();

        return $this->output;
    }

    /**
     * Pre process fetched data
     */
    protected function preProcessData()
    {
        //Remove comments
        $this->output = trim(preg_replace('#\/\*.*?\*\/#si', '', $this->output));
    }

    /**
     * Apply filters
     */
    protected function applyFilters()
    {
        if (empty($this->filters)) return;

        foreach ($this->filters as $filter) {
            $this->output = $this->getFilterOutput($filter, $this->output);
        }
    }

    /**
     * Post process data
     */
    protected function postProcessData()
    {
        $comment = PHP_EOL . '/* ' . (!empty($this->filters) ? implode('|', $this->filters) . '::' : '') . $this->value . ' */' . PHP_EOL;

        $this->output = $comment . $this->output;
    }

    /**
     * Get filter output
     *
     * @param $filter
     * @param $input
     */
    protected function getFilterOutput($filter, $input)
    {
        if ($filter[0] == '\\')
        {
            $filterClass = $filter;
        }
        else
        {
            $filterClass = '\Slab\Concatenator\Filters\\' . $filter;
        }

        if (!class_exists($filterClass))
        {
            throw new \Exception("Invalid filter class specified: " . $filterClass);
        }

        /**
         * @var \Slab\Concatenator\Filters\FilterInterface $filterObject
         */
        $filterObject = new $filterClass();

        $filterObject->filter($input);

        return $filterObject->getOutput();
    }

    /**
     * Get last modified timestamp
     *
     * @return mixed|void
     */
    abstract public function getLastModifiedTimestamp();

    /**
     * Is item valid
     *
     * @return mixed
     */
    abstract public function isValid();
}