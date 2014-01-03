<?php

namespace Boyhagemann\Overview;

use Boyhagemann\Form\FormBuilder;
use Boyhagemann\Model\ModelBuilder;
use Boyhagemann\Form\Element\Type\Choice;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Event;

class OverviewBuilder
{
    protected $mb;
    protected $fb;
    protected $fields = array();
    protected $limit = 10;

    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * @param FormBuilder $fb
     */
    public function setFormBuilder(FormBuilder $fb)
    {
        $this->fb = $fb;
    }

    /**
     * @return FormBuilder
     */
    public function getFormBuilder()
    {
        return $this->fb;
    }

    /**
     * @param ModelBuilder $mb
     */
    public function setModelBuilder(ModelBuilder $mb)
    {
        $this->mb = $mb;
    }

    /**
     * @return ModelBuilder
     */
    public function getModelBuilder()
    {
        return $this->mb;
    }

    /**
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
        if ($this->queryBuilder) {
            return $this->queryBuilder;
        }

        $this->queryBuilder = $this->getModelBuilder()->build()->query();

        return $this->queryBuilder;
    }

    /**
     * @param $limit
     * @return $this
     */
    public function display($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @param $order
     * @param $direction
     * @return $this
     */
    public function order($order, $direction = null)
    {
        $this->getQueryBuilder()->orderBy($order, $direction);
        return $this;
    }

    /**
     * @param array $fields
     */
    public function fields(Array $fields)
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * @param \Closure $callback
     * @return $this
     */
    public function query(\Closure $callback)
    {
        $callback($this->getQueryBuilder());
        return $this;
    }

    /**
     * @return string
     */
    public function build()
    {
        $overview = new Overview();
        $mb = $this->getModelBuilder();
        $fb = $this->getFormBuilder();
        $model = $mb->build();

		if(!$this->fields) {
			$this->fields = $model->getFillable();
		}

        foreach ($this->fields as $field) {

			if(!$fb->has($field)) {
				continue;
			}

			// Get the label for this field
			$element = $fb->get($field);
			$label = $element->getLabel();

            $overview->label($field, $label);
        }

        $q = $this->getQueryBuilder();
        $collection = $q->paginate($this->limit);
        $overview->setCollection($collection);

        foreach ($collection as $record) {

            $columns = array();
            foreach ($this->fields as $field) {

				if(!$fb->has($field)) {
					continue;
				}

                $columns[$field] = $this->buildColumn($field, $fb->get($field), $record);
            }

            $overview->row($record->id, $columns);
        }

        return $overview;
    }

    /**
     * @param $field
     * @param $element
     * @param $record
     * @return string
     */
    public function buildColumn($name, $element, $record)
    {
		$cell = new Column;
		$cell->setValue($record->$name);

		Event::fire('overview.buildColumn', array($cell, $element, $record));

        return $cell->getValue();
    }

}