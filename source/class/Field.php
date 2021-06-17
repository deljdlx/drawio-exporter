<?php
namespace JDLX\DrawioConverter;

class Field extends Entity
{
    public const TYPE_AUTO_ID = 'auto_id';

    protected $name;
    protected $type;

    protected $nullAllowed = true;

    protected $autoincrement = false;


    protected $defaultValue;


    public function __construct($graph = null, $xmlNode = null)
    {
        $this->graph = $graph;
        $this->xml = $xmlNode;

        if($this->xml) {
            $dataNode =$this->xml->xPath('parent::object');
            if(count($dataNode)) {
                $this->dataNode = $dataNode[0];
            }
        }
    }

    public function getDefaultValue()
    {
        if($this->dataNode) {
            $attributes = $this->dataNode->attributes();

            if(isset($attributes['DEFAULT'])) {
                return $this->dataNode['DEFAULT'];
            }
        }
        return null;
    }


    /**
     * @return bool
     */
    public function nullAllowed()
    {
        if($this->dataNode) {
            $attributes = $this->dataNode->attributes();

            if(isset($attributes['NOT_NULL'])) {
                return false;
            }
        }
        return $this->nullAllowed;
    }


    public function getType()
    {
        if(!$this->type) {
            if($this->dataNode) {
                if($this->dataNode['type']) {
                    $this->type = (string) $this->dataNode['type'];
                }
                elseif($this->dataNode['TYPE']) {
                    $this->type = (string) $this->dataNode['TYPE'];
                }
            }
        }
        return $this->type;
    }

    /**
     * @param string $type
     * @return this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }



    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'data' => $this->getData(),
        ];
    }
}
