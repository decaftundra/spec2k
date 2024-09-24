<?php

use App\User;
use App\Issue;
use App\Comment;
use Faker\Generator as Faker;

$factory->define(Issue::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence,
        'content' => implode(' ', $faker->paragraphs),
        'kind' => array_rand(Issue::$kinds),
        'priority' => array_rand(Issue::$priorities),
        'status' => array_rand(Issue::$statuses),
        'posted_by' => function (){
            return factory(User::class, 'user')->create()->fullname;
        }
    ];
});

$factory->state(Issue::class, 'open_issue', function (Faker $faker) {
    return [
        'title' => $faker->sentence,
        'content' => implode(' ', $faker->paragraphs),
        'kind' => array_rand(Issue::$kinds),
        'priority' => array_rand(Issue::$priorities),
        'status' => 'open',
        'posted_by' => function (){
            return factory(User::class, 'user')->create()->fullname;
        }
    ];
});
