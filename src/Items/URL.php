<?php
/**
 * URL Object
 *
 * @package Slab
 * @subpackage Concatenator
 * @author Eric
 */
namespace Slab\Concatenator\Items;

class URL extends \Slab\Concatenator\Items\Base
{
    /**
     * @throws \Exception
     */
    public function fetchData()
    {
        $client = new \GuzzleHttp\Client();
        $result = $client->request('GET', $this->value, []);

        if ($result->getStatusCode() >= 300)
        {
            throw new \Exception("Non-200 response from URL: " . $this->value);
        }

        $this->output = $result->getBody();
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function isValid()
    {
        if (!class_exists('\GuzzleHttp\Client'))
        {
            throw new \Exception("To use a URL concatenator item, please include the guzzlehttp/guzzle library.");
        }

        return filter_var($this->value, FILTER_VALIDATE_URL);
    }
}