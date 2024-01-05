<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

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

        if (!isset($data['username']) || !preg_match("/^[a-zA-Z0-9]+$/", $data['username'])) {
          return $response->getBody()->write("Invalid username");
        }

        if (!isset($data['lastname']) || !preg_match("/^[a-zA-Z]+$/", $data['lastname'])) {
          return $response->getBody()->write("Invalid lastname");
        }

        if (!isset($data['firstname']) || !preg_match("/^[a-zA-Z]+$/", $data['firstname'])) {
          return $response->getBody()->write("Invalid firstname");
        }

        if (!isset($data['address']) || !preg_match("/^[a-zA-Z0-9\s]+$/", $data['address'])) {
          return $response->getBody()->write("Invalid address");
        }

        if (!isset($data['postal']) || !preg_match("/^(?:0[1-9]|[1-8]\d|9[0-8])\d{3}$/i", $data['postal'])) {
          return $response->getBody()->write("Invalid postal");
        }

        if (!isset($data['city']) || !preg_match("/^[a-zA-Z]+$/", $data['city'])) {
          return $response->getBody()->write("Invalid gender");
        }

        if (!isset($data['gender']) || !in_array($data['gender'], ['male', 'female', 'other'])) {
          return $response->getBody()->write("Invalid gender");
        }

        if (!isset($data['phone']) || !preg_match("^((\+|00)[-1])?[\s.-]?(0[\s.-]??[1-9])([\s.-]?\d{2}){4}\d$", $data['phone'])) {
          return $response->getBody()->write("Invalid phone number");
        }

        if (!isset($data['mail']) || !preg_match("^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$", $data['mail'])) {
          return $response->getBody()->write("Invalid mail");
        }

        if (!isset($data['password']) || !preg_match("/^[a-zA-Z0-9]+$/", $data['password'])) {
          return $response->getBody()->write("Invalid password");
        }

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

