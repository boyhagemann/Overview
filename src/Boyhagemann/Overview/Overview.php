<?php

namespace Boyhagemann\Overview;

class Overview
{
    protected $labels = array();
    protected $rows = array();
    protected $collection;

    /**
     * @param mixed $collection
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;
    }

    /**
     * @return mixed
     */
    public function getCollection()
    {
        return $this->collection;
    }

    public function links()
    {
        return $this->getCollection()->links();
    }

    public function row($id, $columns)
    {
        $this->rows[$id] = new Row($columns);
    }

    public function label($name, $value)
    {
        $this->labels[$name] = $value;
    }

    public function labels()
    {
        return $this->labels;
    }

    /**
     * @return array
     */
    public function rows()
    {
        return $this->rows;
    }

}