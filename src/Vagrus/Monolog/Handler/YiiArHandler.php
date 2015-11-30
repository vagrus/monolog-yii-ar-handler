<?php

namespace Vagrus\Monolog\Handler;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

/**
 * Handler sending logs to Yii's AR model
 *
 * @package Vagrus\Monolog\Handler
 */
class YiiArHandler extends AbstractProcessingHandler
{
    /**
     * @var string ActiveRecord model name
     */
    protected $modelName;
    /**
     * @var array context vars to AR model properties mapping settings
     */
    protected $mapping;

    /**
     * @param string $modelName
     * @param string|array $mapping
     * @param int $level
     * @param bool $bubble
     * @throws \InvalidArgumentException
     */
    public function __construct($modelName, $mapping, $level = Logger::DEBUG, $bubble = true)
    {
        $this->modelName = $modelName;
        $this->setMapping($mapping);

        parent::__construct($level, $bubble);
    }

    /**
     * @param string|array $mapping
     * @throws \InvalidArgumentException if mapping not contain model property for log message
     */
    protected function setMapping($mapping)
    {
        if (is_string($mapping)) {
            $mapping = array(
                '*' => $mapping,
            );
        }

        $mapping = (array)$mapping;

        if (!array_key_exists('*', $mapping)) {
            throw new \InvalidArgumentException("Mapping settings must contain AR model property");
        }

        $this->mapping = $mapping;
    }

    /**
     * Writes the record down to the log of the implementing handler
     *
     * @param array $record
     * @return void
     */
    protected function write(array $record)
    {
        /** @var \CActiveRecord $model */
        $model = new $this->modelName;

        $logMessage = $record['formatted'];
        $context = $record['context'];

        foreach ($this->mapping as $key => $modelProperty) {
            if ($key === '*') {
                $model->setAttribute($modelProperty, $logMessage);
            } elseif (array_key_exists($key, $context)) {
                $model->setAttribute($modelProperty, $context[$key]);
            }
        }

        $model->save(false);
    }
}
