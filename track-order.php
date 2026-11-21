<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/layout.php';

$pdo = get_db();
$rows = $pdo->query('SELECT * FROM products ORDER BY is_featured DESC, id ASC')->fetchAll();
$allProducts = array_map('format_product_row', $rows);

render_page_head(
    'Track Your Order | PAHERVESH by Akshree',
    'Track your Pahervesh by Akshree parcel status across India and worldwide.',
    'track'
);
?>
  <main id="top">
    <section class="page-hero">
      <div class="container">
        <div class="page-hero-card reveal">
          <div class="page-hero-copy">
            <nav class="breadcrumb" aria-label="Breadcrumb">
              <a href="index.php">Home</a> <span>/</span> <span>Track Order</span>
            </nav>
            <span class="section-kicker">Live Parcel Concierge</span>
            <h1 class="page-hero-title">Track Your Pahervesh Parcel</h1>
            <p class="section-desc" style="margin-top:12px;">
              Follow your hand-packed cotton ensemble from Akshree’s Jaipur studio straight to your doorstep anywhere in India or worldwide.
            </p>
          </div>
          <div class="page-hero-media">
            <img src="assets/images/brand-packaging.jpg" alt="Pahervesh Kraft Parcel">
          </div>
        </div>
      </div>
    </section>

    <section class="section" style="padding-top:26px;">
      <div class="container" style="max-width:860px;">
        <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius-lg);padding:32px;box-shadow:var(--shadow-md);" class="reveal">
          <h2 style="font-family:var(--font-display);font-size:1.9rem;margin-bottom:8px;">Enter Your Order Number</h2>
          <p style="font-size:0.88rem;color:var(--ink-muted);margin-bottom:16px;">Try one of our live demo orders below or enter the Order ID generated after checkout:</p>

          <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px;">
            <button type="button" class="filter-pill page-sample-order-chip" data-order="PHV-2026-1042">PHV-2026-1042 (Jaipur • Dispatched)</button>
            <button type="button" class="filter-pill page-sample-order-chip" data-order="PHV-2026-1089">PHV-2026-1089 (London UK • Delivered)</button>
          </div>

          <form id="pageTrackOrderForm" style="display:flex;gap:10px;flex-wrap:wrap;">
            <input type="text" id="pageTrackOrderInput" placeholder="e.g. PHV-2026-1042" style="flex:1;min-width:220px;padding:12px 16px;border:1px solid var(--border-strong);border-radius:12px;background:var(--bg-ivory);" required>
            <button type="submit" class="btn btn-primary">Track Parcel</button>
          </form>

          <div id="pageTrackOrderResult" style="margin-top:22px;"></div>
        </div>

        <div class="trust-grid" style="grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));margin-top:28px;">
          <div class="trust-pill reveal">
            <div>
              <h4>Pan-India Dispatch</h4>
              <p>3–6 business days • Free above ₹1,999</p>
            </div>
          </div>
          <div class="trust-pill reveal">
            <div>
              <h4>Worldwide DHL Express</h4>
              <p>5–8 business days across 28+ countries</p>
            </div>
          </div>
          <div class="trust-pill reveal">
            <div>
              <h4>Made-to-Order / Pre-Orders</h4>
              <p>Handcrafted &amp; dispatched within 7–10 days</p>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
<?php
render_page_footer($allProducts, 'All', 'track');
