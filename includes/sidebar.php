<?php
if (!function_exists('renderSidebar')) {
    function renderSidebar($currentPage = 'index')
    {
        $role = getUserRole();

        $adminLinks = [
            ['url' => 'index.php', 'icon' => 'layout-dashboard', 'label' => 'Dashboard'],
            ['url' => 'customers.php', 'icon' => 'users', 'label' => 'Customers'],
            ['url' => 'forms.php', 'icon' => 'file-text', 'label' => 'Forms'],
            ['url' => 'deliveries.php', 'icon' => 'truck', 'label' => 'Deliveries'],
            ['url' => 'pos.php', 'icon' => 'shopping-cart', 'label' => 'Point of Sale'],
            ['url' => 'users.php', 'icon' => 'user-cog', 'label' => 'User Management']
        ];

        $cashierLinks = [
            ['url' => 'index.php', 'icon' => 'layout-dashboard', 'label' => 'Dashboard'],
            ['url' => 'customers.php', 'icon' => 'users', 'label' => 'Customers'],
            ['url' => 'forms.php', 'icon' => 'file-text', 'label' => 'Forms'],
            ['url' => 'deliveries.php', 'icon' => 'truck', 'label' => 'Deliveries']
        ];

        $links = $role === 'admin' ? $adminLinks : $cashierLinks;

        $icons = [
            'layout-dashboard' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>',
            'users' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
            'file-text' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>',
            'truck' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>',
            'shopping-cart' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>',
            'user-cog' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><circle cx="19" cy="11" r="2"/><path d="M19 8v1m0 4v1m2.6-3.5l-.87.5m-3.46 2l-.87.5m5.2 0l-.87-.5m-3.46-2l-.87-.5"/></svg>',
            'log-out' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>'
        ];
?>
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-brand">
                    <div class="sidebar-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="sidebar-title"><?php echo APP_NAME; ?></div>
                        <div class="sidebar-role"><?php echo htmlspecialchars($role); ?></div>
                    </div>
                </div>
            </div>

            <nav class="sidebar-nav">
                <?php foreach ($links as $link): ?>
                    <?php
                    $linkPage = basename($link['url'], '.php');
                    $isActive = $currentPage === $linkPage;
                    ?>
                    <a href="<?php echo htmlspecialchars($link['url']); ?>"
                        class="nav-link <?php echo $isActive ? 'active' : ''; ?>">
                        <?php echo $icons[$link['icon']]; ?>
                        <span><?php echo htmlspecialchars($link['label']); ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>

            <div class="sidebar-footer">
                <button data-open-modal="logoutModal" class="logout-btn">
                    <?php echo $icons['log-out']; ?>
                    <span>Logout</span>
                </button>
            </div>
        </aside>
<?php
    }
}
?>