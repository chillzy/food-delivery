<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude(['bootstrap', 'node_modules', 'public', 'storage', 'vendor'])
    ->notPath('*')
    ->in(__DIR__);

$config = new PhpCsFixer\Config();

return $config
    ->setRiskyAllowed(true)
    ->setRules([
        '@PHP71Migration:risky' => true,
        '@PHPUnit75Migration:risky' => true,
        '@PhpCsFixer' => true,
        '@PhpCsFixer:risky' => true,
        '@PSR12' => true,
        'general_phpdoc_annotation_remove' => ['annotations' => ['expectedDeprecation']],
        'single_trait_insert_per_statement' => false,
        'declare_strict_types' => false,
        'multiline_whitespace_before_semicolons' => [
            'strategy' => 'no_multi_line',
        ],
        'phpdoc_align' => false,
        'no_extra_blank_lines' => false,
        'single_line_comment_style' => false,
        'void_return' => false,
        'php_unit_internal_class' => false,
        'final_internal_class' => false,
        'no_superfluous_phpdoc_tags' => false,
        'is_null' => false,
        'blank_line_before_statement' => false,
        'phpdoc_separation' => false,
        'php_unit_test_class_requires_covers' => false,
        'native_function_invocation' => false,
        'ordered_traits' => false,
        'phpdoc_trim' => false,
        'phpdoc_summary' => false,
        'phpdoc_no_package' => false,
        'yoda_style' => false,
        'php_unit_test_case_static_method_calls' => false,
        'phpdoc_types_order' => false,
        'phpdoc_order' => false,
        'php_unit_strict' => false,
        'self_accessor' => false,
    ])
    ->setFinder($finder);
