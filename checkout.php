<?php
session_start(); // Start the session

require 'Config/config.php';
require 'Config/database.php';
$db = new Database();
$con = $db->conectar();

// Fetch products from the session or set to null
$productos = isset($_SESSION['carrito']['productos']) ? $_SESSION['carrito']['productos'] : null;

// Initialize $lista_carrito
$lista_carrito = array();

if ($productos != null) {
    foreach ($productos as $clave => $cantidad) {
        // Prepare the SQL statement with placeholders
        $sql = $con->prepare("SELECT id, nombre, precio, foto, descuento, ? AS cantidad FROM productos WHERE id=? AND activo=1");
        // Execute the SQL statement with actual values
        $sql->execute([$cantidad, $clave]);
        // Fetch the result as an associative array and add to the cart list
        $lista_carrito[] = $sql->fetch(PDO::FETCH_ASSOC);
    }
}

// Initialize $num_cart to avoid undefined variable error
$num_cart = isset($_SESSION['num_cart']) ? $_SESSION['num_cart'] : 0;

// Debugging output (remove this in production)
print_r($_SESSION);

// session_destroy(); // Uncomment to clear session for testing
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
            Carrito <span id="num_cart" class="badge bg-secondary"><?php echo $num_cart; ?></span>
        </a>
      </div>
    </div>
  </div>
</header>

<!--contenido-->
<main>
    <div class="container">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($lista_carrito == null): ?>
                        <tr>
                            <td colspan="5" class="text-center"><b>Lista vacía</b></td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $total = 0;
                        foreach ($lista_carrito as $producto):
                            $_id = $producto['id'];
                            $nombre = $producto['nombre'];
                            $precio = $producto['precio'];
                            $descuento = $producto['descuento'];
                            $cantidad = $producto['cantidad'];
                            $precio_desc = $precio - (($precio * $descuento) / 100);
                            $subtotal = $cantidad * $precio_desc;
                            $total += $subtotal;
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($nombre); ?></td>
                            <td><?php echo MONEDA . number_format($precio_desc, 2, '.', ','); ?></td>
                            <td>
                                <input type="number" min="1" max="10" step="1" value="<?php echo 
                                $cantidad; ?>" size="5" id="cantidad_<?php echo $_id; ?>" 
                                onchange="actualizaCantidad(this.value, <?php echo $_id; ?>)">
                            </td>
                            <td>
                                <div id="subtotal_<?php echo $_id; ?>" name="subtotal[]"><?php echo MONEDA . number_format($subtotal, 2, '.', ','); ?></div>
                            </td>
                            <td>
                                <a href="#" id="eliminar" class="btn btn-warning btn-sm" data-bs-id="<?php echo $_id; ?>" data-bs-toggle="modal" data-bs-target="#eliminaModal">Eliminar</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="3"></td>
                            <td colspan="2">
                                <p class="h3" id="total"><?php echo MONEDA . number_format($total, 2, '.', ','); ?></p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="row">
            <div class="col-md-5 offset-md-7 d-grid gap-2">
                <button class="btn btn-primary btn-lg">Realizar pago</button>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

<script>
function actualizaCantidad(cantidad, id) {
    let url = 'clases/actualizar_carrito.php';
    let formData = new FormData();
    formData.append('action', 'agregar');
    formData.append('id', id);
    formData.append('cantidad', cantidad);

    fetch(url, {
        method: 'POST',
        body: formData,
        mode: 'cors'
    })
    .then(response => response.json())
    .then(data => {
        if (data.ok) {
            let divsubtotal = document.getElementById('subtotal_' + id);
            divsubtotal.innerHTML = data.sub;

            let total = 0.00
            let list = document.getElementsByName('subtotal_[]')

            for(let i = 0; i < list.length; i++){
              total += parseFloat(list[i].innerHTML.replace(/[$,]/g, ''))

            }

                total = new Intl.NumberFormat('en.US', {
                  minimumFractionDigits: 2
           }).format(total)
           document.getElementById('total').innerHTML = '<?php echo MONEDA ?>' + total
        }

      })
    }

           
</script>
</body>
</html>
