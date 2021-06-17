<?php

namespace JDLX\DrawioConverter;

use JDLX\DrawioConverter\Traits\Timestamped;
use SimpleXMLElement;

class AbstractEntity implements \JsonSerializable
{
    /** @var Graph */
    protected $graph;

    /** @var SimpleXMLElement */
    protected $xml;


    /**
     * @var Field[]
     */
    protected $fields = [];


    /**
     * @var AbstractEntity[]
     */
    protected $parentEntities = [];

    protected $virtualFields = [];

    /**
     * @var Relation[]
     */
    protected $relations = [];

    protected $value;
    protected $dataNode;

    protected $id;
    protected $name;

    protected $primaryKey;

    public function __construct($graph, $xmlNode = null)
    {
        $this->graph = $graph;
        $this->xml = $xmlNode;

        if($this->xml) {
            $dataNode =$this->xml->xPath('parent::object');
            if(count($dataNode)) {
                $this->dataNode = $dataNode[0];
            }
        }
        $this->extractFields();
    }

    /**
     * @param Relation $relation
     * @return Entity
     */
    public function getTargetEntityFromRelation($relation)
    {
        $targetEntity = null;

        if ($relation->getFrom() === $this) {
            $targetEntity = $relation->getTo();
        }
        else {
            foreach($this->getParentEntities() as $parentEntity) {
                if ($relation->getFrom() === $parentEntity) {
                    $targetEntity = $relation->getTo();
                    break;
                }
            }
        }

        if(!$targetEntity) {
            $targetEntity = $relation->getFrom();
        }
        return $targetEntity;
    }

    public function addRelation($relation)
    {
        $this->relations[] = $relation;
        return $this;
    }


    public function inherit($abstractEntity)
    {
        $this->parentEntities[] = $abstractEntity;
        return $this;
    }


    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }


    public function createPrimaryKeyField()
    {
        $this->primaryKey = new Field($this->graph);
        $this->primaryKey->setType(Field::TYPE_AUTO_ID);
        $this->primaryKey->setId('id');
        $this->primaryKey->setName('id');

        array_unshift($this->fields, $this->primaryKey);

        return $this;
    }


    public function getVirtualFields()
    {
        $fields = $this->virtualFields;

        $parentEntities = $this->getParentEntities();

        foreach($parentEntities as $parentEntity) {
            $fields = array_merge($fields, $parentEntity->getVirtualFields());
        }
        return $fields;
    }

    /**
     * @return Relation[]
     */
    public function getRelations()
    {
        $relations = $this->relations;

        $parentEntities = $this->getParentEntities();
        foreach($parentEntities as $parentEntity) {
            $relations = array_merge($relations, $parentEntity->getRelations());
        }
        return $relations;
    }


    /**
     * @return AbstractEntity[]
     */
    public function getParentEntities()
    {
        $parentEntities = $this->parentEntities;

        foreach($parentEntities as $parentEntity) {
            $parentEntities = array_merge($parentEntities, $parentEntity->getParentEntities());
        }
        return $parentEntities;
    }


    /**
     * @return Field[]
     */
    public function getFields()
    {
        $fields = $this->fields;
        $fields = array_merge($fields, $this->getParentFields());
        return $fields;
    }

    /**
     * @return Field[]
     */
    public function getParentFields()
    {
        $fields = [];
        $parentEntities = $this->getParentEntities();

        foreach($parentEntities as $parentEntity) {
            $fields = array_merge($fields, $parentEntity->getFields());
        }
        return $fields;
    }


    /**
     * @return boolean
     */
    public function isReal()
    {
        return false;
    }


    public function extractFields()
    {
        $query = '//mxCell[@parent="' . $this->getId()  . '"]';
        $nodes = $this->xml->xPath($query);

        foreach($nodes as $node) {
            $field = new Field($this, $node);
            if(!preg_match('`^#`', $field->getName())) {
                $this->fields[$field->getName()] = $field;
            }
            else {
                $this->virtualFields[$field->getName()] = $field;
            }
        }
    }

    /**
     * @param string $id
     * @return this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }


    /**
     * @return string
     */
    public function getName()
    {
        if(!$this->name) {
            $this->name = $this->getValue();
        }
        return $this->name;
    }

    /**
     * @param string $name
     * @return this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }



    public function getData($key = null)
    {
        if($key === null) {
            $data = [];
            if($this->dataNode) {
                foreach($this->dataNode->attributes() as $attributeName => $value) {
                    $data[$attributeName] = (string) $value;
                }
            }
            return $data;
        }
        else {
            if ($this->dataNode) {
                if(isset($this->dataNode[$key])) {
                    return (string) $this->dataNode[$key];
                }
            }
        }
        return null;
    }

    public function getValue()
    {
        if($this->dataNode) {
            return (string) $this->dataNode['label'];
        }
        else {
            return (string) $this->xml['value'];
        }
    }

    public function getId()
    {

        if(!$this->id) {
            if($this->dataNode) {
                $this->id = (string) $this->dataNode['id'];
            }
            else {
                $this->id = (string) $this->xml['id'];;
            }
        }

        return $this->id;
    }


    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'data' => $this->getData(),
            'fields' => $this->fields,
        ];
    }
}
