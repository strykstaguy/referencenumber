<h1 align="center">Reference Number Trait</h1>

<p align="center">Generate a reference number for Invoices, Payment Transactions, Form Submissions, etc </p>

This package provides a trait that will generate a Reference Number when saving an Eloquent model. 

## Installation

You can install the package via composer:
``` bash
composer require stryksta/referencenumber
```
## Options
| Option |  Type |  Default | Description  |
| ------------ | ------------ | ------------ | ------------ |
| `field`  | string  | `reference_number`  | This is used to identify the field the generated reference number will be saved to.  |
| `start`  | integer  |  `0` | This is where incrementing will start. When generating a reference number this will always increment by 1. So if you start at 0, your first reference will be 1.  |
|  `prefix` | string  | `''`  | This is a string you want added in front of every reference number  |
| `suffix`  | string  | `''`  | This is a string you want added to the end of every reference number  |
| `padding`  | integer  | `''`  | This will pad the reference number to a specified length. For example, setting padding to 3 will generate 001, 002, 003 and so on.  |


## Usage

1. Your  model should use the `Stryksta\ReferenceNumber\GenerateReferenceNumber` trait
2. You should have a field to save the generated reference number to.

Here's an example of how to implement the trait:

```php
<?php

namespace App;

use Stryksta\ReferenceNumber\GenerateReferenceNumber;
use Illuminate\Database\Eloquent\Model;

class SubmissionModel extends Model
{
    use GenerateReferenceNumber;
   
    /**
     * Get the options for generating a reference number
     */
    public function referenceNumberOptions()
    {
        return [
            'field' => 'reference_number',
            'start' => 0,
            'prefix' => 'S',
            'suffix' => '',
            'padding' => 3,
        ];
    }
}
    public function create() {
        $model = new SubmissionModel();
        $model->name = 'John Smith';
        $model->comment = 'Hello';
        $model->save();

        echo $model->reference_number; // ouputs "S001"
}

```

## Changelog

### 1.0.0 - 2020-01-03

- Initial release

## Testing

``` bash
composer test
```
## Contributing
Suggestions, Improvements, and any other comments are welcome!

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.