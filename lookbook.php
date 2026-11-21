<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/layout.php';

$pdo = get_db();
$rows = $pdo->query('SELECT * FROM products ORDER BY is_featured DESC, id ASC')->fetchAll();
$allProducts = array_map('format_product_row', $rows);
$reviews = $pdo->query('SELECT r.*, p.name AS product_name FROM reviews r JOIN products p ON p.id = r.product_id ORDER BY r.id DESC LIMIT 6')->fetchAll();

render_page_head(
    'Shoppable Lookbook & Story | PAHERVESH by Akshree',
    'Shop Pahervesh by Akshree Instagram looks and discover our signature kraft & pink ribbon cotton packaging story.',
    'lookbook'
);
?>
  <main id="top">
    <section class="page-hero">
      <div class="container">
        <div class="page-hero-card reveal">
          <div class="page-hero-copy">
            <nav class="breadcrumb" aria-label="Breadcrumb">
              <a href="index.php">Home</a> <span>/</span> <span>Lookbook</span>
            </nav>
            <span class="section-kicker">@paherveshbyakshree</span>
            <h1 class="page-hero-title">The Instagram Lookbook &amp; Atelier Story</h1>
            <p class="section-desc" style="margin-top:12px;">
              Tap any curated frame from our Instagram reels and posts to inspect fabric details, pick your size, or request custom tailoring in seconds.
            </p>
            <div class="page-highlights">
              <span class="highlight-chip">✦ Shoppable Reels</span>
              <span class="highlight-chip">✦ Signature Kraft &amp; Ribbon Wrap</span>
              <span class="highlight-chip">✦ Worldwide Dispatch</span>
            </div>
          </div>
          <div class="page-hero-media">
            <img src="assets/images/brand-packaging.jpg" alt="Pahervesh Boutique Packaging">
          </div>
        </div>
      </div>
    </section>

    <!-- Full Shoppable Lookbook Grid -->
    <section class="section" style="padding-top:32px;">
      <div class="container">
        <div class="section-header reveal">
          <div>
            <span class="section-kicker">Tap Any Frame to Shop</span>
            <h2 class="section-title">Featured Reels &amp; Shoots</h2>
          </div>
          <a href="shop.php" class="btn btn-primary btn-sm">Shop Full Catalog</a>
        </div>

        <div class="ig-grid" style="grid-template-columns:repeat(auto-fill, minmax(220px, 1fr));gap:18px;">
          <?php
          $frames = [
              ['pid' => 1, 'tag' => '▶ Bestseller Farshi Reel', 'img' => 'assets/images/prod-blush-farshi.jpg', 'title' => 'Gulab Lotus Farshi Set'],
              ['pid' => 2, 'tag' => '▶ Festive Kalamkari', 'img' => 'assets/images/prod-crimson-farshi.jpg', 'title' => 'Sindoor Kalamkari Farshi'],
              ['pid' => 3, 'tag' => '▶ Citrus Summer Edit', 'img' => 'assets/images/prod-citrus-suit.jpg', 'title' => 'Citrus Sunbeam Suit Set'],
              ['pid' => 4, 'tag' => 'Office Outfit Inspo', 'img' => 'assets/images/prod-charcoal-office.jpg', 'title' => 'Monochrome Bagru Kurta'],
              ['pid' => 6, 'tag' => '▶ Day in My Life', 'img' => 'assets/images/prod-indigo-bandhani.jpg', 'title' => 'Nila Indigo Bandhani'],
              ['pid' => 5, 'tag' => 'Everyday Chic', 'img' => 'assets/images/prod-midnight-bird.jpg', 'title' => 'Neelambari Bird Kurta'],
              ['pid' => 7, 'tag' => 'Indo-Western Drop', 'img' => 'assets/images/prod-sage-coord.jpg', 'title' => 'Aranya Sage Co-ord Set'],
              ['pid' => 8, 'tag' => '▶ Pre-Order Mulmul', 'img' => 'assets/images/prod-rose-mulmul.jpg', 'title' => 'Noor Pastel Rose Suit'],
          ];
          foreach ($frames as $fr):
          ?>
          <div class="ig-tile reveal" data-product-id="<?= $fr['pid'] ?>">
            <span class="ig-reel-tag"><?= htmlspecialchars($fr['tag']) ?></span>
            <img src="<?= htmlspecialchars($fr['img']) ?>" alt="<?= htmlspecialchars($fr['title']) ?>">
            <div class="ig-hover">
              <strong><?= htmlspecialchars($fr['title']) ?></strong>
              <small style="margin:6px 0 10px;">Tap to Open Quick View</small>
              <span class="btn btn-secondary btn-sm">Shop This Look</span>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- Community Reviews -->
    <section class="section story-band">
      <div class="container">
        <div class="section-header reveal">
          <div>
            <span class="section-kicker">Verified Love Notes</span>
            <h2 class="section-title">What Our Community Says</h2>
          </div>
        </div>
        <div class="testimonials-grid">
          <?php foreach ($reviews as $rev): ?>
          <article class="testimonial-card reveal">
            <div class="stars"><?= str_repeat('★', (int)$rev['rating']) ?></div>
            <h4 style="font-family:var(--font-display);font-size:1.25rem;"><?= htmlspecialchars($rev['title']) ?></h4>
            <p style="font-size:0.88rem;color:var(--ink-muted);">“<?= htmlspecialchars($rev['comment']) ?>”</p>
            <div style="margin-top:auto;padding-top:10px;border-top:1px solid var(--border);display:flex;justify-content:space-between;font-size:0.78rem;">
              <strong><?= htmlspecialchars($rev['author_name']) ?> • <?= htmlspecialchars($rev['city']) ?></strong>
              <span style="color:var(--sage);font-weight:600;">✓ <?= htmlspecialchars($rev['product_name']) ?></span>
            </div>
          </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  </main>
<?php
render_page_footer($allProducts, 'All', 'lookbook');
