<?php

namespace Spatie\MediaLibrary\Conversion;

use Spatie\MediaLibrary\Exceptions\InvalidConversionParameter;

class Conversion
{
    /**
     * @var string name
     */
    protected $name = '';

    /**
     * @var array
     */
    protected $manipulations = [];

    /**
     * @var array
     */
    protected $performOnCollections = [];

    /**
     * @var bool
     */
    protected $performOnQueue = true;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public static function create($name)
    {
        return new static($name);
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the manipulations of this conversion.
     */
    public function getManipulations()
    {
        $manipulations = $this->manipulations;

        //if format not is specified, create a jpg
        if (count($manipulations) && !$this->containsFormatManipulation($manipulations)) {
            $manipulations[0]['fm'] = 'jpg';
        };

        return $manipulations;
    }

    /**
     * Set the manipulations for this conversion.
     *
     * @param $manipulations
     *
     * @return $this
     */
    public function setManipulations($manipulations)
    {
        $this->manipulations = func_get_args();

        return $this;
    }

    /**
     * Add the given manipulation as the first manipulation.
     *
     * @param array $manipulation
     *
     * @return $this
     */
    public function addAsFirstManipulation(array $manipulation)
    {
        array_unshift($this->manipulations, $manipulation);

        return $this;
    }

    /**
     * Set the collection names on which this conversion must be performed.
     *
     * @param array $collectionNames
     *
     * @return $this
     */
    public function performOnCollections($collectionNames)
    {
        $this->performOnCollections = func_get_args();

        return $this;
    }

    /*
     * Determine if this conversion should be performed on the given
     * collection.
     */
    public function shouldBePerformedOn($collectionName)
    {
        //if no collections were specified, perform conversion on all collections
        if (!count($this->performOnCollections)) {
            return true;
        }

        if (in_array('*', $this->performOnCollections)) {
            return true;
        }

        return in_array($collectionName, $this->performOnCollections);
    }

    /**
     * Mark this conversion as one that should be queued.
     *
     * @return $this
     */
    public function queued()
    {
        $this->performOnQueue = true;

        return $this;
    }

    /**
     * Mark this conversion as one that should not be queued.
     *
     * @return $this
     */
    public function nonQueued()
    {
        $this->performOnQueue = false;

        return $this;
    }

    /*
     * Determine if the conversion should be queued.
     */
    public function shouldBeQueued()
    {
        return $this->performOnQueue;
    }

    /*
     * Get the extension that the result of this conversion must have.
     */
    public function getResultExtension($originalFileExtension = '')
    {
        return array_reduce($this->getManipulations(), function ($carry, array $manipulation) {

            if (isset($manipulation['fm'])) {
                $keepFormats = ['png', 'gif'];

                return ($manipulation['fm'] === 'src' && in_array($carry, $keepFormats)) ? $carry : $manipulation['fm'];
            }

            return $carry;

        }, $originalFileExtension);
    }

    /*
     * Determine if the given manipulations contain a format manipulation.
     */
    protected function containsFormatManipulation(array $manipulations)
    {
        foreach ($manipulations as $manipulation) {
            if (array_key_exists('fm', $manipulation)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Set the target width.
     * Matches with Glide's 'w'-parameter.
     *
     * @param int $width
     *
     * @return $this
     *
     * @throws \Spatie\MediaLibrary\Exceptions\InvalidConversionParameter
     */
    public function setWidth($width)
    {
        if ($width < 1) {
            throw InvalidConversionParameter::invalidWidth();
        }

        $this->setManipulationParameter('w', $width);

        return $this;
    }

    /**
     * Set the target height.
     * Matches with Glide's 'h'-parameter.
     *
     * @param int $height
     *
     * @return $this
     *
     * @throws \Spatie\MediaLibrary\Exceptions\InvalidConversionParameter
     */
    public function setHeight($height)
    {
        if ($height < 1) {
            throw InvalidConversionParameter::invalidHeight();
        }

        $this->setManipulationParameter('h', $height);

        return $this;
    }

    /**
     * Set the target format.
     * Matches with Glide's 'fm'-parameter.
     *
     * @param string $format
     *
     * @return $this
     *
     * @throws \Spatie\MediaLibrary\Exceptions\InvalidConversionParameter
     */
    public function setFormat($format)
    {
        $validFormats = ['jpg', 'png', 'gif', 'src'];

        if (!in_array($format, $validFormats)) {
            throw InvalidConversionParameter::invalidFormat($format, $validFormats);
        }

        $this->setManipulationParameter('fm', $format);

        return $this;
    }

    /**
     * Set the target fit.
     * Matches with Glide's 'fit'-parameter.
     *
     * @param string $fit
     *
     * @return $this
     *
     * @throws \Spatie\MediaLibrary\Exceptions\InvalidConversionParameter
     */
    public function setFit($fit)
    {
        $validFits = [
            'contain',
            'max',
            'fill',
            'stretch',
            'crop',
            'crop-top-left',
            'crop-top',
            'crop-top-right',
            'crop-left',
            'crop-center',
            'crop-right',
            'crop-bottom-left',
            'crop-bottom',
            'crop-bottom-right',
        ];

        if (!in_array($fit, $validFits)) {
            throw InvalidConversionParameter::invalidFit($fit, $validFits);
        }

        $this->setManipulationParameter('fit', $fit);

        return $this;
    }

    /**
     * Crops the image to specific dimensions prior to any other resize operations.
     *
     * @param int $width
     * @param int $height
     * @param int $x
     * @param int $y
     *
     * @return $this
     *
     * @throws \Spatie\MediaLibrary\Exceptions\InvalidConversionParameter
     */
    public function setCrop($width, $height, $x, $y)
    {
        foreach (compact('width', 'height') as $name => $value) {
            if ($value < 1) {
                throw InvalidConversionParameter::shouldBeGreaterThanOne($name, $value);
            }
        }

        $this->setManipulationParameter('crop', implode(',', [$width, $height, $x, $y]));

        return $this;
    }

    /**
     * Set the manipulation parameter.
     *
     * @param string $name
     * @param string $value
     *
     * @return $this
     */
    public function setManipulationParameter($name, $value)
    {
        $manipulation = array_pop($this->manipulations) ?: [];

        $this->manipulations[] = array_merge($manipulation, [$name => $value]);

        return $this;
    }
}
