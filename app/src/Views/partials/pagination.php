<?php 
$currentPage = (int)($currentPage ?? 1);
$totalPages = (int)($totalPages ?? 1);
$totalResults = $totalResults ?? 0;
$baseUrl = $baseUrl ?? strtok($_SERVER["REQUEST_URI"], '?'); //striping away any existing parameters and getting "clean slate" to build new links.
$baseParams = $queryParams ?? $_GET; // taking all current URL parameters (like search or sort), so that when changing pages, i don't "forget" what i was searching for

// determines the theme: 'peach' (default for dashboard) or 'dark' (for admin)
$theme = $paginationTheme ?? 'peach';
$containerClass = ($theme === 'dark') ? 'text-muted' : 'text-peach opacity-75';
$linkClass = ($theme === 'dark') ? 'custom-pagination-dark' : 'custom-pagination-peach';
?>


<?php if ($totalPages > 1): ?> <!-- if there is only 1 page, don't show pagination at all -->
    <div class="d-flex justify-content-between align-items-center mt-3 px-3 pb-3">
        <div class="<?= $containerClass ?> small">
            Showing page <strong><?= $currentPage ?></strong> of <strong><?= $totalPages ?></strong> 
            <?php if ($totalResults > 0): ?>
                (<?= $totalResults ?> total)
            <?php endif; ?>
        </div>

        <nav aria-label="Page navigation">
            <ul class="pagination pagination-sm mb-0">
                
                <?php 
                    $pParams = $baseParams; // coping current filters (search, sort, etc)
                    $pParams['page'] = $currentPage - 1; // changing only the page number
                    $prevUrl = $baseUrl . "?" . http_build_query($pParams); // turning array back into a URL string, convering ['search' => 'admin', 'page' => 1] into search=admin&page=1
                ?>
                <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link <?= $linkClass ?>" href="<?= ($currentPage > 1) ? $prevUrl : 'javascript:void(0)' ?>">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php 
                        $iParams = $baseParams;
                        $iParams['page'] = $i;
                        $pageUrl = $baseUrl . "?" . http_build_query($iParams); 
                    ?> 
                    <li class="page-item <?= ($i === $currentPage) ? 'active' : '' ?>"> <!-- highlights the number of the page you are currently viewing -->
                        <a class="page-link <?= $linkClass ?>" href="<?= $pageUrl ?>"><?= $i ?></a> 
                    </li>
                <?php endfor; ?>

                <?php 
                    $nParams = $baseParams;
                    $nParams['page'] = $currentPage + 1;
                    $nextUrl = $baseUrl . "?" . http_build_query($nParams);
                ?>
                <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                    <a class="page-link <?= $linkClass ?>" href="<?= ($currentPage < $totalPages) ? $nextUrl : 'javascript:void(0)' ?>">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
<?php endif; ?>

