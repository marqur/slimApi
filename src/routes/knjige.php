<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;



$app = new \Slim\App;



//Allow requests from anywhere

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});
$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});

$app->add(new \Slim\Middleware\HttpBasicAuthentication([
    "path" => ["/api/knjige/delete/{id}","/api/knjige/update/{id}"], 
    "realm" => "Protected",
    "users" => [
        "root" => "t00r",
        "user" => "passw0rd"
    ]
]));





// Get Knjige - Sve knjige

$app->get('/api/knjige', function(Request $request, Response $response){
    $sql = "SELECT * FROM knjige";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->query($sql);
        $knjige = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        //Generisanje JSON objekata
        $json_knjige = json_encode($knjige);
        echo($json_knjige);



    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});


// Get Knjiga - Individualno

$app->get('/api/knjige/{id}', function(Request $request, Response $response){
	$id = $request->getAttribute('id');

    $sql = "SELECT * FROM knjige WHERE id = $id ";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->query($sql);
        $knjiga = $stmt->fetch(PDO::FETCH_OBJ);
        $db = null;
        //Generisanje JSON objekata
        $json_knjiga = json_encode($knjiga);
        echo($json_knjiga);

    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});


// Add - Dodavanje Knjige

$app->post('/api/knjige/add', function(Request $request, Response $response){

    $naziv = $request->getParam('naziv');
    $autor = $request->getParam('autor');
    $god_izdavanja = $request->getParam('god_izdavanja');
    $jezik = $request->getParam('jezik');
    $org_jezik = $request->getParam('org_jezik');
    $sql = "INSERT INTO knjige (naziv,autor,god_izdavanja,jezik,org_jezik) VALUES
    (:naziv,:autor,:god_izdavanja,:jezik,:org_jezik)";
    try{

        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':naziv', $naziv);
        $stmt->bindParam(':autor',  $autor);
        $stmt->bindParam(':god_izdavanja', $god_izdavanja);
        $stmt->bindParam(':jezik', $jezik);
        $stmt->bindParam(':org_jezik', $org_jezik);
        $stmt->execute();
        echo '{"notice": {"text": "Knjiga je dodata"}';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

// Update - Izmena podataka o knjizi
$app->put('/api/knjige/update/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $naziv = $request->getParam('naziv');
    $autor = $request->getParam('autor');
    $god_izdavanja = $request->getParam('god_izdavanja');
    $jezik = $request->getParam('jezik');
    $org_jezik = $request->getParam('org_jezik');
    $sql = "UPDATE knjige SET
				naziv 	= :naziv,
				autor 	= :autor,
                god_izdavanja		= :god_izdavanja,
                jezik		= :jezik,
                org_jezik 	= :org_jezik
			WHERE id = $id";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':naziv', $naziv);
        $stmt->bindParam(':autor',  $autor);
        $stmt->bindParam(':god_izdavanja',      $god_izdavanja);
        $stmt->bindParam(':jezik',      $jezik);
        $stmt->bindParam(':org_jezik',    $org_jezik);
        $stmt->execute();
        echo '{"notice": {"text": "Knjiga je uspešno izmenjena."}';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});


// Delete - Brisanje knjige
$app->delete('/api/knjige/delete/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "DELETE FROM knjige WHERE id = $id";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $db = null;
        echo '{"notice": {"text": "Knjiga je uspešno izbrisana izbrisana."}';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});