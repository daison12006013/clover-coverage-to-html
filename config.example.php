<?php

return [
    /**
     * For Annotations Report
     *
     * This config will be used to determine classes/methods of which methods
     * they are belong to.
     */
    'annotations' => [
        'group',
        'feature',
        'module',
    ],

    /**
     * For Code Coverage
     *
     * This config ignores a line, either an exact or regex pattern.
     */
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
            'catch( |)\((.*)\)',
            '\}( |)else( |)\{',
        ],
    ],
];
