<h1 class="nombre-pagina">Panel de Administración</h1>

<?php
    include_once __DIR__ . '/../templates/barra.php';
?>
<h2>Buscar citas</h2>

<div class="busqueda">
    <form action="" class="formulario">
        <div class="campo">
            <label for="fecha">Fecha</label>
            <input 
                type="date"
                id="fecha"
                name="fecha"
                value="<?php echo $fecha; ?>"
            />
        </div>
    </form>
</div>
<?php 
    if (count($citas) === 0) {
        echo "<h2No hay citas en esta fecha</h2>";
    }
?>
<div id="citas-admin">
    <ul class="citas">
        <?php

        $idCita = 0;

            foreach ($citas as $key => $cita) {
                //Evita repetir la misma cita
                if($idCita !== $cita->id){
                    $total = 0;
                    
        ?>
            <li>
                <p>ID: <span><?php echo $cita->id; ?></span></p>
                <p>Hora <span><?php echo $cita->hora; ?></span></p>
                <p>Cliente: <span><?php echo $cita->cliente; ?></span></p>
                <p>Email: <span><?php echo $cita->email; ?></span></p>
                <p>Telefono: <span><?php echo $cita->telefono; ?></span></p>

                <h3>Servicios</h3>

            <?php 
                $idCita = $cita->id;
            } // Fin del if 
                $total += $cita->precio;
            ?>
                <p class="servicio"><?php echo $cita->servicio ." ". $cita->precio; ?></p>

        <?php 
            $actual = $cita->id;
            $proximo = $citas[$key + 1]->id ?? 0;
            if(esUltimo($actual, $proximo)){ ?>
            
                <p class="total">Total: <span><?php echo $total; ?></span></p>

        <?php 
            }
        } // Fin del foreach ?>
    </ul>
</div>

<?php 
    $script = "<script src='build/js/buscador.js'></script>"
?>