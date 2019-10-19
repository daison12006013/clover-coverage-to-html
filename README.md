<img src="https://raw.githubusercontent.com/daison12006013/clover-coverage-to-html/master/sample.png">

# Clover XML to HTML

Parses a clover xml and maps the coverage based on the relative path on your local and will calculate and creates a static website that shows the coverage.

## Installation

```
composer create-project daison/clover-to-html:dev-master clover-to-html --no-interaction
cd clover-to-html
```

## Executing Command

```
./clover-coverage-to-html process --xml-path=/path/to/my/clover.xml --store-path=/path/to/my/coverage-html/folder --config-path=clover_to_html.php
```

## Example Config

**clover_to_html.php**

```php
return [
    'title'   => 'My Project Code Coverage',
    'ignores' => [
        'exact' => [
            '});',
            ']);',
            ');',
            '}',
            '{',
            ']',
            '[',
            ') {',
            'return [',
            '];',
            'try {',
        ],
        'regex' => [
            'catch( |)\((.*)\)',
            '\}( |)else( |)\{',
        ],
    ],
    'badges' => [
        'Repositories' => 'Repositories/', // single
        'Controllers'  => ['Controllers/Api', 'Controllers/Http'], // grouping

        // or regex sample
        'User'         => '(.*)User(.*)',
        'Payment       => ['/Payment', 'Payment(Controller|Repository)'],
    ],
];
```

You could ignore a code based on the `regex` or an `exact` value of that line.

The computation will be different compared to the original clover computations, where we only combined the (green + red is equal to 100%). This is to simplify the wrong output given by `phpdbg` or `xdebug` drivers as example when using php.
