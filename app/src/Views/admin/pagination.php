<?php if ($totalPages > 1): ?>
<nav class="mt-3">
    <ul class="pagination justify-content-center">

        <!-- Previous -->
        <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
            <a class="page-link"
               href="?<?= http_build_query(array_merge($filters ?? [], ['page' => $currentPage - 1])) ?>">
                Previous
            </a>
        </li>

        <!-- Page numbers -->
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                <a class="page-link"
                   href="?<?= http_build_query(array_merge($filters ?? [], ['page' => $i])) ?>">
                    <?= $i ?>
                </a>
            </li>
        <?php endfor; ?>

        <!-- Next -->
        <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
            <a class="page-link"
               href="?<?= http_build_query(array_merge($filters ?? [], ['page' => $currentPage + 1])) ?>">
                Next
            </a>
        </li>

    </ul>
</nav>
<?php endif; ?>