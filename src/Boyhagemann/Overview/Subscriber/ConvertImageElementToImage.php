<?php

namespace Boyhagemann\Overview\Subscriber;

use Illuminate\Events\Dispatcher as Events;
use Boyhagemann\Form\Element\ElementInterface;
use Boyhagemann\Uploads\Form\ImageElement;
use Boyhagemann\Overview\Column;
use HTML;

/**
 *
 */
class ConvertImageElementToImage
{
	protected $width = 30;
	protected $height = 30;

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
	public function onBuildColumn(Column $column, ElementInterface $element, $record)
	{
		if (!$element instanceof ImageElement) {
			return;
		}

		$name = $element->getName();
		if(!isset($record->$name)) {
			return;
		}

		$path = $record->$name;
		$route = sprintf('image/%d/%d/%s', $this->width, $this->height, $path);
		$value = HTML::image($route);
		$column->setValue($value);
	}

}