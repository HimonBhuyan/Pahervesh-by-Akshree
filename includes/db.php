<?php
declare(strict_types=1);

/**
 * Pahervesh by Akshree — SQLite Database Connection & Auto-Seeding
 */

function get_db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dbDir = __DIR__ . '/../database';
    if (!is_dir($dbDir)) {
        mkdir($dbDir, 0775, true);
    }

    $dbPath = $dbDir . '/pahervesh.sqlite';
    $isNew = !file_exists($dbPath);

    $pdo = new PDO('sqlite:' . $dbPath, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    $pdo->exec('PRAGMA journal_mode = WAL;');
    $pdo->exec('PRAGMA foreign_keys = ON;');

    init_schema($pdo);

    if ($isNew || (int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn() === 0) {
        seed_initial_data($pdo);
    }

    return $pdo;
}

function init_schema(PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS products (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            slug TEXT UNIQUE NOT NULL,
            name TEXT NOT NULL,
            subtitle TEXT NOT NULL,
            category TEXT NOT NULL,
            collection_tag TEXT NOT NULL,
            price_inr INTEGER NOT NULL,
            compare_price_inr INTEGER NOT NULL,
            fabric TEXT NOT NULL,
            craft TEXT NOT NULL,
            color_name TEXT NOT NULL,
            color_hex TEXT NOT NULL,
            sizes_json TEXT NOT NULL,
            images_json TEXT NOT NULL,
            badge TEXT DEFAULT '',
            rating REAL DEFAULT 4.9,
            review_count INTEGER DEFAULT 0,
            stock INTEGER DEFAULT 15,
            is_featured INTEGER DEFAULT 0,
            is_preorder INTEGER DEFAULT 0,
            description TEXT NOT NULL,
            care_notes TEXT NOT NULL,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS reviews (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            product_id INTEGER NOT NULL,
            author_name TEXT NOT NULL,
            city TEXT NOT NULL,
            rating INTEGER NOT NULL CHECK(rating BETWEEN 1 AND 5),
            title TEXT NOT NULL,
            comment TEXT NOT NULL,
            verified INTEGER DEFAULT 1,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS orders (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            order_number TEXT UNIQUE NOT NULL,
            customer_name TEXT NOT NULL,
            customer_email TEXT NOT NULL,
            customer_phone TEXT NOT NULL,
            country TEXT NOT NULL DEFAULT 'India',
            address_line TEXT NOT NULL,
            city TEXT NOT NULL,
            state TEXT NOT NULL,
            postal_code TEXT NOT NULL,
            shipping_method TEXT NOT NULL,
            payment_method TEXT NOT NULL,
            coupon_code TEXT DEFAULT '',
            subtotal_inr INTEGER NOT NULL,
            discount_inr INTEGER NOT NULL DEFAULT 0,
            shipping_inr INTEGER NOT NULL DEFAULT 0,
            total_inr INTEGER NOT NULL,
            currency TEXT NOT NULL DEFAULT 'INR',
            gift_wrap INTEGER NOT NULL DEFAULT 0,
            gift_note TEXT DEFAULT '',
            status TEXT NOT NULL DEFAULT 'Confirmed',
            items_json TEXT NOT NULL,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS newsletter_subscribers (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT UNIQUE NOT NULL,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS custom_enquiries (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            contact TEXT NOT NULL,
            product_name TEXT DEFAULT '',
            measurements_json TEXT DEFAULT '{}',
            notes TEXT NOT NULL,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP
        );
    ");
}

function seed_initial_data(PDO $pdo): void
{
    $products = [
        [
            'slug' => 'gulab-lotus-farshi-set',
            'name' => 'Gulab Lotus Farshi Salwar Set',
            'subtitle' => 'Hand-Block Floral Cotton Farshi Kurta, Pleated Salwar & Organza Dupatta',
            'category' => 'Farshi Sets',
            'collection_tag' => 'Signature Bestseller',
            'price_inr' => 2890,
            'compare_price_inr' => 3490,
            'fabric' => '100% Pure Cambric Cotton & Mulmul',
            'craft' => 'Sanganeri Hand-Block Print',
            'color_name' => 'Blush Rose',
            'color_hex' => '#E5A3AB',
            'sizes_json' => json_encode(['XS', 'S', 'M', 'L', 'XL', 'XXL', 'Custom']),
            'images_json' => json_encode([
                'assets/images/prod-blush-farshi.jpg',
                'assets/images/hero-editorial.jpg',
                'assets/images/brand-packaging.jpg'
            ]),
            'badge' => 'Bestseller',
            'rating' => 4.9,
            'review_count' => 3,
            'stock' => 14,
            'is_featured' => 1,
            'is_preorder' => 0,
            'description' => 'Our most-loved Pahervesh signature! Crafted in breathable 60s cambric cotton dipped in soft lotus blush tones, this 3-piece Farshi set brings effortless royal grace to festive brunches, family gatherings, and intimate pujas. Features wide pleated Farshi bottoms with delicate gota edging and a featherlight floral dupatta.',
            'care_notes' => 'Gentle hand wash separately in cold water with mild detergent. Dry in shade. Warm steam iron on reverse.',
        ],
        [
            'slug' => 'crimson-kalamkari-farshi-set',
            'name' => 'Sindoor Kalamkari Farshi Set',
            'subtitle' => 'Deep Crimson Artisanal Kalamkari Block Print Kurta & Wide Farshi Salwar',
            'category' => 'Farshi Sets',
            'collection_tag' => 'Festive Edit',
            'price_inr' => 3190,
            'compare_price_inr' => 3790,
            'fabric' => 'Pure Handloom Cotton',
            'craft' => 'Kalamkari Block Print',
            'color_name' => 'Crimson Maroon',
            'color_hex' => '#7C2330',
            'sizes_json' => json_encode(['XS', 'S', 'M', 'L', 'XL', 'XXL', 'Custom']),
            'images_json' => json_encode([
                'assets/images/prod-crimson-farshi.jpg',
                'assets/images/brand-packaging.jpg'
            ]),
            'badge' => 'Trending',
            'rating' => 4.9,
            'review_count' => 2,
            'stock' => 9,
            'is_featured' => 1,
            'is_preorder' => 0,
            'description' => 'Rich sindoor-crimson tones meet heritage Kalamkari botanical motifs in this showstopping Farshi silhouette. Designed with a flattering V-notch neckline, relaxed bell-inspired sleeves, and voluminous Farshi trousers that drape like poetry with every step.',
            'care_notes' => 'First wash dry clean recommended; subsequent gentle cold hand wash. Expect subtle artisanal dye character.',
        ],
        [
            'slug' => 'citrus-marigold-suit-set',
            'name' => 'Citrus Sunbeam Cotton Suit Set',
            'subtitle' => 'Mustard & Ivory Striped Botanical Block Print 3-Piece Mulmul Suit',
            'category' => 'Suit Sets',
            'collection_tag' => 'Summer Unfold',
            'price_inr' => 2490,
            'compare_price_inr' => 2990,
            'fabric' => 'Pure 60x60 Mulmul Cotton',
            'craft' => 'Bagru Floral Block Print',
            'color_name' => 'Citrus Mustard',
            'color_hex' => '#D49B27',
            'sizes_json' => json_encode(['XS', 'S', 'M', 'L', 'XL', 'XXL']),
            'images_json' => json_encode([
                'assets/images/prod-citrus-suit.jpg',
                'assets/images/brand-packaging.jpg'
            ]),
            'badge' => 'New Drop',
            'rating' => 4.8,
            'review_count' => 2,
            'stock' => 18,
            'is_featured' => 1,
            'is_preorder' => 0,
            'description' => 'Sun-drenched mustard stripes layered with hand-carved citrus and floral block prints. Tailored for tropical Indian summers and destination haldi mornings, complete with ultra-soft mulmul lining and a breezy 2.5m hand-printed dupatta.',
            'care_notes' => 'Gentle cold hand wash. Do not bleach or wring. Line dry in shade.',
        ],
        [
            'slug' => 'charcoal-office-kurta-set',
            'name' => 'Monochrome Bagru Office Kurta Set',
            'subtitle' => 'Charcoal & Cream Floral Block Print Straight Kurta with Cropped Trousers',
            'category' => 'Office Kurtis',
            'collection_tag' => 'Workwear Capsule',
            'price_inr' => 1690,
            'compare_price_inr' => 2090,
            'fabric' => 'Breathable Slub Cotton',
            'craft' => 'Dabu Mud-Resist Print',
            'color_name' => 'Charcoal Ivory',
            'color_hex' => '#332E2B',
            'sizes_json' => json_encode(['XS', 'S', 'M', 'L', 'XL', 'XXL']),
            'images_json' => json_encode([
                'assets/images/prod-charcoal-office.jpg',
                'assets/images/brand-packaging.jpg'
            ]),
            'badge' => 'Office Favorite',
            'rating' => 4.9,
            'review_count' => 2,
            'stock' => 22,
            'is_featured' => 1,
            'is_preorder' => 0,
            'description' => 'Power dressing rooted in Indian craft. This charcoal and warm-oatmeal floral block-printed kurta set features a crisp mandarin collar, concealed side pockets, and tailored straight pants built for 9-to-7 comfort.',
            'care_notes' => 'Machine wash cold on delicate cycle or hand wash. Easy low-heat steam iron.',
        ],
        [
            'slug' => 'midnight-bird-motif-kurta',
            'name' => 'Neelambari Bird-Motif Cotton Kurta',
            'subtitle' => 'Deep Navy Pure Cotton A-Line Kurta with Golden Sand Bird Block Prints',
            'category' => 'Office Kurtis',
            'collection_tag' => 'Artisan Everyday',
            'price_inr' => 1490,
            'compare_price_inr' => 1890,
            'fabric' => 'Pure Combed Cotton',
            'craft' => 'Hand-Block Motif Print',
            'color_name' => 'Midnight Navy',
            'color_hex' => '#1F314D',
            'sizes_json' => json_encode(['XS', 'S', 'M', 'L', 'XL', 'XXL']),
            'images_json' => json_encode([
                'assets/images/prod-midnight-bird.jpg',
                'assets/images/brand-packaging.jpg'
            ]),
            'badge' => 'Everyday Chic',
            'rating' => 4.8,
            'review_count' => 1,
            'stock' => 16,
            'is_featured' => 0,
            'is_preorder' => 0,
            'description' => 'Inspired by twilight skies, our Neelambari kurta pairs deep midnight indigo cotton with dainty flying-bird wooden block motifs in warm sand. Effortless to style with white palazzos or denim for work and coffee runs.',
            'care_notes' => 'Wash separately in cold water for the first two washes. Dry away from direct sunlight.',
        ],
        [
            'slug' => 'royal-indigo-bandhani-set',
            'name' => 'Nila Indigo Bandhani Suit Set',
            'subtitle' => 'Hand-Tied Royal Indigo Bandhani Flared Kurta & Matching Trousers',
            'category' => 'Suit Sets',
            'collection_tag' => 'Heritage Weaves',
            'price_inr' => 2690,
            'compare_price_inr' => 3190,
            'fabric' => 'Pure Soft Cotton',
            'craft' => 'Artisanal Bandhani Tie-Dye',
            'color_name' => 'Royal Indigo',
            'color_hex' => '#264478',
            'sizes_json' => json_encode(['XS', 'S', 'M', 'L', 'XL', 'XXL', 'Custom']),
            'images_json' => json_encode([
                'assets/images/prod-indigo-bandhani.jpg',
                'assets/images/brand-packaging.jpg'
            ]),
            'badge' => 'Artisan Pick',
            'rating' => 5.0,
            'review_count' => 2,
            'stock' => 11,
            'is_featured' => 1,
            'is_preorder' => 0,
            'description' => 'Hand-tied in classic Kutchi Bandhani dots over rich indigo cotton, this flared A-line kurta and trouser duo moves gracefully from daytime temple visits to evening celebrations.',
            'care_notes' => 'Dry clean first time, then cold hand wash separately. Do not soak overnight.',
        ],
        [
            'slug' => 'sage-botanical-coord-set',
            'name' => 'Aranya Sage Botanical Co-ord Set',
            'subtitle' => 'Indo-Western Collared Tunic & Wide-Leg Trousers in Hand-Printed Cotton',
            'category' => 'Co-ord Sets',
            'collection_tag' => 'Indo-Western Edit',
            'price_inr' => 2190,
            'compare_price_inr' => 2590,
            'fabric' => 'Pure Textured Cotton',
            'craft' => 'Jaipur Botanical Block Print',
            'color_name' => 'Sage Olive',
            'color_hex' => '#6E8268',
            'sizes_json' => json_encode(['XS', 'S', 'M', 'L', 'XL', 'XXL']),
            'images_json' => json_encode([
                'assets/images/prod-sage-coord.jpg',
                'assets/images/brand-packaging.jpg'
            ]),
            'badge' => 'Indo-Western',
            'rating' => 4.9,
            'review_count' => 2,
            'stock' => 13,
            'is_featured' => 1,
            'is_preorder' => 0,
            'description' => 'Where relaxed resort tailoring meets Jaipur hand-block artistry. The Aranya Co-ord features coconut-shell buttons, deep trouser pockets, and an airy silhouette ideal for airport travel, brunches, and creative workspaces.',
            'care_notes' => 'Cold gentle wash. Steam press lightly while slightly damp for crisp tailoring.',
        ],
        [
            'slug' => 'noor-rose-mulmul-suit',
            'name' => 'Noor Pastel Rose Mulmul Suit Set',
            'subtitle' => 'Featherlight Dusty-Rose Floral Mulmul Kurta, Pants & Scalloped Dupatta',
            'category' => 'Suit Sets',
            'collection_tag' => 'Pre-Order Exclusive',
            'price_inr' => 2790,
            'compare_price_inr' => 3390,
            'fabric' => '100% Ultra-Fine Mulmul Cotton',
            'craft' => 'Sanganeri Chintz & Crochet Lace',
            'color_name' => 'Dusty Rose',
            'color_hex' => '#D4969E',
            'sizes_json' => json_encode(['XS', 'S', 'M', 'L', 'XL', 'XXL', 'Custom']),
            'images_json' => json_encode([
                'assets/images/prod-rose-mulmul.jpg',
                'assets/images/hero-editorial.jpg'
            ]),
            'badge' => 'Pre-Order',
            'rating' => 5.0,
            'review_count' => 1,
            'stock' => 7,
            'is_featured' => 1,
            'is_preorder' => 1,
            'description' => 'Made to order in small batches by our master karigars (dispatches in 7–10 days). Airy pastel rose mulmul kissed with delicate floral motifs and edged with hand-finished cotton crochet lace along the scalloped dupatta.',
            'care_notes' => 'Gentle hand wash in cold water. Dry flat or on padded hanger in shade.',
        ],
        [
            'slug' => 'ivory-palash-farshi-preorder',
            'name' => 'Palash Ivory & Blush Heirloom Farshi',
            'subtitle' => 'Limited-Batch Pre-Order Farshi Ensemble with Hand-Finished Gota',
            'category' => 'Pre-Orders',
            'collection_tag' => 'Atelier Limited Drop',
            'price_inr' => 3390,
            'compare_price_inr' => 3990,
            'fabric' => 'Pure Chanderi-Cotton Blend',
            'craft' => 'Hand-Block & Gota Patti',
            'color_name' => 'Ivory Blush',
            'color_hex' => '#EFE2D6',
            'sizes_json' => json_encode(['XS', 'S', 'M', 'L', 'XL', 'XXL', 'Custom']),
            'images_json' => json_encode([
                'assets/images/hero-editorial.jpg',
                'assets/images/prod-blush-farshi.jpg'
            ]),
            'badge' => 'Limited Pre-Order',
            'rating' => 4.9,
            'review_count' => 1,
            'stock' => 6,
            'is_featured' => 0,
            'is_preorder' => 1,
            'description' => 'An heirloom keepsake crafted exclusively on pre-order with custom measurement support. Combines luminous ivory cotton-chanderi with blush lotus motifs and fine silver-gold gota trimming.',
            'care_notes' => 'Dry clean recommended to preserve delicate gota detailing.',
        ],
        [
            'slug' => 'jaipur-terracotta-coord',
            'name' => 'Bagru Terracotta Relaxed Co-ord',
            'subtitle' => 'Earthy Rust & Ivory Hand-Block Tunic Set for Effortless Days',
            'category' => 'Co-ord Sets',
            'collection_tag' => 'Indo-Western Edit',
            'price_inr' => 2090,
            'compare_price_inr' => 2490,
            'fabric' => 'Pure Breathable Cotton',
            'craft' => 'Natural Dye Block Print',
            'color_name' => 'Terracotta Rust',
            'color_hex' => '#A64438',
            'sizes_json' => json_encode(['XS', 'S', 'M', 'L', 'XL', 'XXL']),
            'images_json' => json_encode([
                'assets/images/prod-crimson-farshi.jpg',
                'assets/images/prod-sage-coord.jpg'
            ]),
            'badge' => 'Small Batch',
            'rating' => 4.8,
            'review_count' => 1,
            'stock' => 12,
            'is_featured' => 0,
            'is_preorder' => 0,
            'description' => 'Dyed with natural madder root hues and tailored into a sleek two-piece Indo-Western co-ord with side slits, elastic back waist trousers, and functional pockets.',
            'care_notes' => 'Gentle cold wash separately with pH-neutral detergent.',
        ],
    ];

    $stmt = $pdo->prepare("
        INSERT INTO products (
            slug, name, subtitle, category, collection_tag, price_inr, compare_price_inr,
            fabric, craft, color_name, color_hex, sizes_json, images_json, badge,
            rating, review_count, stock, is_featured, is_preorder, description, care_notes
        ) VALUES (
            :slug, :name, :subtitle, :category, :collection_tag, :price_inr, :compare_price_inr,
            :fabric, :craft, :color_name, :color_hex, :sizes_json, :images_json, :badge,
            :rating, :review_count, :stock, :is_featured, :is_preorder, :description, :care_notes
        )
    ");

    foreach ($products as $p) {
        $stmt->execute($p);
    }

    // Seed authentic customer reviews
    $reviews = [
        [1, 'Ananya Sharma', 'Jaipur', 5, 'Pure dreamy cotton!', 'Ordered the Gulab Lotus Farshi Set for my sister’s roka brunch and got endless compliments. Even the packaging came tied with a pink ribbon and baby’s breath!'],
        [1, 'Meera Nair', 'Bengaluru', 5, 'Worth every rupee', 'The Farshi flare is so graceful and the cambric cotton breathes beautifully even in humid weather. Fits true to size.'],
        [1, 'Rhea Kapoor', 'London, UK', 5, 'Smooth worldwide delivery', 'Received my parcel in London in 6 days! The blush print looks even softer and prettier in person.'],
        [2, 'Kavya Deshmukh', 'Pune', 5, 'Rich crimson shade', 'The Kalamkari print doesn’t bleed and the fall of the Farshi salwar feels regal without feeling heavy.'],
        [2, 'Sneha Patel', 'Ahmedabad', 5, 'Custom sizing fit like a glove', 'Shared my measurements on custom sizing and Akshree’s team tailored it impeccably.'],
        [3, 'Ishita Verma', 'New Delhi', 5, 'Perfect haldi outfit', 'The mustard citrus tone photographed wonderfully in sunlight. Dupatta is super airy.'],
        [4, 'Divya Iyer', 'Mumbai', 5, 'My go-to office kurta', 'Wore it for a 10-hour workday and didn’t want to take it off. Love the deep pockets!'],
        [6, 'Tanvi Joshi', 'Hyderabad', 5, 'Authentic Bandhani cotton', 'Such a vibrant royal indigo and super soft fabric. Already eyeing my next order.'],
        [7, 'Zoya Merchant', 'Dubai, UAE', 5, 'Chic Indo-Western silhouette', 'Wore the Sage Co-ord on vacation and straight onto the flight back. Zero wrinkling fuss!'],
        [8, 'Priyanka Sen', 'Kolkata', 5, 'Pre-order worth the wait', 'Arrived in 8 days wrapped like a gift. The crochet lace on the dupatta is so delicate.'],
    ];

    $rStmt = $pdo->prepare("
        INSERT INTO reviews (product_id, author_name, city, rating, title, comment, verified)
        VALUES (?, ?, ?, ?, ?, ?, 1)
    ");
    foreach ($reviews as $r) {
        $rStmt->execute($r);
    }

    // Seed 2 sample orders for Order Tracking demo
    $sampleOrders = [
        [
            'order_number' => 'PHV-2026-1042',
            'customer_name' => 'Ananya Sharma',
            'customer_email' => 'ananya.demo@example.com',
            'customer_phone' => '+91 98765 43210',
            'country' => 'India',
            'address_line' => '42 Civil Lines, Near Hawa Mahal Road',
            'city' => 'Jaipur',
            'state' => 'Rajasthan',
            'postal_code' => '302006',
            'shipping_method' => 'Express Pan-India Air',
            'payment_method' => 'UPI / Instant Pay',
            'coupon_code' => 'AKSHREE10',
            'subtotal_inr' => 2890,
            'discount_inr' => 289,
            'shipping_inr' => 0,
            'total_inr' => 2601,
            'currency' => 'INR',
            'gift_wrap' => 1,
            'gift_note' => 'Happy Birthday Didi! With love.',
            'status' => 'Dispatched',
            'items_json' => json_encode([
                [
                    'id' => 1,
                    'name' => 'Gulab Lotus Farshi Salwar Set',
                    'size' => 'M',
                    'qty' => 1,
                    'price_inr' => 2890,
                    'image' => 'assets/images/prod-blush-farshi.jpg'
                ]
            ])
        ],
        [
            'order_number' => 'PHV-2026-1089',
            'customer_name' => 'Rhea Kapoor',
            'customer_email' => 'rhea.demo@example.com',
            'customer_phone' => '+44 7700 900123',
            'country' => 'United Kingdom',
            'address_line' => '18 Kensington Park Gardens',
            'city' => 'London',
            'state' => 'Greater London',
            'postal_code' => 'W11 3HD',
            'shipping_method' => 'Worldwide DHL Express',
            'payment_method' => 'Card / International',
            'coupon_code' => '',
            'subtotal_inr' => 5380,
            'discount_inr' => 0,
            'shipping_inr' => 1490,
            'total_inr' => 6870,
            'currency' => 'GBP',
            'gift_wrap' => 1,
            'gift_note' => 'Signature kraft & pink ribbon packaging please!',
            'status' => 'Delivered',
            'items_json' => json_encode([
                [
                    'id' => 1,
                    'name' => 'Gulab Lotus Farshi Salwar Set',
                    'size' => 'S',
                    'qty' => 1,
                    'price_inr' => 2890,
                    'image' => 'assets/images/prod-blush-farshi.jpg'
                ],
                [
                    'id' => 3,
                    'name' => 'Citrus Sunbeam Cotton Suit Set',
                    'size' => 'S',
                    'qty' => 1,
                    'price_inr' => 2490,
                    'image' => 'assets/images/prod-citrus-suit.jpg'
                ]
            ])
        ]
    ];

    $oStmt = $pdo->prepare("
        INSERT INTO orders (
            order_number, customer_name, customer_email, customer_phone, country,
            address_line, city, state, postal_code, shipping_method, payment_method,
            coupon_code, subtotal_inr, discount_inr, shipping_inr, total_inr,
            currency, gift_wrap, gift_note, status, items_json
        ) VALUES (
            :order_number, :customer_name, :customer_email, :customer_phone, :country,
            :address_line, :city, :state, :postal_code, :shipping_method, :payment_method,
            :coupon_code, :subtotal_inr, :discount_inr, :shipping_inr, :total_inr,
            :currency, :gift_wrap, :gift_note, :status, :items_json
        )
    ");
    foreach ($sampleOrders as $o) {
        $oStmt->execute($o);
    }
}
