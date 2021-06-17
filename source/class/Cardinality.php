<?php
namespace JDLX\DrawioConverter;

class Cardinality  implements \JsonSerializable
{
    protected $raw;

    protected $min;
    protected $max;



    public function __construct($raw)
    {
        $this->raw = $raw;
        $data=explode(',', $raw);

        if(count($data) < 2) {
            $data=explode(';', $raw);
        }
        if(count($data) < 2) {
            $data=explode('/', $raw);
        }

        if(count($data) === 2) {
            $this->min = trim($data[0]);
            $this->max = trim($data[1]);
        }

    }

    public function __toString()
    {
        return $this->min .','. $this->max;
    }

    public function getMax()
    {
        return $this->max;
    }

    public function getMin()
    {
        return $this->min;
    }

    /**
     * @return bool
     */
    public function requireForeignKey()
    {
        if($this->max != 'n') {
            return true;
        }

        return false;
    }

    public function jsonSerialize()
    {
        return [
            'min' => $this->min,
            'max' => $this->max
        ];
    }
}
