<?php
require_once __DIR__ . '/../src/controllers/AlojamientoController.php';
$alojamientos = AlojamientoController::obtenerTodos();
include __DIR__ . '/../views/header.php';
?>

<main class="container">
  <section class="hero">
    <h1>Turismo San Luis Antioquia</h1>
    <p>Descubre fincas, glampings y ecohoteles únicos en San Luis.</p>
  </section>

  <section class="alojamientos">
    <h2>Alojamientos recomendados</h2>
    <div class="grid">
      <?php foreach ($alojamientos as $a): ?>
        <div class="card">
          <img src="assets/img/<?= htmlspecialchars($a['imagen']) ?>" alt="Foto alojamiento">
          <h3><?= htmlspecialchars($a['nombre']) ?></h3>
          <p><?= htmlspecialchars($a['tipo']) ?> · Desde $<?= htmlspecialchars($a['precio_min']) ?></p>
          <a href="detalle.php?id=<?= $a['id'] ?>">Ver detalles</a>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
</main>

<?php include __DIR__ . '/../views/footer.php'; ?>
