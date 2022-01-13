<?php

declare(strict_types=1);


use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {
    

    $app->group('/currency', function(RouteCollectorProxy $view) {

       /** 
        * Gets one currency sysmbol and price.
        *
        * @param Request $request
        *
        * @param Response $response
        *
        * @param array $args
        *
        * @return Bool
        *
        */ 
        $view->get('/single/{search}', function (Request $request, Response $response, array $args) {
            $cSql = "SELECT * FROM currencies WHERE id = :search or symbol = :search";

            try {
                $oDatabase = new Database();
                $conn = $oDatabase->connect();

                $stmt = $conn->prepare($cSql);
                $stmt->bindParam(':search', $args['search']);

                $stmt->execute();
                $results = $stmt->fetch(PDO::FETCH_OBJ);
                $oDatabase = null;
                $response->getBody()->write(json_encode($results));
                return $response->withHeader('content-type', 'application/json')
                ->withStatus(200);
            } catch (PDOException $e) {
                $aError = array(
                    'message' => $e->getMessage()
                );
            
                $response->getBody()->write(json_encode($aError));
                return $response->withHeader('content-type', 'application/json')
                ->withStatus(500);
            }
        });


        /** 
        * Gets all currencies
        *
        * @param Request $request
        *
        * @param Response $response
        *
        * @param array $args
        *
        * @return Bool
        *
        */ 
        $view->get('/all', function (Request $request, Response $response, array $args) {
            $cSql = "SELECT * FROM currencies";

            try {
                $oDatabase = new Database();
                $conn = $oDatabase->connect();

                $stmt = $conn->query($cSql);
                $aCurrencies = $stmt->fetchAll(PDO::FETCH_OBJ);

                $oDatabase = null;
                $response->getBody()->write(json_encode($aCurrencies));
                return $response->withHeader('content-type', 'application/json')
                ->withStatus(200);
            } catch (PDOException $e) {
                $aError = array(
                    'message' => $e->getMessage()
                );
            
                $response->getBody()->write(json_encode($aError));
                return $response->withHeader('content-type', 'application/json')
                ->withStatus(500);
            }
        });




        /** 
        * View one Currency pair with Price.
        *
        * @param Request $request
        *
        * @param Response $response
        *
        * @param array $args
        *
        * @return Json
        *
        */
        $view->get('/pair/{currency1}/{currency2}', function (Request $request, Response $response, array $args) {
            $cSql = "SELECT * FROM currencies WHERE symbol = :symbol1 or symbol = :symbol2 GROUP BY symbol";

            try {
                $oDatabase = new Database();
                $conn = $oDatabase->connect();

                $stmt = $conn->prepare($cSql);
                $stmt->bindParam(':symbol1', $args['currency1']);
                $stmt->bindParam(':symbol2', $args['currency2']);
                $stmt->execute();

                $aCurrencies = $stmt->fetchAll();
                $ret = array(); 
                if (count($aCurrencies) != 2) {
                    $ret['message'] = "Could not find the currencies you passed.";
                } else {
                    if ($aCurrencies[0]['hierarchy'] < $aCurrencies[1]['hierarchy']) {
                        $ret['pair'] = "{$aCurrencies[0]['symbol']}|{$aCurrencies[1]['symbol']}";
                        $ret['price'] = $aCurrencies[0]['price']/$aCurrencies[1]['price'];
                    } else {
                        $ret['pair'] = "{$aCurrencies[1]['symbol']}|{$aCurrencies[0]['symbol']}";
                        $ret['price'] = round($aCurrencies[1]['price']/$aCurrencies[0]['price'], 5);
                    }
                }

                $oDatabase = null;
                $response->getBody()->write(json_encode($ret));
                return $response->withHeader('content-type', 'application/json')
                ->withStatus(200);
            } catch (PDOException $e) {
                $aError = array(
                    'message' => $e->getMessage()
                );
            
                $response->getBody()->write(json_encode($aError));
                return $response->withHeader('content-type', 'application/json')
                ->withStatus(500);
            }
        });



        /** 
        * Updates one currencies price
        *
        * @param Request $request
        *
        * @param Response $response
        *
        * @param array $args
        *
        * @return Bool
        *
        */        
        $view->put('/update/{search}/{price}', function (Request $request, Response $response, array $args) {
            $cSql = "UPDATE currencies SET price = :price WHERE symbol = :search";

            try {
                $oDatabase = new Database();
                $conn = $oDatabase->connect();

                $stmt = $conn->prepare($cSql);
                $stmt->bindParam(':price', $args['price']);
                $stmt->bindParam(':search', $args['search']);
                $results = $stmt->execute();
                
                $oDatabase = null;
                $response->getBody()->write(json_encode($results));
                return $response->withHeader('content-type', 'application/json')
                ->withStatus(200);
            } catch (PDOException $e) {
                $aError = array(
                    'message' => $e->getMessage()
                );
            
                $response->getBody()->write(json_encode($aError));
                return $response->withHeader('content-type', 'application/json')
                ->withStatus(500);
            }
        });


       /** 
        * Deletes one Currency.
        *
        * @param Request $request
        *
        * @param Response $response
        *
        * @param array $args
        *
        * @return Bool
        *
        */ 
        $view->delete('/delete',  function(Request $request, Response $response, array $args) {
            $cSql = "DELETE FROM currencies WHERE symbol = :symbol";
            $data = $request->getParsedBody();
            $cSymbol = $data['symbol'];

            try {
                $oDatabase = new Database();
                $conn = $oDatabase->connect();

                $stmt = $conn->prepare($cSql);
                $stmt->bindParam(':symbol', $cSymbol, PDO::PARAM_STR);

                $results = $stmt->execute();
                $oDatabase = null;
                $response->getBody()->write(json_encode($results));
                return $response->withHeader('content-type', 'application/json')
                ->withStatus(200);
            } catch (PDOException $e) {
                $aError = array(
                    'message' => $e->getMessage()
                );
            
                $response->getBody()->write(json_encode($aError));
                return $response->withHeader('content-type', 'application/json')
                ->withStatus(500);
            }
        });



        $view->add('/add',  function(Request $request, Response $response, array $args) {
            $cSql = "INSERT INTO currencies (symbol, hierarchy, price ) VALUES (:symbol, :hierarchy, :price)";
            $data = $request->getParsedBody();
            $cSymbol = $data['symbol'];
            $cHierarchy = $data['hierarchy'];
            $cPrice = $data['price'];

            try {
                $oDatabase = new Database();
                $conn = $oDatabase->connect();

                $stmt = $conn->prepare($cSql);
                $stmt->bindParam(':symbol', $cSymbol, PDO::PARAM_STR);
                $stmt->bindParam(':symbol', $cHierarchy, PDO::PARAM_STR);
                $stmt->bindParam(':price', $cPrice, PDO::PARAM_STR);

                $results = $stmt->execute();
                $oDatabase = null;
                $response->getBody()->write(json_encode($results));
                return $response->withHeader('content-type', 'application/json')
                ->withStatus(200);
            } catch (PDOException $e) {
                $aError = array(
                    'message' => $e->getMessage()
                );
            
                $response->getBody()->write(json_encode($aError));
                return $response->withHeader('content-type', 'application/json')
                ->withStatus(500);
            }
        });



    });
}


?>