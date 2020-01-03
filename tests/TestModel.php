<?php

namespace Stryksta\ReferenceNumber\Test;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stryksta\ReferenceNumber\GenerateReferenceNumber;

class TestModel extends Model
{
    use SoftDeletes,
        GenerateReferenceNumber;

    protected $table = 'test_models';

    protected $guarded = [];

    public $timestamps = false;

    /**
     * Get the options for generating the reference number.
     */
    public function referenceNumberOptions() : array
    {
        return $this->referenceNumberOptions ?? $this->getDefaultReferenceNumberOptions();
    }

    /**
     * Set the options for generating the reference number.
     *
     * @param $referenceNumberOptions
     * @return TestModel
     */
    public function setReferenceNumberOptions($referenceNumberOptions)
    {
        $this->referenceNumberOptions = $referenceNumberOptions;

        return $this;
    }

    /**
     * Get the default reference number options used in the tests.
     */
    public function getDefaultReferenceNumberOptions()
    {
        return [
            'field' => 'reference_number',
            'default' => 0,
            'prefix' => '',
            'suffix' => '',
            'padding' => '',
        ];
    }
}