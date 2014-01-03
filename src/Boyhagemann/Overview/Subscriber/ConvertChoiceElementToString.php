<?php

namespace Boyhagemann\Overview\Subscriber;

use Illuminate\Events\Dispatcher as Events;
use Boyhagemann\Form\Element\ElementInterface;
use Boyhagemann\Form\Element\Type\Choice;
use Boyhagemann\Overview\Column;

/**
 *
 */
class ConvertChoiceElementToString
{
    /**
	 * Register the listeners for the subscriber.
	 *
	 * @param Events $events
	 */
	public function subscribe(Events $events)
	{
		$events->listen('overview.buildColumn', array($this, 'onBuildColumn'));
	}

	/**
	 * @param Column  $column
	 * @param ElementInterface $element
	 */
	public function onBuildColumn(Column $column, ElementInterface $element)
	{
		if (!$element instanceof Choice) {
			return;
		}

		$value = $column->getValue();
		$choices = $element->getChoices();
		$selected = array();
		foreach ($choices as $key => $label) {

			if (in_array($key, (array) $value)) {
				$selected[] = $label;
			}
		}

		$value = implode(', ', $selected);
		$column->setValue($value);
	}

}