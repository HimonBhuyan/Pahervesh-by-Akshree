<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/layout.php';

/**
 * Renders a dedicated Collection Page (Farshi Sets, Suit Sets, Co-ord Sets, Kurtis, or Shop All)
 */
function render_collection_page(array $cfg): void
{
    $pdo = get_db();
    $rows = $pdo->query('SELECT * FROM products ORDER BY is_featured DESC, id ASC')->fetchAll();
    $allProducts = array_map('format_product_row', $rows);

    $category = $cfg['category'] ?? 'All';
    $activeNav = $cfg['activeNav'] ?? 'shop';
    $title = $cfg['title'] ?? 'Shop All | PAHERVESH by Akshree';
    $description = $cfg['description'] ?? 'Handcrafted cotton ethnic wear by Pahervesh by Akshree.';
    $kicker = $cfg['kicker'] ?? 'Curated Collection';
    $heading = $cfg['heading'] ?? 'Shop All Collections';
    $lead = $cfg['lead'] ?? '';
    $heroImage = $cfg['heroImage'] ?? 'assets/images/hero-editorial.jpg';
    $highlights = $cfg['highlights'] ?? [];

    render_page_head($title, $description, $activeNav);
    ?>
  <main id="top">
    <!-- Dedicated Collection Hero Banner -->
    <section class="page-hero">
      <div class="container">
        <div class="page-hero-card reveal">
          <div class="page-hero-copy">
            <nav class="breadcrumb" aria-label="Breadcrumb">
              <a href="index.php">Home</a> <span>/</span> <span><?= htmlspecialchars($heading) ?></span>
            </nav>
            <span class="section-kicker"><?= htmlspecialchars($kicker) ?></span>
            <h1 class="page-hero-title"><?= htmlspecialchars($heading) ?></h1>
            <p class="section-desc" style="margin-top:12px;"><?= htmlspecialchars($lead) ?></p>
            <?php if ($highlights): ?>
              <div class="page-highlights">
                <?php foreach ($highlights as $hl): ?>
                  <span class="highlight-chip">✦ <?= htmlspecialchars($hl) ?></span>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
            <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:24px;">
              <a href="#catalog" class="btn btn-primary btn-sm">Browse Pieces ↓</a>
              <a href="custom-fit.php" class="btn btn-secondary btn-sm">Request Custom Sizing</a>
            </div>
          </div>
          <div class="page-hero-media">
            <img src="<?= htmlspecialchars($heroImage) ?>" alt="<?= htmlspecialchars($heading) ?>">
          </div>
        </div>
      </div>
    </section>

    <!-- Collection Catalog -->
    <section class="section" id="catalog" style="padding-top:28px;">
      <div class="container">
        <div class="section-header reveal">
          <div>
            <span class="section-kicker">Handcrafted Cotton</span>
            <h2 class="section-title"><?= htmlspecialchars($category === 'All' ? 'Complete Pahervesh Edit' : $category) ?></h2>
          </div>
          <p class="section-desc" id="catalogResultCount">Loading pieces...</p>
        </div>

        <div class="catalog-controls reveal">
          <div class="category-pills" id="categoryTabs" role="tablist">
            <?php
            $tabs = ['All' => 'All Pieces', 'Farshi Sets' => 'Farshi Sets', 'Suit Sets' => 'Suit Sets', 'Co-ord Sets' => 'Co-ord Sets', 'Office Kurtis' => 'Office Kurtis', 'Pre-Orders' => 'Pre-Orders'];
            foreach ($tabs as $val => $label):
                $isActive = (strcasecmp($category, $val) === 0);
            ?>
              <button class="filter-pill <?= $isActive ? 'active' : '' ?>" data-category="<?= htmlspecialchars($val) ?>"><?= htmlspecialchars($label) ?></button>
            <?php endforeach; ?>
          </div>

          <div class="catalog-right-controls">
            <div class="search-box">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.35-4.35"/></svg>
              <input type="search" class="search-input" id="catalogSearch" placeholder="Search prints, fabrics, colors...">
            </div>
            <select class="sort-select" id="catalogSort" aria-label="Sort Products">
              <option value="featured">Sort: Featured</option>
              <option value="price_asc">Price: Low to High</option>
              <option value="price_desc">Price: High to Low</option>
              <option value="rating">Highest Rated</option>
              <option value="newest">Newest Drops</option>
            </select>
          </div>
        </div>

        <div class="products-grid" id="productsGrid"></div>
      </div>
    </section>
  </main>
    <?php
    render_page_footer($allProducts, $category, $activeNav);
}
