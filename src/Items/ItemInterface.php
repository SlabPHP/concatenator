<?php
/**
 * Interface for a concatenator object
 *
 * @package Slab
 * @subpackage Concatenator
 * @author Eric
 */
namespace Slab\Concatenator\Items;

interface ItemInterface
{
    /**
     * Constructor must have the value and filters
     *
     * @param $value
     * @param array $filters
     */
    public function initialize($value, $filters = []);

    /**
     * Return the processed data
     *
     * @return mixed
     */
    public function getData();

    /**
     * Return the actual value
     *
     * @return mixed
     */
    public function getValue();

    /**
     * Return the last modified timestamp
     *
     * @return mixed
     */
    public function getLastModifiedTimestamp();

    /**
     * Is everything ok to start processing this object
     *
     * @return mixed
     */
    public function isValid();
}