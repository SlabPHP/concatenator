# concatenator

SlabPHP File and Resource Concatenator, used in conjunction with the SlabPHP media concatenator controller.

## Installation

    composer require slabphp/concatenator

## Usage

Create a manager:

    $manager = new \Slab\Concatenator\Manager();
    
Set it up for where to look for files:    

    $manager
        ->setFileSearchDirectories([__DIR__ . '/resources'])
        ->addObject('css/test.css', [])
        ->addObject('css/something.css', [])
        ->concatenateObjectList();

Then just concatenate it:

    $output = $manager->getOutput();
