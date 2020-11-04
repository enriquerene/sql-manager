# SQL Manager
A query builder for SQL database.

## Table of Contents
- [Support](https://github.com/enriquerene/sql-manager#support)
- [Installation](https://github.com/enriquerene/sql-manager#installation)
- [Usage](https://github.com/enriquerene/sql-manager#usage)
	+ [Reading from Database](https://github.com/enriquerene/sql-manager#reading-from-database)
	+ [Inserting into Database](https://github.com/enriquerene/sql-manager#inserting-into-database)
	+ [Delete from Database](https://github.com/enriquerene/sql-manager#delete-from-database)
- [Plan](https://github.com/enriquerene/sql-manager#plan)
- [Contribute](https://github.com/enriquerene/sql-manager#contribute)

## <a name="support"></a> Support
If you need some help you can open an issue or get in touch by email ([contato@enriquerene.com.br](mailto:contato@enriquerene.com.br)).

## <a name="installation"></a> Installation
There are some installation ways. You can choose the best way for you.

### Composer (recommended)
This way requires [Composer](https://getcomposer.org):
```bash
$ composer require restful-template/sql-manager
```

### Git
Clone the repo into your project:
```bash
$ git clone https://github.com/enriquerene/sql-manager.git
```

### Zip
Dowload the package and uncpack it into your project:
[Dowload ZIP](https://github.com/enriquerene/sql-manager/archive/main.zip)

## <a name="usage"></a> Usage
Here we cover how to use properly SQLManager library. The `SQLManager` instance receives the database connection info and schema via associative array as following example:
```php
<?php
$dbInfo = [
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
```
Note that `$dbInfo[ "prefix" ]` is a table prefix in that databaase and `$dbInfo[ "tables" ][ "cars" ][ "prefix" ]` is the column prefix into cars table. In the above example we have a database named as `database_name`, access by user `database_user` using `database_password` as password. Inside this example schema, there is a table named as `db_cars` which has columns `car_id`, `car_model`, `car_brand` and `car_year`. The `$dbInfo[ "tables" ][ "cars" ][ "fields" ]` is an associative array where the keys are column names (without prefix) and values are the column types.

### <a name="reading-from-database"></a> Reading from Database
For reading data there is the `SQLManager::select` method:
```php
<?php
use RESTfulTemplate\SQLManager as SMan;

$db = new SMan( $dbInfo );
$tableName = "cars";
$data = $db->select( $tableName );
// $data contains an array of database result rows
```

The `SQLManager::select` accepts two more optional arguments:
```php
<?php
use RESTfulTemplate\SQLManager as SMan;

$db = new SMan( $dbInfo );
$tableName = "cars";
$data = $db->select( $tableName, [ "id, year" ] );
// $data contains the two requested columns (id and year) for each row in cars table

$data = $db->select( $tableName, [], [ "year" => "2010" ] );
// $data contains rows from cars table where column year has value 2010
```

### <a name="inserting-into-database"></a> Inserting into Database
To insert data into database we can use `SQLManager::insert` method:
```php
<?php
use RESTfulTemplate\SQLManager as SMan;

$body = [
	"model" => "new model",
	"brand" => "some brand",
	"year" => "2020"
];

$db = new SMan( $dbInfo );
$tableName = "cars";
$result = $db->insert( $tableName, $body );
// $result is true if everything ok and false if some error happens.
```

It's possible update existing row in database using `SQLManager::update` method:
```php
<?php
use RESTfulTemplate\SQLManager as SMan;

$body = [
	"model" => "correct model",
];
$carId = 1;

$db = new SMan( $dbInfo );
$tableName = "cars";
$result = $db->update( $tableName, $body, $carId );
// $result is true if everything ok and false if some error happens.
```

### <a name="delete-from-database"></a> Delete from Database
Remove data from database using `SQLManager::delete` method:
```php
<?php
use RESTfulTemplate\SQLManager as SMan;

$db = new SMan( $dbInfo );
$tableName = "cars";
$where = [ "id" => 1 ];
$result = $db->delete( $tableName, $where );
// $result is true if everything ok and false if some error happens.
```
**WARNING!!!** It's important to note that `$where` parameter in `SQLManager::update` and  `SQLManager::update` is optional. If  not given the methods will act in entire table.

## <a name="plan"></a> Plan
Currently support only MySQL. Future versions will support SQLite and PostgreSQL also.

## <a name="contribute"></a> Contribute
Do a pull request or send email to Support.
