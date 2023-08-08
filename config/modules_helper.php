<?php

return [
    'files'  => [
        ['name'  => 'base', 'path'  => 'App/Http/Controllers/ApiController.php', 'tag'  => ''],
        ['name' => 'enum', 'path' => 'Modules/{{module}}/Enums/{{class}}Enum.php', 'tag' => 'Enum'],
        ['name' => 'controller', 'path' => 'Modules/{{module}}/Http/Controllers/{{class}}Controller.php', 'tag' => 'Controller'],
        ['name'  =>  'resource', 'path' => 'Modules/{{module}}/Transformers/{{class}}Resource.php', 'tag' => 'Resource'],
        ['name'  =>  'store', 'path' => 'Modules/{{module}}/Http/Requests/{{class}}StoreRequest.php', 'tag' => 'Request'],
        ['name'  =>  'update', 'path' => 'Modules/{{module}}/Http/Requests/{{class}}UpdateRequest.php', 'tag' => 'Request'],
        ['name'  =>  'destory', 'path' => 'Modules/{{module}}/Http/Requests/{{class}}DestroyRequest.php', 'tag' => 'Request'],
    ]
];
