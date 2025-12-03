<?php
session_start();
require_once "includes/db.php";

$mensaje_enviado = false;
$errores = [];

// Procesar formulario de contacto
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $asunto = trim($_POST['asunto'] ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');

    // Validaciones
    if ($nombre === '') {
        $errores[] = "El nombre es obligatorio.";
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "Ingresa un correo electrónico válido.";
    }
    if ($mensaje === '') {
        $errores[] = "El mensaje es obligatorio.";
    }

    if (empty($errores)) {
        // Aquí podrías guardar en BD o enviar por email
        // Por ahora solo mostramos mensaje de éxito
        $mensaje_enviado = true;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contacto - Mi Tienda</title>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <style>
        .contact-icon {
            width: 60px;
            height: 60px;
            background: #212529;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }
        .contact-icon i {
            font-size: 1.5rem;
            color: white;
        }
        .contact-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .contact-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .map-container {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .social-link {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            color: #212529;
            margin: 0 5px;
            transition: all 0.3s ease;
        }
        .social-link:hover {
            background: #212529;
            color: white;
            transform: scale(1.1);
        }
    </style>
</head>
<body>

<?php include "includes/navbar.php"; ?>

<!-- Header -->
<header class="py-5 bg-dark">
    <div class="container px-4 px-lg-5">
        <div class="text-center text-white">
            <h1 class="fw-bolder">Contáctanos</h1>
            <p class="lead mb-0">Estamos aquí para ayudarte</p>
        </div>
    </div>
</header>

<section class="py-5">
    <div class="container px-4 px-lg-5">
        
        <!-- Tarjetas de información -->
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card contact-card h-100 text-center p-4 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="contact-icon mx-auto">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <h5 class="fw-bold">Ubicación</h5>
                        <p class="text-muted mb-0">
                            Av. Insurgentes Sur 1234<br>
                            Col. Del Valle, CDMX<br>
                            CP 03100, México
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card contact-card h-100 text-center p-4 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="contact-icon mx-auto">
                            <i class="bi bi-telephone-fill"></i>
                        </div>
                        <h5 class="fw-bold">Teléfono</h5>
                        <p class="text-muted mb-1">
                            <a href="tel:+525512345678" class="text-decoration-none text-muted">
                                +52 55 1234 5678
                            </a>
                        </p>
                        <p class="text-muted mb-0">
                            <a href="https://wa.me/525512345678" class="text-decoration-none text-success">
                                <i class="bi bi-whatsapp"></i> WhatsApp
                            </a>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card contact-card h-100 text-center p-4 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="contact-icon mx-auto">
                            <i class="bi bi-envelope-fill"></i>
                        </div>
                        <h5 class="fw-bold">Email</h5>
                        <p class="text-muted mb-1">
                            <a href="mailto:ventas@mitienda.com" class="text-decoration-none text-muted">
                                ventas@mitienda.com
                            </a>
                        </p>
                        <p class="text-muted mb-0">
                            <a href="mailto:soporte@mitienda.com" class="text-decoration-none text-muted">
                                soporte@mitienda.com
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-5">
            <!-- Formulario de contacto -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="bi bi-chat-dots-fill me-2"></i>Envíanos un mensaje</h5>
                    </div>
                    <div class="card-body p-4">
                        
                        <?php if ($mensaje_enviado): ?>
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                <strong>¡Mensaje enviado!</strong> Nos pondremos en contacto contigo pronto.
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($errores)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errores as $e): ?>
                                        <li><?php echo htmlspecialchars($e); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form method="post">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nombre completo *</label>
                                    <input type="text" name="nombre" class="form-control" required
                                           value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>"
                                           placeholder="Tu nombre">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Correo electrónico *</label>
                                    <input type="email" name="email" class="form-control" required
                                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                           placeholder="tu@email.com">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Teléfono</label>
                                    <input type="tel" name="telefono" class="form-control"
                                           value="<?php echo htmlspecialchars($_POST['telefono'] ?? ''); ?>"
                                           placeholder="55 1234 5678">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Asunto</label>
                                    <select name="asunto" class="form-select">
                                        <option value="consulta">Consulta general</option>
                                        <option value="pedido">Información de pedido</option>
                                        <option value="devolucion">Devoluciones</option>
                                        <option value="otro">Otro</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Mensaje *</label>
                                    <textarea name="mensaje" class="form-control" rows="5" required
                                              placeholder="¿En qué podemos ayudarte?"><?php echo htmlspecialchars($_POST['mensaje'] ?? ''); ?></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-dark w-100">
                                        <i class="bi bi-send-fill me-2"></i>Enviar mensaje
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Información adicional y mapa -->
            <div class="col-lg-6">
                <!-- Horarios -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="bi bi-clock-fill me-2"></i>Horario de atención</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Lunes - Viernes</span>
                            <strong>9:00 AM - 7:00 PM</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Sábados</span>
                            <strong>10:00 AM - 4:00 PM</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Domingos</span>
                            <strong class="text-muted">Cerrado</strong>
                        </div>
                    </div>
                </div>

                <!-- Mapa -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="bi bi-map-fill me-2"></i>Nuestra ubicación</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="map-container">
                            <iframe 
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3763.5022893033!2d-99.17283032496756!3d19.39178818187898!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x85d1ff7c60000001%3A0x1fda4b9e3afe76c8!2sAv.%20Insurgentes%20Sur%2C%20Ciudad%20de%20M%C3%A9xico%2C%20CDMX!5e0!3m2!1ses!2smx!4v1700000000000!5m2!1ses!2smx"
                                width="100%" 
                                height="250" 
                                style="border:0;" 
                                allowfullscreen="" 
                                loading="lazy">
                            </iframe>
                        </div>
                    </div>
                </div>

                <!-- Redes sociales -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="bi bi-share-fill me-2"></i>Síguenos</h5>
                    </div>
                    <div class="card-body text-center">
                        <a href="#" class="social-link" title="Facebook">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="#" class="social-link" title="Instagram">
                            <i class="bi bi-instagram"></i>
                        </a>
                        <a href="#" class="social-link" title="Twitter">
                            <i class="bi bi-twitter-x"></i>
                        </a>
                        <a href="#" class="social-link" title="TikTok">
                            <i class="bi bi-tiktok"></i>
                        </a>
                        <a href="#" class="social-link" title="YouTube">
                            <i class="bi bi-youtube"></i>
                        </a>
                        <p class="text-muted mt-3 mb-0">
                            <small>@mitienda en todas las plataformas</small>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ rápido -->
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="fw-bold text-center mb-4">Preguntas frecuentes</h3>
            </div>
            <div class="col-md-6">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                ¿Cuánto tarda el envío?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Los envíos dentro de CDMX tardan 1-2 días hábiles. Para el resto del país, de 3-5 días hábiles.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                ¿Puedo devolver un producto?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Sí, tienes 30 días para devoluciones. El producto debe estar sin usar y con etiquetas originales.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="accordion" id="faqAccordion2">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                ¿Los productos son originales?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion2">
                            <div class="accordion-body">
                                Sí, todos nuestros productos son 100% originales con garantía de autenticidad.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                ¿Qué métodos de pago aceptan?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion2">
                            <div class="accordion-body">
                                Aceptamos tarjetas de crédito/débito, PayPal, transferencia bancaria y pago en OXXO.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<footer class="py-5 bg-dark">
    <div class="container">
        <p class="m-0 text-center text-white">&copy; Mi Tienda <?php echo date('Y'); ?></p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/scripts.js"></script>
</body>
</html>
