/* ==========================================================================
   PAHERVESH BY AKSHREE — Full Interactive E-Commerce Storefront Engine
   ========================================================================== */

(() => {
  'use strict';

  const CURRENCIES = {
    INR: { symbol: '₹', rate: 1, decimals: 0 },
    USD: { symbol: '$', rate: 0.012, decimals: 2 },
    GBP: { symbol: '£', rate: 0.0095, decimals: 2 },
    AED: { symbol: 'AED ', rate: 0.044, decimals: 0 },
  };

  const state = {
    products: Array.isArray(window.__INITIAL_PRODUCTS__) ? window.__INITIAL_PRODUCTS__ : [],
    filteredProducts: [],
    category: window.__INITIAL_CATEGORY__ || 'All',
    search: '',
    sort: 'featured',
    currency: localStorage.getItem('phv_currency') || 'INR',
    cart: JSON.parse(localStorage.getItem('phv_cart') || '[]'),
    wishlist: JSON.parse(localStorage.getItem('phv_wishlist') || '[]'),
    coupon: null,
    activeProduct: null,
    selectedSize: 'M',
    checkoutStep: 1,
    checkoutData: {
      country: 'India',
      shipping_method: 'Standard Pan-India',
      payment_method: 'UPI / Instant Pay',
      gift_wrap: true,
    },
  };

  const $ = (sel, root = document) => root.querySelector(sel);
  const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));

  function formatMoney(inrAmount) {
    const cfg = CURRENCIES[state.currency] || CURRENCIES.INR;
    const converted = inrAmount * cfg.rate;
    if (cfg.decimals === 0) {
      return cfg.symbol + Math.round(converted).toLocaleString('en-IN');
    }
    return cfg.symbol + converted.toFixed(cfg.decimals);
  }

  function escapeHtml(str) {
    return String(str ?? '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  function showToast(msg) {
    const wrap = $('#toastContainer');
    if (!wrap) return;
    const t = document.createElement('div');
    t.className = 'toast';
    t.textContent = msg;
    wrap.appendChild(t);
    setTimeout(() => {
      t.style.opacity = '0';
      t.style.transform = 'translateX(20px)';
      t.style.transition = 'all 0.3s ease';
      setTimeout(() => t.remove(), 320);
    }, 3000);
  }

  function saveState() {
    localStorage.setItem('phv_cart', JSON.stringify(state.cart));
    localStorage.setItem('phv_wishlist', JSON.stringify(state.wishlist));
    localStorage.setItem('phv_currency', state.currency);
  }

  /* --------------------------------------------------------------------------
     PRELOADER, SCROLL PROGRESS, PARALLAX, PETALS & REVEAL CHOREOGRAPHY
     -------------------------------------------------------------------------- */
  function initChrome() {
    const preloader = $('#preloader');
    window.addEventListener('load', () => {
      setTimeout(() => preloader?.classList.add('hidden'), 550);
    });
    setTimeout(() => preloader?.classList.add('hidden'), 1800);

    // Inject Back-to-Top circular scroll progress button
    let topBtn = $('#scrollTopBtn');
    if (!topBtn) {
      topBtn = document.createElement('button');
      topBtn.id = 'scrollTopBtn';
      topBtn.className = 'scroll-top-btn';
      topBtn.setAttribute('aria-label', 'Back to Top');
      topBtn.innerHTML = '<span class="scroll-top-inner">↑</span>';
      topBtn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
      document.body.appendChild(topBtn);
    }

    // Inject decorative scroll-driven floating petals
    let petalsLayer = $('.scroll-petals');
    const petals = [];
    if (!petalsLayer && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      petalsLayer = document.createElement('div');
      petalsLayer.className = 'scroll-petals';
      const positions = [
        { left: '4%', top: '18%', speed: 0.14, rot: 22 },
        { left: '93%', top: '28%', speed: -0.18, rot: -35 },
        { left: '8%', top: '62%', speed: 0.22, rot: 45 },
        { left: '89%', top: '72%', speed: -0.15, rot: -20 },
        { left: '48%', top: '42%', speed: 0.1, rot: 15 },
      ];
      positions.forEach((cfg) => {
        const p = document.createElement('div');
        p.className = 'scroll-petal';
        p.style.left = cfg.left;
        p.style.top = cfg.top;
        petalsLayer.appendChild(p);
        petals.push({ el: p, speed: cfg.speed, baseRot: cfg.rot });
      });
      document.body.appendChild(petalsLayer);
    }

    const progress = $('#scrollProgress');
    const header = $('#siteHeader');
    const heroImg = $('.hero-img');
    const heroFloat = $('#heroFeaturedCard');
    const aura1 = $('.hero-aura-1');
    const aura2 = $('.hero-aura-2');
    const storyImg = $('.story-media img');
    const storySeal = $('.story-seal');

    let ticking = false;
    const onScrollFrame = () => {
      ticking = false;
      const scrollTop = window.scrollY;
      const docH = document.documentElement.scrollHeight - window.innerHeight;
      const pct = docH > 0 ? Math.min(100, (scrollTop / docH) * 100) : 0;

      if (progress) progress.style.width = pct + '%';
      header?.classList.toggle('scrolled', scrollTop > 24);

      topBtn.classList.toggle('visible', scrollTop > 380);
      topBtn.style.setProperty('--scroll-deg', (pct * 3.6).toFixed(1) + 'deg');

      // Smooth Hero & Aura Parallax
      const activeHeroImg = $('.hero-slide.active .hero-slide-img');
      if (activeHeroImg && scrollTop < 950) {
        activeHeroImg.style.transform = `translate3d(0, ${(scrollTop * 0.08).toFixed(1)}px, 0) scale(${(1.06 + scrollTop * 0.00012).toFixed(4)})`;
      }
      if (heroFloat && scrollTop < 900) {
        heroFloat.style.transform = `translate3d(0, ${(-scrollTop * 0.06).toFixed(1)}px, 0)`;
      }
      if (aura1) aura1.style.transform = `translate3d(${(scrollTop * 0.04).toFixed(1)}px, ${(scrollTop * 0.09).toFixed(1)}px, 0)`;
      if (aura2) aura2.style.transform = `translate3d(${(-scrollTop * 0.05).toFixed(1)}px, ${(-scrollTop * 0.07).toFixed(1)}px, 0)`;

      // Story section scroll parallax
      if (storyImg) {
        const rect = storyImg.getBoundingClientRect();
        if (rect.top < window.innerHeight && rect.bottom > 0) {
          const offset = (window.innerHeight * 0.5 - (rect.top + rect.height * 0.5)) * 0.06;
          storyImg.style.transform = `scale(1.06) translate3d(0, ${offset.toFixed(1)}px, 0)`;
          if (storySeal) storySeal.style.transform = `translate3d(0, ${(-offset * 1.3).toFixed(1)}px, 0)`;
        }
      }

      // Floating petals scroll choreography
      petals.forEach((pt, idx) => {
        const y = Math.sin(scrollTop * 0.004 + idx) * 28 + scrollTop * pt.speed * 0.35;
        const x = Math.cos(scrollTop * 0.003 + idx) * 18;
        const r = pt.baseRot + scrollTop * (idx % 2 === 0 ? 0.08 : -0.08);
        pt.el.style.transform = `translate3d(${x.toFixed(1)}px, ${y.toFixed(1)}px, 0) rotate(${r.toFixed(1)}deg)`;
      });
    };

    window.addEventListener('scroll', () => {
      if (!ticking) {
        ticking = true;
        requestAnimationFrame(onScrollFrame);
      }
    }, { passive: true });
    onScrollFrame();
  }

  let revealObserver;
  function applyChoreographyClasses() {
    // Assign directional & clip-path reveals automatically across pages
    $$('.section-header').forEach((el) => el.classList.add('reveal'));
    $$('.trust-grid > *, .testimonials-grid > *, .story-points > *').forEach((el, idx) => {
      el.classList.add('reveal', 'reveal-zoom');
      el.style.transitionDelay = `${(idx % 4) * 85}ms`;
    });
    $$('.categories-grid > *, .ig-grid > *').forEach((el, idx) => {
      el.classList.add('reveal', 'reveal-clip');
      el.style.transitionDelay = `${(idx % 6) * 75}ms`;
    });
    $$('.story-media, .page-hero-copy').forEach((el) => el.classList.add('reveal', 'reveal-left'));
    $$('.story-grid > div:last-child, .page-hero-media').forEach((el) => el.classList.add('reveal', 'reveal-right'));
    $$('.newsletter-box').forEach((el) => el.classList.add('reveal', 'reveal-zoom'));
  }

  function animateMetricCounters() {
    $$('.metric-val').forEach((el) => {
      if (el.dataset.animated) return;
      const raw = el.textContent.trim();
      const match = raw.match(/^([\d.]+)(.*)$/);
      if (!match) return;
      el.dataset.animated = '1';
      const target = parseFloat(match[1]);
      const suffix = match[2] || '';
      const isDecimal = raw.includes('.');
      const start = performance.now();
      const dur = 1100;
      const step = (now) => {
        const t = Math.min(1, (now - start) / dur);
        const eased = 1 - Math.pow(1 - t, 3);
        const val = target * eased;
        el.textContent = (isDecimal ? val.toFixed(1) : Math.round(val)) + suffix;
        if (t < 1) requestAnimationFrame(step);
      };
      requestAnimationFrame(step);
    });
  }

  function observeReveals() {
    applyChoreographyClasses();
    if (!revealObserver) {
      revealObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('in-view');
            if (entry.target.querySelector('.metric-val')) {
              animateMetricCounters();
            }
            revealObserver.unobserve(entry.target);
          }
        });
      }, { threshold: 0.08, rootMargin: '0px 0px -24px 0px' });
    }
    $$('.reveal:not(.in-view)').forEach((el) => revealObserver.observe(el));
    // Trigger counters immediately if hero already visible
    if ($('.hero-metrics-couture')) animateMetricCounters();
  }

  function initHeroSlideshow() {
    const slides = $$('.hero-slide');
    const tabs = $$('.hero-slide-tab');
    const card = $('#heroFeaturedCard');
    if (!slides.length || !tabs.length) return;

    const campaigns = [
      {
        pid: 1,
        titleHtml: 'Heirloom Craft,<br><em>Breezy Everyday</em><br>Elegance.',
        desc: 'Hand-block printed Farshi Sets, featherlight Mulmul Suit Sets, and modern Indo-Western Co-ords — stitched in pure breathable cotton and delivered across India & Worldwide.',
        ctaText: 'Explore Farshi Sets',
        ctaHref: 'farshi-sets.php',
        cardImg: 'assets/images/prod-blush-farshi.jpg',
        cardTag: 'Look 01 • Signature Bestseller',
        cardTitle: 'Gulab Lotus Farshi Set',
        cardInr: 2890,
      },
      {
        pid: 2,
        titleHtml: 'Sindoor Kalamkari,<br><em>Woven for</em><br>Celebrations.',
        desc: 'Deep crimson botanical motifs hand-printed on breathable handloom cotton with graceful wide-pleated Farshi trousers.',
        ctaText: 'Shop Festive Farshi',
        ctaHref: 'farshi-sets.php',
        cardImg: 'assets/images/prod-crimson-farshi.jpg',
        cardTag: 'Look 02 • Festive Edit',
        cardTitle: 'Sindoor Kalamkari Farshi Set',
        cardInr: 3190,
      },
      {
        pid: 3,
        titleHtml: 'Sunlit Mulmul,<br><em>Light as</em><br>Morning.',
        desc: 'Citrus marigold block-printed 3-piece cotton suit sets with airy 2.5m dupattas made for destination haldis and sunlit brunches.',
        ctaText: 'Explore Suit Sets',
        ctaHref: 'suit-sets.php',
        cardImg: 'assets/images/prod-citrus-suit.jpg',
        cardTag: 'Look 03 • Summer Unfold',
        cardTitle: 'Citrus Sunbeam Cotton Suit',
        cardInr: 2490,
      },
    ];

    let activeIdx = 0;
    let timer;

    const goToSlide = (idx) => {
      activeIdx = (idx + campaigns.length) % campaigns.length;
      const c = campaigns[activeIdx];
      slides.forEach((s, i) => s.classList.toggle('active', i === activeIdx));
      tabs.forEach((t, i) => t.classList.toggle('active', i === activeIdx));

      const titleEl = $('#heroSlideTitle');
      const descEl = $('#heroSlideDesc');
      const ctaEl = $('#heroPrimaryCta');
      if (titleEl) titleEl.innerHTML = c.titleHtml;
      if (descEl) descEl.textContent = c.desc;
      if (ctaEl) {
        ctaEl.href = c.ctaHref;
        ctaEl.querySelector('span').textContent = c.ctaText;
      }
      if (card) {
        card.dataset.activePid = String(c.pid);
        $('#heroCardImg').src = c.cardImg;
        $('#heroCardTag').textContent = c.cardTag;
        $('#heroCardTitle').textContent = c.cardTitle;
        const priceEl = $('#heroCardPrice');
        priceEl.dataset.inr = String(c.cardInr);
        priceEl.textContent = formatMoney(c.cardInr);
      }
    };

    const startAuto = () => {
      clearInterval(timer);
      timer = setInterval(() => goToSlide(activeIdx + 1), 6000);
    };

    tabs.forEach((t) => {
      t.addEventListener('click', () => {
        goToSlide(Number(t.dataset.slideTo));
        startAuto();
      });
    });

    const heroSec = $('#heroSection');
    heroSec?.addEventListener('mouseenter', () => clearInterval(timer));
    heroSec?.addEventListener('mouseleave', startAuto);

    goToSlide(0);
    startAuto();
  }

  /* --------------------------------------------------------------------------
     CATALOG RENDER & API FILTERING
     -------------------------------------------------------------------------- */
  async function fetchProducts() {
    const params = new URLSearchParams();
    if (state.category !== 'All') params.set('category', state.category);
    if (state.search.trim()) params.set('q', state.search.trim());
    if (state.sort) params.set('sort', state.sort);

    try {
      const res = await fetch('api/products.php?' + params.toString());
      const data = await res.json();
      if (Array.isArray(data.products)) {
        state.filteredProducts = data.products;
        renderCatalog();
        return;
      }
    } catch (_) {
      // Fallback to client-side filtering if offline
    }
    applyClientFilterFallback();
  }

  function applyClientFilterFallback() {
    let list = [...state.products];
    if (state.category !== 'All') {
      list = list.filter((p) =>
        state.category === 'Pre-Orders' ? (p.category === 'Pre-Orders' || p.is_preorder) : p.category === state.category
      );
    }
    if (state.search.trim()) {
      const q = state.search.toLowerCase();
      list = list.filter((p) =>
        [p.name, p.subtitle, p.craft, p.fabric, p.color_name, p.category].some((f) =>
          String(f).toLowerCase().includes(q)
        )
      );
    }
    if (state.sort === 'price_asc') list.sort((a, b) => a.price_inr - b.price_inr);
    else if (state.sort === 'price_desc') list.sort((a, b) => b.price_inr - a.price_inr);
    else if (state.sort === 'rating') list.sort((a, b) => b.rating - a.rating);
    else if (state.sort === 'newest') list.sort((a, b) => b.id - a.id);

    state.filteredProducts = list;
    renderCatalog();
  }

  function renderCatalog() {
    const grid = $('#productsGrid');
    const counter = $('#catalogResultCount');
    if (!grid) return;

    const list = state.filteredProducts;
    if (counter) {
      counter.textContent = `Showing ${list.length} handcrafted cotton ${list.length === 1 ? 'piece' : 'pieces'}${
        state.category !== 'All' ? ` in ${state.category}` : ''
      }`;
    }

    if (!list.length) {
      grid.innerHTML = `
        <div style="grid-column:1/-1;text-align:center;padding:56px 20px;background:#fff;border-radius:18px;border:1px solid var(--border);">
          <h3 style="font-family:var(--font-display);font-size:1.8rem;">No matching cotton pieces found</h3>
          <p style="color:var(--ink-muted);margin:8px 0 18px;">Try clearing your search or switching back to All Collections.</p>
          <button type="button" class="btn btn-primary btn-sm" id="resetFiltersBtn">View All Pieces</button>
        </div>`;
      $('#resetFiltersBtn')?.addEventListener('click', () => {
        state.category = 'All';
        state.search = '';
        $('#catalogSearch').value = '';
        updateCategoryTabsUI();
        fetchProducts();
      });
      return;
    }

    grid.innerHTML = list.map((p, idx) => {
      const wished = state.wishlist.includes(p.id);
      const primaryImg = p.images[0] || 'assets/images/prod-blush-farshi.jpg';
      const secondaryImg = p.images[1] || primaryImg;
      const discountPct = p.compare_price_inr > p.price_inr
        ? Math.round(((p.compare_price_inr - p.price_inr) / p.compare_price_inr) * 100)
        : 0;

      return `
        <article class="product-card reveal reveal-zoom" style="transition-delay:${Math.min((idx % 4) * 75, 240)}ms" data-pid="${p.id}">
          <div class="product-media" data-open-product="${p.id}">
            ${p.badge ? `<span class="product-badge ${p.is_preorder ? 'preorder' : ''}">${escapeHtml(p.badge)}</span>` : ''}
            <button type="button" class="wishlist-toggle ${wished ? 'active' : ''}" data-toggle-wish="${p.id}" aria-label="Toggle Wishlist">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="${wished ? 'currentColor' : 'none'}" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
            </button>
            <img src="${escapeHtml(primaryImg)}" alt="${escapeHtml(p.name)}" class="primary-img" loading="lazy">
            <img src="${escapeHtml(secondaryImg)}" alt="${escapeHtml(p.name)} alternate view" class="secondary-img" loading="lazy">
            <div class="quick-add-bar" onclick="event.stopPropagation()">
              <div style="font-size:0.68rem;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-muted);text-align:center;margin-bottom:5px;font-weight:700;">Quick Add Size</div>
              <div class="quick-sizes">
                ${p.sizes.slice(0, 6).map((sz) => `<button type="button" class="quick-size-btn" data-quick-add="${p.id}" data-size="${escapeHtml(sz)}">${escapeHtml(sz)}</button>`).join('')}
              </div>
            </div>
          </div>

          <div class="product-body">
            <div class="product-meta-top">
              <span>${escapeHtml(p.category)}</span>
              <span class="product-rating">★ ${p.rating.toFixed(1)}</span>
            </div>
            <h3 class="product-title" data-open-product="${p.id}">${escapeHtml(p.name)}</h3>
            <p class="product-craft">${escapeHtml(p.craft)}</p>
            <div class="product-footer">
              <div class="price-wrap">
                <span class="price-current">${formatMoney(p.price_inr)}</span>
                ${discountPct > 0 ? `<span class="price-compare">${formatMoney(p.compare_price_inr)}</span>` : ''}
              </div>
              <span class="color-dot" title="${escapeHtml(p.color_name)}" style="background:${escapeHtml(p.color_hex)}"></span>
            </div>
          </div>
        </article>`;
    }).join('');

    observeReveals();
    refreshDynamicPrices();
  }

  function refreshDynamicPrices() {
    $$('.price-dynamic').forEach((el) => {
      const inr = Number(el.dataset.inr || 0);
      if (inr > 0) el.textContent = formatMoney(inr);
    });
  }

  function updateCategoryTabsUI() {
    $$('#categoryTabs .filter-pill').forEach((btn) => {
      btn.classList.toggle('active', btn.dataset.category === state.category);
    });
  }

  /* --------------------------------------------------------------------------
     PRODUCT QUICK VIEW & REVIEWS & SHIPPING ESTIMATOR
     -------------------------------------------------------------------------- */
  async function openProductModal(productId) {
    let product = state.products.find((p) => p.id === Number(productId));
    try {
      const res = await fetch('api/products.php?id=' + Number(productId));
      const data = await res.json();
      if (data.product) product = data.product;
    } catch (_) {
      // use local fallback
    }
    if (!product) return;

    state.activeProduct = product;
    state.selectedSize = product.sizes.includes('M') ? 'M' : (product.sizes[0] || 'M');

    const content = $('#productModalContent');
    const imgs = product.images.length ? product.images : ['assets/images/prod-blush-farshi.jpg'];
    const reviews = product.reviews || [];
    const wished = state.wishlist.includes(product.id);

    content.innerHTML = `
      <div>
        <div class="qv-main-img-wrap" id="qvMainImgWrap" title="Click to zoom">
          <img src="${escapeHtml(imgs[0])}" alt="${escapeHtml(product.name)}" id="qvMainImg">
        </div>
        <div class="qv-thumbs">
          ${imgs.map((src, i) => `
            <button type="button" class="qv-thumb ${i === 0 ? 'active' : ''}" data-qv-thumb="${escapeHtml(src)}">
              <img src="${escapeHtml(src)}" alt="Thumbnail ${i + 1}">
            </button>`).join('')}
        </div>
      </div>

      <div>
        <span class="section-kicker">${escapeHtml(product.collection_tag)} • ${escapeHtml(product.category)}</span>
        <h2 style="font-family:var(--font-display);font-size:2.1rem;line-height:1.1;margin-bottom:6px;">${escapeHtml(product.name)}</h2>
        <p style="font-size:0.88rem;color:var(--ink-muted);margin-bottom:12px;">${escapeHtml(product.subtitle)}</p>

        <div style="display:flex;align-items:center;gap:14px;margin-bottom:16px;flex-wrap:wrap;">
          <span style="font-size:1.45rem;font-weight:700;color:var(--maroon);">${formatMoney(product.price_inr)}</span>
          ${product.compare_price_inr > product.price_inr ? `<span class="price-compare">${formatMoney(product.compare_price_inr)}</span>` : ''}
          <span class="product-rating">★ ${product.rating.toFixed(1)} (${product.review_count} reviews)</span>
          <span style="font-size:0.78rem;color:var(--sage);font-weight:600;">${product.is_preorder ? 'Made on Pre-Order (7–10 days)' : `In Stock (${product.stock} left)`}</span>
        </div>

        <p style="font-size:0.9rem;color:var(--ink-muted);margin-bottom:16px;">${escapeHtml(product.description)}</p>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;background:var(--bg-white);padding:12px 14px;border-radius:12px;border:1px solid var(--border);font-size:0.8rem;margin-bottom:16px;">
          <div><strong>Fabric:</strong> ${escapeHtml(product.fabric)}</div>
          <div><strong>Craft:</strong> ${escapeHtml(product.craft)}</div>
          <div><strong>Color:</strong> ${escapeHtml(product.color_name)}</div>
          <div><strong>Shipping:</strong> India &amp; Worldwide</div>
        </div>

        <div style="display:flex;justify-content:space-between;align-items:center;">
          <strong style="font-size:0.84rem;">Select Size</strong>
          <button type="button" id="qvSizeGuideLink" style="font-size:0.78rem;color:var(--maroon);text-decoration:underline;">Size Guide</button>
        </div>

        <div class="size-grid" id="qvSizeGrid">
          ${product.sizes.map((sz) => `
            <button type="button" class="size-pill ${sz === state.selectedSize ? 'active' : ''}" data-qv-size="${escapeHtml(sz)}">${escapeHtml(sz)}</button>
          `).join('')}
        </div>

        <div id="customMeasureBox" hidden style="background:#fff;padding:12px;border-radius:12px;border:1px solid var(--rose);margin-bottom:14px;">
          <div style="font-size:0.78rem;font-weight:700;color:var(--maroon);margin-bottom:8px;">Custom Tailoring Measurements (Inches)</div>
          <div class="form-grid">
            <div class="field" style="margin:0;"><label>Bust</label><input type="text" id="customBustInput" placeholder="e.g. 36 in"></div>
            <div class="field" style="margin:0;"><label>Waist</label><input type="text" id="customWaistInput" placeholder="e.g. 30 in"></div>
          </div>
          <div class="field" style="margin:8px 0 0;"><label>Kurta &amp; Bottom Length Notes</label><input type="text" id="customLengthInput" placeholder="e.g. Kurta 44 in, Farshi 39 in"></div>
        </div>

        <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:16px;">
          <button type="button" class="btn btn-primary" style="flex:1;" id="qvAddToBagBtn">Add to Bag</button>
          <button type="button" class="btn btn-secondary" id="qvBuyNowBtn">Buy Now</button>
          <button type="button" class="btn btn-secondary btn-sm" id="qvWishBtn">${wished ? '♥ Saved' : '♡ Save'}</button>
        </div>

        <!-- Delivery ETA Checker -->
        <div style="background:var(--bg-white);padding:12px 14px;border-radius:12px;border:1px solid var(--border);margin-bottom:18px;">
          <div style="font-size:0.78rem;font-weight:700;margin-bottom:7px;">Check India / Worldwide Delivery ETA</div>
          <div style="display:flex;gap:8px;">
            <input type="text" id="etaInput" placeholder="Enter PIN (e.g. 302006) or City/Country" style="flex:1;padding:7px 11px;border:1px solid var(--border-strong);border-radius:8px;font-size:0.82rem;">
            <button type="button" class="btn btn-secondary btn-sm" id="etaCheckBtn">Check</button>
          </div>
          <div id="etaResult" style="font-size:0.78rem;color:var(--sage);margin-top:6px;"></div>
        </div>

        <!-- Care & Reviews -->
        <details style="margin-bottom:14px;font-size:0.84rem;background:#fff;padding:11px 14px;border-radius:12px;border:1px solid var(--border);">
          <summary style="font-weight:700;cursor:pointer;">Wash &amp; Cotton Care Instructions</summary>
          <p style="margin-top:8px;color:var(--ink-muted);">${escapeHtml(product.care_notes)}</p>
        </details>

        <div style="border-top:1px solid var(--border);padding-top:14px;">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
            <strong style="font-size:0.9rem;">Customer Reviews (${reviews.length})</strong>
            <button type="button" id="toggleReviewFormBtn" style="font-size:0.78rem;color:var(--maroon);font-weight:700;">+ Write a Review</button>
          </div>

          <form id="reviewSubmitForm" hidden style="background:#fff;padding:12px;border-radius:12px;border:1px solid var(--border);margin-bottom:12px;">
            <div class="form-grid">
              <div class="field"><label>Your Name *</label><input type="text" name="author_name" required></div>
              <div class="field"><label>City / Country</label><input type="text" name="city" placeholder="e.g. Mumbai"></div>
            </div>
            <div class="form-grid">
              <div class="field"><label>Rating</label>
                <select name="rating">
                  <option value="5">★★★★★ (5/5)</option>
                  <option value="4">★★★★☆ (4/5)</option>
                  <option value="3">★★★☆☆ (3/5)</option>
                </select>
              </div>
              <div class="field"><label>Headline *</label><input type="text" name="title" required placeholder="Loved the cotton!"></div>
            </div>
            <div class="field"><label>Review *</label><textarea name="comment" rows="2" required placeholder="How was the fit and fabric?"></textarea></div>
            <button type="submit" class="btn btn-primary btn-sm">Submit Review</button>
          </form>

          <div id="qvReviewsList" style="display:grid;gap:8px;max-height:190px;overflow-y:auto;">
            ${reviews.length ? reviews.map((r) => `
              <div style="background:#fff;padding:10px 12px;border-radius:10px;border:1px solid var(--border);font-size:0.82rem;">
                <div style="display:flex;justify-content:space-between;">
                  <strong>${escapeHtml(r.author_name)} <span style="color:var(--ink-light);font-weight:400;">(${escapeHtml(r.city)})</span></strong>
                  <span style="color:#D4941A;">${'★'.repeat(Number(r.rating))}</span>
                </div>
                <div style="font-weight:600;margin-top:3px;">${escapeHtml(r.title)}</div>
                <p style="color:var(--ink-muted);margin-top:2px;">${escapeHtml(r.comment)}</p>
              </div>`).join('') : '<p style="font-size:0.82rem;color:var(--ink-muted);">Be the first to review this cotton piece!</p>'}
          </div>
        </div>
      </div>`;

    bindProductModalEvents(product);
    openModal('productModal');
  }

  function bindProductModalEvents(product) {
    $('#qvMainImgWrap')?.addEventListener('click', (e) => e.currentTarget.classList.toggle('zoomed'));
    $$('[data-qv-thumb]').forEach((btn) => {
      btn.addEventListener('click', () => {
        $('#qvMainImg').src = btn.dataset.qvThumb;
        $$('[data-qv-thumb]').forEach((b) => b.classList.remove('active'));
        btn.classList.add('active');
      });
    });

    $$('[data-qv-size]').forEach((btn) => {
      btn.addEventListener('click', () => {
        state.selectedSize = btn.dataset.qvSize;
        $$('[data-qv-size]').forEach((b) => b.classList.remove('active'));
        btn.classList.add('active');
        $('#customMeasureBox').hidden = state.selectedSize !== 'Custom';
      });
    });

    $('#qvSizeGuideLink')?.addEventListener('click', () => openModal('sizeGuideModal'));

    const buildCartPayload = () => {
      let customMeasurements = null;
      if (state.selectedSize === 'Custom') {
        customMeasurements = {
          bust: $('#customBustInput')?.value.trim() || 'Standard',
          waist: $('#customWaistInput')?.value.trim() || 'Standard',
          notes: $('#customLengthInput')?.value.trim() || '',
        };
      }
      return { size: state.selectedSize, customMeasurements };
    };

    $('#qvAddToBagBtn')?.addEventListener('click', () => {
      const { size, customMeasurements } = buildCartPayload();
      addToCart(product.id, size, 1, customMeasurements);
      closeAllModals();
      openDrawer('cartDrawer');
    });

    $('#qvBuyNowBtn')?.addEventListener('click', () => {
      const { size, customMeasurements } = buildCartPayload();
      addToCart(product.id, size, 1, customMeasurements);
      closeAllModals();
      openCheckout();
    });

    $('#qvWishBtn')?.addEventListener('click', (e) => {
      toggleWishlist(product.id);
      const isW = state.wishlist.includes(product.id);
      e.currentTarget.textContent = isW ? '♥ Saved' : '♡ Save';
    });

    $('#etaCheckBtn')?.addEventListener('click', () => {
      const val = ($('#etaInput')?.value || '').trim();
      const out = $('#etaResult');
      if (!val) {
        out.textContent = 'Enter a 6-digit Indian PIN code or international city.';
        return;
      }
      if (/^\d{6}$/.test(val)) {
        const prefix = val[0];
        const days = ['3', '4', '1', '2'].includes(prefix) ? '3–5 business days' : '4–6 business days';
        out.textContent = `✓ Deliverable to PIN ${val}! Estimated arrival: ${days} (Free above ₹1,999).`;
      } else {
        out.textContent = `✓ Worldwide DHL Express available to ${val}: 5–8 business days.`;
      }
    });

    $('#toggleReviewFormBtn')?.addEventListener('click', () => {
      const f = $('#reviewSubmitForm');
      f.hidden = !f.hidden;
    });

    $('#reviewSubmitForm')?.addEventListener('submit', async (e) => {
      e.preventDefault();
      const fd = new FormData(e.currentTarget);
      const payload = {
        product_id: product.id,
        author_name: fd.get('author_name'),
        city: fd.get('city') || 'India',
        rating: Number(fd.get('rating') || 5),
        title: fd.get('title'),
        comment: fd.get('comment'),
      };
      try {
        const res = await fetch('api/reviews.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload),
        });
        const data = await res.json();
        if (!res.ok) throw new Error(data.error || 'Could not save review');
        showToast('Thank you! Your review has been published.');
        openProductModal(product.id);
      } catch (err) {
        showToast(err.message);
      }
    });
  }

  /* --------------------------------------------------------------------------
     SHOPPING BAG & WISHLIST LOGIC
     -------------------------------------------------------------------------- */
  function addToCart(productId, size = 'M', qty = 1, customMeasurements = null) {
    const product = state.products.find((p) => p.id === Number(productId));
    if (!product) return;

    const existing = state.cart.find((item) => item.id === product.id && item.size === size);
    if (existing) {
      existing.qty = Math.min(20, existing.qty + qty);
      if (customMeasurements) existing.custom_measurements = customMeasurements;
    } else {
      state.cart.push({
        id: product.id,
        name: product.name,
        price_inr: product.price_inr,
        image: product.images[0] || 'assets/images/prod-blush-farshi.jpg',
        size,
        qty,
        custom_measurements: customMeasurements,
      });
    }

    saveState();
    renderCart();
    bumpBadge('#cartCount');
    showToast(`Added ${product.name} (${size}) to your bag`);
  }

  function updateCartQty(idx, delta) {
    if (!state.cart[idx]) return;
    state.cart[idx].qty += delta;
    if (state.cart[idx].qty <= 0) {
      state.cart.splice(idx, 1);
    }
    if (state.cart.length === 0) state.coupon = null;
    saveState();
    renderCart();
  }

  function getCartTotals() {
    const subtotal = state.cart.reduce((acc, item) => acc + item.price_inr * item.qty, 0);
    const discount = state.coupon ? Math.min(subtotal, state.coupon.discount_inr || 0) : 0;
    const total = Math.max(0, subtotal - discount);
    return { subtotal, discount, total };
  }

  function renderCart() {
    const count = state.cart.reduce((a, b) => a + b.qty, 0);
    $('#cartCount').textContent = count;
    $('#drawerBagCount').textContent = count;
    const mobileCnt = $('#mobileCartCount');
    if (mobileCnt) mobileCnt.textContent = count;

    const { subtotal, discount, total } = getCartTotals();
    const freeThreshold = 1999;
    const remaining = Math.max(0, freeThreshold - total);
    const pct = Math.min(100, Math.round((total / freeThreshold) * 100));

    $('#shippingMeterFill').style.width = pct + '%';
    $('#shippingMeterText').innerHTML = remaining === 0
      ? '<strong>Yay!</strong> You unlocked <strong>Free Pan-India Shipping</strong>.'
      : `Add <strong>${formatMoney(remaining)}</strong> more for Free Pan-India Shipping!`;

    const listEl = $('#cartItemsList');
    if (!state.cart.length) {
      listEl.innerHTML = `
        <div style="text-align:center;padding:48px 16px;">
          <div style="font-size:2.4rem;margin-bottom:8px;">🪷</div>
          <h4 style="font-family:var(--font-display);font-size:1.5rem;">Your bag is empty</h4>
          <p style="font-size:0.86rem;color:var(--ink-muted);margin:6px 0 18px;">Explore our hand-block printed Farshi Sets &amp; Mulmul Suits.</p>
          <button type="button" class="btn btn-primary btn-sm" data-close-drawers>Continue Shopping</button>
        </div>`;
    } else {
      listEl.innerHTML = state.cart.map((item, idx) => `
        <div class="cart-item">
          <img src="${escapeHtml(item.image)}" alt="${escapeHtml(item.name)}">
          <div>
            <div style="display:flex;justify-content:space-between;gap:8px;">
              <strong style="font-family:var(--font-display);font-size:1.12rem;line-height:1.15;">${escapeHtml(item.name)}</strong>
              <button type="button" data-remove-cart="${idx}" style="color:var(--ink-light);font-size:0.85rem;" aria-label="Remove item">✕</button>
            </div>
            <div style="font-size:0.78rem;color:var(--ink-muted);margin:3px 0 10px;">Size: <strong>${escapeHtml(item.size)}</strong></div>
            <div style="display:flex;justify-content:space-between;align-items:center;">
              <div class="qty-stepper">
                <button type="button" data-qty-delta="-1" data-idx="${idx}">−</button>
                <span>${item.qty}</span>
                <button type="button" data-qty-delta="1" data-idx="${idx}">+</button>
              </div>
              <strong>${formatMoney(item.price_inr * item.qty)}</strong>
            </div>
          </div>
        </div>`).join('');
    }

    $('#cartSubtotalDisplay').textContent = formatMoney(subtotal);
    const discRow = $('#cartDiscountRow');
    if (discount > 0 && state.coupon) {
      discRow.hidden = false;
      $('#cartDiscountDisplay').textContent = '−' + formatMoney(discount);
    } else {
      discRow.hidden = true;
    }
    $('#cartTotalDisplay').textContent = formatMoney(total);
  }

  function toggleWishlist(productId) {
    const pid = Number(productId);
    const idx = state.wishlist.indexOf(pid);
    if (idx >= 0) {
      state.wishlist.splice(idx, 1);
      showToast('Removed from saved favorites');
    } else {
      state.wishlist.push(pid);
      showToast('Saved to your Pahervesh favorites ♥');
      bumpBadge('#wishlistCount');
    }
    saveState();
    renderWishlist();
    renderCatalog();
  }

  function renderWishlist() {
    $('#wishlistCount').textContent = state.wishlist.length;
    const container = $('#wishlistItemsList');
    if (!container) return;

    const items = state.products.filter((p) => state.wishlist.includes(p.id));
    if (!items.length) {
      container.innerHTML = `
        <div style="text-align:center;padding:48px 16px;">
          <h4 style="font-family:var(--font-display);font-size:1.5rem;">No saved pieces yet</h4>
          <p style="font-size:0.86rem;color:var(--ink-muted);margin-top:6px;">Tap the heart icon on any cotton ensemble to save it here.</p>
        </div>`;
      return;
    }

    container.innerHTML = items.map((p) => `
      <div class="cart-item">
        <img src="${escapeHtml(p.images[0])}" alt="${escapeHtml(p.name)}">
        <div>
          <strong style="font-family:var(--font-display);font-size:1.12rem;">${escapeHtml(p.name)}</strong>
          <div style="font-size:0.82rem;color:var(--maroon);font-weight:700;margin:4px 0 8px;">${formatMoney(p.price_inr)}</div>
          <div style="display:flex;gap:8px;">
            <button type="button" class="btn btn-primary btn-sm" data-move-bag="${p.id}">Move to Bag</button>
            <button type="button" class="btn btn-secondary btn-sm" data-toggle-wish="${p.id}">Remove</button>
          </div>
        </div>
      </div>`).join('');
  }

  function bumpBadge(selector) {
    const el = $(selector);
    if (!el) return;
    el.classList.add('bump');
    setTimeout(() => el.classList.remove('bump'), 350);
  }

  /* --------------------------------------------------------------------------
     COUPON VALIDATION & MULTI-STEP CHECKOUT
     -------------------------------------------------------------------------- */
  async function applyCoupon(codeInput) {
    const { subtotal } = getCartTotals();
    const msgEl = $('#couponStatusMsg');
    if (subtotal <= 0) {
      msgEl.textContent = 'Add items to your bag before applying a coupon.';
      msgEl.style.color = 'var(--maroon)';
      return;
    }

    try {
      const res = await fetch('api/coupon.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ code: codeInput, subtotal_inr: subtotal }),
      });
      const data = await res.json();
      if (data.valid) {
        state.coupon = data;
        msgEl.textContent = '✓ ' + data.message;
        msgEl.style.color = 'var(--sage)';
        renderCart();
      } else {
        state.coupon = null;
        msgEl.textContent = data.message || 'Invalid coupon';
        msgEl.style.color = 'var(--maroon)';
        renderCart();
      }
    } catch (_) {
      msgEl.textContent = 'Could not verify coupon right now.';
    }
  }

  function getCheckoutShippingCost() {
    const { total } = getCartTotals();
    const isIndia = (state.checkoutData.country || 'India') === 'India';
    const method = state.checkoutData.shipping_method;
    if (isIndia) {
      if (method === 'Boutique Pickup (Jaipur)') return 0;
      if (method === 'Express Pan-India Air') return total >= 3999 ? 0 : 199;
      return total >= 1999 ? 0 : 120;
    }
    return total >= 12000 ? 0 : 1490;
  }

  function openCheckout() {
    if (!state.cart.length) {
      showToast('Your shopping bag is empty!');
      return;
    }
    state.checkoutStep = 1;
    renderCheckoutModal();
    openModal('checkoutModal');
  }

  function renderCheckoutModal(placedOrder = null) {
    const wrap = $('#checkoutModalContent');
    if (placedOrder) {
      wrap.innerHTML = `
        <div style="text-align:center;padding:10px 6px;">
          <div style="width:68px;height:68px;border-radius:50%;background:var(--bg-blush);color:var(--maroon);display:grid;place-items:center;margin:0 auto 14px;font-size:1.9rem;">🪷</div>
          <span class="section-kicker">Order Confirmed</span>
          <h3 class="section-title" style="font-size:2.1rem;">Thank You, ${escapeHtml(placedOrder.customer_name)}!</h3>
          <p style="color:var(--ink-muted);margin:8px 0 18px;">Your handcrafted cotton parcel is queued for ribbon wrapping at Akshree’s studio.</p>
          <div style="background:#fff;border:1px solid var(--border);border-radius:16px;padding:18px;text-align:left;margin-bottom:18px;">
            <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
              <span>Order Number</span>
              <strong style="color:var(--maroon);">${escapeHtml(placedOrder.order_number)}</strong>
            </div>
            <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
              <span>Destination</span>
              <strong>${escapeHtml(placedOrder.city)}, ${escapeHtml(placedOrder.country)}</strong>
            </div>
            <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
              <span>Payment Method</span>
              <strong>${escapeHtml(placedOrder.payment_method)}</strong>
            </div>
            <div style="display:flex;justify-content:space-between;padding-top:8px;border-top:1px solid var(--border);">
              <strong>Total Paid</strong>
              <strong>${formatMoney(placedOrder.total_inr)}</strong>
            </div>
          </div>
          <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
            <button type="button" class="btn btn-primary" id="trackPlacedOrderBtn">Track This Order</button>
            <button type="button" class="btn btn-secondary" onclick="window.print()">Print Receipt</button>
          </div>
        </div>`;
      launchPetalCelebration();
      $('#trackPlacedOrderBtn')?.addEventListener('click', () => {
        closeAllModals();
        openModal('trackModal');
        $('#trackOrderInput').value = placedOrder.order_number;
        lookupOrder(placedOrder.order_number);
      });
      return;
    }

    const { subtotal, discount, total } = getCartTotals();
    const shipCost = getCheckoutShippingCost();
    const grandTotal = total + shipCost;

    wrap.innerHTML = `
      <span class="section-kicker">Secure Boutique Checkout</span>
      <h3 class="section-title" style="font-size:2rem;margin-bottom:14px;">Complete Your Order</h3>

      <form id="checkoutForm">
        <div class="form-grid">
          <div class="field"><label>Full Name *</label><input type="text" name="customer_name" required placeholder="Ananya Sharma"></div>
          <div class="field"><label>Email Address *</label><input type="email" name="customer_email" required placeholder="ananya@example.com"></div>
        </div>
        <div class="form-grid">
          <div class="field"><label>Phone / WhatsApp *</label><input type="tel" name="customer_phone" required placeholder="+91 98765 43210"></div>
          <div class="field"><label>Country *</label>
            <select name="country" id="chkCountry">
              <option value="India" ${state.checkoutData.country === 'India' ? 'selected' : ''}>India</option>
              <option value="United States" ${state.checkoutData.country === 'United States' ? 'selected' : ''}>United States</option>
              <option value="United Kingdom" ${state.checkoutData.country === 'United Kingdom' ? 'selected' : ''}>United Kingdom</option>
              <option value="UAE" ${state.checkoutData.country === 'UAE' ? 'selected' : ''}>United Arab Emirates</option>
              <option value="Canada" ${state.checkoutData.country === 'Canada' ? 'selected' : ''}>Canada</option>
              <option value="Australia" ${state.checkoutData.country === 'Australia' ? 'selected' : ''}>Australia</option>
            </select>
          </div>
        </div>
        <div class="field"><label>Street Address / Apartment *</label><input type="text" name="address_line" required placeholder="House No, Street, Landmark"></div>
        <div class="form-grid">
          <div class="field"><label>City *</label><input type="text" name="city" required placeholder="Jaipur"></div>
          <div class="field"><label>State / Postal PIN *</label><input type="text" name="postal_code" required placeholder="Rajasthan / 302006"></div>
        </div>

        <div class="form-grid">
          <div class="field">
            <label>Shipping Method</label>
            <select name="shipping_method" id="chkShippingMethod">
              <option value="Standard Pan-India">Standard Pan-India (Free ₹1,999+)</option>
              <option value="Express Pan-India Air">Express Pan-India Air (₹199)</option>
              <option value="Worldwide DHL Express">Worldwide DHL Express (₹1,490)</option>
              <option value="Boutique Pickup (Jaipur)">Boutique Pickup (Jaipur - Free)</option>
            </select>
          </div>
          <div class="field">
            <label>Payment Method</label>
            <select name="payment_method">
              <option value="UPI / Instant Pay">UPI / GPay / PhonePe</option>
              <option value="Credit / Debit Card">Credit / Debit Card</option>
              <option value="Cash on Delivery (India)">Cash on Delivery (India)</option>
              <option value="International Card / PayPal">International Card / PayPal</option>
            </select>
          </div>
        </div>

        <label style="display:flex;align-items:center;gap:9px;font-size:0.84rem;margin:6px 0 10px;cursor:pointer;">
          <input type="checkbox" name="gift_wrap" checked>
          <span>Include complimentary Pahervesh kraft bag, pink satin ribbon &amp; baby’s breath note</span>
        </label>
        <div class="field"><label>Handwritten Gift / Tailoring Note (Optional)</label><input type="text" name="gift_note" placeholder="Write a warm note for the recipient..."></div>

        <div style="background:#fff;border:1px solid var(--border);border-radius:14px;padding:14px;margin:14px 0;font-size:0.86rem;display:grid;gap:6px;">
          <div style="display:flex;justify-content:space-between;"><span>Items Subtotal (${state.cart.reduce((a,b)=>a+b.qty,0)})</span><span>${formatMoney(subtotal)}</span></div>
          ${discount > 0 ? `<div style="display:flex;justify-content:space-between;color:var(--sage);"><span>Coupon (${escapeHtml(state.coupon.code)})</span><span>−${formatMoney(discount)}</span></div>` : ''}
          <div style="display:flex;justify-content:space-between;"><span>Shipping</span><strong id="chkShippingDisplay">${shipCost === 0 ? 'FREE' : formatMoney(shipCost)}</strong></div>
          <div style="display:flex;justify-content:space-between;font-size:1.08rem;padding-top:8px;border-top:1px solid var(--border);"><strong>Grand Total</strong><strong id="chkGrandTotalDisplay" style="color:var(--maroon);">${formatMoney(grandTotal)}</strong></div>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;" id="placeOrderBtn">Place Order • ${formatMoney(grandTotal)}</button>
      </form>`;

    const countrySel = $('#chkCountry');
    const shipSel = $('#chkShippingMethod');
    const recalc = () => {
      state.checkoutData.country = countrySel.value;
      if (countrySel.value !== 'India') shipSel.value = 'Worldwide DHL Express';
      state.checkoutData.shipping_method = shipSel.value;
      const sc = getCheckoutShippingCost();
      const gt = total + sc;
      $('#chkShippingDisplay').textContent = sc === 0 ? 'FREE' : formatMoney(sc);
      $('#chkGrandTotalDisplay').textContent = formatMoney(gt);
      $('#placeOrderBtn').textContent = `Place Order • ${formatMoney(gt)}`;
    };

    countrySel?.addEventListener('change', recalc);
    shipSel?.addEventListener('change', recalc);

    $('#checkoutForm')?.addEventListener('submit', async (e) => {
      e.preventDefault();
      const btn = $('#placeOrderBtn');
      btn.disabled = true;
      btn.textContent = 'Confirming your order...';

      const fd = new FormData(e.currentTarget);
      const payload = {
        customer_name: fd.get('customer_name'),
        customer_email: fd.get('customer_email'),
        customer_phone: fd.get('customer_phone'),
        country: fd.get('country'),
        address_line: fd.get('address_line'),
        city: fd.get('city'),
        state: fd.get('postal_code'),
        postal_code: fd.get('postal_code'),
        shipping_method: fd.get('shipping_method'),
        payment_method: fd.get('payment_method'),
        coupon_code: state.coupon?.code || '',
        currency: state.currency,
        gift_wrap: !!fd.get('gift_wrap'),
        gift_note: fd.get('gift_note'),
        items: state.cart,
      };

      try {
        const res = await fetch('api/orders.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload),
        });
        const data = await res.json();
        if (!res.ok || !data.order) throw new Error(data.error || 'Order failed');
        state.cart = [];
        state.coupon = null;
        saveState();
        renderCart();
        renderCheckoutModal(data.order);
      } catch (err) {
        showToast(err.message);
        btn.disabled = false;
        btn.textContent = `Place Order • ${formatMoney(total + getCheckoutShippingCost())}`;
      }
    });
  }

  function launchPetalCelebration() {
    const colors = ['#D98C96', '#6E1F2E', '#C69749', '#F4D3D7'];
    for (let i = 0; i < 36; i++) {
      const p = document.createElement('div');
      p.style.cssText = `
        position:fixed;left:${Math.random() * 100}vw;top:-14px;width:10px;height:14px;
        background:${colors[i % colors.length]};border-radius:60% 40% 60% 40%;
        z-index:2000;pointer-events:none;opacity:0.9;
        transition:transform ${1.6 + Math.random()}s cubic-bezier(.2,.8,.3,1), opacity 2s ease;
      `;
      document.body.appendChild(p);
      requestAnimationFrame(() => {
        p.style.transform = `translateY(${window.innerHeight + 60}px) translateX(${(Math.random() - 0.5) * 180}px) rotate(${Math.random() * 540}deg)`;
        p.style.opacity = '0';
      });
      setTimeout(() => p.remove(), 2600);
    }
  }

  /* --------------------------------------------------------------------------
     ORDER TRACKING & SIZE GUIDE & CUSTOM ENQUIRY
     -------------------------------------------------------------------------- */
  async function lookupOrder(orderNumber, targetSelector = '#trackOrderResult') {
    const out = $(targetSelector);
    if (!out) return;
    out.innerHTML = '<p style="font-size:0.86rem;color:var(--ink-muted);">Fetching parcel timeline...</p>';
    try {
      const res = await fetch('api/orders.php?order_number=' + encodeURIComponent(orderNumber));
      const data = await res.json();
      if (!res.ok || !data.order) throw new Error(data.error || 'Order not found');

      const o = data.order;
      const stages = ['Confirmed', 'Hand-Packed with Ribbon', 'Dispatched', 'Out for Delivery', 'Delivered'];
      const currentIdx = Math.max(0, stages.indexOf(o.status));

      out.innerHTML = `
        <div style="background:#fff;border:1px solid var(--border);border-radius:16px;padding:18px;">
          <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:8px;margin-bottom:10px;">
            <div>
              <strong style="font-size:1.05rem;color:var(--maroon);">${escapeHtml(o.order_number)}</strong>
              <div style="font-size:0.8rem;color:var(--ink-muted);">${escapeHtml(o.customer_name)} • ${escapeHtml(o.city)}, ${escapeHtml(o.country)}</div>
            </div>
            <span class="filter-pill active" style="padding:5px 12px;">${escapeHtml(o.status)}</span>
          </div>
          <div class="track-steps">
            ${stages.map((st, idx) => `
              <div class="track-step ${idx <= currentIdx ? 'done' : ''}">
                <div class="track-dot">${idx <= currentIdx ? '✓' : idx + 1}</div>
                <div style="font-size:0.74rem;font-weight:${idx <= currentIdx ? '700' : '500'};">${st}</div>
              </div>`).join('')}
          </div>
          <div style="border-top:1px solid var(--border);padding-top:12px;font-size:0.82rem;">
            <strong>Items (${o.items.length}):</strong>
            ${o.items.map((it) => `<div>• ${escapeHtml(it.name)} (Size ${escapeHtml(it.size)}) × ${it.qty}</div>`).join('')}
          </div>
        </div>`;
    } catch (err) {
      out.innerHTML = `<p style="color:var(--maroon);font-size:0.86rem;">${escapeHtml(err.message)}</p>`;
    }
  }

  function renderSizeGuide(unit = 'in', tableSelector = '#sizeChartTable') {
    const rows = [
      ['XS', 32, 26, 35, 43],
      ['S', 34, 28, 37, 43.5],
      ['M', 36, 30, 39, 44],
      ['L', 38, 32, 41, 44.5],
      ['XL', 40, 34, 43, 45],
      ['XXL', 42, 36, 45, 45],
    ];
    const fmt = (v) => (unit === 'cm' ? Math.round(v * 2.54) + ' cm' : v + '"');
    const tbl = $(tableSelector);
    if (!tbl) return;

    tbl.innerHTML = `
      <thead>
        <tr style="background:var(--bg-blush);text-align:left;">
          <th style="padding:10px 12px;">Size</th>
          <th style="padding:10px 12px;">Bust</th>
          <th style="padding:10px 12px;">Waist</th>
          <th style="padding:10px 12px;">Hip</th>
          <th style="padding:10px 12px;">Kurta Length</th>
        </tr>
      </thead>
      <tbody>
        ${rows.map((r) => `
          <tr style="border-top:1px solid var(--border);">
            <td style="padding:9px 12px;font-weight:700;">${r[0]}</td>
            <td style="padding:9px 12px;">${fmt(r[1])}</td>
            <td style="padding:9px 12px;">${fmt(r[2])}</td>
            <td style="padding:9px 12px;">${fmt(r[3])}</td>
            <td style="padding:9px 12px;">${fmt(r[4])}</td>
          </tr>`).join('')}
      </tbody>`;
  }

  /* --------------------------------------------------------------------------
     DRAWER & MODAL CONTROLLERS
     -------------------------------------------------------------------------- */
  function openDrawer(id) {
    closeAllDrawers();
    closeAllModals();
    $('#backdrop').classList.add('open');
    $('#' + id)?.classList.add('open');
  }

  function closeAllDrawers() {
    $$('.drawer').forEach((d) => d.classList.remove('open'));
    if (!$$('.modal.open').length) {
      $('#backdrop').classList.remove('open');
    }
  }

  function openModal(id) {
    closeAllDrawers();
    closeAllModals();
    $('#backdrop').classList.add('open');
    $('#' + id)?.classList.add('open');
  }

  function closeAllModals() {
    $$('.modal').forEach((m) => m.classList.remove('open'));
    if (!$$('.drawer.open').length) {
      $('#backdrop').classList.remove('open');
    }
  }

  /* --------------------------------------------------------------------------
     EVENT WIRING
     -------------------------------------------------------------------------- */
  function initEvents() {
    // Currency
    const curSel = $('#currencySelect');
    const mCurSel = $('#mobileCurrencySelect');
    if (curSel) curSel.value = state.currency;
    if (mCurSel) mCurSel.value = state.currency;

    const onCurChange = (val) => {
      state.currency = val;
      if (curSel) curSel.value = val;
      if (mCurSel) mCurSel.value = val;
      saveState();
      renderCatalog();
      renderCart();
      renderWishlist();
      refreshDynamicPrices();
    };
    curSel?.addEventListener('change', (e) => onCurChange(e.target.value));
    mCurSel?.addEventListener('change', (e) => onCurChange(e.target.value));

    // Category tabs & jump links
    $$('#categoryTabs .filter-pill').forEach((btn) => {
      btn.addEventListener('click', () => {
        state.category = btn.dataset.category;
        updateCategoryTabsUI();
        fetchProducts();
      });
    });

    $$('[data-cat-jump]').forEach((el) => {
      el.addEventListener('click', () => {
        state.category = el.dataset.catJump;
        updateCategoryTabsUI();
        fetchProducts();
        closeAllDrawers();
        $('#catalog')?.scrollIntoView({ behavior: 'smooth' });
      });
    });

    // Search & Sort
    let searchTimer;
    $('#catalogSearch')?.addEventListener('input', (e) => {
      state.search = e.target.value;
      clearTimeout(searchTimer);
      searchTimer = setTimeout(fetchProducts, 220);
    });

    $('#catalogSort')?.addEventListener('change', (e) => {
      state.sort = e.target.value;
      fetchProducts();
    });

    $('#headerSearchBtn')?.addEventListener('click', () => {
      const searchEl = $('#catalogSearch');
      if (searchEl) {
        $('#catalog')?.scrollIntoView({ behavior: 'smooth' });
        setTimeout(() => searchEl.focus(), 400);
      } else {
        window.location.href = 'shop.php#catalog';
      }
    });

    // Delegated clicks inside Products Grid, IG Lookbook, Cart, Wishlist
    document.addEventListener('click', (e) => {
      const openProd = e.target.closest('[data-open-product], [data-product-id]');
      if (openProd && !e.target.closest('[data-toggle-wish]') && !e.target.closest('[data-quick-add]')) {
        const pid = openProd.dataset.openProduct || openProd.dataset.productId;
        openProductModal(pid);
        return;
      }

      const wishBtn = e.target.closest('[data-toggle-wish]');
      if (wishBtn) {
        e.stopPropagation();
        toggleWishlist(wishBtn.dataset.toggleWish);
        return;
      }

      const quickAdd = e.target.closest('[data-quick-add]');
      if (quickAdd) {
        e.stopPropagation();
        addToCart(quickAdd.dataset.quickAdd, quickAdd.dataset.size, 1);
        openDrawer('cartDrawer');
        return;
      }

      const qtyBtn = e.target.closest('[data-qty-delta]');
      if (qtyBtn) {
        updateCartQty(Number(qtyBtn.dataset.idx), Number(qtyBtn.dataset.qtyDelta));
        return;
      }

      const remBtn = e.target.closest('[data-remove-cart]');
      if (remBtn) {
        updateCartQty(Number(remBtn.dataset.removeCart), -999);
        return;
      }

      const moveBag = e.target.closest('[data-move-bag]');
      if (moveBag) {
        addToCart(moveBag.dataset.moveBag, 'M', 1);
        openDrawer('cartDrawer');
        return;
      }

      if (e.target.closest('[data-close-drawers]')) closeAllDrawers();
      if (e.target.closest('[data-close-modals]')) closeAllModals();
    });

    $('#backdrop')?.addEventListener('click', () => {
      closeAllDrawers();
      closeAllModals();
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        closeAllDrawers();
        closeAllModals();
      }
    });

    // Drawers & Modals triggers
    $('#openMobileMenu')?.addEventListener('click', () => openDrawer('mobileDrawer'));
    $('#openCartBtn')?.addEventListener('click', () => openDrawer('cartDrawer'));
    $('#mobileCartBtn')?.addEventListener('click', () => openDrawer('cartDrawer'));
    $('#openWishlistBtn')?.addEventListener('click', () => openDrawer('wishlistDrawer'));
    $('#mobileWishlistBtn')?.addEventListener('click', () => openDrawer('wishlistDrawer'));

    $('#heroFeaturedCard')?.addEventListener('click', () => {
      const pid = Number($('#heroFeaturedCard')?.dataset.activePid || 1);
      openProductModal(pid);
    });
    $('#applyCouponBtn')?.addEventListener('click', () => applyCoupon($('#cartCouponInput').value));
    $('#proceedCheckoutBtn')?.addEventListener('click', openCheckout);

    // Track Order
    ['#openTrackBtn', '#footerTrackBtn', '#mobileTrackTrigger'].forEach((s) =>
      $(s)?.addEventListener('click', () => openModal('trackModal'))
    );
    $$('.sample-order-chip').forEach((chip) => {
      chip.addEventListener('click', () => {
        $('#trackOrderInput').value = chip.dataset.order;
        lookupOrder(chip.dataset.order);
      });
    });
    $('#trackOrderForm')?.addEventListener('submit', (e) => {
      e.preventDefault();
      lookupOrder($('#trackOrderInput').value.trim());
    });

    // Dedicated Track Order Page bindings
    $$('.page-sample-order-chip').forEach((chip) => {
      chip.addEventListener('click', () => {
        const inp = $('#pageTrackOrderInput');
        if (inp) inp.value = chip.dataset.order;
        lookupOrder(chip.dataset.order, '#pageTrackOrderResult');
      });
    });
    $('#pageTrackOrderForm')?.addEventListener('submit', (e) => {
      e.preventDefault();
      lookupOrder($('#pageTrackOrderInput').value.trim(), '#pageTrackOrderResult');
    });

    // Size Guide (Modal + Dedicated Custom Fit Page)
    renderSizeGuide('in', '#sizeChartTable');
    renderSizeGuide('in', '#pageSizeChartTable');
    $('#unitInBtn')?.addEventListener('click', (e) => {
      e.currentTarget.classList.add('active');
      $('#unitCmBtn')?.classList.remove('active');
      renderSizeGuide('in', '#sizeChartTable');
    });
    $('#unitCmBtn')?.addEventListener('click', (e) => {
      e.currentTarget.classList.add('active');
      $('#unitInBtn')?.classList.remove('active');
      renderSizeGuide('cm', '#sizeChartTable');
    });
    $('#pageUnitInBtn')?.addEventListener('click', (e) => {
      e.currentTarget.classList.add('active');
      $('#pageUnitCmBtn')?.classList.remove('active');
      renderSizeGuide('in', '#pageSizeChartTable');
    });
    $('#pageUnitCmBtn')?.addEventListener('click', (e) => {
      e.currentTarget.classList.add('active');
      $('#pageUnitInBtn')?.classList.remove('active');
      renderSizeGuide('cm', '#pageSizeChartTable');
    });
    ['#storySizeGuideBtn', '#footerSizeGuideBtn'].forEach((s) =>
      $(s)?.addEventListener('click', () => openModal('sizeGuideModal'))
    );

    // Custom Tailoring / Concierge (Modal + Dedicated Custom Fit Page)
    ['#openCustomBtn', '#storyCustomBtn', '#footerCustomBtn', '#mobileCustomTrigger'].forEach((s) =>
      $(s)?.addEventListener('click', () => openModal('customModal'))
    );

    const handleEnquirySubmit = async (e, resultSelector) => {
      e.preventDefault();
      const fd = new FormData(e.currentTarget);
      const out = $(resultSelector);
      try {
        const res = await fetch('api/enquiry.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            name: fd.get('name'),
            contact: fd.get('contact'),
            product_name: fd.get('product_name'),
            measurements: { bust: fd.get('bust'), waist: fd.get('waist'), length: fd.get('length') },
            notes: fd.get('notes'),
          }),
        });
        const data = await res.json();
        if (!res.ok) throw new Error(data.error || 'Could not send enquiry');
        if (out) {
          out.style.color = 'var(--sage)';
          out.textContent = '✓ ' + data.message;
        }
        e.currentTarget.reset();
        showToast('Custom tailoring request sent to Akshree’s studio!');
      } catch (err) {
        if (out) {
          out.style.color = 'var(--maroon)';
          out.textContent = err.message;
        }
      }
    };

    $('#customEnquiryForm')?.addEventListener('submit', (e) => handleEnquirySubmit(e, '#customEnquiryResult'));
    $('#pageCustomEnquiryForm')?.addEventListener('submit', (e) => handleEnquirySubmit(e, '#pageCustomEnquiryResult'));

    // Newsletter
    $('#newsletterForm')?.addEventListener('submit', async (e) => {
      e.preventDefault();
      const email = $('#newsletterEmail').value.trim();
      const fb = $('#newsletterFeedback');
      try {
        const res = await fetch('api/newsletter.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ email }),
        });
        const data = await res.json();
        if (!res.ok) throw new Error(data.error || 'Subscription failed');
        fb.textContent = '✓ ' + data.message;
        e.currentTarget.reset();
        showToast('Welcome code AKSHREE10 unlocked!');
      } catch (err) {
        fb.textContent = err.message;
      }
    });
  }

  /* --------------------------------------------------------------------------
     BOOT
     -------------------------------------------------------------------------- */
  initChrome();
  initHeroSlideshow();
  applyClientFilterFallback();
  renderCart();
  renderWishlist();
  observeReveals();
  initEvents();
})();
