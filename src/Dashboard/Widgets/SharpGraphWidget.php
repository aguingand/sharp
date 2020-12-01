<?php

namespace Code16\Sharp\Dashboard\Widgets;

abstract class SharpGraphWidget extends SharpWidget
{

    /**
     * @var string
     */
    protected $display;

    /**
     * @var string
     */
    protected $ratio;

    /**
     * @var array
     */
    protected $options;

    /**
     * @param string $key
     * @param string $type
     */
    protected function __construct(string $key, string $type)
    {
        parent::__construct($key, $type);

        $this->ratio = [16,9];
    }

    /**
     * @param string $ratio 16:9, 1:1, ...
     * @return static
     */
    public function setRatio(string $ratio)
    {
        $this->ratio = explode(":", $ratio);

        return $this;
    }


    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return parent::buildArray(array_merge([
            "display" => $this->display,
            "ratioX" => $this->ratio ? (int)$this->ratio[0] : null,
            "ratioY" => $this->ratio ? (int)$this->ratio[1] : null,
            "options" => $this->options,
        ], $this->options ?? []));
    }

    /**
     * Return specific validation rules.
     *
     * @return array
     */
    protected function validationRules()
    {
        return [
            "display" => "required|in:bar,line,pie"
        ];
    }

}