<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$config = new Config();

$finder = Finder::create()
    ->exclude(['bootstrap', 'storage', 'vendor', 'database'])
    ->in(getcwd())
    ->name('*.php')
    ->name('*.phpt')
    ->notName('*.blade.php')
    ->notName('_ide_helper.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true)
;

$rules = [
    '@PhpCsFixer' => true,
    'single_line_throw' => false,
    'no_empty_comment' => false,
    'new_with_braces' => false,
    'concat_space' => ['spacing' => 'one'],
    'ordered_imports' => [
        'sort_algorithm' => 'alpha',
    ],
];

return $config->setRules($rules)
    ->setFinder($finder)
    ->setUsingCache(true)
;
