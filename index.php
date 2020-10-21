<?php
/* Settings in header-info: 
1. JSONdata is the output
2. The restApi will be able to reach from alla domains, asterix* = all
3. Settings so alla HTTP-methods is avalible
4. Set so all headers is taken*/ 
header('Content-Type: application/json; charset=utf8mb4');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Origin, Access-Control-Allow-Methods, Authorization, X-Requested-With');
include_once('includes/config.php');
/* Check wich HTTP_method is used and save it in a variable*/
$HTTP_method = $_SERVER['REQUEST_METHOD'];

/* Check if a id is send in via GET-method and save it in a variable*/

$table = null;
$id = 0;
if (isset($_GET['table'])) {
    $table = $_GET['table'];
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
}

$post = new PostModel();
$control = new PostController();
/* Kollar vilken HTTP-metod som används och startar respektive metod*/
switch ($HTTP_method) {
    case 'GET':
            // Hämtar alla kurser
            $result = $control->getDataFromTables($table, $id);
        
        // Kollar om resultatet innehåller något och skapar HTTP-responsecode
        if(sizeof($result) > 0) {
            if(isset($result['message'])) {
            http_response_code(404);// Not found
            } else {
                http_response_code(200); // Succes
            }
        } else {
            http_response_code(404); // Not found
            $result = ["message" => "Ingenting hittat med angivna variabler, läs readme filen och försök igen!"];
        }
         
    break;
    case 'POST':
        $case = "new";
        $result = $control->whichTableInput($case);
        /*if($result) {
            //var_dump($result);
            http_response_code(201); // Kurs skapad
            echo json_encode($result, JSON_PRETTY_PRINT);
        }*/
    break;
    case 'PUT':
        $case = "update";
        $result = $control->whichTableInput($case);
    break;
    case 'DELETE':
        $case = "delete";
        $result = $control->whichTableInput($case);
    break;

    // $this->close();

}
// Create a json file and echo out
echo json_encode($result, JSON_PRETTY_PRINT);
