<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/layout.php';

$pdo = get_db();
$rows = $pdo->query('SELECT * FROM products ORDER BY is_featured DESC, id ASC')->fetchAll();
$allProducts = array_map('format_product_row', $rows);

render_page_head(
    'Custom Fit & Size Guide | PAHERVESH by Akshree',
    'Explore Pahervesh by Akshree size guide in inches & centimeters or submit custom tailoring measurements.',
    'custom'
);
?>
  <main id="top">
    <section class="page-hero">
      <div class="container">
        <div class="page-hero-card reveal">
          <div class="page-hero-copy">
            <nav class="breadcrumb" aria-label="Breadcrumb">
              <a href="index.php">Home</a> <span>/</span> <span>Custom Fit</span>
            </nav>
            <span class="section-kicker">Atelier Tailoring</span>
            <h1 class="page-hero-title">Tailored Just for You</h1>
            <p class="section-desc" style="margin-top:12px;">
              Whether you need custom Farshi salwar length, petite sleeve alterations, or made-to-measure sizing from XS to XXL+, Akshree’s studio crafts each piece with generous inner margins.
            </p>
          </div>
          <div class="page-hero-media">
            <img src="assets/images/prod-rose-mulmul.jpg" alt="Custom Tailored Mulmul Cotton">
          </div>
        </div>
      </div>
    </section>

    <section class="section" style="padding-top:28px;">
      <div class="container">
        <div class="story-grid" style="align-items:start;">
          <!-- Interactive Size Chart Card -->
          <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius-lg);padding:28px;box-shadow:var(--shadow-sm);" class="reveal">
            <span class="section-kicker">Standard Fit Chart</span>
            <h2 style="font-family:var(--font-display);font-size:2rem;margin-bottom:10px;">Cotton Ethnic Size Guide</h2>
            <div style="display:flex;gap:8px;margin-bottom:16px;">
              <button type="button" class="filter-pill active" id="pageUnitInBtn">Inches</button>
              <button type="button" class="filter-pill" id="pageUnitCmBtn">Centimeters</button>
            </div>
            <div style="overflow-x:auto;">
              <table style="width:100%;border-collapse:collapse;font-size:0.86rem;" id="pageSizeChartTable"></table>
            </div>
            <div style="margin-top:18px;padding:14px;border-radius:12px;background:var(--bg-blush);font-size:0.84rem;">
              <strong>How to Measure:</strong> Measure around the fullest part of your bust and natural waist with a relaxed tape. Our cotton sets include a 1.5-inch inner seam allowance for easy future adjustments.
            </div>
          </div>

          <!-- Custom Tailoring Form -->
          <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius-lg);padding:28px;box-shadow:var(--shadow-md);" class="reveal">
            <span class="section-kicker">Personal Concierge</span>
            <h2 style="font-family:var(--font-display);font-size:2rem;margin-bottom:8px;">Request Custom Tailoring</h2>
            <p style="font-size:0.86rem;color:var(--ink-muted);margin-bottom:18px;">Submit your measurements below — our styling desk replies within 4 hours.</p>

            <form id="pageCustomEnquiryForm">
              <div class="form-grid">
                <div class="field"><label>Your Name *</label><input type="text" name="name" required placeholder="e.g. Meera Kapoor"></div>
                <div class="field"><label>WhatsApp / Instagram Handle *</label><input type="text" name="contact" required placeholder="+91 98... or @handle"></div>
              </div>
              <div class="field"><label>Outfit / Category of Interest</label>
                <select name="product_name">
                  <option value="Gulab Lotus Farshi Salwar Set">Gulab Lotus Farshi Salwar Set</option>
                  <option value="Sindoor Kalamkari Farshi Set">Sindoor Kalamkari Farshi Set</option>
                  <option value="Citrus Sunbeam Cotton Suit Set">Citrus Sunbeam Cotton Suit Set</option>
                  <option value="Noor Pastel Rose Mulmul Suit Set">Noor Pastel Rose Mulmul Suit Set</option>
                  <option value="Aranya Sage Botanical Co-ord Set">Aranya Sage Botanical Co-ord Set</option>
                  <option value="Other / Bespoke Pre-Order">Other / Bespoke Pre-Order</option>
                </select>
              </div>
              <div class="form-grid">
                <div class="field"><label>Bust (Inches)</label><input type="text" name="bust" placeholder="e.g. 36"></div>
                <div class="field"><label>Waist (Inches)</label><input type="text" name="waist" placeholder="e.g. 30"></div>
              </div>
              <div class="field"><label>Preferred Kurta &amp; Salwar Length</label><input type="text" name="length" placeholder="e.g. Kurta 44 in, Farshi 39 in"></div>
              <div class="field"><label>Styling Notes / Occasion Date *</label><textarea name="notes" rows="3" required placeholder="Tell us your event date, sleeve preference, or international shipping city..."></textarea></div>
              <button type="submit" class="btn btn-primary" style="width:100%;">Submit Custom Request</button>
            </form>
            <div id="pageCustomEnquiryResult" style="margin-top:12px;font-size:0.86rem;"></div>
          </div>
        </div>
      </div>
    </section>
  </main>
<?php
render_page_footer($allProducts, 'All', 'custom');
