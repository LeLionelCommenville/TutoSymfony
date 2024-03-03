<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class BanWord extends Constraint
{

    public function __construct(
        public string $message = 'This contains a banned word  {{ banWord }}',
        public $banWords = ['fuck', 'spam', 'viaga', 'porn'],
        ?array $groups = null,
        mixed $payload = null)
    {
        parent::__construct(null,  $groups, $payload);
    }
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
}
