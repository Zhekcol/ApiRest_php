<?php 

$host = "localhost";
$user = "root";
$password = "";
$database = "apiusuarios";

$conexion = new mysqli($host, $user, $password, $database);

if ($conexion->connect_error) {
    die("Falló al conectarse con la base de datos: " . $conexion->connect_error);
}
//Establece el encabezado de respuesta para indicar que estará en formato JSON
header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD']; //Sea get, post, put o delete

$path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';

$searchId = explode('/', $path);

$id = ($path !== '/') ? end($searchId) : null;

switch ($method) {

    case 'GET':
        getUsers($conexion, $id);
        break;
    case 'POST':
        createUser($conexion);
        break;
    case 'PUT':
        updateUser($conexion, $id);
        break;
    case 'DELETE':
        deleteUser($conexion, $id);
        break;

    default:
        echo "Método no permitido";
        break;
}

function getUsers($conexion, $id){
    $sql = ($id === null) ? "SELECT * FROM usuarios" : "SELECT * FROM usuarios WHERE id=$id";
    $result = $conexion->query($sql);

    if ($result) {
        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode($data);
    }
}

function createUser($conexion){
    $data = json_decode(file_get_contents("php://input"), true);
    $names = $data['names'];//el data['valor'] debe llamarse igual a la variable que se le asigna este dato
    $lastnames = $data['lastnames'];
    $email = $data['email'];
    $phone = $data['phone'];
    $old = $data['old'];

    $sql = "INSERT INTO usuarios (nombres, apellidos, correo, telefono, edad) VALUES 
    ('$names', '$lastnames', '$email', '$phone', '$old')";

    $result = $conexion->query($sql);
    if ($result) {
        $data['id'] = $conexion->insert_id;
        echo json_encode($data);
    }else {
        echo json_encode(array('error' => 'Error al crear usuario'));
    }
}

function updateUser($conexion, $id){
    $data = json_decode(file_get_contents("php://input"), true);
    $names = $data['names'];//el data['valor'] debe llamarse igual a la variable que se le asigna este dato
    $lastnames = $data['lastnames'];
    $email = $data['email'];
    $phone = $data['phone'];
    $old = $data['old'];

    $sql = "UPDATE usuarios SET nombres = '$names', apellidos = '$lastnames', correo = '$email', telefono = '$phone', edad = '$old' WHERE id=$id";
    $result = $conexion->query($sql);

    if ($result) {
        echo json_encode(array("mensaje" => "Usuario actualizado con éxito."));
    }else {
        echo json_encode(array("error" => "Error al actualizar usuario."));
    }
}

function deleteUser($conexion, $id){
    echo "El id a eliminar es: " . $id;

    $sql = "DELETE FROM usuarios WHERE id=$id";
    $result = $conexion->query($sql);

    if ($result) {
        echo json_encode(array("mensaje" => "Usuario eliminado con éxito."));
    }else {
        echo json_encode(array("error" => "Error al eliminar usuario."));
    }
}

?>