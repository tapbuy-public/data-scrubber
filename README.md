# Data scrubbing

A tool to help with scrubbing sensitive data. 

## Usage

`$dataScrubber = new DataScrubber(string $url)`  
Api url should return a JSON array of keys.  
Result is stored in `var/data-scrubbing-keys.json`.  
It will fetch from API if file does not exists.  
To update the file, use the command  
`php bin/updateKeys.php https://domain.com/keys`

`$dataScrubber->anonymizeObject(array|object $data): array|object`

```php
$dataScrubber = new DataScrubber('https://domain.com/keys');
$data = [
    "userName" => "John Doe",
    "something" => [
        "email" => "john.doe@mail.com",
        "nonPersonalKey" => "value"
    ],
    "test" => [
        "phonenumber" => [
            "phonenumber" => 606060606,
        ]
    ],
    "arrayValue": [
        "shouldBeScrubbed1",
        "shouldBeScrubbed2",
        "shouldBeScrubbed3",
    ]
];
$data = $dataScrubber->anonymizeObject($data);
```

For arrays your API endpoint should return `key[]`, in the upon exemple `arrayValue[]`. 

With this api response exemple : `["userName", "email", "phonenumber", "arrayValue[]"]` :

```json
[
    "userName" => "********",
    "something" => [
        "email" => "*****************",
        "nonPersonalKey" => "value"
    ],
    "test" => [
        "phonenumber" => [
            "phonenumber" => 394720143,
        ]
    ],
    "arrayValue": [
        "*****************",
        "*****************",
        "*****************",
    ]
]
```