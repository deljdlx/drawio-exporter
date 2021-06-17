<?php

namespace JDLX\DrawioConverter\Traits;

Trait Timestamped
{

    protected $timestamped = false;

    /**
     * @return boolean
     */
    public function isTimestamped()
    {
        return $this->timestamped;
    }


    public function getTimestampFields()
    {
        $sql = "    `created_at` DATETIME,\n";
        $sql .= "    `updated_at` DATETIME";
        return $sql;
    }
}

