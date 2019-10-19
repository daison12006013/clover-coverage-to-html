<?php

return [
    'title'   => 'My Code Coverage',

    'ignores' => [
        /**
         * Each line of code, once we found these kind of value
         * we shall ignore them.
         */
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

        /**
         * Each line of code, once we found these kind regex
         * we shall ignore it.
         */
        'regex' => [
            // '\($',
            // '\[$',
            'catch( |)\((.*)\)',
            '\}( |)else( |)\{',
        ],
    ],

    /**
     * These configuration will create a badge search for us,
     * To easily find the common file names
     */
    'badges'  => [
        'Models'      => 'Models/',
        'Controllers' => ['Controllers/Api/', 'Controllers/Http'],
    ],
];
