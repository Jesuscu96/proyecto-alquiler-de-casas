<?php
// Footer para todas las páginas
$year = date('Y');
?>

<footer class="footer mt-5">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <p class="footer-text">
                    <i class="bi bi-c-circle"></i> <?php echo $year; ?> CasasApp. Todos los derechos reservados.
                </p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="footer-text">
                    Desarrollado con <i class="bi bi-heart-fill text-danger"></i> por CasasApp Team
                </p>
            </div>
        </div>
    </div>
</footer>

<style>
    .footer {
        background: linear-gradient(90deg, var(--dark) 0%, var(--primary) 100%);
        color: white;
        padding: 2rem 0;
        margin-top: auto;
        border-top: 2px solid var(--secondary);
    }

    .footer-text {
        margin-bottom: 0;
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .footer-text:hover {
        opacity: 1;
        transition: opacity 0.3s ease;
    }

    /* Asegurar que el footer esté al final */
    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    main {
        flex: 1;
    }
</style>