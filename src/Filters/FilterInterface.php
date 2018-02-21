<?php
/**
 * Configurable filter object interface
 *
 * @package Slab
 * @subpackage Concatenator
 * @author Eric
 */
namespace Slab\Concatenator\Filters;

interface FilterInterface
{
    /**
     * Execute Filtering
     *
     * @param string $input
     * @return $this
     */
    public function filter($input);

    /**
     * Get output
     *
     * @return string
     */
    public function getOutput();
}