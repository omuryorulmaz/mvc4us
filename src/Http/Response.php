<?php
namespace Mvc4us\Http;

class Response extends \Symfony\Component\HttpFoundation\Response
{

    /**
     *
     * @var array
     */
    private $map = [];

    /**
     *
     * @var \Exception
     */
    private $exception;

    /**
     *
     * {@inheritdoc}
     *
     */
    public function __construct($content = '', $status = 200, $headers = array())
    {
        parent::__construct($content, $status, $headers);
    }

    public function setContent($content, $nl2br = false)
    {
        return parent::setContent($content = $nl2br ? nl2br($content) : $content);
    }

    /**
     * Getter for map
     *
     * @param string $key
     * @return array|mixed
     */
    public function getMap($key = null)
    {
        if (! isset($key))
            return $this->map;

        return $this->map[$key];
    }

    /**
     * Setter for map
     *
     * @param string $key
     * @param mixed $value
     */
    public function putMap($key, $value)
    {
        $this->map[$key] = $value;
    }

    public function setMap($map)
    {
        $this->map = $map;
    }

    public function getException()
    {
        return $this->exception;
    }

    public function setException($exception)
    {
        $this->exception = $exception;
    }
}
