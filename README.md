# Flex2Cell

**Flex2Cell** is a flexible and efficient PHP library for exporting data to Excel (XLS, XLSX) with support for headers, data mappings, formatters, and handling large datasets. It's dependent on the [PhpSpreadsheet](https://github.com/PHPOffice/PhpSpreadsheet) library.

## Features

- Export data to Excel in XLS or XLSX format using Microsoft's format.
- Supports data from collections, Eloquent models, or multi-dimensional arrays alternative to `Eloquent\Collection::toArray()` format.
- Allows custom column headers and mapping between data fields and Excel columns.
- Includes value formatting using transformers (e.g., formatting dates, transforming IDs to names).
- Supports hidden fields, meaning you can choose which fields should not appear in the export.
- Provides meta settings for the output file (e.g., author, title).
- Efficient memory usage when exporting large datasets.
- Supports partial exports with `replace` or `append` modes, allowing export in chunks.
- Custom column and row merging rules.

## Installation

To install the library, add it to your project via Composer:

```bash
composer require zuko/flex2cell
```
# Usage
## Simple Export Example

```php
use Zuko\Flex2Cell\ExcelExporter;


$data = // your data here...

ExcelExporter::export($data, 'export.xlsx', [
    'headers' => ['#', 'Name', 'Product Group', 'Owner Name', 'Business Type', 'District', 'Commune'],
    'mapping' => [
        'id' => '#',
        'name' => 'Name',
        'product_group' => 'Product Group',
        'owner_name' => 'Owner Name',
        'owner_business_type' => 'Business Type',
        'district.name' => 'District',
        'village.name' => 'Commune',
    ],
    'formatters' => [
        'owner_business_type' => new CustomFormatter(),
        'district.name' => 'FullyQualifiedFormatterClassName',
    ],
    'columnMergeRules' => [
        ['start' => 'district.name', 'end' => 'village.name', 'label' => 'Address', 'shiftDown' => true]
    ],
    'rowMergeRules' => [
        'product_group', 'owner_name', 'owner_business_type'
    ]
]);
```

## Fluent API Usage

You can also use a fluent interface for more control:

```php
use Zuko\Flex2Cell\ExcelExporter;

// custom formatter class
class BusinessTypeFormatter implements Zuko\Flex2Cell\Contracts\FormatterInterface
{
    public function formatValue($value)
    {
        return ucwords(str_replace('_', ' ', $value));
    }
}


$data = // your data here...

ExcelExporter::make()
             ->setData($data)
             ->setHeaders(['ID', 'Name', 'Product Area', 'Nursery Area', 'Category'])
             ->setMapping([
                 'id' => 'ID',
                 'name' => 'Name',
                 'product_area' => 'Product Area',
                 'nursery_area' => 'Nursery Area',
                 'category.name' => 'Category'
             ])
             ->setFormatters([
                 'category.name' => fn($value) => ucfirst($value),
                 'owner_business_type' => new CustomFormatter(),
                 
             ])
             ->setColumnMergeRules([
                 ['start' => 'C', 'end' => 'D', 'label' => 'AREA']
             ])
             ->setRowMergeRules([
                 'E' => ['field' => 'category.name']
             ])
             ->export('export.xlsx');
```
