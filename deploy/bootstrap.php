<?php
	use Doctrine\ORM\Tools\Setup;
	use Doctrine\ORM\EntityManager;
	date_default_timezone_set('America/Lima');
	require_once "vendor/autoload.php";
	$isDevMode = true;
	$config = Setup::createYAMLMetadataConfiguration(array(__DIR__ . "/config/yaml"), $isDevMode);
	$conn = array(
	'host' => 'dpg-cm23hri1hbls73bu4a30-a.oregon-postgres.render.com',

	'driver' => 'pdo_pgsql',
	'user' => 'bellot_db_lokl_user',
	'password' => 'UZ6zmzT6ivZoz6w9SLAZTfN4MQiRyqu2',
	'dbname' => 'bellot_db_lokl',
	'port' => '5432'
	);

	$entityManager = EntityManager::create($conn, $config);



