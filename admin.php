<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

$pdo = get_db();
$orders = $pdo->query('SELECT * FROM orders ORDER BY id DESC')->fetchAll();
$products = $pdo->query('SELECT * FROM products ORDER BY id ASC')->fetchAll();
$enquiries = $pdo->query('SELECT * FROM custom_enquiries ORDER BY id DESC LIMIT 20')->fetchAll();
$subCount = (int)$pdo->query('SELECT COUNT(*) FROM newsletter_subscribers')->fetchColumn();
$revenue = (int)$pdo->query('SELECT COALESCE(SUM(total_inr), 0) FROM orders')->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Boutique Atelier Admin | PAHERVESH by Akshree</title>
  <link rel="icon" type="image/jpeg" href="assets/images/logo.jpg">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/styles.css">
  <style>
    .admin-wrap { padding: 34px 0 64px; }
    .kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin: 22px 0 30px; }
    .kpi-card { background: #fff; border: 1px solid var(--border); border-radius: 16px; padding: 20px; box-shadow: var(--shadow-sm); }
    .kpi-card small { font-size: 0.74rem; text-transform: uppercase; letter-spacing: 0.12em; color: var(--ink-muted); }
    .kpi-card strong { display: block; font-family: var(--font-display); font-size: 2.1rem; color: var(--maroon); margin-top: 4px; }
    .panel { background: #fff; border: 1px solid var(--border); border-radius: 18px; padding: 22px; margin-bottom: 24px; box-shadow: var(--shadow-sm); overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; font-size: 0.86rem; }
    th, td { text-align: left; padding: 11px 10px; border-bottom: 1px solid var(--border); }
    th { font-size: 0.76rem; text-transform: uppercase; letter-spacing: 0.08em; color: var(--ink-muted); }
    @media (max-width: 780px) { .kpi-grid { grid-template-columns: 1fr 1fr; } }
  </style>
</head>
<body>
  <header class="site-header">
    <div class="container nav-inner">
      <a href="index.php" class="brand-lockup">
        <div class="brand-badge">
          <img src="assets/images/logo.jpg" alt="Pahervesh by Akshree">
        </div>
        <div class="brand-text-wrap">
          <span class="brand-name">PAHERVESH</span>
          <span class="brand-by">Boutique Atelier Admin</span>
        </div>
      </a>
      <a href="index.php" class="btn btn-primary btn-sm">← Back to Storefront</a>
    </div>
  </header>

  <main class="container admin-wrap">
    <span class="section-kicker">Client Demo Console</span>
    <h1 class="section-title">Akshree’s Studio Dashboard</h1>

    <div class="kpi-grid">
      <div class="kpi-card"><small>Total Orders</small><strong><?= count($orders) ?></strong></div>
      <div class="kpi-card"><small>Gross Revenue</small><strong>₹<?= number_format($revenue) ?></strong></div>
      <div class="kpi-card"><small>Active Pieces</small><strong><?= count($products) ?></strong></div>
      <div class="kpi-card"><small>Newsletter Club</small><strong><?= $subCount ?></strong></div>
    </div>

    <section class="panel">
      <h2 style="font-family:var(--font-display);font-size:1.65rem;margin-bottom:14px;">Recent Orders &amp; Fulfillment Status</h2>
      <table>
        <thead>
          <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Destination</th>
            <th>Total</th>
            <th>Payment</th>
            <th>Status (Live Update)</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($orders as $o): ?>
          <tr>
            <td><strong><?= htmlspecialchars($o['order_number']) ?></strong></td>
            <td><?= htmlspecialchars($o['customer_name']) ?><br><small style="color:var(--ink-muted);"><?= htmlspecialchars($o['customer_phone']) ?></small></td>
            <td><?= htmlspecialchars($o['city']) ?>, <?= htmlspecialchars($o['country']) ?></td>
            <td><strong>₹<?= number_format((int)$o['total_inr']) ?></strong></td>
            <td><?= htmlspecialchars($o['payment_method']) ?></td>
            <td>
              <select class="sort-select status-updater" data-order="<?= htmlspecialchars($o['order_number']) ?>">
                <?php foreach (['Confirmed', 'Hand-Packed with Ribbon', 'Dispatched', 'Out for Delivery', 'Delivered'] as $st): ?>
                  <option value="<?= $st ?>" <?= $o['status'] === $st ? 'selected' : '' ?>><?= $st ?></option>
                <?php endforeach; ?>
              </select>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </section>

    <section class="panel">
      <h2 style="font-family:var(--font-display);font-size:1.65rem;margin-bottom:14px;">Add New Cotton Piece to Storefront</h2>
      <form id="adminAddProductForm" class="form-grid">
        <div class="field"><label>Piece Name *</label><input type="text" name="name" required placeholder="e.g. Champa Ivory Farshi Set"></div>
        <div class="field"><label>Category</label>
          <select name="category">
            <option>Farshi Sets</option>
            <option>Suit Sets</option>
            <option>Co-ord Sets</option>
            <option>Office Kurtis</option>
            <option>Pre-Orders</option>
          </select>
        </div>
        <div class="field"><label>Price (INR) *</label><input type="number" name="price_inr" required min="500" placeholder="2590"></div>
        <div class="field"><label>Craft / Print</label><input type="text" name="craft" placeholder="Sanganeri Hand-Block Print"></div>
        <div class="field"><label>Photo Asset</label>
          <select name="image">
            <option value="assets/images/prod-blush-farshi.jpg">Blush Farshi Look</option>
            <option value="assets/images/prod-crimson-farshi.jpg">Crimson Kalamkari Look</option>
            <option value="assets/images/prod-citrus-suit.jpg">Citrus Mustard Look</option>
            <option value="assets/images/prod-sage-coord.jpg">Sage Co-ord Look</option>
            <option value="assets/images/prod-indigo-bandhani.jpg">Indigo Bandhani Look</option>
          </select>
        </div>
        <div class="field" style="justify-content:flex-end;">
          <button type="submit" class="btn btn-primary">Publish Piece Live</button>
        </div>
      </form>
      <div id="adminProductMsg" style="font-size:0.86rem;margin-top:8px;"></div>
    </section>

    <section class="panel">
      <h2 style="font-family:var(--font-display);font-size:1.65rem;margin-bottom:14px;">Custom Tailoring &amp; DM Enquiries</h2>
      <?php if (!$enquiries): ?>
        <p style="color:var(--ink-muted);font-size:0.86rem;">No custom enquiries submitted yet. Submit one from the storefront’s Custom Fit modal to see it appear here!</p>
      <?php else: ?>
        <table>
          <thead><tr><th>Name</th><th>Contact</th><th>Outfit</th><th>Notes</th></tr></thead>
          <tbody>
            <?php foreach ($enquiries as $eq): ?>
            <tr>
              <td><strong><?= htmlspecialchars($eq['name']) ?></strong></td>
              <td><?= htmlspecialchars($eq['contact']) ?></td>
              <td><?= htmlspecialchars($eq['product_name']) ?></td>
              <td><?= htmlspecialchars($eq['notes']) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </section>
  </main>

  <script>
    document.querySelectorAll('.status-updater').forEach((sel) => {
      sel.addEventListener('change', async () => {
        await fetch('api/orders.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            action: 'update_status',
            order_number: sel.dataset.order,
            status: sel.value
          })
        });
      });
    });

    document.getElementById('adminAddProductForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      const fd = new FormData(e.currentTarget);
      const res = await fetch('api/products.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(Object.fromEntries(fd.entries()))
      });
      const data = await res.json();
      const msg = document.getElementById('adminProductMsg');
      if (res.ok) {
        msg.style.color = 'var(--sage)';
        msg.textContent = '✓ Published! Refresh or return to Storefront to see it live.';
        e.currentTarget.reset();
      } else {
        msg.style.color = 'var(--maroon)';
        msg.textContent = data.error || 'Failed to publish.';
      }
    });
  </script>
</body>
</html>
