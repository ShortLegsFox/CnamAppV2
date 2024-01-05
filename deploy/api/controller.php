<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Entity\Utilisateur;

	function optionsCatalogue (Request $request, Response $response, $args) {
	    
	    // Evite que le front demande une confirmation à chaque modification
	    $response = $response->withHeader("Access-Control-Max-Age", 600);
	    
	    return addHeaders ($response);
	}

	function hello(Request $request, Response $response, $args) {
	    $array = [];
	    $array ["nom"] = $args ['name'];
	    $response->getBody()->write(json_encode ($array));
	    return $response;
	}
	
	function  getSearchCalatogue (Request $request, Response $response, $args) {
	    $filtre = $args['filtre'];
	    $flux = '[{"titre":"linux","ref":"001","prix":"20"},{"titre":"java","ref":"002","prix":"21"},{"titre":"windows","ref":"003","prix":"22"},{"titre":"angular","ref":"004","prix":"23"},{"titre":"unix","ref":"005","prix":"25"},{"titre":"javascript","ref":"006","prix":"19"},{"titre":"html","ref":"007","prix":"15"},{"titre":"css","ref":"008","prix":"10"}]';
	   
	    if ($filtre) {
	      $data = json_decode($flux, true); 
	    	
		$res = array_filter($data, function($obj) use ($filtre)
		{ 
		    return strpos($obj["titre"], $filtre) !== false;
		});
		$response->getBody()->write(json_encode(array_values($res)));
	    } else {
		 $response->getBody()->write($flux);
	    }

	    return addHeaders ($response);
	}

	// API Nécessitant un Jwt valide
	function getCatalogue (Request $request, Response $response, $args) {
	    $flux = '[{"titre":"linux","ref":"001","prix":"20"},{"titre":"java","ref":"002","prix":"21"},{"titre":"windows","ref":"003","prix":"22"},{"titre":"angular","ref":"004","prix":"23"},{"titre":"unix","ref":"005","prix":"25"},{"titre":"javascript","ref":"006","prix":"19"},{"titre":"html","ref":"007","prix":"15"},{"titre":"css","ref":"008","prix":"10"}]';
	    $data = json_decode($flux, true); 
	    
	    $response->getBody()->write(json_encode($data));
	    
	    return addHeaders ($response);
	}

	function optionsUtilisateur (Request $request, Response $response, $args) {
	    
	    // Evite que le front demande une confirmation à chaque modification
	    $response = $response->withHeader("Access-Control-Max-Age", 600);
	    
	    return addHeaders ($response);
	}

	function createUser (Request $request, Response $response, $args) {
        global $entityManager;

        $data = $request->getParsedBody();
        $err = false;

        /*
        if (!isset($data['username']) || !preg_match("/^[a-zA-Z0-9]+$/", $data['username'])) {
          $err = true;
        }

        if (!isset($data['lastname']) || !preg_match("/^[a-zA-Z]+$/", $data['lastname'])) {
          $err = true;
        }

        if (!isset($data['firstname']) || !preg_match("/^[a-zA-Z]+$/", $data['firstname'])) {
          $err = true;
        }

        if (!isset($data['address']) || !preg_match("/^[a-zA-Z0-9\s]+$/", $data['address'])) {
          $err = true;
        }

        if (!isset($data['postal']) || !preg_match("^\d{5}$", $data['postal'])) {
          $err = true;
        }

        if (!isset($data['city']) || !preg_match("/^[a-zA-Z]+$/", $data['city'])) {
          $err = true;
        }

        if (!isset($data['gender']) || !in_array($data['gender'], ['male', 'female', 'other'])) {
          $err = true;
        }

        if (!isset($data['phone']) || !preg_match("^\d{10}$", $data['phone'])) {
          $err = true;
        }

        if (!isset($data['mail']) || !preg_match("^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$", $data['mail'])) {
          $err = true;
        }

        if (!isset($data['password']) || !preg_match("/^[a-zA-Z0-9]+$/", $data['password'])) {
          $err = true;
        }*/

        if (!$err) {
            $utilisateur = new Utilisateur();
            $utilisateur->setPrenom($data['firstname']);
            $utilisateur->setNom($data['lastname']);
            $utilisateur->setAdresse($data['address']);
            $utilisateur->setCodepostal($data['postal']);
            $utilisateur->setVille($data['city']);
            $utilisateur->setEmail($data['mail']);
            $utilisateur->setSexe($data['gender']);
            $utilisateur->setLogin($data['username']);
            $utilisateur->setPassword($data['password']);

            $entityManager->persist($utilisateur);
            $entityManager->flush();

            $response = addHeaders ($response);
            $response = createJwT ($response);
            $response->getBody()->write(json_encode(['status' => 'success']));
        }
        else {
            $response = $response->withStatus(500);
        }
        return addHeaders ($response);
    }

	// API Nécessitant un Jwt valide
	function getUtilisateur (Request $request, Response $response, $args) {
	    global $entityManager;
	    
	    $payload = getJWTToken($request);
	    $login  = $payload->userid;
	    
	    $utilisateurRepository = $entityManager->getRepository('Utilisateurs');
	    $utilisateur = $utilisateurRepository->findOneBy(array('login' => $login));
	    if ($utilisateur) {
		$data = array('nom' => $utilisateur->getNom(), 'prenom' => $utilisateur->getPrenom());
		$response = addHeaders ($response);
		$response = createJwT ($response);
		$response->getBody()->write(json_encode($data));
	    } else {
		$response = $response->withStatus(404);
	    }

	    return addHeaders ($response);
	}

	// APi d'authentification générant un JWT
	function postLogin (Request $request, Response $response, $args) {   
	    global $entityManager;
	    $err=false;
	    $body = $request->getParsedBody();
	    $login = $body ['login'] ?? "";
	    $pass = $body ['password'] ?? "";

	    if (!preg_match("/[a-zA-Z0-9]{1,20}/",$login))   {
		$err = true;
	    }
	    if (!preg_match("/[a-zA-Z0-9]{1,20}/",$pass))  {
		$err=true;
	    }
	    if (!$err) {
		$utilisateurRepository = $entityManager->getRepository('Utilisateurs');
		$utilisateur = $utilisateurRepository->findOneBy(array('login' => $login, 'password' => $pass));
		if ($utilisateur and $login == $utilisateur->getLogin() and $pass == $utilisateur->getPassword()) {
		    $response = addHeaders ($response);
		    $response = createJwT ($response);
		    $data = array('nom' => $utilisateur->getNom(), 'prenom' => $utilisateur->getPrenom());
		    $response->getBody()->write(json_encode($data));
		} else {          
		    $response = $response->withStatus(403);
		}
	    } else {
		$response = $response->withStatus(500);
	    }

	    return addHeaders ($response);
	}

