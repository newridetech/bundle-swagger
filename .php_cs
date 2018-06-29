<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__.'/app')
    ->in(__DIR__.'/bootstrap')
    ->in(__DIR__.'/config')
    ->in(__DIR__.'/tests')
;

return PhpCsFixer\Config::create()
    ->setRules([
        '@PSR2' => true,
        '@Symfony' => true,
        // 'strict_param' => true,
        // 'array_syntax' => ['syntax' => 'short'],
    ])
    ->setFinder($finder)
;
