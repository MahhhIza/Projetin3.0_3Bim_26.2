<?php if ($tipoUsuario === "admin"): ?>

    <div class="card shadow-sm border-0 mt-5">
        <div class="card-body">

            <h3 class="fw-bold mb-3">
                ⚙️ Gerenciamento
            </h3>

            <p class="text-muted">
                Gerencie os produtos e os usuários da Art&Co.
            </p>

            <div class="d-flex flex-wrap gap-2">

                <a
                    href="admin/produtos/index.php"
                    class="btn btn-primary"
                >
                    🎨 Gerenciar produtos
                </a>

                <a
                    href="admin/usuarios/index.php"
                    class="btn btn-outline-secondary"
                >
                    👥 Gerenciar usuários
                </a>

            </div>

        </div>
    </div>

<?php endif; ?>