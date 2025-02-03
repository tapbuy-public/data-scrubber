# Data Scrubber

PHP library for anonymizing sensitive data in objects and arrays.

## Installation

```bash
composer require tapbuy/data-scrubber
```

## Usage

```php
use Tapbuy\DataScrubber\Anonymizer;

$anonymizer = new Anonymizer('https://your-api-url.com/keys');

$data = [
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'phonenumber' => [
        'ssn' => '123-45-6789'
    ]
];

$anonymized = $anonymizer->anonymizeObject($data);
```

## API

### Anonymizer Class
```php
class Anonymizer {
    public function __construct(string $url);
    public function updateKeys(): void;
    public function anonymizeObject(object|array $data): object|array;
}
```

### Keys Class
```php
class Keys {
    public function __construct(string $url);
    public function fetchKeys(): void;
    public function getKeys(): array;
}
```

## Keys Format

Your API endpoint must return:
```json
{
    "success": true,
    "data": ["name", "email", "ssn", "numbers[]"]
}
```
Keys with `[]` suffix indicate array fields that should have all elements anonymized.

## Anonymization Rules

- Strings: Replaced with `*` of same length
  ```php
  "John Doe" → "********"
  ```

- Numbers: Random number of same length/type
  ```php
  12345 → 98765
  123.45 → 987.65
  ```

- Arrays: If key marked with [], all elements anonymized
  ```php
  'numbers' => [123, 456] → [789, 012]
  ```

## CLI

Update keys via command line:
```bash
php bin/updateKeys.php https://your-api-url.com/keys
```

## Directory Structure
```
data-scrubber/
├── src/
│   ├── Anonymizer.php
│   └── Keys.php
├── bin/
│   └── updateKeys.php
└── composer.json
```