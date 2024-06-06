<?php
//session_start(); // Start the session

require 'Config/config.php';
require 'Config/database.php';
$db = new Database();
$con = $db->conectar();

$producto = isset($_SESSION['carrito']['productos']) ? $_SESSION['carrito']['productos'] : null;


print_r($_SESSION);

$lista_carrito = array();

if($productos != null){
    foreach($productos as $clave => $cantidad) {

            $sql = $con->prepare("SELECT id, nombre, precio, foto, descuento, $cantidad AS cantidad  FROM productos WHERE  id=? AND activo=1");
            $sql->execute([$clave]);
            $lista_carrito[] = $sql->fetch(PDO::FETCH_ASSOC);

    }

}

// Initialize $num_cart to avoid undefined variable error
$num_cart = isset($_SESSION['num_cart']) ? $_SESSION['num_cart'] : 0;

session_destroy();
print_r($_SESSION);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" 
    rel="stylesheet" 
    integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" 
    crossorigin="anonymous">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

<header>
  <div class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <a href="#" class="navbar-brand">
        <strong>Automotriz Refaccionaria 5 carrera</strong>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarHeader">
         <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
                <a href="#" class="nav-link active">Catalogo</a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">Contacto</a>
            </li>
         </ul>
         <a href="carrito.php" class="btn btn-primary">
            Carrito <span id="num_cart" class="badge bg-secondary"><?php  echo $num_cart; ?></span>
        </a>
      </div>
    </div>
  </div>
</header>

<!--contenido-->
<main>
    <div class="container">
      
      
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>


<script>

function addProducto(id, token){
    let url = 'clases/carrito.php'
    let formData = new FormData()
    formData.append('id', id)
    formData.append('token', token)

    fetch(url, {
        method: 'POST',
        body: formData,
        mode: 'cors'
    }).then(response => response.json())
    .then(data => {
        if(data.ok){  
            let elemento = document.getElementById("num_cart")
            elemento.innerHTML = data.numero
        }
})
}
    

 </script>







</body>
</html>
