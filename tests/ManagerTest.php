<?php
/**
 * Concatenator Manager Test
 *
 * @package Slab
 * @subpackage Tests
 * @author Eric
 */
namespace Slab\Tests\Concatenator;

class ManagerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test simple concatenation
     */
    public function testSimpleConcatenation()
    {
        $manager = new \Slab\Concatenator\Manager();

        $manager
            ->setFileSearchDirectories([__DIR__ . '/resources'])
            ->addObject('css/test.css', [])
            ->addObject('css/something.css', [])
            ->concatenateObjectList();

        $output = $manager->getOutput();

        $this->assertContains('#thing { font-weight: bold; }', $output);
        $this->assertContains('#thing { font-style: oblique; }', $output);
    }
}