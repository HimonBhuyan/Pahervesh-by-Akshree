<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/layout.php';

$pdo = get_db();
$rows = $pdo->query('SELECT * FROM products ORDER BY is_featured DESC, id ASC')->fetchAll();
$initialProducts = array_map('format_product_row', $rows);
$initialReviews = $pdo->query("
    SELECT r.*, p.name AS product_name
    FROM reviews r
    JOIN products p ON p.id = r.product_id
    ORDER BY r.id DESC
    LIMIT 6
")->fetchAll();

render_page_head(
    'PAHERVESH by Akshree | Pure Cotton Ethnic & Indo-Western Wear',
    'Curated hand-block printed pure cotton Farshi Sets, Suit Sets, Co-ord Sets & Office Kurtis by Pahervesh by Akshree. Shipping across India & Worldwide.',
    'home'
);
?>
  <main id="top">
    <!-- Full-Bleed Couture Editorial Hero -->
    <section class="hero-couture" id="heroSection">
      <!-- 3-Slide Crossfading Full-Bleed Photography -->
      <div class="hero-slides" id="heroSlides">
        <div class="hero-slide active" data-slide="0">
          <img src="assets/images/hero-editorial.jpg" alt="Gulab Lotus Farshi Set in Rajasthan Courtyard" class="hero-slide-img" style="object-position: 78% 15%;">
        </div>
        <div class="hero-slide" data-slide="1">
          <img src="assets/images/prod-crimson-farshi.jpg" alt="Sindoor Kalamkari Farshi Set" class="hero-slide-img" style="object-position: 76% 18%;">
        </div>
        <div class="hero-slide" data-slide="2">
          <img src="assets/images/prod-citrus-suit.jpg" alt="Citrus Sunbeam Cotton Suit Set" class="hero-slide-img" style="object-position: 74% 16%;">
        </div>
      </div>

      <!-- Feathered Ivory-Blush Veil + Cotton Paper Grain -->
      <div class="hero-feather-veil"></div>
      <div class="hero-paper-grain" aria-hidden="true"></div>
      <div class="hero-aura hero-aura-1"></div>
      <div class="hero-aura hero-aura-2"></div>

      <!-- Rotating Atelier Seal with Official Center Logo -->
      <div class="hero-seal-stamp" aria-hidden="true">
        <svg class="hero-seal-ring" viewBox="0 0 120 120" width="118" height="118">
          <defs>
            <path id="sealCircle" d="M 60, 60 m -45, 0 a 45,45 0 1,1 90,0 a 45,45 0 1,1 -90,0"/>
          </defs>
          <circle cx="60" cy="60" r="54" fill="rgba(250,246,240,0.48)" stroke="rgba(68,77,54,0.16)" stroke-width="1"/>
          <text font-size="7.6" font-weight="700" letter-spacing="2.35" fill="#444D36">
            <textPath href="#sealCircle" textLength="272">HANDCRAFTED IN JAIPUR • PURE COTTON • </textPath>
          </text>
        </svg>
        <div class="hero-seal-center">
          <img src="assets/images/logo.jpg" alt="Pahervesh by Akshree">
        </div>
      </div>

      <div class="container hero-couture-grid">
        <div class="hero-content-couture">
          <div class="hero-ornament-kicker">
            <span class="ornament-line"></span>
            <span class="ornament-diamond">✦</span>
            <span>Est. Jaipur &nbsp;•&nbsp; Beauty, Uncomplicated.</span>
            <span class="ornament-diamond">✦</span>
            <span class="ornament-line"></span>
          </div>

          <h1 class="hero-title" id="heroSlideTitle">
            Heirloom Craft,<br><em>Breezy Everyday</em><br>Elegance.
          </h1>

          <p class="hero-subtitle" id="heroSlideDesc">
            Hand-block printed Farshi Sets, featherlight Mulmul Suit Sets, and modern Indo-Western Co-ords — stitched in pure breathable cotton and delivered across India &amp; Worldwide.
          </p>

          <div class="hero-ctas">
            <a href="farshi-sets.php" class="btn-couture" id="heroPrimaryCta">
              <span>Explore Farshi Sets</span>
              <span class="couture-arrow">→</span>
            </a>
            <a href="shop.php" class="btn-couture-outline">
              <span>Shop All Collections</span>
            </a>
          </div>

          <div class="hero-metrics-couture">
            <div class="metric-item">
              <div class="metric-val">100%</div>
              <div class="metric-lbl">Pure Handloom Cotton</div>
            </div>
            <div class="metric-divider"></div>
            <div class="metric-item">
              <div class="metric-val">28+</div>
              <div class="metric-lbl">Countries Shipped</div>
            </div>
            <div class="metric-divider"></div>
            <div class="metric-item">
              <div class="metric-val">4.9 <span class="gold-star">★</span></div>
              <div class="metric-lbl">Loved by Women</div>
            </div>
          </div>

          <!-- 3-Slide Campaign Switcher -->
          <div class="hero-slide-controls" id="heroSlideControls" aria-label="Featured Campaign Slides">
            <button type="button" class="hero-slide-tab active" data-slide-to="0">
              <span class="slide-num">01</span>
              <span class="slide-name">Gulab Farshi</span>
              <span class="slide-bar"><i></i></span>
            </button>
            <button type="button" class="hero-slide-tab" data-slide-to="1">
              <span class="slide-num">02</span>
              <span class="slide-name">Sindoor Kalamkari</span>
              <span class="slide-bar"><i></i></span>
            </button>
            <button type="button" class="hero-slide-tab" data-slide-to="2">
              <span class="slide-num">03</span>
              <span class="slide-name">Citrus Mulmul</span>
              <span class="slide-bar"><i></i></span>
            </button>
          </div>
        </div>

        <div class="hero-right-stage">
          <div class="hero-floating-card couture-glass" id="heroFeaturedCard" role="button" tabindex="0">
            <img src="assets/images/prod-blush-farshi.jpg" alt="Gulab Lotus Farshi Set" id="heroCardImg">
            <div class="hero-floating-meta">
              <small id="heroCardTag">Look 01 • Signature Bestseller</small>
              <strong id="heroCardTitle">Gulab Lotus Farshi Set</strong>
              <div class="hero-card-action">
                <span class="price-dynamic" data-inr="2890" id="heroCardPrice">₹2,890</span>
                <span class="shop-look-link">Shop Look →</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <a href="#categories" class="hero-scroll-cue">
        <span>Scroll to Unfold</span>
        <span class="scroll-cue-line"></span>
      </a>
    </section>

    <!-- Trust Strip -->
    <section class="trust-strip">
      <div class="container trust-grid">
        <div class="trust-pill reveal">
          <div class="trust-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
          </div>
          <div>
            <h4>India &amp; Worldwide</h4>
            <p>Express tracked delivery globally</p>
          </div>
        </div>
        <div class="trust-pill reveal">
          <div class="trust-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          </div>
          <div>
            <h4>100% Pure Cotton</h4>
            <p>Breathable Sanganeri &amp; Mulmul weaves</p>
          </div>
        </div>
        <div class="trust-pill reveal">
          <div class="trust-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
          </div>
          <div>
            <h4>Boutique Ribbon Wrap</h4>
            <p>Kraft bags, twine &amp; floral love</p>
          </div>
        </div>
        <div class="trust-pill reveal">
          <div class="trust-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
          </div>
          <div>
            <h4>Custom Tailoring</h4>
            <p>Made-to-measure XS to XXL+</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Curated Categories -->
    <section class="section" id="categories">
      <div class="container">
        <div class="section-header reveal">
          <div>
            <span class="section-kicker">Curated Silhouettes</span>
            <h2 class="section-title">Shop by Collection</h2>
          </div>
          <p class="section-desc">From regal Farshi salwar flares to crisp block-printed work kurtis — explore each dedicated collection.</p>
        </div>

        <div class="categories-grid">
          <a href="farshi-sets.php" class="category-card reveal">
            <img src="assets/images/prod-blush-farshi.jpg" alt="Farshi Sets">
            <div class="category-overlay">
              <span>Signature Edit</span>
              <h3>Farshi Sets</h3>
            </div>
          </a>
          <a href="suit-sets.php" class="category-card reveal">
            <img src="assets/images/prod-citrus-suit.jpg" alt="Suit Sets">
            <div class="category-overlay">
              <span>3-Piece Mulmul</span>
              <h3>Suit Sets</h3>
            </div>
          </a>
          <a href="coord-sets.php" class="category-card reveal">
            <img src="assets/images/prod-sage-coord.jpg" alt="Co-ord Sets">
            <div class="category-overlay">
              <span>Indo-Western</span>
              <h3>Co-ord Sets</h3>
            </div>
          </a>
          <a href="kurtis.php" class="category-card reveal">
            <img src="assets/images/prod-charcoal-office.jpg" alt="Office Kurtis">
            <div class="category-overlay">
              <span>9-to-7 Comfort</span>
              <h3>Office Kurtis</h3>
            </div>
          </a>
          <a href="shop.php?category=Pre-Orders" class="category-card reveal">
            <img src="assets/images/prod-rose-mulmul.jpg" alt="Pre-Orders">
            <div class="category-overlay">
              <span>Small Batch</span>
              <h3>Pre-Orders</h3>
            </div>
          </a>
        </div>
      </div>
    </section>

    <!-- Main Interactive Product Catalog -->
    <section class="section" id="catalog">
      <div class="container">
        <div class="section-header reveal">
          <div>
            <span class="section-kicker">Handcrafted Wardrobe</span>
            <h2 class="section-title">The Pahervesh Edit</h2>
          </div>
          <p class="section-desc" id="catalogResultCount">Showing all handcrafted cotton ensembles</p>
        </div>

        <div class="catalog-controls reveal">
          <div class="category-pills" id="categoryTabs" role="tablist">
            <button class="filter-pill active" data-category="All">All Pieces</button>
            <button class="filter-pill" data-category="Farshi Sets">Farshi Sets</button>
            <button class="filter-pill" data-category="Suit Sets">Suit Sets</button>
            <button class="filter-pill" data-category="Co-ord Sets">Co-ord Sets</button>
            <button class="filter-pill" data-category="Office Kurtis">Office Kurtis</button>
            <button class="filter-pill" data-category="Pre-Orders">Pre-Orders</button>
          </div>

          <div class="catalog-right-controls">
            <div class="search-box">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.35-4.35"/></svg>
              <input type="search" class="search-input" id="catalogSearch" placeholder="Search Farshi, Kalamkari, Indigo...">
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

    <!-- Brand Story & Signature Packaging -->
    <section class="section story-band" id="story">
      <div class="container story-grid">
        <div class="story-media reveal">
          <img src="assets/images/brand-packaging.jpg" alt="Pahervesh Signature Kraft & Pink Ribbon Packaging">
          <div class="story-seal">
            <div class="script">With Love</div>
            <small>Packed by Akshree</small>
          </div>
        </div>

        <div class="reveal">
          <span class="section-kicker">The Pahervesh Promise</span>
          <h2 class="section-title">Unfolding Heritage, Tied with Ribbon &amp; Care</h2>
          <p class="section-desc" style="margin-top:12px;">
            Every Pahervesh parcel leaves our studio wrapped in raw kraft paper, tied with jute twine and blush satin ribbon — because receiving artisanal cotton should feel like opening a letter from a dear friend.
          </p>

          <div class="story-points">
            <div class="story-point">
              <div class="story-point-num">01</div>
              <div>
                <strong>Hand-Carved Wooden Blocks</strong>
                <p style="font-size:0.84rem;color:var(--ink-muted);">Collaborating directly with Jaipur &amp; Bagru block-printing families in small, mindful batches.</p>
              </div>
            </div>
            <div class="story-point">
              <div class="story-point-num">02</div>
              <div>
                <strong>Skin-Loving 60s Cambric &amp; Mulmul</strong>
                <p style="font-size:0.84rem;color:var(--ink-muted);">Pre-washed breathable cotton tailored with deep pockets and soft inner mulmul lining.</p>
              </div>
            </div>
            <div class="story-point">
              <div class="story-point-num">03</div>
              <div>
                <strong>Personal Custom Fit &amp; DM Concierge</strong>
                <p style="font-size:0.84rem;color:var(--ink-muted);">Need custom sleeve length or Farshi flare? Request tailored sizing directly at checkout.</p>
              </div>
            </div>
          </div>

          <div style="display:flex;gap:12px;flex-wrap:wrap;">
            <a href="custom-fit.php" class="btn btn-primary">Request Custom Tailoring</a>
            <button class="btn btn-secondary" id="storySizeGuideBtn">View Size Chart</button>
          </div>
        </div>
      </div>
    </section>

    <!-- Shoppable Instagram Lookbook Preview -->
    <section class="section" id="lookbook">
      <div class="container">
        <div class="section-header reveal">
          <div>
            <span class="section-kicker">@paherveshbyakshree</span>
            <h2 class="section-title">As Seen on Instagram</h2>
          </div>
          <a href="lookbook.php" class="btn btn-secondary btn-sm">Open Full Lookbook →</a>
        </div>

        <div class="ig-grid">
          <div class="ig-tile reveal" data-product-id="1">
            <span class="ig-reel-tag">▶ Bestseller Reel</span>
            <img src="assets/images/prod-blush-farshi.jpg" alt="Gulab Lotus Farshi">
            <div class="ig-hover">
              <strong>Gulab Lotus Farshi</strong>
              <small style="margin:6px 0 10px;">Tap to Shop Look</small>
              <span class="btn btn-secondary btn-sm">Quick View</span>
            </div>
          </div>
          <div class="ig-tile reveal" data-product-id="2">
            <span class="ig-reel-tag">▶ Festive Farshi</span>
            <img src="assets/images/prod-crimson-farshi.jpg" alt="Sindoor Kalamkari Farshi">
            <div class="ig-hover">
              <strong>Sindoor Kalamkari Farshi</strong>
              <small style="margin:6px 0 10px;">Tap to Shop Look</small>
              <span class="btn btn-secondary btn-sm">Quick View</span>
            </div>
          </div>
          <div class="ig-tile reveal" data-product-id="3">
            <span class="ig-reel-tag">▶ Citrus Edit</span>
            <img src="assets/images/prod-citrus-suit.jpg" alt="Citrus Sunbeam Suit">
            <div class="ig-hover">
              <strong>Citrus Sunbeam Suit</strong>
              <small style="margin:6px 0 10px;">Tap to Shop Look</small>
              <span class="btn btn-secondary btn-sm">Quick View</span>
            </div>
          </div>
          <div class="ig-tile reveal" data-product-id="4">
            <span class="ig-reel-tag">Office Outfit</span>
            <img src="assets/images/prod-charcoal-office.jpg" alt="Monochrome Bagru Office Kurta">
            <div class="ig-hover">
              <strong>Monochrome Bagru Kurta</strong>
              <small style="margin:6px 0 10px;">Tap to Shop Look</small>
              <span class="btn btn-secondary btn-sm">Quick View</span>
            </div>
          </div>
          <div class="ig-tile reveal" data-product-id="6">
            <span class="ig-reel-tag">▶ Day in My Life</span>
            <img src="assets/images/prod-indigo-bandhani.jpg" alt="Nila Indigo Bandhani">
            <div class="ig-hover">
              <strong>Nila Indigo Bandhani</strong>
              <small style="margin:6px 0 10px;">Tap to Shop Look</small>
              <span class="btn btn-secondary btn-sm">Quick View</span>
            </div>
          </div>
          <div class="ig-tile reveal" data-product-id="5">
            <span class="ig-reel-tag">Everyday Chic</span>
            <img src="assets/images/prod-midnight-bird.jpg" alt="Neelambari Bird Kurta">
            <div class="ig-hover">
              <strong>Neelambari Bird Kurta</strong>
              <small style="margin:6px 0 10px;">Tap to Shop Look</small>
              <span class="btn btn-secondary btn-sm">Quick View</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Customer Love / Reviews -->
    <section class="section" style="padding-top:20px;">
      <div class="container">
        <div class="section-header reveal">
          <div>
            <span class="section-kicker">Community Love</span>
            <h2 class="section-title">Stories from Our Cuties</h2>
          </div>
          <p class="section-desc">Real notes from women wearing Pahervesh across India, UK &amp; UAE.</p>
        </div>

        <div class="testimonials-grid">
          <?php foreach (array_slice($initialReviews, 0, 3) as $rev): ?>
          <article class="testimonial-card reveal">
            <div class="stars"><?= str_repeat('★', (int)$rev['rating']) ?></div>
            <h4 style="font-family:var(--font-display);font-size:1.25rem;"><?= htmlspecialchars($rev['title']) ?></h4>
            <p style="font-size:0.88rem;color:var(--ink-muted);">“<?= htmlspecialchars($rev['comment']) ?>”</p>
            <div style="margin-top:auto;padding-top:10px;border-top:1px solid var(--border);display:flex;justify-content:space-between;font-size:0.78rem;">
              <strong><?= htmlspecialchars($rev['author_name']) ?> • <?= htmlspecialchars($rev['city']) ?></strong>
              <span style="color:var(--sage);font-weight:600;">✓ Verified Buyer</span>
            </div>
          </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- Newsletter Club -->
    <section class="section">
      <div class="container">
        <div class="newsletter-box reveal">
          <div>
            <span class="section-kicker" style="color:var(--rose-soft);">Pahervesh Inner Circle</span>
            <h2 class="section-title" style="color:#fff;">Unlock 10% Off Your First Cotton Set</h2>
            <p style="color:rgba(255,248,245,0.84);margin-top:10px;font-size:0.94rem;">
              Get early access to small-batch Farshi drops, pre-order slots, and festive styling edits straight from Akshree.
            </p>
          </div>
          <div>
            <form class="newsletter-form" id="newsletterForm">
              <input type="email" id="newsletterEmail" placeholder="Enter your email address..." required>
              <button type="submit" class="btn btn-secondary">Join &amp; Get Code</button>
            </form>
            <p id="newsletterFeedback" style="font-size:0.82rem;margin-top:10px;color:var(--rose-soft);"></p>
          </div>
        </div>
      </div>
    </section>
  </main>
<?php
render_page_footer($initialProducts, 'All', 'home');
