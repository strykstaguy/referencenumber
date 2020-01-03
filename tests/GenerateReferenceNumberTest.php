<?php

namespace Stryksta\ReferenceNumber\Test;

class GenerateReferenceNumberTest extends TestCase
{

    /** @test */
    public function it_will_save_a_reference_number_to_a_difference_field_if_specified()
    {
        $model = new class extends TestModel {
            public function referenceNumberOptions(): array
            {
                return [
                    'field' => 'transaction_id',
                ];
            }
        };
        $model->name = 'Purchase';
        $model->save();
        $this->assertEquals('1', $model->transaction_id);
    }

    /** @test */
    public function it_will_save_a_reference_number_if__no_field_is_specified()
    {
        $model = TestModel::create(['name' => 'Purchase']);
        $this->assertEquals('1', $model->reference_number);
    }

    /** @test */
    public function it_will_generate_a_reference_number_with_padding()
    {
        $model = new class extends TestModel {
            public function referenceNumberOptions(): array
            {
                return [
                    'padding' => 3,
                ];
            }
        };
        $model->name = 'Purchase';
        $model->save();
        $this->assertEquals('001', $model->reference_number);
    }

    /** @test */
    public function it_will_generate_a_reference_number_with_a_prefix()
    {
        $model = new class extends TestModel {
            public function referenceNumberOptions(): array
            {
                return [
                    'prefix' => 'T',
                ];
            }
        };
        $model->name = 'Purchase';
        $model->save();
        $this->assertEquals('T1', $model->reference_number);
    }

    /** @test */
    public function it_will_generate_a_reference_number_with_a_suffix()
    {
        $model = new class extends TestModel {
            public function referenceNumberOptions(): array
            {
                return [
                    'suffix' => 'S',
                ];
            }
        };
        $model->name = 'Purchase';
        $model->save();
        $this->assertEquals('1S', $model->reference_number);
    }

    /** @test */
    public function it_will_generate_a_reference_number_with_padding_suffix_and_prefix()
    {
        $model = new class extends TestModel {
            public function referenceNumberOptions(): array
            {
                return [
                    'padding' => 3,
                    'prefix' => 'T',
                    'suffix' => 'S'
                ];
            }
        };
        $model->name = 'Purchase';
        $model->save();
        $this->assertEquals('T001S', $model->reference_number);
    }

    /** @test */
    public function it_will_increment_by_one_when_generating_a_reference_number()
    {

        //Previous Number, Start at Zero
        $previousNumber = 0;
        foreach (range(1, 10) as $i) {
            //Add One to Previous Number
            $previousNumberPlusOne = $previousNumber + 1;

            //Add Item
            $model = TestModel::create(['name' => 'Purchase']);

            //Does newly inserted Reference Number equal the previous plus one.
            $this->assertEquals("{$previousNumberPlusOne}", $model->reference_number);

            //Set previous number to item number
            $previousNumber = $i;
        }
    }

    /** @test */
    public function it_will_generate_a_reference_number_with_soft_deletes()
    {
        TestModel::create(['name' => 'Purchase']);
        TestModel::create(['name' => 'Cancelled Purchase', 'deleted_at' => date('Y-m-d h:i:s')]);
        $model = TestModel::create(['name' => 'Purchase']);

        $this->assertEquals('3', $model->reference_number);
    }

    /** @test */
    public function it_will_increment_new_default_if_changed_to_higher_number()
    {
        TestModel::create(['name' => 'Purchase']);
        TestModel::create(['name' => 'Purchase']);

        //Change start
        $model = new class extends TestModel {
            public function referenceNumberOptions(): array
            {
                return [
                    'start' => '5',
                ];
            }
        };
        $model->name = 'Purchase';
        $model->save();

        $this->assertEquals('6', $model->reference_number);

        $oneMore = TestModel::create(['name' => 'Purchase']);
        $this->assertEquals('7', $oneMore->reference_number);
    }

    /** @test */
    public function it_will_continue_incrementing_if_default_changed_to_lower_number()
    {
        TestModel::create(['name' => 'Purchase']);
        TestModel::create(['name' => 'Purchase']);

        //Change start
        $model = new class extends TestModel {
            public function referenceNumberOptions(): array
            {
                return [
                    'start' => '1',
                ];
            }
        };
        $model->name = 'Purchase';
        $model->save();

        $this->assertEquals('3', $model->reference_number);

        $oneMore = TestModel::create(['name' => 'Purchase']);
        $this->assertEquals('4', $oneMore->reference_number);


    }
}
