<?php
/**
 * JS Collapser Filter
 *
 * @package Slab
 * @subpackage Concatenator
 * @author Eric
 */
namespace Slab\Concatenator\Filters\Collapse;

class JS extends \Slab\Concatenator\Filters\Base
{
    /**
     * Filter input
     *
     * @param string $input
     * @return $this|void
     */
    public function filter($input)
    {
        $collapser = new \SalernoLabs\Collapser\Javascript();
        $collapser
            ->setDeleteComments(true)
            ->setPreserveNewLines(false)
            ->setDebugMode(true);

        $this->output = $collapser->collapse($input);

        return $this;
    }
}