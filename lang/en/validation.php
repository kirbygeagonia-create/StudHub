<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines — StudHub
    |--------------------------------------------------------------------------
    |
    | Human-friendly validation messages for SEAIT students.
    |
    */

    'custom' => [
        'subject_id' => [
            'required' => 'Please select a subject.',
            'integer' => 'Please select a valid subject.',
            'exists' => 'The selected subject does not exist.',
        ],
        'type_wanted' => [
            'required' => 'Please select what type of resource you need.',
            'string' => 'Please select a valid resource type.',
            'in' => 'Please select a valid resource type.',
        ],
        'urgency' => [
            'required' => 'Please select an urgency level.',
            'string' => 'Please select a valid urgency level.',
            'in' => 'Please select a valid urgency level.',
        ],
        'needed_by' => [
            'date' => 'Please enter a valid date.',
            'after_or_equal' => 'The needed-by date must be today or later.',
        ],
        'description' => [
            'max' => 'The description must not exceed 2,000 characters.',
        ],
        'body' => [
            'required' => 'Please write your feedback before submitting.',
            'string' => 'Please write your feedback as text.',
            'min' => 'Your feedback must be at least 5 characters.',
            'max' => 'Your feedback must not exceed 2,000 characters.',
        ],
        'type' => [
            'in' => 'Please select a valid feedback type.',
        ],
        'name' => [
            'required' => 'Please enter your name.',
        ],
        'email' => [
            'required' => 'Please enter your email address.',
            'email' => 'Please enter a valid email address.',
            'unique' => 'An account with this email already exists.',
        ],
        'password' => [
            'required' => 'Please enter a password.',
            'min' => 'Your password must be at least 8 characters.',
            'confirmed' => 'The passwords do not match.',
        ],
        'display_name' => [
            'required' => 'Please enter a display name.',
            'min' => 'Your display name must be at least 3 characters.',
            'max' => 'Your display name must not exceed 30 characters.',
        ],
        'year_level' => [
            'required' => 'Please select your year level.',
        ],
        'program_id' => [
            'required' => 'Please select your program.',
        ],
        'resource_id' => [
            'required' => 'Please select a resource.',
            'exists' => 'The selected resource does not exist.',
        ],
        'title' => [
            'required' => 'Please enter a title for your resource.',
        ],
        'file' => [
            'max' => 'The file must not exceed :max kilobytes.',
        ],
        'reason' => [
            'required' => 'Please select a reason for your report.',
            'string' => 'Please select a valid reason.',
        ],
        'reported_type' => [
            'required' => 'Please select what you are reporting.',
            'in' => 'Please select a valid type to report.',
        ],
        'reported_id' => [
            'required' => 'Please specify what you are reporting.',
            'integer' => 'Please report a valid item.',
        ],
        'days' => [
            'required' => 'Please specify the number of days.',
            'integer' => 'Please enter a valid number of days.',
            'min' => 'Please enter at least 1 day.',
        ],
        'user_id' => [
            'required' => 'Please select a user.',
            'exists' => 'The selected user does not exist.',
        ],
    ],

    'attributes' => [],
];
