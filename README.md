# Data scrubbing

A tool to help with scrubbing sensitive data

## Usage

```php
$dataScrubber = new DataScrubber('https://api.dev.tapbuy.io/scrubbing-keys');
$data = [
    "userName" => "John Doe",
    "truc" => [
        "email" => "valentin.leveque@gmail.com",
        "bidule" => "eprlepfo"
    ],
    "test" => [
        "phonenumber" => [
            "phonenumber" => 606060606,
        ]
    ]
];
$data = $dataScrubber->anonymizeObject($data);
```