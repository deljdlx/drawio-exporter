<?php
namespace JDLX\DrawioConverter;

use JDLX\DrawioConverter\Traits\Timestamped;

class RelationTable
{

    use Timestamped;

    /** @var Entity */
    protected $from;

    /** @var Entity */
    protected $to;

    /** @var Relation */
    protected $relation;


    /** @var bool */
    protected $hasId = true;

    /**
     * @param Entity $from
     * @param Entity $to
     * @param Relation $relation
     */
    public function __construct($from, $to, $relation = null)
    {
        $this-> from = $from;
        $this->to = $to;
        $this->relation = $relation;
    }

    public function hasId($value = null)
    {
        if($value === null) {
            $this->hasId = $value;
            return $this;
        }
        else {
            return $this->hasId;
        }
    }

    /**
     * @return Entity
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @return Entity
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @return Relation
     */
    public function getRelation()
    {
        return $this->relation;
    }
}
