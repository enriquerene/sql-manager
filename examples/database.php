<?php

$database = [
	"host" => "localhost",
	"name" => "database_name",
	"charset" => "utf8",
	"user" => "database_user",
	"password" => "database_password",
	"prefix" => "db_",
	"tables" => [
		"cars" => [
			"prefix" => "car_",
			"primary" => "id",
			"unique" => [ "name" ],
			"fields" => [
				"id" => "int(11)",
				"model" => "varchar(30)",
				"brand" => "varchar(30)",
				"year" => "char(4)"
			]
		]
	]
];

return [
    "settings" => [
        "displayErrorDetails" => true,
        "addContentLengthHeader" => true,
        "database" => $database
    ]
];

