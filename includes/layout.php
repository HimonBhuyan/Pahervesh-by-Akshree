<?php
declare(strict_types=1);

/**
 * Shared Header, Navigation, Footer, Drawers & Modals for all Pahervesh pages
 */

function render_page_head(string $title, string $description, string $activeNav = 'home'): void
{
    $showPreloader = ($activeNav === 'home');
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title) ?></title>
  <meta name="description" content="<?= htmlspecialchars($description) ?>">
  <link rel="icon" type="image/jpeg" href="assets/images/logo.jpg">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,500;1,600&family=Great+Vibes&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
  <div class="scroll-progress" id="scrollProgress"></div>

  <?php if ($showPreloader): ?>
  <div class="preloader" id="preloader" aria-hidden="true">
    <div class="preloader-logo-ring">
      <img src="assets/images/logo.jpg" alt="Pahervesh by Akshree Logo">
    </div>
    <div class="preloader-sub">Beauty, Uncomplicated. • Handcrafted Cotton</div>
  </div>
  <?php endif; ?>

  <div class="announcement-bar">
    <div class="announcement-track">
      <span>Free Pan-India Shipping Above ₹1,999</span><span class="announcement-dot"></span>
      <span>Worldwide Express Delivery Available</span><span class="announcement-dot"></span>
      <span>Use Code <strong>AKSHREE10</strong> for 10% Off Your First Order</span><span class="announcement-dot"></span>
      <span>Farshi Sets • Suit Sets • Co-ord Sets • Office Kurtis</span><span class="announcement-dot"></span>
      <span>Free Pan-India Shipping Above ₹1,999</span><span class="announcement-dot"></span>
      <span>Worldwide Express Delivery Available</span><span class="announcement-dot"></span>
      <span>Use Code <strong>AKSHREE10</strong> for 10% Off Your First Order</span><span class="announcement-dot"></span>
      <span>Farshi Sets • Suit Sets • Co-ord Sets • Office Kurtis</span><span class="announcement-dot"></span>
    </div>
  </div>

  <header class="site-header" id="siteHeader">
    <div class="container nav-inner">
      <div style="display:flex;align-items:center;gap:10px;">
        <button class="icon-btn mobile-menu-btn" id="openMobileMenu" aria-label="Open Navigation Menu">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
        </button>
        <a href="index.php" class="brand-lockup">
          <div class="brand-badge">
            <img src="assets/images/logo.jpg" alt="Pahervesh by Akshree">
          </div>
          <div class="brand-text-wrap">
            <span class="brand-name">PAHERVESH</span>
            <span class="brand-by">by Akshree</span>
          </div>
        </a>
      </div>

      <ul class="nav-links">
        <li><a href="shop.php" class="<?= $activeNav === 'shop' ? 'active' : '' ?>">Shop All</a></li>
        <li><a href="farshi-sets.php" class="<?= $activeNav === 'farshi' ? 'active' : '' ?>">Farshi Sets</a></li>
        <li><a href="suit-sets.php" class="<?= $activeNav === 'suits' ? 'active' : '' ?>">Suit Sets</a></li>
        <li><a href="coord-sets.php" class="<?= $activeNav === 'coords' ? 'active' : '' ?>">Co-ord Sets</a></li>
        <li><a href="kurtis.php" class="<?= $activeNav === 'kurtis' ? 'active' : '' ?>">Kurtis</a></li>
        <li><a href="lookbook.php" class="<?= $activeNav === 'lookbook' ? 'active' : '' ?>">Lookbook</a></li>
        <li><a href="track-order.php" class="<?= $activeNav === 'track' ? 'active' : '' ?>">Track Order</a></li>
        <li><a href="custom-fit.php" class="<?= $activeNav === 'custom' ? 'active' : '' ?>">Custom Fit</a></li>
      </ul>

      <div class="nav-actions">
        <select class="currency-select" id="currencySelect" aria-label="Select Currency">
          <option value="INR">INR ₹</option>
          <option value="USD">USD $</option>
          <option value="GBP">GBP £</option>
          <option value="AED">AED</option>
        </select>
        <button class="icon-btn" id="headerSearchBtn" aria-label="Search Products">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.35-4.35"/></svg>
        </button>
        <button class="icon-btn" id="openWishlistBtn" aria-label="Open Wishlist">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
          <span class="badge-count" id="wishlistCount">0</span>
        </button>
        <button class="icon-btn" id="openCartBtn" aria-label="Open Shopping Bag">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
          <span class="badge-count" id="cartCount">0</span>
        </button>
      </div>
    </div>
  </header>
    <?php
}

function render_page_footer(array $allProducts, string $initialCategory = 'All', string $activeNav = 'home'): void
{
    ?>
  <footer class="site-footer">
    <div class="container">
      <div class="footer-grid">
        <div>
          <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
            <img src="assets/images/logo.jpg" alt="Pahervesh by Akshree" style="width:54px;height:54px;border-radius:50%;object-fit:cover;border:1.5px solid rgba(235,196,201,0.45);">
            <div>
              <div style="font-family:var(--font-script);font-size:1.95rem;color:var(--rose-soft);line-height:1;">Pahervesh</div>
              <div style="font-size:0.72rem;letter-spacing:0.16em;text-transform:uppercase;color:#C7B9B1;">by Akshree • Beauty, Uncomplicated.</div>
            </div>
          </div>
          <p style="font-size:0.86rem;color:#C7B9B1;max-width:34ch;">
            Curated cotton ethnic &amp; Indo-Western wear rooted in Jaipur craft. Handcrafted Farshi sets, suit sets, and everyday kurtis shipped across India &amp; Worldwide.
          </p>
        </div>
        <div>
          <h4 class="footer-heading">Collections</h4>
          <ul class="footer-links">
            <li><a href="farshi-sets.php">Farshi Salwar Sets</a></li>
            <li><a href="suit-sets.php">Mulmul Suit Sets</a></li>
            <li><a href="coord-sets.php">Indo-Western Co-ords</a></li>
            <li><a href="kurtis.php">Office &amp; Everyday Kurtis</a></li>
            <li><a href="shop.php?category=Pre-Orders">Pre-Order Exclusives</a></li>
          </ul>
        </div>
        <div>
          <h4 class="footer-heading">Client Care</h4>
          <ul class="footer-links">
            <li><a href="track-order.php">Track Your Order</a></li>
            <li><button type="button" id="footerSizeGuideBtn">Size Guide &amp; Fit</button></li>
            <li><a href="custom-fit.php">Custom Tailoring Request</a></li>
            <li><a href="lookbook.php">Instagram Lookbook</a></li>
            <li><a href="admin.php">Boutique Atelier Admin</a></li>
          </ul>
        </div>
        <div>
          <h4 class="footer-heading">Connect &amp; Shipping</h4>
          <ul class="footer-links">
            <li>Instagram: @paherveshbyakshree</li>
            <li>Studio: Jaipur, Rajasthan, India</li>
            <li>Shipping: Pan-India &amp; 28+ Countries</li>
            <li>Promo Codes: <strong>AKSHREE10</strong> • <strong>PAHERVESH15</strong></li>
          </ul>
        </div>
      </div>

      <div class="footer-bottom">
        <span>© <?= date('Y') ?> PAHERVESH by Akshree. All rights reserved.</span>
        <span>Crafted with pure cotton &amp; love in India</span>
      </div>
    </div>
  </footer>

  <!-- Mobile Bottom Action Bar -->
  <nav class="mobile-bottom-bar" aria-label="Mobile Quick Navigation">
    <a href="index.php" class="mobile-bar-btn <?= $activeNav === 'home' ? 'active' : '' ?>">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
      <span>Home</span>
    </a>
    <a href="shop.php" class="mobile-bar-btn <?= in_array($activeNav, ['shop','farshi','suits','coords','kurtis'], true) ? 'active' : '' ?>">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
      <span>Shop</span>
    </a>
    <button type="button" class="mobile-bar-btn" id="mobileWishlistBtn">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
      <span>Saved</span>
    </button>
    <button type="button" class="mobile-bar-btn" id="mobileCartBtn">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><path d="M3 6h18"/></svg>
      <span>Bag (<em id="mobileCartCount" style="font-style:normal;">0</em>)</span>
    </button>
  </nav>

  <!-- Floating Concierge -->
  <a href="custom-fit.php" class="floating-concierge" id="conciergeBtn">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M20.52 3.48A11.86 11.86 0 0 0 12.06 0C5.5 0 .16 5.34.16 11.9c0 2.1.55 4.15 1.6 5.95L0 24l6.32-1.66a11.87 11.87 0 0 0 5.74 1.46h.01c6.56 0 11.9-5.34 11.9-11.9 0-3.18-1.24-6.17-3.45-8.42z"/></svg>
    <span>DM / Styling Help</span>
  </a>

  <div class="backdrop" id="backdrop"></div>

  <!-- Mobile Navigation Drawer -->
  <aside class="drawer left" id="mobileDrawer" aria-label="Mobile Navigation">
    <div class="drawer-header">
      <div class="drawer-title">Pahervesh Menu</div>
      <button class="icon-btn" data-close-drawers aria-label="Close Menu">✕</button>
    </div>
    <div class="drawer-body">
      <div style="display:grid;gap:10px;">
        <a href="index.php" class="btn btn-secondary">Home</a>
        <a href="shop.php" class="btn btn-secondary">Shop All Collections</a>
        <a href="farshi-sets.php" class="btn btn-secondary">Farshi Sets</a>
        <a href="suit-sets.php" class="btn btn-secondary">Suit Sets</a>
        <a href="coord-sets.php" class="btn btn-secondary">Co-ord Sets</a>
        <a href="kurtis.php" class="btn btn-secondary">Office Kurtis</a>
        <a href="lookbook.php" class="btn btn-secondary">Instagram Lookbook</a>
        <a href="track-order.php" class="btn btn-primary">Track Your Order</a>
        <a href="custom-fit.php" class="btn btn-secondary">Custom Fit &amp; Tailoring</a>
        <div class="field" style="margin-top:12px;">
          <label for="mobileCurrencySelect">Currency (Worldwide Shipping)</label>
          <select id="mobileCurrencySelect">
            <option value="INR">INR ₹ (India)</option>
            <option value="USD">USD $ (International)</option>
            <option value="GBP">GBP £ (United Kingdom)</option>
            <option value="AED">AED (UAE)</option>
          </select>
        </div>
      </div>
    </div>
  </aside>

  <!-- Shopping Bag Drawer -->
  <aside class="drawer" id="cartDrawer" aria-label="Shopping Bag">
    <div class="drawer-header">
      <div class="drawer-title">Your Bag (<span id="drawerBagCount">0</span>)</div>
      <button class="icon-btn" data-close-drawers aria-label="Close Bag">✕</button>
    </div>
    <div class="drawer-body">
      <div class="shipping-meter">
        <div id="shippingMeterText">Add ₹1,999 more for Free Pan-India Shipping!</div>
        <div class="meter-track"><div class="meter-fill" id="shippingMeterFill" style="width:0%"></div></div>
      </div>
      <div id="cartItemsList"></div>
    </div>
    <div class="drawer-footer">
      <div style="display:flex;gap:8px;margin-bottom:12px;">
        <input type="text" id="cartCouponInput" placeholder="Coupon (try AKSHREE10)" style="flex:1;padding:9px 12px;border:1px solid var(--border-strong);border-radius:10px;background:#fff;font-size:0.84rem;">
        <button type="button" class="btn btn-secondary btn-sm" id="applyCouponBtn">Apply</button>
      </div>
      <div id="couponStatusMsg" style="font-size:0.78rem;margin-bottom:8px;"></div>
      <div style="display:grid;gap:6px;font-size:0.88rem;margin-bottom:14px;">
        <div style="display:flex;justify-content:space-between;"><span>Subtotal</span><strong id="cartSubtotalDisplay">₹0</strong></div>
        <div style="display:flex;justify-content:space-between;color:var(--sage);" id="cartDiscountRow" hidden><span>Discount</span><strong id="cartDiscountDisplay">-₹0</strong></div>
        <div style="display:flex;justify-content:space-between;font-size:1.05rem;padding-top:6px;border-top:1px solid var(--border);"><strong>Estimated Total</strong><strong id="cartTotalDisplay">₹0</strong></div>
      </div>
      <button type="button" class="btn btn-primary" style="width:100%;" id="proceedCheckoutBtn">Proceed to Checkout →</button>
    </div>
  </aside>

  <!-- Wishlist Drawer -->
  <aside class="drawer" id="wishlistDrawer" aria-label="Saved Wishlist">
    <div class="drawer-header">
      <div class="drawer-title">Saved Favorites</div>
      <button class="icon-btn" data-close-drawers aria-label="Close Wishlist">✕</button>
    </div>
    <div class="drawer-body" id="wishlistItemsList"></div>
  </aside>

  <!-- Product Quick View Modal -->
  <div class="modal" id="productModal" aria-hidden="true">
    <div class="modal-card">
      <button class="modal-close" data-close-modals aria-label="Close Product Modal">✕</button>
      <div class="qv-grid" id="productModalContent"></div>
    </div>
  </div>

  <!-- Multi-Step Checkout Modal -->
  <div class="modal" id="checkoutModal" aria-hidden="true">
    <div class="modal-card narrow">
      <button class="modal-close" data-close-modals aria-label="Close Checkout">✕</button>
      <div style="padding:28px;" id="checkoutModalContent"></div>
    </div>
  </div>

  <!-- Track Order Modal -->
  <div class="modal" id="trackModal" aria-hidden="true">
    <div class="modal-card narrow">
      <button class="modal-close" data-close-modals aria-label="Close Order Tracker">✕</button>
      <div style="padding:28px;">
        <span class="section-kicker">Live Parcel Status</span>
        <h3 class="section-title" style="font-size:2rem;margin-bottom:8px;">Track Your Order</h3>
        <p style="font-size:0.86rem;color:var(--ink-muted);margin-bottom:16px;">Enter your Pahervesh Order ID or try a sample demo order below:</p>
        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px;">
          <button type="button" class="filter-pill sample-order-chip" data-order="PHV-2026-1042">PHV-2026-1042 (Jaipur)</button>
          <button type="button" class="filter-pill sample-order-chip" data-order="PHV-2026-1089">PHV-2026-1089 (London UK)</button>
        </div>
        <form id="trackOrderForm" style="display:flex;gap:8px;">
          <input type="text" id="trackOrderInput" placeholder="e.g. PHV-2026-1042" style="flex:1;padding:11px 14px;border:1px solid var(--border-strong);border-radius:12px;background:#fff;" required>
          <button type="submit" class="btn btn-primary btn-sm">Track</button>
        </form>
        <div id="trackOrderResult" style="margin-top:18px;"></div>
      </div>
    </div>
  </div>

  <!-- Size Guide Modal -->
  <div class="modal" id="sizeGuideModal" aria-hidden="true">
    <div class="modal-card narrow">
      <button class="modal-close" data-close-modals aria-label="Close Size Guide">✕</button>
      <div style="padding:28px;">
        <span class="section-kicker">Pahervesh Fit Chart</span>
        <h3 class="section-title" style="font-size:2rem;margin-bottom:10px;">Cotton Ethnic Size Guide</h3>
        <div style="display:flex;gap:8px;margin-bottom:14px;">
          <button type="button" class="filter-pill active" id="unitInBtn">Inches</button>
          <button type="button" class="filter-pill" id="unitCmBtn">Centimeters</button>
        </div>
        <div style="overflow-x:auto;">
          <table style="width:100%;border-collapse:collapse;font-size:0.86rem;background:#fff;border-radius:12px;overflow:hidden;" id="sizeChartTable"></table>
        </div>
        <p style="font-size:0.8rem;color:var(--ink-muted);margin-top:14px;">
          Tip: Our pure cotton Farshi &amp; Suit Sets are pre-shrunk with a comfortable 1.5-inch margin inside seams. Between sizes? Pick <strong>Custom</strong>!
        </p>
      </div>
    </div>
  </div>

  <!-- Custom Tailoring Modal -->
  <div class="modal" id="customModal" aria-hidden="true">
    <div class="modal-card narrow">
      <button class="modal-close" data-close-modals aria-label="Close Custom Tailoring">✕</button>
      <div style="padding:28px;">
        <span class="section-kicker">Atelier Concierge</span>
        <h3 class="section-title" style="font-size:2rem;margin-bottom:6px;">Custom Fit &amp; DM Styling</h3>
        <p style="font-size:0.86rem;color:var(--ink-muted);margin-bottom:18px;">Share your measurements or occasion notes — Akshree’s atelier will tailor or reserve your cotton piece.</p>
        <form id="customEnquiryForm">
          <div class="form-grid">
            <div class="field"><label>Your Name *</label><input type="text" name="name" required placeholder="e.g. Meera Kapoor"></div>
            <div class="field"><label>WhatsApp / Instagram Handle *</label><input type="text" name="contact" required placeholder="+91 98... or @handle"></div>
          </div>
          <div class="field"><label>Outfit / Category of Interest</label><input type="text" name="product_name" id="enquiryProductField" placeholder="e.g. Gulab Lotus Farshi Set"></div>
          <div class="form-grid">
            <div class="field"><label>Bust / Chest (in)</label><input type="text" name="bust" placeholder="e.g. 36"></div>
            <div class="field"><label>Kurta Length (in)</label><input type="text" name="length" placeholder="e.g. 44"></div>
          </div>
          <div class="field"><label>Styling Notes / Delivery Date *</label><textarea name="notes" rows="3" required placeholder="Tell us your event date, preferred sleeve style, or international city..."></textarea></div>
          <button type="submit" class="btn btn-primary" style="width:100%;">Send to Akshree’s Studio</button>
        </form>
        <div id="customEnquiryResult" style="margin-top:12px;font-size:0.86rem;"></div>
      </div>
    </div>
  </div>

  <div class="toast-container" id="toastContainer"></div>

  <script>
    window.__INITIAL_PRODUCTS__ = <?= json_encode($allProducts, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    window.__INITIAL_CATEGORY__ = <?= json_encode($initialCategory, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
  </script>
  <script src="assets/js/app.js"></script>
</body>
</html>
    <?php
}
