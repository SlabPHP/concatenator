<?php
/**
 * Base filter abstract class
 *
 * @package Slab
 * @subpackage Concatenator
 * @author Eric
 */
namespace Slab\Concatenator\Filters;

abstract class Base
    implements \Slab\Concatenator\Filters\FilterInterface
{
    /**
     * @var string
     */
    protected $output;

    /**
     * Execute filtering
     *
     * @return $this
     */
    abstract public function filter($input);

    /**
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }
}