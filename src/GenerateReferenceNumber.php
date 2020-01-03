<?php

namespace Stryksta\ReferenceNumber;

use Illuminate\Database\Eloquent\Model;

trait GenerateReferenceNumber
{

    protected $referenceNumberOptions = [];

    /**
     * Return the  Reference Number configuration array for this model.
     *
     * @return array
     */
    abstract public function referenceNumberOptions(): array;

    /**
     * Generate Course Number when creating the model.
     *
     * @return void
     */
    public static function bootGenerateReferenceNumber()
    {
        static::creating(function (Model $model) {
            $model->addReferenceNumber();
        });
    }

    /**
     * Generate Course Number when creating the model.
     *
     * @return void
     */
    protected function getReferenceNumberOptions() : array
    {
        //Defines the Reference Number field for the model.
        $options = $this->referenceNumberOptions = [
            'field' => isset($this->referenceNumberOptions()['field']) ? $this->referenceNumberOptions()['field'] : 'reference_number',
            'start' => isset($this->referenceNumberOptions()['start']) ? $this->referenceNumberOptions()['start'] : 0,
            'prefix' => isset($this->referenceNumberOptions()['prefix']) ? $this->referenceNumberOptions()['prefix'] : '',
            'suffix' => isset($this->referenceNumberOptions()['suffix']) ? $this->referenceNumberOptions()['suffix'] : '',
            'padding' => isset($this->referenceNumberOptions()['padding']) ? $this->referenceNumberOptions()['padding'] : '',
        ];

        return $options;
    }

    /**
     * Adds the generated Reference Number to the specified field
     */
    protected function addReferenceNumber()
    {
        //Get Options (User provided or default)
        $this->referenceNumberOptions = $this->getReferenceNumberOptions();

        //Generate Reference Number
        $referenceNumber = $this->generateReference();

        //Add Reference Number
        $referenceNumberField = $this->referenceNumberOptions['field'];
        $this->$referenceNumberField = $referenceNumber;
    }

    /**
     * Generates the Course Number
     * @return string
     */
    protected function generateReference() : string
    {
        //Get last Reference Number
        $number = $this->getLastRecordNumber();

        // Removes prefix
        if (!empty($this->referenceNumberOptions['prefix'])) {
            $number = str_replace($this->referenceNumberOptions['prefix'],'', $number);
        }
        // Removes suffix
        if (!empty($this->referenceNumberOptions['suffix'])) {
            $number = str_replace($this->referenceNumberOptions['suffix'],'', $number);
        }
        // Removes Padding
        $number = (int) ltrim($number, '0');

        //If start is more than the last reference number, set the start number as the new start
        if ($this->referenceNumberOptions['start'] > $number) {
            $number = (int) $this->referenceNumberOptions['start'];
        }

        // Increase number
        ++$number;

        // Add padding
        if (!empty($this->referenceNumberOptions['padding'])) {
            $number = str_pad($number, $this->referenceNumberOptions['padding'], "0", STR_PAD_LEFT);
        }
        // Add prefix
        if (!empty($this->referenceNumberOptions['prefix'])) {
            $number = $this->referenceNumberOptions['prefix'].$number;
        }
        // Add suffix
        if (!empty($this->referenceNumberOptions['suffix'])) {
            $number = $number.$this->referenceNumberOptions['suffix'];
        }

        return $number;
    }

    /**
     * Get records of field in array to get the max
     * @return string
     */
    protected function getLastRecordNumber() : string
    {

        $referenceField = $this->referenceNumberOptions['field'];

        //If soft deletes are enabled, include in result
        if ($this->usesSoftDelete(static::class)) {
            $query = static::withTrashed()->pluck($referenceField)->toArray();
        } else {
            $query = static::all()->pluck($referenceField)->toArray();
        }

        $lastRecordNumber = $this->getMax($query);

        return $lastRecordNumber ?? $this->referenceNumberOptions['start'];
    }

    /**
     * Checks if the Model uses Soft Delete
     * @return bool
     */
    protected function usesSoftDelete($model): bool
    {
        return (bool) in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses_recursive($model));
    }

    /**
     * Remvoe everything but numbers from array to get max if possible
     * @return int|null
     */
    protected function getMax($array) : ?int
    {
        $max = null;
        if (!empty($array)) {
            //Strip everything but numbers
            array_walk($array, function(&$item) {
                $item = preg_replace('/\D/', '', $item);
            });

            $max = max($array);
        }

        return $max;
    }
}