<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\ProductBundle;
use App\Models\BundleItem;
use App\Models\ProductVariant;
use App\Models\Review;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserSkinProfile;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CosmeticsDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Users (Admin, Manager, Customer)
        $admin = User::firstOrCreate(
            ['email' => 'admin@beautyvault.com'],
            [
                'name' => 'Dr. Elena Vance (Head Chemist & Admin)',
                'password' => Hash::make('password123'),
                'phone' => '+1 (555) 234-5678',
                'role' => 'admin',
                'avatar_url' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=400&q=80',
                'is_active' => true,
            ]
        );

        $customer = User::firstOrCreate(
            ['email' => 'customer@beautyvault.com'],
            [
                'name' => 'Sophia Chen',
                'password' => Hash::make('password123'),
                'phone' => '+1 (555) 987-6543',
                'role' => 'customer',
                'avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=400&q=80',
                'is_active' => true,
            ]
        );

        // Seed Customer Skin Profile
        UserSkinProfile::updateOrCreate(
            ['user_id' => $customer->id],
            [
                'skin_type' => 'combination',
                'undertone' => 'warm',
                'shade_depth' => 4,
                'concerns' => ['hyperpigmentation', 'large_pores', 'dullness'],
                'allergies' => ['fragrance'],
                'hair_type' => 'wavy',
                'hair_porosity' => 'medium',
            ]
        );

        // Seed Customer Address
        UserAddress::firstOrCreate(
            ['user_id' => $customer->id, 'label' => 'Home Penthouse'],
            [
                'recipient_name' => 'Sophia Chen',
                'recipient_phone' => '+1 (555) 987-6543',
                'street_address' => '742 Evergreen Terrace, Apt 4B',
                'apartment_or_suite' => 'Apt 4B',
                'city' => 'Beverly Hills',
                'state_or_province' => 'CA',
                'postal_code' => '90210',
                'country' => 'US',
                'is_default_shipping' => true,
                'is_default_billing' => true,
            ]
        );

        // 2. Seed Brands
        $brandsData = [
            [
                'name' => 'Fenty Beauty',
                'slug' => 'fenty-beauty',
                'description' => 'Created by Rihanna, renowned for game-changing 50 inclusive foundation shades and weightless textures.',
                'logo_url' => 'https://images.unsplash.com/photo-1516975080664-ed2fc6a32937?auto=format&fit=crop&w=300&q=80',
                'banner_url' => 'https://images.unsplash.com/photo-1512496015851-a90fb38ba796?auto=format&fit=crop&w=1200&q=80',
                'origin_country' => 'USA',
                'is_featured' => true,
                'cruelty_free' => true,
                'vegan_certified' => true,
                'clean_certified' => false,
            ],
            [
                'name' => 'The Ordinary',
                'slug' => 'the-ordinary',
                'description' => 'Clinical formulations with clinical integrity. High concentrations of proven skincare actives at honest prices.',
                'logo_url' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=300&q=80',
                'banner_url' => 'https://images.unsplash.com/photo-1608248597359-54876e9389f4?auto=format&fit=crop&w=1200&q=80',
                'origin_country' => 'Canada',
                'is_featured' => true,
                'cruelty_free' => true,
                'vegan_certified' => true,
                'clean_certified' => true,
            ],
            [
                'name' => 'Rare Beauty',
                'slug' => 'rare-beauty',
                'description' => 'Selena Gomez’s breathable formulas that break down unrealistic beauty standards with weightless liquid blushes.',
                'logo_url' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=300&q=80',
                'banner_url' => 'https://images.unsplash.com/photo-1526947425960-945c6e72858f?auto=format&fit=crop&w=1200&q=80',
                'origin_country' => 'USA',
                'is_featured' => true,
                'cruelty_free' => true,
                'vegan_certified' => true,
                'clean_certified' => true,
            ],
            [
                'name' => 'CeraVe',
                'slug' => 'cerave',
                'description' => 'Developed with dermatologists, packed with 3 essential ceramides to restore skin’s natural protective barrier.',
                'logo_url' => 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&w=300&q=80',
                'banner_url' => 'https://images.unsplash.com/photo-1556228722-d0b5d034b074?auto=format&fit=crop&w=1200&q=80',
                'origin_country' => 'USA',
                'is_featured' => false,
                'cruelty_free' => false,
                'vegan_certified' => false,
                'clean_certified' => true,
            ],
            [
                'name' => 'Paula\'s Choice',
                'slug' => 'paulas-choice',
                'description' => 'Science-backed skincare with zero fragrance, zero fluff. World-famous BHA chemical exfoliants.',
                'logo_url' => 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?auto=format&fit=crop&w=300&q=80',
                'banner_url' => 'https://images.unsplash.com/photo-1508746829417-e6f548d8d6ed?auto=format&fit=crop&w=1200&q=80',
                'origin_country' => 'USA',
                'is_featured' => true,
                'cruelty_free' => true,
                'vegan_certified' => true,
                'clean_certified' => true,
            ],
            [
                'name' => 'Summer Fridays',
                'slug' => 'summer-fridays',
                'description' => 'Clean, effortless beauty products made with clean ingredients that give skin an immediate glow.',
                'logo_url' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=300&q=80',
                'banner_url' => 'https://images.unsplash.com/photo-1512496015851-a90fb38ba796?auto=format&fit=crop&w=1200&q=80',
                'origin_country' => 'USA',
                'is_featured' => true,
                'cruelty_free' => true,
                'vegan_certified' => true,
                'clean_certified' => true,
            ],
            [
                'name' => 'Charlotte Tilbury',
                'slug' => 'charlotte-tilbury',
                'description' => 'British luxury makeup and skincare artist brand delivering red-carpet perfection and soft-focus formulas.',
                'logo_url' => 'https://images.unsplash.com/photo-1599733589046-10c005739ef9?auto=format&fit=crop&w=300&q=80',
                'banner_url' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=1200&q=80',
                'origin_country' => 'United Kingdom',
                'is_featured' => true,
                'cruelty_free' => true,
                'vegan_certified' => false,
                'clean_certified' => false,
            ],
            [
                'name' => 'La Roche-Posay',
                'slug' => 'la-roche-posay',
                'description' => 'French dermatological powerhouse harnessing prebiotic thermal spring water and advanced UV Mexoryl 400 filters.',
                'logo_url' => 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&w=300&q=80',
                'banner_url' => 'https://images.unsplash.com/photo-1556228722-d0b5d034b074?auto=format&fit=crop&w=1200&q=80',
                'origin_country' => 'France',
                'is_featured' => true,
                'cruelty_free' => false,
                'vegan_certified' => false,
                'clean_certified' => true,
            ],
            [
                'name' => 'COSRX',
                'slug' => 'cosrx',
                'description' => 'Korean clinical skincare formulated with minimal, highly effective skin-friendly ingredients.',
                'logo_url' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=300&q=80',
                'banner_url' => 'https://images.unsplash.com/photo-1608248597359-54876e9389f4?auto=format&fit=crop&w=1200&q=80',
                'origin_country' => 'South Korea',
                'is_featured' => true,
                'cruelty_free' => true,
                'vegan_certified' => false,
                'clean_certified' => true,
            ],
            [
                'name' => 'Rhode Skin',
                'slug' => 'rhode-skin',
                'description' => 'Hailey Bieber’s line of curated peptide-powered skincare essentials for radiant, glazed donut skin.',
                'logo_url' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=300&q=80',
                'banner_url' => 'https://images.unsplash.com/photo-1526947425960-945c6e72858f?auto=format&fit=crop&w=1200&q=80',
                'origin_country' => 'USA',
                'is_featured' => true,
                'cruelty_free' => true,
                'vegan_certified' => true,
                'clean_certified' => true,
            ],
        ];

        $brands = [];
        foreach ($brandsData as $b) {
            $brands[$b['slug']] = Brand::updateOrCreate(['slug' => $b['slug']], $b);
        }

        // 3. Seed Categories
        $categoriesData = [
            ['name' => 'Skincare', 'slug' => 'skincare', 'description' => 'Serums, moisturizers, cleansers, and targeted treatments.'],
            ['name' => 'Complexion & Foundation', 'slug' => 'complexion', 'description' => 'Foundations, concealers, and skin tints in 50+ inclusive undertones.'],
            ['name' => 'Cheeks & Glow', 'slug' => 'cheeks-glow', 'description' => 'Liquid blushes, highlighters, and contour wands.'],
            ['name' => 'Sun Protection (SPF)', 'slug' => 'sun-protection', 'description' => 'Invisible mineral and chemical UV filters without white cast.'],
            ['name' => 'Lip Care & Color', 'slug' => 'lip-care', 'description' => 'Plumping oils, velvet mattes, and hydrating balms.'],
        ];

        $categories = [];
        foreach ($categoriesData as $c) {
            $categories[$c['slug']] = Category::updateOrCreate(['slug' => $c['slug']], $c);
        }

        // 4. Seed Ingredients Glossary
        $ingredientsData = [
            [
                'name' => 'Niacinamide',
                'inci_name' => 'Niacinamide (Vitamin B3)',
                'slug' => 'niacinamide',
                'description' => 'Regulates sebum, reduces pore diameter, strengthens lipid barrier, and fades dark spots.',
                'comedogenic_rating' => 0,
                'ewg_safety_score' => 1,
                'function_type' => 'Antioxidant & Pore Refiner',
                'benefits' => ['pore_minimizing', 'oil_control', 'barrier_repair', 'brightening'],
                'is_active_ingredient' => true,
                'is_potential_irritant' => false,
                'safe_for_fungal_acne' => true,
            ],
            [
                'name' => 'Hyaluronic Acid',
                'inci_name' => 'Sodium Hyaluronate',
                'slug' => 'hyaluronic-acid',
                'description' => 'Holds up to 1,000 times its molecular weight in water, drawing deep multi-depth hydration.',
                'comedogenic_rating' => 0,
                'ewg_safety_score' => 1,
                'function_type' => 'Humectant',
                'benefits' => ['intense_hydration', 'plumping', 'fine_line_smoothing'],
                'is_active_ingredient' => true,
                'is_potential_irritant' => false,
                'safe_for_fungal_acne' => true,
            ],
            [
                'name' => 'Salicylic Acid',
                'inci_name' => 'Salicylic Acid (BHA)',
                'slug' => 'salicylic-acid',
                'description' => 'Lipid-soluble beta hydroxy acid that penetrates deep into pores to dissolve sebum and dead keratin cells.',
                'comedogenic_rating' => 0,
                'ewg_safety_score' => 1,
                'function_type' => 'Chemical Exfoliant (BHA)',
                'benefits' => ['acne_treatment', 'blackhead_removal', 'pore_clearing'],
                'is_active_ingredient' => true,
                'is_potential_irritant' => true,
                'safe_for_fungal_acne' => true,
            ],
            [
                'name' => 'Pure Encapsulated Retinol',
                'inci_name' => 'Retinol',
                'slug' => 'retinol',
                'description' => 'Gold standard anti-aging derivative that accelerates cellular renewal and collagen synthesis.',
                'comedogenic_rating' => 0,
                'ewg_safety_score' => 2,
                'function_type' => 'Cell-Communicating Active',
                'benefits' => ['anti_aging', 'wrinkle_reduction', 'texture_smoothing'],
                'is_active_ingredient' => true,
                'is_potential_irritant' => true,
                'safe_for_fungal_acne' => true,
            ],
            [
                'name' => 'Centella Asiatica Extract (Cica)',
                'inci_name' => 'Madecassoside / Centella Asiatica',
                'slug' => 'centella-asiatica',
                'description' => 'Traditional medicinal botanical renowned for neutralizing redness and accelerating wound repair.',
                'comedogenic_rating' => 0,
                'ewg_safety_score' => 1,
                'function_type' => 'Soothing Botanical',
                'benefits' => ['redness_reduction', 'barrier_healing', 'calming'],
                'is_active_ingredient' => true,
                'is_potential_irritant' => false,
                'safe_for_fungal_acne' => true,
            ],
            [
                'name' => 'Zinc Oxide (Non-Nano)',
                'inci_name' => 'Zinc Oxide',
                'slug' => 'zinc-oxide',
                'description' => 'Broad-spectrum mineral sunscreen active reflecting UVA and UVB rays without dermal absorption.',
                'comedogenic_rating' => 0,
                'ewg_safety_score' => 1,
                'function_type' => 'Mineral Sunscreen Filter',
                'benefits' => ['uv_protection', 'soothing'],
                'is_active_ingredient' => true,
                'is_potential_irritant' => false,
                'safe_for_fungal_acne' => true,
            ],
            [
                'name' => 'Virgin Coconut Oil',
                'inci_name' => 'Cocos Nucifera (Coconut) Oil',
                'slug' => 'coconut-oil',
                'description' => 'Rich emollient oil high in lauric acid; highly occlusive and pore-clogging on facial skin.',
                'comedogenic_rating' => 4,
                'ewg_safety_score' => 1,
                'function_type' => 'Occlusive Emollient',
                'benefits' => ['body_hydration', 'hair_softening'],
                'is_active_ingredient' => false,
                'is_potential_irritant' => false,
                'safe_for_fungal_acne' => false,
            ],
            [
                'name' => 'Peptide Complex',
                'inci_name' => 'Palmitoyl Tripeptide-1 & Tetrapeptide-7',
                'slug' => 'peptides',
                'description' => 'Signaling amino acid chains that stimulate collagen synthesis, firming skin and plumping lips.',
                'comedogenic_rating' => 0,
                'ewg_safety_score' => 1,
                'function_type' => 'Cell-Signaling Peptide',
                'benefits' => ['collagen_boosting', 'lip_plumping', 'firming', 'fine_line_smoothing'],
                'is_active_ingredient' => true,
                'is_potential_irritant' => false,
                'safe_for_fungal_acne' => true,
            ],
            [
                'name' => 'Triple Ceramide Complex',
                'inci_name' => 'Ceramide NP, AP, EOP',
                'slug' => 'ceramides',
                'description' => 'Bio-identical lipids that mirror 50% of the skin lipid matrix, repairing barrier function and preventing transepidermal water loss.',
                'comedogenic_rating' => 0,
                'ewg_safety_score' => 1,
                'function_type' => 'Skin-Identical Barrier Lipid',
                'benefits' => ['barrier_repair', 'deep_moisture', 'calming', 'protection'],
                'is_active_ingredient' => true,
                'is_potential_irritant' => false,
                'safe_for_fungal_acne' => true,
            ],
            [
                'name' => 'Snail Secretion Filtrate (96%)',
                'inci_name' => 'Snail Secretion Filtrate',
                'slug' => 'snail-secretion',
                'description' => 'Naturally rich in allantoin, glycolic acid, and glycoproteins for intense cellular regeneration and glow.',
                'comedogenic_rating' => 0,
                'ewg_safety_score' => 1,
                'function_type' => 'Regenerative Mucilage',
                'benefits' => ['skin_repair', 'glass_skin_glow', 'hydration', 'acne_scar_fading'],
                'is_active_ingredient' => true,
                'is_potential_irritant' => false,
                'safe_for_fungal_acne' => true,
            ],
        ];

        $ingredients = [];
        foreach ($ingredientsData as $ing) {
            $ingredients[$ing['slug']] = Ingredient::updateOrCreate(['slug' => $ing['slug']], $ing);
        }

        // 5. Seed Flagship Products
        // Product 1: Fenty Pro Filt'r Soft Matte Longwear Foundation
        $fentyFoundation = Product::updateOrCreate(
            ['slug' => 'fenty-pro-filtr-soft-matte-foundation'],
            [
                'brand_id' => $brands['fenty-beauty']->id,
                'category_id' => $categories['complexion']->id,
                'name' => 'Pro Filt\'r Soft Matte Longwear Foundation',
                'subtitle' => 'Award-winning climate-adaptive foundation in 50 inclusive shades',
                'description' => 'A soft matte, longwear foundation featuring climate-adaptive technology that gives skin an instantly smooth, pore-diffused, shine-free finish. Oil-free, sweat-resistant, and won’t clog pores.',
                'how_to_use' => 'Shake well before application. Dispense 1-2 pumps onto the back of your hand. Using a damp sponge or dense foundation brush, buff onto the center of the face and blend outward.',
                'full_ingredients_text' => 'Aqua/Water/Eau, Dimethicone, Talc, Peg-10 Dimethicone, Trimethylsiloxysilicate, Polypropylene, Isododecane, Cetyl Peg/Ppg-10/1 Dimethicone, Nylon-12, Hdi/Trimethylol Hexyllactone Crosspolymer, Phenoxyethanol, Sodium Chloride.',
                'target_skin_types' => ['oily', 'combination', 'normal'],
                'target_concerns' => ['large_pores', 'excess_shine', 'uneven_tone'],
                'finish_type' => 'matte',
                'coverage_level' => 'medium',
                'spf_rating' => null,
                'pao_months' => 12,
                'base_price' => 42.00,
                'discount_price' => 38.00,
                'is_featured' => true,
                'is_bestseller' => true,
                'is_active' => true,
                'rating_avg' => 4.80,
                'reviews_count' => 142,
                'thumbnail_url' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=800&q=80',
                'gallery_images' => [
                    'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1512496015851-a90fb38ba796?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=800&q=80'
                ],
            ]
        );

        // Product 1 Variants (Realistic Shades with accurate HEX and undertones)
        $shades = [
            ['sku' => 'FNT-PF-120N', 'title' => '120 - Fair with Neutral Undertones', 'shade_name' => '120 Fair Neutral', 'shade_hex' => '#F4DCBD', 'depth_level' => 1, 'undertone' => 'neutral', 'stock' => 35],
            ['sku' => 'FNT-PF-150L', 'title' => '150 - Light with Cool Pink Undertones', 'shade_name' => '150 Light Cool', 'shade_hex' => '#EFC4A4', 'depth_level' => 2, 'undertone' => 'cool', 'stock' => 40],
            ['sku' => 'FNT-PF-230W', 'title' => '230 - Medium with Warm Golden Undertones', 'shade_name' => '230 Medium Warm', 'shade_hex' => '#D8A279', 'depth_level' => 4, 'undertone' => 'warm', 'stock' => 50],
            ['sku' => 'FNT-PF-290O', 'title' => '290 - Medium to Tan with Olive Undertones', 'shade_name' => '290 Medium Olive', 'shade_hex' => '#C48D63', 'depth_level' => 5, 'undertone' => 'olive', 'stock' => 28],
            ['sku' => 'FNT-PF-350T', 'title' => '350 - Tan with Warm Peachy Undertones', 'shade_name' => '350 Tan Peach', 'shade_hex' => '#AD6E46', 'depth_level' => 7, 'undertone' => 'peach', 'stock' => 32],
            ['sku' => 'FNT-PF-440D', 'title' => '440 - Deep with Cool Red Undertones', 'shade_name' => '440 Deep Cool', 'shade_hex' => '#653D28', 'depth_level' => 9, 'undertone' => 'cool', 'stock' => 24],
            ['sku' => 'FNT-PF-490E', 'title' => '490 - Very Deep with Neutral Undertones', 'shade_name' => '490 Deep Neutral', 'shade_hex' => '#422519', 'depth_level' => 10, 'undertone' => 'neutral', 'stock' => 18],
        ];

        foreach ($shades as $s) {
            $variant = ProductVariant::updateOrCreate(
                ['sku' => $s['sku']],
                [
                    'product_id' => $fentyFoundation->id,
                    'title' => $s['title'],
                    'shade_name' => $s['shade_name'],
                    'shade_hex' => $s['shade_hex'],
                    'depth_level' => $s['depth_level'],
                    'undertone' => $s['undertone'],
                    'volume_or_weight' => '32 ml / 1.08 fl. oz',
                    'price' => 38.00,
                    'compare_at_price' => 42.00,
                    'stock_quantity' => $s['stock'],
                    'low_stock_threshold' => 10,
                    'is_active' => true,
                    'image_url' => $fentyFoundation->thumbnail_url,
                ]
            );

            // Seed FIFO Batches for variant
            ProductBatch::firstOrCreate(
                ['batch_number' => 'LOT-2025-' . substr($s['sku'], -4) . '-A'],
                [
                    'product_variant_id' => $variant->id,
                    'supplier_name' => 'Kendo Fragrances & Cosmetics Lab',
                    'manufactured_at' => Carbon::now()->subMonths(3)->toDateString(),
                    'expires_at' => Carbon::now()->addMonths(21)->toDateString(),
                    'initial_quantity' => $s['stock'],
                    'current_quantity' => $s['stock'],
                    'unit_cost' => 14.50,
                    'status' => 'active',
                    'warehouse_location' => 'Zone C, Shelf 14',
                ]
            );
        }

        // Product 2: The Ordinary Niacinamide 10% + Zinc 1%
        $ordinaryNiacinamide = Product::updateOrCreate(
            ['slug' => 'the-ordinary-niacinamide-10-zinc-1'],
            [
                'brand_id' => $brands['the-ordinary']->id,
                'category_id' => $categories['skincare']->id,
                'name' => 'Niacinamide 10% + Zinc 1%',
                'subtitle' => 'High-strength vitamin and mineral blemish formula',
                'description' => 'Niacinamide (Vitamin B3) is indicated to reduce the appearance of skin blemishes and congestion. A high 10% concentration of this vitamin is supported in the formula by zinc salt of pyrrolidone carboxylic acid to balance visible aspects of sebum activity.',
                'how_to_use' => 'Apply to entire face morning and evening before heavier creams. If irritation occurs, cease use and consult a physician.',
                'full_ingredients_text' => 'Aqua (Water), Niacinamide, Pentylene Glycol, Zinc PCA, Dimethyl Isosorbide, Tamarindus Indica Seed Gum, Xanthan gum, Isoceteth-20, Ethoxydiglycol, Phenoxyethanol, Chlorphenesin.',
                'target_skin_types' => ['oily', 'combination', 'normal', 'sensitive'],
                'target_concerns' => ['acne', 'large_pores', 'excess_oil', 'redness'],
                'finish_type' => 'natural',
                'coverage_level' => null,
                'spf_rating' => null,
                'pao_months' => 12,
                'base_price' => 12.00,
                'discount_price' => 10.80,
                'is_featured' => true,
                'is_bestseller' => true,
                'is_active' => true,
                'rating_avg' => 4.90,
                'reviews_count' => 520,
                'thumbnail_url' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=800&q=80',
                'gallery_images' => [
                    'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1608248597359-54876e9389f4?auto=format&fit=crop&w=800&q=80',
                ],
            ]
        );

        // Attach Key Active Ingredients
        $ordinaryNiacinamide->ingredients()->syncWithoutDetaching([
            $ingredients['niacinamide']->id => ['concentration_percentage' => 10.00, 'is_key_active' => true],
            $ingredients['hyaluronic-acid']->id => ['concentration_percentage' => 1.50, 'is_key_active' => false],
        ]);

        // Product 2 Variant
        $niacinamideVariant = ProductVariant::updateOrCreate(
            ['sku' => 'ORD-NIA-60ML'],
            [
                'product_id' => $ordinaryNiacinamide->id,
                'title' => 'Standard Dropper - 60ml Value Size',
                'volume_or_weight' => '60 ml / 2.0 fl. oz',
                'price' => 10.80,
                'compare_at_price' => 12.00,
                'stock_quantity' => 120,
                'low_stock_threshold' => 15,
                'is_active' => true,
                'image_url' => $ordinaryNiacinamide->thumbnail_url,
            ]
        );

        // FIFO Batches for Niacinamide: Batch A (near-expiry for alert testing) and Batch B (fresh)
        ProductBatch::firstOrCreate(
            ['batch_number' => 'LOT-ORD-2024-EXP-NEAR'],
            [
                'product_variant_id' => $niacinamideVariant->id,
                'supplier_name' => 'DECIEM Toronto Cleanroom',
                'manufactured_at' => Carbon::now()->subMonths(16)->toDateString(),
                'expires_at' => Carbon::now()->addDays(45)->toDateString(), // 45 days until expiry!
                'initial_quantity' => 25,
                'current_quantity' => 20,
                'unit_cost' => 3.20,
                'status' => 'active',
                'warehouse_location' => 'Front FIFO Rack A1',
            ]
        );

        ProductBatch::firstOrCreate(
            ['batch_number' => 'LOT-ORD-2025-FRESH'],
            [
                'product_variant_id' => $niacinamideVariant->id,
                'supplier_name' => 'DECIEM Toronto Cleanroom',
                'manufactured_at' => Carbon::now()->subMonths(1)->toDateString(),
                'expires_at' => Carbon::now()->addMonths(24)->toDateString(),
                'initial_quantity' => 100,
                'current_quantity' => 100,
                'unit_cost' => 3.20,
                'status' => 'active',
                'warehouse_location' => 'Main Pallet P-4',
            ]
        );

        // Product 3: Rare Beauty Soft Pinch Liquid Blush
        $rareBlush = Product::updateOrCreate(
            ['slug' => 'rare-beauty-soft-pinch-liquid-blush'],
            [
                'brand_id' => $brands['rare-beauty']->id,
                'category_id' => $categories['cheeks-glow']->id,
                'name' => 'Soft Pinch Liquid Blush',
                'subtitle' => 'Weightless, long-lasting liquid blush that blends and builds beautifully',
                'description' => 'A weightless, long-lasting liquid blush that blends and builds effortlessly for a soft, healthy flush. Available in both matte and dewy finishes.',
                'how_to_use' => 'Gently remove excess product from applicator. Use the doe-foot applicator and place 1-2 dots on each cheek. Use fingertips and gently pat into skin for a seamless finish.',
                'full_ingredients_text' => 'Hydrogenated Polyisobutene, Hydrogenated Poly(C6-14 Olefin), Mica, Octyldodecanol, Ethylene/Propylene/Styrene Copolymer, Trimethylsiloxysilicate, Isododecane, 1,2-Hexanediol.',
                'target_skin_types' => ['normal', 'dry', 'combination', 'oily', 'sensitive'],
                'target_concerns' => ['dullness', 'uneven_tone'],
                'finish_type' => 'dewy',
                'coverage_level' => 'buildable',
                'spf_rating' => null,
                'pao_months' => 12,
                'base_price' => 23.00,
                'discount_price' => 23.00,
                'is_featured' => true,
                'is_bestseller' => true,
                'is_active' => true,
                'rating_avg' => 4.95,
                'reviews_count' => 310,
                'thumbnail_url' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=800&q=80',
                'gallery_images' => [
                    'https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=800&q=80',
                ],
            ]
        );

        $blushShades = [
            ['sku' => 'RB-BLUSH-HOPE', 'title' => 'Hope - Nude Mauve (Dewy)', 'shade_name' => 'Hope', 'shade_hex' => '#DE8B87', 'stock' => 45],
            ['sku' => 'RB-BLUSH-JOY', 'title' => 'Joy - Muted Peach (Dewy)', 'shade_name' => 'Joy', 'shade_hex' => '#EB8A74', 'stock' => 30],
            ['sku' => 'RB-BLUSH-HAPPY', 'title' => 'Happy - Cool Pink (Dewy)', 'shade_name' => 'Happy', 'shade_hex' => '#EA7899', 'stock' => 50],
            ['sku' => 'RB-BLUSH-GRATITUDE', 'title' => 'Gratitude - True Terracotta (Dewy)', 'shade_name' => 'Gratitude', 'shade_hex' => '#B85848', 'stock' => 20],
        ];

        foreach ($blushShades as $b) {
            $blushVariant = ProductVariant::updateOrCreate(
                ['sku' => $b['sku']],
                [
                    'product_id' => $rareBlush->id,
                    'title' => $b['title'],
                    'shade_name' => $b['shade_name'],
                    'shade_hex' => $b['shade_hex'],
                    'depth_level' => 3,
                    'undertone' => 'neutral',
                    'volume_or_weight' => '7.5 ml / 0.25 fl. oz',
                    'price' => 23.00,
                    'compare_at_price' => null,
                    'stock_quantity' => $b['stock'],
                    'low_stock_threshold' => 10,
                    'is_active' => true,
                    'image_url' => $rareBlush->thumbnail_url,
                ]
            );

            ProductBatch::firstOrCreate(
                ['batch_number' => 'LOT-RB-2025-' . substr($b['sku'], -4)],
                [
                    'product_variant_id' => $blushVariant->id,
                    'supplier_name' => 'Rare Beauty Labs California',
                    'manufactured_at' => Carbon::now()->subMonths(2)->toDateString(),
                    'expires_at' => Carbon::now()->addMonths(22)->toDateString(),
                    'initial_quantity' => $b['stock'],
                    'current_quantity' => $b['stock'],
                    'unit_cost' => 6.80,
                    'status' => 'active',
                    'warehouse_location' => 'Rack D-02',
                ]
            );
        }

        // Product 4: Paula's Choice Skin Perfecting 2% BHA Liquid Exfoliant
        $paulaBHA = Product::updateOrCreate(
            ['slug' => 'paulas-choice-skin-perfecting-2-bha-liquid-exfoliant'],
            [
                'brand_id' => $brands['paulas-choice']->id,
                'category_id' => $categories['skincare']->id,
                'name' => 'Skin Perfecting 2% BHA Liquid Exfoliant',
                'subtitle' => 'Global #1 bestseller for blackheads, enlarged pores, and wrinkles',
                'description' => 'A unique leave-on formula that’s gentle enough for daily use on all skin types. Exfoliates dead skin cells while clearing pores for a more even, radiant tone, plus visibly reduces fine lines and wrinkles.',
                'how_to_use' => 'Apply once or twice daily after cleansing and toning. Lightly soak a cotton pad and apply over the entire face, including the eye area (avoid lash line and eyelids). Do not rinse. For daytime, follow with SPF 30+.',
                'full_ingredients_text' => 'Water (Aqua), Methylpropanediol, Butylene Glycol, Salicylic Acid, Polysorbate 20, Camellia Oleifera (Green Tea) Leaf Extract, Sodium Hydroxide, Tetrasodium EDTA.',
                'target_skin_types' => ['oily', 'combination', 'normal', 'dry'],
                'target_concerns' => ['acne', 'large_pores', 'fine_lines', 'texture'],
                'finish_type' => 'natural',
                'coverage_level' => null,
                'spf_rating' => null,
                'pao_months' => 12,
                'base_price' => 35.00,
                'discount_price' => 35.00,
                'is_featured' => true,
                'is_bestseller' => true,
                'is_active' => true,
                'rating_avg' => 4.88,
                'reviews_count' => 840,
                'thumbnail_url' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=800&q=80',
                'gallery_images' => [
                    'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=800&q=80',
                ],
            ]
        );

        $paulaBHA->ingredients()->syncWithoutDetaching([
            $ingredients['salicylic-acid']->id => ['concentration_percentage' => 2.00, 'is_key_active' => true],
            $ingredients['centella-asiatica']->id => ['concentration_percentage' => 0.50, 'is_key_active' => false],
        ]);

        $bhaVariant = ProductVariant::updateOrCreate(
            ['sku' => 'PC-BHA-118ML'],
            [
                'product_id' => $paulaBHA->id,
                'title' => 'Full Size - 118ml / 4 fl. oz.',
                'volume_or_weight' => '118 ml / 4 fl. oz',
                'price' => 35.00,
                'compare_at_price' => null,
                'stock_quantity' => 75,
                'low_stock_threshold' => 12,
                'is_active' => true,
                'image_url' => $paulaBHA->thumbnail_url,
            ]
        );

        ProductBatch::firstOrCreate(
            ['batch_number' => 'LOT-PC-2025-088'],
            [
                'product_variant_id' => $bhaVariant->id,
                'supplier_name' => 'Paula’s Choice Formulation Plant Seattle',
                'manufactured_at' => Carbon::now()->subMonths(1)->toDateString(),
                'expires_at' => Carbon::now()->addMonths(30)->toDateString(),
                'initial_quantity' => 75,
                'current_quantity' => 75,
                'unit_cost' => 8.50,
                'status' => 'active',
                'warehouse_location' => 'Shelf B-09',
            ]
        );

        // Product 5: CeraVe Hydrating Mineral Sunscreen SPF 50
        $ceraveSPF = Product::updateOrCreate(
            ['slug' => 'cerave-hydrating-mineral-sunscreen-spf50'],
            [
                'brand_id' => $brands['cerave']->id,
                'category_id' => $categories['sun-protection']->id,
                'name' => 'Hydrating Mineral Sunscreen SPF 50',
                'subtitle' => '100% mineral sunscreen with ceramides and niacinamide',
                'description' => 'Formulated with 100% mineral sunscreen filters Titanium Dioxide and Zinc Oxide to form a barrier on the skin’s surface that reflects the sun’s harmful UVA/UVB rays without irritating sensitive skin.',
                'how_to_use' => 'Apply liberally 15 minutes before sun exposure. Reapply at least every 2 hours and immediately after swimming or towel drying.',
                'full_ingredients_text' => 'Titanium Dioxide (9%), Zinc Oxide (7%), Water, Glycerin, C12-15 Alkyl Benzoate, Dimethicone, Isododecane, Niacinamide, Ceramide NP, Ceramide AP, Ceramide EOP, Hyaluronic Acid.',
                'target_skin_types' => ['sensitive', 'dry', 'normal', 'combination'],
                'target_concerns' => ['sun_damage', 'redness', 'barrier_repair'],
                'finish_type' => 'natural',
                'coverage_level' => 'sheer',
                'spf_rating' => 50,
                'pao_months' => 12,
                'base_price' => 19.99,
                'discount_price' => 17.99,
                'is_featured' => true,
                'is_bestseller' => false,
                'is_active' => true,
                'rating_avg' => 4.70,
                'reviews_count' => 95,
                'thumbnail_url' => 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&w=800&q=80',
                'gallery_images' => [
                    'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&w=800&q=80',
                ],
            ]
        );

        $ceraveSPF->ingredients()->syncWithoutDetaching([
            $ingredients['zinc-oxide']->id => ['concentration_percentage' => 7.00, 'is_key_active' => true],
            $ingredients['niacinamide']->id => ['concentration_percentage' => 2.00, 'is_key_active' => false],
            $ingredients['hyaluronic-acid']->id => ['concentration_percentage' => 1.00, 'is_key_active' => false],
        ]);

        $spfVariant = ProductVariant::updateOrCreate(
            ['sku' => 'CRV-SPF-75ML'],
            [
                'product_id' => $ceraveSPF->id,
                'title' => 'Tube - 75ml / 2.5 oz',
                'volume_or_weight' => '75 ml / 2.5 fl. oz',
                'price' => 17.99,
                'compare_at_price' => 19.99,
                'stock_quantity' => 60,
                'low_stock_threshold' => 10,
                'is_active' => true,
                'image_url' => $ceraveSPF->thumbnail_url,
            ]
        );

        ProductBatch::firstOrCreate(
            ['batch_number' => 'LOT-CRV-2025-SPF50'],
            [
                'product_variant_id' => $spfVariant->id,
                'supplier_name' => 'L\'Oréal Dermatological Beauty Plant',
                'manufactured_at' => Carbon::now()->subMonths(2)->toDateString(),
                'expires_at' => Carbon::now()->addMonths(18)->toDateString(),
                'initial_quantity' => 60,
                'current_quantity' => 60,
                'unit_cost' => 5.40,
                'status' => 'active',
                'warehouse_location' => 'Sun Care Bay S-3',
            ]
        );

        // Product 6: Fenty Beauty Gloss Bomb Universal Lip Luminizer (Lip Care)
        $fentyGlossBomb = Product::updateOrCreate(
            ['slug' => 'fenty-beauty-gloss-bomb-universal-lip-luminizer'],
            [
                'brand_id' => $brands['fenty-beauty']->id,
                'category_id' => $categories['lip-care']->id,
                'name' => 'Gloss Bomb Universal Lip Luminizer',
                'subtitle' => 'The ultimate gotta-have-it lip gloss with explosive shine and nourishing shea butter',
                'description' => 'The stop-everything, give-it-to-me lip gloss that feels as good as it looks. Fenty Beauty Gloss Bomb Universal Lip Luminizer delivers explosive shine in handpicked shades that flatter all skin tones.',
                'how_to_use' => 'Wear Gloss Bomb alone or layer over lipstick as the finishing touch to the Fenty Face. The XXL wand gives you more to love with every swipe.',
                'full_ingredients_text' => 'Polybutene, Octyldodecanol, Bis-Diglyceryl Polyacyladipate-2, Tricaprylin, Cera Microcristallina, Polyethylene, Silica Dimethyl Silylate, Butyrospermum Parkii (Shea) Butter, Sorbitan Oleate, VP/Eicosene Copolymer, VP/Hexadecene Copolymer, Silica, Ethylhexyl Palmitate, Caprylyl Glycol, Aroma/Flavor, Parfum/Fragrance.',
                'target_skin_types' => ['normal', 'dry', 'oily', 'combination', 'sensitive'],
                'target_concerns' => ['dullness', 'dehydration'],
                'finish_type' => 'glossy',
                'coverage_level' => 'sheer',
                'spf_rating' => null,
                'pao_months' => 12,
                'base_price' => 21.00,
                'discount_price' => 21.00,
                'is_featured' => true,
                'is_bestseller' => true,
                'is_active' => true,
                'rating_avg' => 4.92,
                'reviews_count' => 640,
                'thumbnail_url' => 'https://images.unsplash.com/photo-1586495777744-4413f21062fa?auto=format&fit=crop&w=800&q=80',
                'gallery_images' => [
                    'https://images.unsplash.com/photo-1586495777744-4413f21062fa?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=800&q=80',
                ],
            ]
        );

        $glossVariants = [
            ['sku' => 'FNT-GB-GLOW', 'title' => 'Fenty Glow - Universal Shimmering Rose Nude', 'shade_name' => 'Fenty Glow', 'shade_hex' => '#D68B75', 'stock' => 65],
            ['sku' => 'FNT-GB-FUSSY', 'title' => 'Fu$$y - Soft Romantic Shimmering Pink', 'shade_name' => 'Fu$$y', 'shade_hex' => '#E88C9E', 'stock' => 45],
            ['sku' => 'FNT-GB-HOTCHOC', 'title' => 'Hot Chocolit - Rich Shimmering Cocoa', 'shade_name' => 'Hot Chocolit', 'shade_hex' => '#693E2F', 'stock' => 38],
        ];

        foreach ($glossVariants as $gv) {
            $variant = ProductVariant::updateOrCreate(
                ['sku' => $gv['sku']],
                [
                    'product_id' => $fentyGlossBomb->id,
                    'title' => $gv['title'],
                    'shade_name' => $gv['shade_name'],
                    'shade_hex' => $gv['shade_hex'],
                    'depth_level' => 3,
                    'undertone' => 'neutral',
                    'volume_or_weight' => '9 ml / 0.30 fl. oz',
                    'price' => 21.00,
                    'compare_at_price' => null,
                    'stock_quantity' => $gv['stock'],
                    'low_stock_threshold' => 10,
                    'is_active' => true,
                    'image_url' => $fentyGlossBomb->thumbnail_url,
                ]
            );

            ProductBatch::firstOrCreate(
                ['batch_number' => 'LOT-FNT-LIP-' . substr($gv['sku'], -4)],
                [
                    'product_variant_id' => $variant->id,
                    'supplier_name' => 'Kendo Fragrances & Cosmetics Lab',
                    'manufactured_at' => Carbon::now()->subMonths(2)->toDateString(),
                    'expires_at' => Carbon::now()->addMonths(24)->toDateString(),
                    'initial_quantity' => $gv['stock'],
                    'current_quantity' => $gv['stock'],
                    'unit_cost' => 4.20,
                    'status' => 'active',
                    'warehouse_location' => 'Lip Bay L-01',
                ]
            );
        }

        // Product 7: Summer Fridays Lip Butter Balm (Lip Care)
        $summerFridaysBalm = Product::updateOrCreate(
            ['slug' => 'summer-fridays-lip-butter-balm'],
            [
                'brand_id' => $brands['summer-fridays']->id,
                'category_id' => $categories['lip-care']->id,
                'name' => 'Lip Butter Balm',
                'subtitle' => '100% vegan silky lip mask and daily balm with shea & murumuru seed butter',
                'description' => 'A silky, 100% vegan lip balm that conditions and soothes parched lips in seconds while delivering a touch of sheer sweet shine. Pocket-sized hydration for on-the-go or as an overnight lip mask.',
                'how_to_use' => 'Apply as needed throughout the day, whenever lips need extra hydration. Can be used under or over lipstick.',
                'full_ingredients_text' => 'Phytosteryl/Isostearyl/Cetyl/Stearyl/Behenyl Dimer Dilinoleate, Diisostearyl Malate, Hydrogenated Polyisobutene, Polybutene, Hydrogenated Poly(C6-14 Olefin), Butyrospermum Parkii (Shea) Butter, Microcrystalline Wax, Astrocaryum Murumuru Seed Butter, Synthetic Wax, Polyglyceryl-2 Triisostearate, Vanillin.',
                'target_skin_types' => ['normal', 'dry', 'sensitive', 'combination', 'oily'],
                'target_concerns' => ['dehydration', 'chapped_lips'],
                'finish_type' => 'natural',
                'coverage_level' => 'sheer',
                'spf_rating' => null,
                'pao_months' => 12,
                'base_price' => 24.00,
                'discount_price' => 24.00,
                'is_featured' => true,
                'is_bestseller' => true,
                'is_active' => true,
                'rating_avg' => 4.96,
                'reviews_count' => 890,
                'thumbnail_url' => 'https://images.unsplash.com/photo-1599305090598-fe179d501227?auto=format&fit=crop&w=800&q=80',
                'gallery_images' => [
                    'https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1586495777744-4413f21062fa?auto=format&fit=crop&w=800&q=80',
                ],
            ]
        );

        $sfVariants = [
            ['sku' => 'SF-LBB-VANILLA', 'title' => 'Vanilla - Clear Luminous Glaze', 'shade_name' => 'Vanilla', 'shade_hex' => '#F7F2E7', 'stock' => 50],
            ['sku' => 'SF-LBB-BEIGE', 'title' => 'Vanilla Beige - Sheer Warm Nude Tint', 'shade_name' => 'Vanilla Beige', 'shade_hex' => '#C79777', 'stock' => 40],
            ['sku' => 'SF-LBB-CHERRY', 'title' => 'Cherry - Juicy Sheer Red Flush', 'shade_name' => 'Cherry', 'shade_hex' => '#A62432', 'stock' => 35],
        ];

        foreach ($sfVariants as $sf) {
            $variant = ProductVariant::updateOrCreate(
                ['sku' => $sf['sku']],
                [
                    'product_id' => $summerFridaysBalm->id,
                    'title' => $sf['title'],
                    'shade_name' => $sf['shade_name'],
                    'shade_hex' => $sf['shade_hex'],
                    'depth_level' => 2,
                    'undertone' => 'neutral',
                    'volume_or_weight' => '15 g / 0.5 oz',
                    'price' => 24.00,
                    'compare_at_price' => null,
                    'stock_quantity' => $sf['stock'],
                    'low_stock_threshold' => 10,
                    'is_active' => true,
                    'image_url' => $summerFridaysBalm->thumbnail_url,
                ]
            );

            ProductBatch::firstOrCreate(
                ['batch_number' => 'LOT-SF-LBB-' . substr($sf['sku'], -4)],
                [
                    'product_variant_id' => $variant->id,
                    'supplier_name' => 'Summer Fridays Clean Formulation Labs',
                    'manufactured_at' => Carbon::now()->subMonths(1)->toDateString(),
                    'expires_at' => Carbon::now()->addMonths(24)->toDateString(),
                    'initial_quantity' => $sf['stock'],
                    'current_quantity' => $sf['stock'],
                    'unit_cost' => 4.80,
                    'status' => 'active',
                    'warehouse_location' => 'Lip Bay L-04',
                ]
            );
        }

        // Product 8: Rhode Peptide Lip Tint (Lip Care)
        $rhodeLipTint = Product::updateOrCreate(
            ['slug' => 'rhode-peptide-lip-tint'],
            [
                'brand_id' => $brands['rhode-skin']->id,
                'category_id' => $categories['lip-care']->id,
                'name' => 'Peptide Lip Tint',
                'subtitle' => 'Restorative peptide treatment with a rich tint of buildable color',
                'description' => 'A restorative peptide treatment that melts onto lips for a sheer-but-buildable wash of color. Formulated with shea butter, peptides, cupuacu, and babassu for pillowy-soft, glazed lips.',
                'how_to_use' => 'Apply day or night for soft, plump, hydrated lips. Layer with your favorite lip liner for an amplified glazed finish.',
                'full_ingredients_text' => 'Hydrogenated Polyisobutene, Diisostearyl Malate, Butyrospermum Parkii (Shea) Butter, Polybutene, Microcrystalline Wax, Synthetic Wax, Theobroma Grandiflorum Seed Butter, Orbignya Oleifera Seed Oil, Palmitoyl Tripeptide-1, Ethylhexyl Palmitate, Tribehenin, Sorbitan Isostearate, Lactic Acid.',
                'target_skin_types' => ['normal', 'dry', 'sensitive', 'combination', 'oily'],
                'target_concerns' => ['fine_lines', 'dehydration'],
                'finish_type' => 'glossy',
                'coverage_level' => 'buildable',
                'spf_rating' => null,
                'pao_months' => 12,
                'base_price' => 18.00,
                'discount_price' => 18.00,
                'is_featured' => true,
                'is_bestseller' => true,
                'is_active' => true,
                'rating_avg' => 4.90,
                'reviews_count' => 740,
                'thumbnail_url' => 'https://images.unsplash.com/photo-1616683693504-3ea7e9ad6fec?auto=format&fit=crop&w=800&q=80',
                'gallery_images' => [
                    'https://images.unsplash.com/photo-1616683693504-3ea7e9ad6fec?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1586495777744-4413f21062fa?auto=format&fit=crop&w=800&q=80',
                ],
            ]
        );

        $rhodeLipTint->ingredients()->syncWithoutDetaching([
            $ingredients['peptides']->id => ['concentration_percentage' => 2.00, 'is_key_active' => true],
        ]);

        $rhodeVariants = [
            ['sku' => 'RHD-PLT-RIBBON', 'title' => 'Ribbon - Sheer Ballet Pink Gloss', 'shade_name' => 'Ribbon', 'shade_hex' => '#F0A7B5', 'stock' => 55],
            ['sku' => 'RHD-PLT-TOAST', 'title' => 'Toast - Warm Glazed Rose Taupe', 'shade_name' => 'Toast', 'shade_hex' => '#AF796B', 'stock' => 48],
            ['sku' => 'RHD-PLT-RASP', 'title' => 'Raspberry Jelly - Crushed Berry Tint', 'shade_name' => 'Raspberry Jelly', 'shade_hex' => '#7F2841', 'stock' => 32],
        ];

        foreach ($rhodeVariants as $rv) {
            $variant = ProductVariant::updateOrCreate(
                ['sku' => $rv['sku']],
                [
                    'product_id' => $rhodeLipTint->id,
                    'title' => $rv['title'],
                    'shade_name' => $rv['shade_name'],
                    'shade_hex' => $rv['shade_hex'],
                    'depth_level' => 3,
                    'undertone' => 'neutral',
                    'volume_or_weight' => '10 ml / 0.3 fl. oz',
                    'price' => 18.00,
                    'compare_at_price' => null,
                    'stock_quantity' => $rv['stock'],
                    'low_stock_threshold' => 10,
                    'is_active' => true,
                    'image_url' => $rhodeLipTint->thumbnail_url,
                ]
            );

            ProductBatch::firstOrCreate(
                ['batch_number' => 'LOT-RHD-PLT-' . substr($rv['sku'], -4)],
                [
                    'product_variant_id' => $variant->id,
                    'supplier_name' => 'Rhode Clean Beauty Facility California',
                    'manufactured_at' => Carbon::now()->subMonths(1)->toDateString(),
                    'expires_at' => Carbon::now()->addMonths(20)->toDateString(),
                    'initial_quantity' => $rv['stock'],
                    'current_quantity' => $rv['stock'],
                    'unit_cost' => 3.60,
                    'status' => 'active',
                    'warehouse_location' => 'Lip Bay L-06',
                ]
            );
        }

        // Product 9: Charlotte Tilbury Airbrush Flawless Finish Powder (Complexion)
        $ctPowder = Product::updateOrCreate(
            ['slug' => 'charlotte-tilbury-airbrush-flawless-finish-powder'],
            [
                'brand_id' => $brands['charlotte-tilbury']->id,
                'category_id' => $categories['complexion']->id,
                'name' => 'Airbrush Flawless Finish Micro-Powder',
                'subtitle' => 'Award-winning micro-fine finishing powder with soft-focus blurring pigments',
                'description' => 'A breathable, micro-fine powder technology that gives you an immaculate complexion that acts like a luxurious, micro-fine cashmere veil atop your skin for an effortlessly flawless, poreless finish.',
                'how_to_use' => 'Using a powder brush, apply Airbrush Flawless Finish powder around the nose, T-zone, and any other oily areas that need to be locked down.',
                'full_ingredients_text' => 'Talc, Mica, Polymethyl Methacrylate, Dimethicone, Silica, Pentaerythrityl Tetraisostearate, Zinc Stearate, Zea Mays (Corn) Starch, Caprylyl Glycol, Phenoxyethanol, Hexylene Glycol, Dehydroacetic Acid, Prunus Amygdalus Dulcis (Sweet Almond) Oil.',
                'target_skin_types' => ['oily', 'combination', 'normal'],
                'target_concerns' => ['large_pores', 'excess_shine', 'texture'],
                'finish_type' => 'matte',
                'coverage_level' => 'sheer',
                'spf_rating' => null,
                'pao_months' => 24,
                'base_price' => 48.00,
                'discount_price' => 48.00,
                'is_featured' => true,
                'is_bestseller' => true,
                'is_active' => true,
                'rating_avg' => 4.88,
                'reviews_count' => 520,
                'thumbnail_url' => 'https://images.unsplash.com/photo-1631274437974-79d0f0e60d75?auto=format&fit=crop&w=800&q=80',
                'gallery_images' => [
                    'https://images.unsplash.com/photo-1512496015851-a90fb38ba796?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1599733589046-10c005739ef9?auto=format&fit=crop&w=800&q=80',
                ],
            ]
        );

        $powderShades = [
            ['sku' => 'CT-POW-1FAIR', 'title' => '1 Fair - For Fair Complexions', 'shade_name' => '1 Fair', 'shade_hex' => '#F6E4D1', 'depth_level' => 1, 'undertone' => 'neutral', 'stock' => 40],
            ['sku' => 'CT-POW-2MED', 'title' => '2 Medium - For Medium Complexions', 'shade_name' => '2 Medium', 'shade_hex' => '#E4BD98', 'depth_level' => 4, 'undertone' => 'warm', 'stock' => 45],
            ['sku' => 'CT-POW-3TAN', 'title' => '3 Tan - For Olive & Tan Complexions', 'shade_name' => '3 Tan', 'shade_hex' => '#B67F55', 'depth_level' => 7, 'undertone' => 'olive', 'stock' => 30],
            ['sku' => 'CT-POW-4DEEP', 'title' => '4 Deep - For Deep Complexions', 'shade_name' => '4 Deep', 'shade_hex' => '#66402B', 'depth_level' => 10, 'undertone' => 'cool', 'stock' => 25],
        ];

        foreach ($powderShades as $ps) {
            $variant = ProductVariant::updateOrCreate(
                ['sku' => $ps['sku']],
                [
                    'product_id' => $ctPowder->id,
                    'title' => $ps['title'],
                    'shade_name' => $ps['shade_name'],
                    'shade_hex' => $ps['shade_hex'],
                    'depth_level' => $ps['depth_level'],
                    'undertone' => $ps['undertone'],
                    'volume_or_weight' => '8 g / 0.28 oz',
                    'price' => 48.00,
                    'compare_at_price' => null,
                    'stock_quantity' => $ps['stock'],
                    'low_stock_threshold' => 10,
                    'is_active' => true,
                    'image_url' => $ctPowder->thumbnail_url,
                ]
            );

            ProductBatch::firstOrCreate(
                ['batch_number' => 'LOT-CT-POW-' . substr($ps['sku'], -4)],
                [
                    'product_variant_id' => $variant->id,
                    'supplier_name' => 'Charlotte Tilbury Beauty Labs Italy',
                    'manufactured_at' => Carbon::now()->subMonths(3)->toDateString(),
                    'expires_at' => Carbon::now()->addMonths(30)->toDateString(),
                    'initial_quantity' => $ps['stock'],
                    'current_quantity' => $ps['stock'],
                    'unit_cost' => 12.80,
                    'status' => 'active',
                    'warehouse_location' => 'Complexion Shelf C-18',
                ]
            );
        }

        // Product 10: Rare Beauty Positive Light Liquid Luminizer (Cheeks & Glow)
        $rareLuminizer = Product::updateOrCreate(
            ['slug' => 'rare-beauty-positive-light-liquid-luminizer'],
            [
                'brand_id' => $brands['rare-beauty']->id,
                'category_id' => $categories['cheeks-glow']->id,
                'name' => 'Positive Light Liquid Luminizer',
                'subtitle' => 'Second-skin liquid highlighter for a soft-focus, lit-from-within glow',
                'description' => 'A silky, second-skin liquid highlighter that creates a lasting, soft, and luminous finish. Packed with light-reflecting pearl particles and a botanical blend of lotus, gardenia, and white water lily.',
                'how_to_use' => 'Apply 1-2 dots directly over high points of cheekbones, brow bones, and cupid’s bow. Blend with fingertips or brush for a glowing sheen.',
                'full_ingredients_text' => 'Isododecane, Mica, Hydrogenated Polyisobutene, Hydrogenated Olive Oil Unsaponifiables, Hydrogenated Styrene/Isoprene Copolymer, Octyldodecanol, Dimethicone, Gardenia Florida Fruit Extract, Nelumbo Nucifera Flower Extract.',
                'target_skin_types' => ['normal', 'dry', 'combination', 'oily', 'sensitive'],
                'target_concerns' => ['dullness'],
                'finish_type' => 'dewy',
                'coverage_level' => 'sheer',
                'spf_rating' => null,
                'pao_months' => 12,
                'base_price' => 25.00,
                'discount_price' => 25.00,
                'is_featured' => true,
                'is_bestseller' => false,
                'is_active' => true,
                'rating_avg' => 4.89,
                'reviews_count' => 380,
                'thumbnail_url' => 'https://images.unsplash.com/photo-1617897903246-719242758050?auto=format&fit=crop&w=800&q=80',
                'gallery_images' => [
                    'https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=800&q=80',
                ],
            ]
        );

        $luminizerShades = [
            ['sku' => 'RB-LUM-ENLIGHT', 'title' => 'Enlighten - Champagne Soft Gold Glow', 'shade_name' => 'Enlighten', 'shade_hex' => '#F6E8CE', 'stock' => 35],
            ['sku' => 'RB-LUM-MESMER', 'title' => 'Mesmerize - Rose Bronze Radiant Glow', 'shade_name' => 'Mesmerize', 'shade_hex' => '#D99480', 'stock' => 40],
            ['sku' => 'RB-LUM-FLAUNT', 'title' => 'Flaunt - True Warm Gold Glow', 'shade_name' => 'Flaunt', 'shade_hex' => '#D9A757', 'stock' => 28],
        ];

        foreach ($luminizerShades as $ls) {
            $variant = ProductVariant::updateOrCreate(
                ['sku' => $ls['sku']],
                [
                    'product_id' => $rareLuminizer->id,
                    'title' => $ls['title'],
                    'shade_name' => $ls['shade_name'],
                    'shade_hex' => $ls['shade_hex'],
                    'depth_level' => 3,
                    'undertone' => 'warm',
                    'volume_or_weight' => '15 ml / 0.5 fl. oz',
                    'price' => 25.00,
                    'compare_at_price' => null,
                    'stock_quantity' => $ls['stock'],
                    'low_stock_threshold' => 10,
                    'is_active' => true,
                    'image_url' => $rareLuminizer->thumbnail_url,
                ]
            );

            ProductBatch::firstOrCreate(
                ['batch_number' => 'LOT-RB-LUM-' . substr($ls['sku'], -4)],
                [
                    'product_variant_id' => $variant->id,
                    'supplier_name' => 'Rare Beauty Labs California',
                    'manufactured_at' => Carbon::now()->subMonths(2)->toDateString(),
                    'expires_at' => Carbon::now()->addMonths(22)->toDateString(),
                    'initial_quantity' => $ls['stock'],
                    'current_quantity' => $ls['stock'],
                    'unit_cost' => 6.20,
                    'status' => 'active',
                    'warehouse_location' => 'Cheeks Shelf H-12',
                ]
            );
        }

        // Product 11: La Roche-Posay Anthelios UVMune 400 Invisible Fluid SPF 50+ (Sun Protection)
        $lrpAnthelios = Product::updateOrCreate(
            ['slug' => 'la-roche-posay-anthelios-uvmune-400-spf50'],
            [
                'brand_id' => $brands['la-roche-posay']->id,
                'category_id' => $categories['sun-protection']->id,
                'name' => 'Anthelios UVMune 400 Invisible Fluid SPF 50+',
                'subtitle' => 'Ultimate broad spectrum protection against ultra-long UVA rays with Mexoryl 400',
                'description' => 'The highest UVA protection in Anthelios history. Protects against ultra-long UVA rays with revolutionary Mexoryl 400 filter. Ultra-resistant to water and sweat, invisible finish with zero white cast.',
                'how_to_use' => 'Shake vigorously before use. Apply liberally to face and neck 15 minutes before sun exposure. Reapply frequently after swimming, sweating, or toweling.',
                'full_ingredients_text' => 'Aqua / Water, Alcohol Denat., Triethyl Citrate, Diisopropyl Sebacate, Silica, Ethylhexyl Salicylate, Bis-Ethylhexyloxyphenol Methoxyphenyl Triazine, Ethylhexyl Triazone, Butyl Methoxydibenzoylmethane, Glycerin, Methoxypropylamino Cyclohexenylidene Ethoxyethylcyanoacetate (Mexoryl 400), Tocopherol.',
                'target_skin_types' => ['normal', 'combination', 'oily', 'sensitive'],
                'target_concerns' => ['sun_damage', 'hyperpigmentation', 'anti_aging'],
                'finish_type' => 'natural',
                'coverage_level' => 'sheer',
                'spf_rating' => 50,
                'pao_months' => 12,
                'base_price' => 24.99,
                'discount_price' => 22.50,
                'is_featured' => true,
                'is_bestseller' => true,
                'is_active' => true,
                'rating_avg' => 4.94,
                'reviews_count' => 670,
                'thumbnail_url' => 'https://images.unsplash.com/photo-1556228722-d0b5d034b074?auto=format&fit=crop&w=800&q=80',
                'gallery_images' => [
                    'https://images.unsplash.com/photo-1556228722-d0b5d034b074?auto=format&fit=crop&w=800&q=80',
                ],
            ]
        );

        $lrpVariant = ProductVariant::updateOrCreate(
            ['sku' => 'LRP-ANTH-50ML'],
            [
                'product_id' => $lrpAnthelios->id,
                'title' => 'Invisible Shaka Fluid - 50ml / 1.7 fl. oz',
                'volume_or_weight' => '50 ml / 1.7 fl. oz',
                'price' => 22.50,
                'compare_at_price' => 24.99,
                'stock_quantity' => 85,
                'low_stock_threshold' => 15,
                'is_active' => true,
                'image_url' => $lrpAnthelios->thumbnail_url,
            ]
        );

        ProductBatch::firstOrCreate(
            ['batch_number' => 'LOT-LRP-UVM-5011'],
            [
                'product_variant_id' => $lrpVariant->id,
                'supplier_name' => 'La Roche-Posay Dermatological Laboratory France',
                'manufactured_at' => Carbon::now()->subMonths(1)->toDateString(),
                'expires_at' => Carbon::now()->addMonths(24)->toDateString(),
                'initial_quantity' => 85,
                'current_quantity' => 85,
                'unit_cost' => 7.10,
                'status' => 'active',
                'warehouse_location' => 'Sun Care Bay S-01',
            ]
        );

        // Product 12: COSRX Advanced Snail 96 Mucin Power Essence (Skincare)
        $cosrxSnail = Product::updateOrCreate(
            ['slug' => 'cosrx-advanced-snail-96-mucin-power-essence'],
            [
                'brand_id' => $brands['cosrx']->id,
                'category_id' => $categories['skincare']->id,
                'name' => 'Advanced Snail 96 Mucin Power Essence',
                'subtitle' => '96.3% snail secretion filtrate for deep cellular repair and the iconic glass-skin radiance',
                'description' => 'Enriched with 96.3% skin-boosting snail secretion filtrate, this lightweight essence sinks quickly into the skin to give skin a natural glow from the inside. Created from nutritious, low-stimulation filtered snail mucin to keep your skin moisturized and illuminated all day.',
                'how_to_use' => 'After cleansing and toning, apply a small amount on your entire face. Gently pat using fingertips to aid absorption, and then go forth with your moisturizers.',
                'full_ingredients_text' => 'Snail Secretion Filtrate, Betaine, Butylene Glycol, 1,2-Hexanediol, Sodium Polyacrylate, Phenoxyethanol, Sodium Hyaluronate, Allantoin, Carbomer, Panthenol, Arginine.',
                'target_skin_types' => ['dry', 'normal', 'combination', 'sensitive'],
                'target_concerns' => ['barrier_repair', 'dullness', 'acne_scars', 'dehydration'],
                'finish_type' => 'dewy',
                'coverage_level' => null,
                'spf_rating' => null,
                'pao_months' => 12,
                'base_price' => 25.00,
                'discount_price' => 19.00,
                'is_featured' => true,
                'is_bestseller' => true,
                'is_active' => true,
                'rating_avg' => 4.95,
                'reviews_count' => 1250,
                'thumbnail_url' => 'https://images.unsplash.com/photo-1608248597359-54876e9389f4?auto=format&fit=crop&w=800&q=80',
                'gallery_images' => [
                    'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=800&q=80',
                ],
            ]
        );

        $cosrxSnail->ingredients()->syncWithoutDetaching([
            $ingredients['snail-secretion']->id => ['concentration_percentage' => 96.30, 'is_key_active' => true],
            $ingredients['hyaluronic-acid']->id => ['concentration_percentage' => 1.00, 'is_key_active' => false],
        ]);

        $cosrxVariant = ProductVariant::updateOrCreate(
            ['sku' => 'CSX-SNAIL-100ML'],
            [
                'product_id' => $cosrxSnail->id,
                'title' => 'Pump Bottle - 100ml / 3.38 fl. oz',
                'volume_or_weight' => '100 ml / 3.38 fl. oz',
                'price' => 19.00,
                'compare_at_price' => 25.00,
                'stock_quantity' => 110,
                'low_stock_threshold' => 15,
                'is_active' => true,
                'image_url' => $cosrxSnail->thumbnail_url,
            ]
        );

        ProductBatch::firstOrCreate(
            ['batch_number' => 'LOT-CSX-96-2025'],
            [
                'product_variant_id' => $cosrxVariant->id,
                'supplier_name' => 'COSRX Clean Formulation Facility Seoul',
                'manufactured_at' => Carbon::now()->subMonths(1)->toDateString(),
                'expires_at' => Carbon::now()->addMonths(28)->toDateString(),
                'initial_quantity' => 110,
                'current_quantity' => 110,
                'unit_cost' => 5.20,
                'status' => 'active',
                'warehouse_location' => 'K-Beauty Bay K-02',
            ]
        );

        // Product 13: CeraVe Hydrating Facial Cleanser (Skincare)
        $ceraveCleanser = Product::updateOrCreate(
            ['slug' => 'cerave-hydrating-facial-cleanser'],
            [
                'brand_id' => $brands['cerave']->id,
                'category_id' => $categories['skincare']->id,
                'name' => 'Hydrating Facial Cleanser with Ceramides',
                'subtitle' => 'Dermatologist-developed non-foaming lotion cleanser with 3 essential ceramides',
                'description' => 'A gentle, non-foaming face wash that cleanses, hydrates, and helps restore the protective skin barrier. Formulated with three essential ceramides and hyaluronic acid to remove dirt and oil without disrupting the skin’s natural protective barrier.',
                'how_to_use' => 'Wet skin with lukewarm water. Massage cleanser into skin in a gentle, circular motion. Rinse thoroughly.',
                'full_ingredients_text' => 'Aqua / Water, Glycerin, Cetearyl Alcohol, Peg-40 Stearate, Stearyl Alcohol, Potassium Phosphate, Ceramide NP, Ceramide AP, Ceramide EOP, Carbomer, Glyceryl Stearate, Behentrimonium Methosulfate, Sodium Lauroyl Lactylate, Sodium Hyaluronate, Cholesterol, Phenoxyethanol, Disodium EDTA, Dipotassium Phosphate, Tocopherol, Phytosphingosine, Xanthan Gum.',
                'target_skin_types' => ['normal', 'dry', 'sensitive'],
                'target_concerns' => ['barrier_repair', 'dehydration', 'redness'],
                'finish_type' => 'natural',
                'coverage_level' => null,
                'spf_rating' => null,
                'pao_months' => 12,
                'base_price' => 18.99,
                'discount_price' => 16.99,
                'is_featured' => false,
                'is_bestseller' => true,
                'is_active' => true,
                'rating_avg' => 4.86,
                'reviews_count' => 1120,
                'thumbnail_url' => 'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?auto=format&fit=crop&w=800&q=80',
                'gallery_images' => [
                    'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=800&q=80',
                ],
            ]
        );

        $ceraveCleanser->ingredients()->syncWithoutDetaching([
            $ingredients['ceramides']->id => ['concentration_percentage' => 3.00, 'is_key_active' => true],
            $ingredients['hyaluronic-acid']->id => ['concentration_percentage' => 1.00, 'is_key_active' => false],
        ]);

        $ceraveCleanserVariant = ProductVariant::updateOrCreate(
            ['sku' => 'CRV-CLN-473ML'],
            [
                'product_id' => $ceraveCleanser->id,
                'title' => 'Value Size Pump - 473ml / 16 fl. oz',
                'volume_or_weight' => '473 ml / 16 fl. oz',
                'price' => 16.99,
                'compare_at_price' => 18.99,
                'stock_quantity' => 95,
                'low_stock_threshold' => 15,
                'is_active' => true,
                'image_url' => $ceraveCleanser->thumbnail_url,
            ]
        );

        ProductBatch::firstOrCreate(
            ['batch_number' => 'LOT-CRV-CLN-2025'],
            [
                'product_variant_id' => $ceraveCleanserVariant->id,
                'supplier_name' => 'L\'Oréal Dermatological Beauty Plant',
                'manufactured_at' => Carbon::now()->subMonths(2)->toDateString(),
                'expires_at' => Carbon::now()->addMonths(30)->toDateString(),
                'initial_quantity' => 95,
                'current_quantity' => 95,
                'unit_cost' => 4.10,
                'status' => 'active',
                'warehouse_location' => 'Cleansers Shelf CL-03',
            ]
        );

        // ─────────────────────────────────────────────────────
        // ADDITIONAL PRODUCTS (14-23) — richening every category
        // ─────────────────────────────────────────────────────

        // Product 14: Paula's Choice BHA 2% Skin Perfecting Gel (Skincare)
        $paulasBHA2Gel = Product::updateOrCreate(
            ['slug' => 'paulas-choice-2-bha-skin-perfecting-gel'],
            [
                'brand_id'             => $brands['paulas-choice']->id,
                'category_id'          => $categories['skincare']->id,
                'name'                 => '2% BHA Skin Perfecting Gel Exfoliant',
                'subtitle'             => 'Oil-free liquid BHA exfoliant with willow bark extract',
                'description'          => 'An oil-free leave-on liquid BHA exfoliant that gently but thoroughly unclogs and minimizes enlarged pores, removes excess oil, and improves skin texture. Formulated with 2% salicylic acid, green tea extract, and antioxidants.',
                'how_to_use'           => 'After cleansing and toning, apply a small amount to face with a cotton pad or fingertips. Leave on — do not rinse. Start once daily, then morning and/or evening. Avoid eye area.',
                'full_ingredients_text'=> 'Aqua, Methylpropanediol, Salicylic Acid, Sodium Xylenesulfonate, Camellia Oleifera (Green Tea) Leaf Extract, Panthenol, Bisabolol, Glycerin, Sodium Hydroxide, Tetrasodium EDTA, Methylisothiazolinone.',
                'target_skin_types'    => ['oily', 'combination', 'normal'],
                'target_concerns'      => ['blackheads', 'large_pores', 'excess_oil', 'acne'],
                'finish_type'          => 'natural',
                'coverage_level'       => null,
                'spf_rating'           => null,
                'pao_months'           => 12,
                'base_price'           => 35.00,
                'discount_price'       => 30.00,
                'is_featured'          => true,
                'is_bestseller'        => true,
                'is_active'            => true,
                'rating_avg'           => 4.91,
                'reviews_count'        => 890,
                'thumbnail_url'        => 'https://images.unsplash.com/photo-1608248597359-54876e9389f4?auto=format&fit=crop&w=800&q=80',
                'gallery_images'       => [
                    'https://images.unsplash.com/photo-1608248597359-54876e9389f4?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?auto=format&fit=crop&w=800&q=80',
                ],
            ]
        );
        $paulasBHA2Gel->ingredients()->syncWithoutDetaching([
            $ingredients['salicylic-acid']->id => ['concentration_percentage' => 2.00, 'is_key_active' => true],
        ]);
        $paulaBHAGelVariant = ProductVariant::updateOrCreate(
            ['sku' => 'PC-BHA2-GEL-110ML'],
            [
                'product_id'          => $paulasBHA2Gel->id,
                'title'               => 'Oil-Control Gel - 110ml Pump',
                'volume_or_weight'    => '110 ml / 3.7 fl. oz',
                'price'               => 30.00,
                'compare_at_price'    => 35.00,
                'stock_quantity'      => 70,
                'low_stock_threshold' => 12,
                'is_active'           => true,
                'image_url'           => $paulasBHA2Gel->thumbnail_url,
            ]
        );
        ProductBatch::firstOrCreate(
            ['batch_number' => 'LOT-PC-BHA2-GEL-2025'],
            [
                'product_variant_id' => $paulaBHAGelVariant->id,
                'supplier_name'      => "Paula's Choice Research Lab Seattle",
                'manufactured_at'    => Carbon::now()->subMonths(2)->toDateString(),
                'expires_at'         => Carbon::now()->addMonths(24)->toDateString(),
                'initial_quantity'   => 70,
                'current_quantity'   => 70,
                'unit_cost'          => 8.50,
                'status'             => 'active',
                'warehouse_location' => 'Skincare Shelf SK-07',
            ]
        );

        // Product 15: Rhode Skin Peptide Glazing Fluid (Skincare)
        $rhodeGlazingFluid = Product::updateOrCreate(
            ['slug' => 'rhode-skin-peptide-glazing-fluid'],
            [
                'brand_id'             => $brands['rhode-skin']->id,
                'category_id'          => $categories['skincare']->id,
                'name'                 => 'Peptide Glazing Fluid',
                'subtitle'             => 'Weightless peptide serum for the iconic glazed donut skin finish',
                'description'          => 'A weightless, fast-absorbing peptide serum that instantly hydrates and locks in a glassy, glazed finish. Packed with niacinamide, peptides, and marshmallow root to visibly reduce redness and leave skin looking pillowy and dewy.',
                'how_to_use'           => 'Apply 2-3 drops onto clean skin morning and night before moisturizer. Gently press into skin with palms for an instant glow.',
                'full_ingredients_text'=> 'Aqua, Propanediol, Glycerin, Niacinamide, Palmitoyl Tripeptide-1, Palmitoyl Tetrapeptide-7, Althaea Officinalis Root Extract, Sodium Hyaluronate, Panthenol, Betaine, Sodium PCA, 1,2-Hexanediol, Phenoxyethanol.',
                'target_skin_types'    => ['dry', 'normal', 'combination', 'sensitive'],
                'target_concerns'      => ['dehydration', 'dullness', 'fine_lines', 'redness'],
                'finish_type'          => 'dewy',
                'coverage_level'       => null,
                'spf_rating'           => null,
                'pao_months'           => 12,
                'base_price'           => 29.00,
                'discount_price'       => 29.00,
                'is_featured'          => true,
                'is_bestseller'        => true,
                'is_active'            => true,
                'rating_avg'           => 4.93,
                'reviews_count'        => 620,
                'thumbnail_url'        => 'https://images.unsplash.com/photo-1607748851687-ba9a10438621?auto=format&fit=crop&w=800&q=80',
                'gallery_images'       => [
                    'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=800&q=80',
                ],
            ]
        );
        $rhodeGlazingFluid->ingredients()->syncWithoutDetaching([
            $ingredients['niacinamide']->id => ['concentration_percentage' => 5.00, 'is_key_active' => true],
            $ingredients['peptides']->id    => ['concentration_percentage' => 2.00, 'is_key_active' => true],
        ]);
        $rhodeGlazingVariant = ProductVariant::updateOrCreate(
            ['sku' => 'RHD-GF-30ML'],
            [
                'product_id'          => $rhodeGlazingFluid->id,
                'title'               => 'Glazing Fluid - 30ml Dropper',
                'volume_or_weight'    => '30 ml / 1.0 fl. oz',
                'price'               => 29.00,
                'compare_at_price'    => null,
                'stock_quantity'      => 80,
                'low_stock_threshold' => 12,
                'is_active'           => true,
                'image_url'           => $rhodeGlazingFluid->thumbnail_url,
            ]
        );
        ProductBatch::firstOrCreate(
            ['batch_number' => 'LOT-RHD-GF-2025'],
            [
                'product_variant_id' => $rhodeGlazingVariant->id,
                'supplier_name'      => 'Rhode Clean Beauty Facility California',
                'manufactured_at'    => Carbon::now()->subMonths(1)->toDateString(),
                'expires_at'         => Carbon::now()->addMonths(24)->toDateString(),
                'initial_quantity'   => 80,
                'current_quantity'   => 80,
                'unit_cost'          => 6.80,
                'status'             => 'active',
                'warehouse_location' => 'Skincare Shelf SK-09',
            ]
        );

        // Product 16: Fenty Beauty Eaze Drop Blurring Skin Tint (Complexion)
        $fentySkintint = Product::updateOrCreate(
            ['slug' => 'fenty-beauty-eaze-drop-blurring-skin-tint'],
            [
                'brand_id'             => $brands['fenty-beauty']->id,
                'category_id'          => $categories['complexion']->id,
                'name'                 => 'Eaze Drop Blurring Skin Tint',
                'subtitle'             => 'Weightless "your skin but better" tint in 25 inclusive shades',
                'description'          => 'A buildable, weightless skin tint that blurs, evens, and smooths skin with a satin-matte finish. Infused with niacinamide and a natural plum complex, it minimizes pores and improves skin over time. 25 shades across every undertone.',
                'how_to_use'           => 'Squeeze or pour a small amount onto fingertips, a sponge, or foundation brush. Blend outward from center of face. Build for more coverage.',
                'full_ingredients_text'=> 'Aqua / Water, Hydrogenated Poly(C6-14 Olefin), Niacinamide, Mica, Prunus Domestica Fruit Extract, Polyglyceryl-3 Diisostearate, Silica, Dimethicone, Magnesium Sulfate, Phenoxyethanol, Triethoxycaprylylsilane.',
                'target_skin_types'    => ['normal', 'combination', 'dry', 'oily'],
                'target_concerns'      => ['large_pores', 'uneven_tone', 'redness'],
                'finish_type'          => 'natural',
                'coverage_level'       => 'buildable',
                'spf_rating'           => null,
                'pao_months'           => 12,
                'base_price'           => 30.00,
                'discount_price'       => 30.00,
                'is_featured'          => true,
                'is_bestseller'        => true,
                'is_active'            => true,
                'rating_avg'           => 4.85,
                'reviews_count'        => 960,
                'thumbnail_url'        => 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=800&q=80',
                'gallery_images'       => [
                    'https://images.unsplash.com/photo-1512496015851-a90fb38ba796?auto=format&fit=crop&w=800&q=80',
                ],
            ]
        );
        $skintintShades = [
            ['sku' => 'FNT-EZD-1N',  'title' => '1N (Fair, Neutral Undertone)',   'shade_name' => '1N',  'shade_hex' => '#F5E6D8', 'depth_level' => 1,  'undertone' => 'neutral', 'stock' => 55],
            ['sku' => 'FNT-EZD-5W',  'title' => '5W (Light, Warm Undertone)',     'shade_name' => '5W',  'shade_hex' => '#E8C9A0', 'depth_level' => 3,  'undertone' => 'warm',    'stock' => 60],
            ['sku' => 'FNT-EZD-12N', 'title' => '12N (Medium, Neutral Undertone)','shade_name' => '12N', 'shade_hex' => '#C49272', 'depth_level' => 5,  'undertone' => 'neutral', 'stock' => 40],
            ['sku' => 'FNT-EZD-22C', 'title' => '22C (Tan, Cool Undertone)',      'shade_name' => '22C', 'shade_hex' => '#8A5A3C', 'depth_level' => 8,  'undertone' => 'cool',    'stock' => 35],
            ['sku' => 'FNT-EZD-25D', 'title' => '25D (Deep, Warm Undertone)',     'shade_name' => '25D', 'shade_hex' => '#4C2714', 'depth_level' => 10, 'undertone' => 'warm',    'stock' => 30],
        ];
        foreach ($skintintShades as $st) {
            $stVariant = ProductVariant::updateOrCreate(
                ['sku' => $st['sku']],
                [
                    'product_id'          => $fentySkintint->id,
                    'title'               => $st['title'],
                    'shade_name'          => $st['shade_name'],
                    'shade_hex'           => $st['shade_hex'],
                    'depth_level'         => $st['depth_level'],
                    'undertone'           => $st['undertone'],
                    'volume_or_weight'    => '32 ml / 1.08 fl. oz',
                    'price'               => 30.00,
                    'compare_at_price'    => null,
                    'stock_quantity'      => $st['stock'],
                    'low_stock_threshold' => 10,
                    'is_active'           => true,
                    'image_url'           => $fentySkintint->thumbnail_url,
                ]
            );
            ProductBatch::firstOrCreate(
                ['batch_number' => 'LOT-FNT-EZD-' . substr($st['sku'], -4)],
                [
                    'product_variant_id' => $stVariant->id,
                    'supplier_name'      => 'Kendo Fragrances & Cosmetics Lab',
                    'manufactured_at'    => Carbon::now()->subMonths(2)->toDateString(),
                    'expires_at'         => Carbon::now()->addMonths(20)->toDateString(),
                    'initial_quantity'   => $st['stock'],
                    'current_quantity'   => $st['stock'],
                    'unit_cost'          => 7.50,
                    'status'             => 'active',
                    'warehouse_location' => 'Complexion Bay CP-04',
                ]
            );
        }

        // Product 17: Charlotte Tilbury Hollywood Flawless Filter (Complexion)
        $ctHollywood = Product::updateOrCreate(
            ['slug' => 'charlotte-tilbury-hollywood-flawless-filter'],
            [
                'brand_id'             => $brands['charlotte-tilbury']->id,
                'category_id'          => $categories['complexion']->id,
                'name'                 => 'Hollywood Flawless Filter',
                'subtitle'             => 'Award-winning lit-from-within complexion booster and primer',
                'description'          => 'A multi-tasking complexion booster loved by makeup artists. Wear alone for a naturally flawless filter or mix into foundation for buildable coverage. Packed with light-reflecting photon particles that diffuse imperfections.',
                'how_to_use'           => 'Apply alone as a primer/highlighter using fingertips, blend from center outward. Or mix 1-2 drops into your foundation for a lit-from-within finish.',
                'full_ingredients_text'=> 'Hydrogenated Polyisobutene, Mica, Polybutene, Diisostearyl Malate, Hydrogenated Poly(C6-14 Olefin), Trimethylsiloxysilicate, Isododecane, Microcrystalline Wax, Ethylene/Propylene/Styrene Copolymer, Caprylyl Glycol, Phenoxyethanol.',
                'target_skin_types'    => ['normal', 'dry', 'combination', 'oily', 'sensitive'],
                'target_concerns'      => ['dullness', 'uneven_tone', 'texture'],
                'finish_type'          => 'dewy',
                'coverage_level'       => 'sheer',
                'spf_rating'           => null,
                'pao_months'           => 24,
                'base_price'           => 49.00,
                'discount_price'       => 49.00,
                'is_featured'          => true,
                'is_bestseller'        => true,
                'is_active'            => true,
                'rating_avg'           => 4.92,
                'reviews_count'        => 1140,
                'thumbnail_url'        => 'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&w=800&q=80',
                'gallery_images'       => [
                    'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&w=800&q=80',
                ],
            ]
        );
        $ctHfShades = [
            ['sku' => 'CT-HFF-1FAIR', 'title' => '1 Fair - Porcelain Luminous Filter',  'shade_name' => '1 Fair',   'shade_hex' => '#F5E1CE', 'depth_level' => 1,  'undertone' => 'neutral', 'stock' => 45],
            ['sku' => 'CT-HFF-3MED',  'title' => '3 Medium - Golden Warm Filter',       'shade_name' => '3 Medium', 'shade_hex' => '#D9A97B', 'depth_level' => 5,  'undertone' => 'warm',    'stock' => 50],
            ['sku' => 'CT-HFF-6DEEP', 'title' => '6 Deep - Bronze Copper Rich Filter',  'shade_name' => '6 Deep',   'shade_hex' => '#7A4525', 'depth_level' => 9,  'undertone' => 'warm',    'stock' => 30],
        ];
        foreach ($ctHfShades as $hf) {
            $hfVariant = ProductVariant::updateOrCreate(
                ['sku' => $hf['sku']],
                [
                    'product_id'          => $ctHollywood->id,
                    'title'               => $hf['title'],
                    'shade_name'          => $hf['shade_name'],
                    'shade_hex'           => $hf['shade_hex'],
                    'depth_level'         => $hf['depth_level'],
                    'undertone'           => $hf['undertone'],
                    'volume_or_weight'    => '30 ml / 1.0 fl. oz',
                    'price'               => 49.00,
                    'compare_at_price'    => null,
                    'stock_quantity'      => $hf['stock'],
                    'low_stock_threshold' => 10,
                    'is_active'           => true,
                    'image_url'           => $ctHollywood->thumbnail_url,
                ]
            );
            ProductBatch::firstOrCreate(
                ['batch_number' => 'LOT-CT-HFF-' . substr($hf['sku'], -4)],
                [
                    'product_variant_id' => $hfVariant->id,
                    'supplier_name'      => 'Charlotte Tilbury Luxury Formulation Lab UK',
                    'manufactured_at'    => Carbon::now()->subMonths(2)->toDateString(),
                    'expires_at'         => Carbon::now()->addMonths(36)->toDateString(),
                    'initial_quantity'   => $hf['stock'],
                    'current_quantity'   => $hf['stock'],
                    'unit_cost'          => 12.00,
                    'status'             => 'active',
                    'warehouse_location' => 'Complexion Bay CP-06',
                ]
            );
        }

        // Product 18: Charlotte Tilbury Pillow Talk Blusher (Cheeks & Glow)
        $ctBlush = Product::updateOrCreate(
            ['slug' => 'charlotte-tilbury-matte-beauty-blush-wand'],
            [
                'brand_id'             => $brands['charlotte-tilbury']->id,
                'category_id'          => $categories['cheeks-glow']->id,
                'name'                 => 'Matte Beauty Blush Wand',
                'subtitle'             => 'Cushiony matte gel blush for a soft-focus, your-skin-but-better flush',
                'description'          => 'A universally flattering gel-blush wand with a cushion-matte finish. The innovative micro-fine pigment formula glides on like silk, blends instantly, and builds beautifully for a long-lasting flush that looks like a natural healthy glow.',
                'how_to_use'           => 'Glide the wand directly onto cheekbones. Use fingertips or a brush to blend in circular motions for a seamless finish. Build layers for more intensity.',
                'full_ingredients_text'=> 'Hydrogenated Poly(C6-14 Olefin), Hydrogenated Polyisobutene, Polybutene, Mica, Trimethylsiloxysilicate, Isododecane, Caprylyl Glycol, Ethylhexyl Palmitate, Phenoxyethanol.',
                'target_skin_types'    => ['normal', 'dry', 'combination', 'oily', 'sensitive'],
                'target_concerns'      => ['dullness', 'uneven_tone'],
                'finish_type'          => 'matte',
                'coverage_level'       => 'buildable',
                'spf_rating'           => null,
                'pao_months'           => 24,
                'base_price'           => 38.00,
                'discount_price'       => 38.00,
                'is_featured'          => true,
                'is_bestseller'        => false,
                'is_active'            => true,
                'rating_avg'           => 4.87,
                'reviews_count'        => 420,
                'thumbnail_url'        => 'https://images.unsplash.com/photo-1583241475880-083f84372725?auto=format&fit=crop&w=800&q=80',
                'gallery_images'       => [
                    'https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=800&q=80',
                ],
            ]
        );
        $ctBlushShades = [
            ['sku' => 'CT-BBW-PLOVE', 'title' => 'Pillow Talk Loves - Peachy Pink Glow',  'shade_name' => 'Pillow Talk Loves',  'shade_hex' => '#E8A3A1', 'stock' => 40],
            ['sku' => 'CT-BBW-CORAL', 'title' => 'Coral Bliss - Sun-Kissed Coral',         'shade_name' => 'Coral Bliss',        'shade_hex' => '#E07B5E', 'stock' => 35],
            ['sku' => 'CT-BBW-BERRY', 'title' => 'Pillow Talk Berry - Romantic Berry Tint','shade_name' => 'Pillow Talk Berry', 'shade_hex' => '#B55B7A', 'stock' => 30],
        ];
        foreach ($ctBlushShades as $cb) {
            $cbVariant = ProductVariant::updateOrCreate(
                ['sku' => $cb['sku']],
                [
                    'product_id'          => $ctBlush->id,
                    'title'               => $cb['title'],
                    'shade_name'          => $cb['shade_name'],
                    'shade_hex'           => $cb['shade_hex'],
                    'depth_level'         => 3,
                    'undertone'           => 'neutral',
                    'volume_or_weight'    => '6.5 ml / 0.22 fl. oz',
                    'price'               => 38.00,
                    'compare_at_price'    => null,
                    'stock_quantity'      => $cb['stock'],
                    'low_stock_threshold' => 10,
                    'is_active'           => true,
                    'image_url'           => $ctBlush->thumbnail_url,
                ]
            );
            ProductBatch::firstOrCreate(
                ['batch_number' => 'LOT-CT-BBW-' . substr($cb['sku'], -4)],
                [
                    'product_variant_id' => $cbVariant->id,
                    'supplier_name'      => 'Charlotte Tilbury Luxury Formulation Lab UK',
                    'manufactured_at'    => Carbon::now()->subMonths(1)->toDateString(),
                    'expires_at'         => Carbon::now()->addMonths(36)->toDateString(),
                    'initial_quantity'   => $cb['stock'],
                    'current_quantity'   => $cb['stock'],
                    'unit_cost'          => 9.50,
                    'status'             => 'active',
                    'warehouse_location' => 'Cheeks Shelf H-08',
                ]
            );
        }

        // Product 19: EltaMD UV Clear SPF 46 (Sun Protection)
        $eltaMDSpf = Product::updateOrCreate(
            ['slug' => 'elta-md-uv-clear-broad-spectrum-spf-46'],
            [
                'brand_id'             => $brands['la-roche-posay']->id, // Using LRP as proxy brand
                'category_id'          => $categories['sun-protection']->id,
                'name'                 => 'UV Clear Broad-Spectrum SPF 46',
                'subtitle'             => 'Award-winning, oil-free SPF for acne-prone, sensitive and post-procedure skin',
                'description'          => 'A lightweight, oil-free SPF formulated with niacinamide (Vitamin B3), lactic acid, and hyaluronic acid. Calms and protects sensitive skin types prone to acne, rosacea, and discoloration. Zero white cast, zero clogged pores.',
                'how_to_use'           => 'Apply liberally to face 15 minutes before sun exposure. Use as the last step of skincare, before makeup. Reapply every 2 hours.',
                'full_ingredients_text'=> 'Active: Zinc Oxide 9%, Octinoxate 7.5%. Inactive: Aqua, Niacinamide, Lactic Acid, Sodium Hyaluronate, Tocopheryl Acetate, Hyaluronic Acid, Glycerin, Dimethicone, Cyclopentasiloxane, Phenoxyethanol.',
                'target_skin_types'    => ['oily', 'combination', 'sensitive', 'acne-prone'],
                'target_concerns'      => ['sun_damage', 'acne', 'redness', 'hyperpigmentation'],
                'finish_type'          => 'natural',
                'coverage_level'       => 'sheer',
                'spf_rating'           => 46,
                'pao_months'           => 12,
                'base_price'           => 37.00,
                'discount_price'       => 37.00,
                'is_featured'          => true,
                'is_bestseller'        => true,
                'is_active'            => true,
                'rating_avg'           => 4.96,
                'reviews_count'        => 1820,
                'thumbnail_url'        => 'https://images.unsplash.com/photo-1576426863848-c21f53c60b19?auto=format&fit=crop&w=800&q=80',
                'gallery_images'       => [
                    'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&w=800&q=80',
                ],
            ]
        );
        $eltaMDVariant = ProductVariant::updateOrCreate(
            ['sku' => 'ELT-UVC-48G'],
            [
                'product_id'          => $eltaMDSpf->id,
                'title'               => 'UV Clear Tinted SPF 46 - 48g / 1.7 oz',
                'volume_or_weight'    => '48 g / 1.7 oz',
                'price'               => 37.00,
                'compare_at_price'    => null,
                'stock_quantity'      => 90,
                'low_stock_threshold' => 15,
                'is_active'           => true,
                'image_url'           => $eltaMDSpf->thumbnail_url,
            ]
        );
        ProductBatch::firstOrCreate(
            ['batch_number' => 'LOT-ELT-UVC-2025'],
            [
                'product_variant_id' => $eltaMDVariant->id,
                'supplier_name'      => 'EltaMD Pharmaceutical Grade Lab USA',
                'manufactured_at'    => Carbon::now()->subMonths(1)->toDateString(),
                'expires_at'         => Carbon::now()->addMonths(24)->toDateString(),
                'initial_quantity'   => 90,
                'current_quantity'   => 90,
                'unit_cost'          => 9.20,
                'status'             => 'active',
                'warehouse_location' => 'Sun Care Bay S-03',
            ]
        );

        // Product 20: COSRX Sun Cream SPF 50+ PA++++ (Sun Protection)
        $cosrxSPF = Product::updateOrCreate(
            ['slug' => 'cosrx-aloe-soothing-sun-cream-spf50'],
            [
                'brand_id'             => $brands['cosrx']->id,
                'category_id'          => $categories['sun-protection']->id,
                'name'                 => 'Aloe Soothing Sun Cream SPF 50+ PA++++',
                'subtitle'             => 'Lightweight Korean suncream with soothing aloe vera extract and zero white cast',
                'description'          => 'A refreshing, lightweight Korean sun cream with 68.7% aloe vera extract for all-day UV protection with deep soothing hydration. Filters both UVA and UVB with PA++++ rating. Suitable for sensitive skin. Zero white cast on all skin tones.',
                'how_to_use'           => 'Apply the last step in AM skincare, before makeup. Reapply every 2 hours. Shake well before use.',
                'full_ingredients_text'=> 'Aloe Barbadensis Leaf Juice, Homosalate, Bis-Ethylhexyloxyphenol Methoxyphenyl Triazine, Ethylhexyl Salicylate, Ethylhexyl Triazone, Diethylhexyl Butamido Triazone, Zinc Oxide, Glycerin, Niacinamide, Panthenol.',
                'target_skin_types'    => ['dry', 'normal', 'combination', 'sensitive'],
                'target_concerns'      => ['sun_damage', 'redness', 'dehydration'],
                'finish_type'          => 'natural',
                'coverage_level'       => 'sheer',
                'spf_rating'           => 50,
                'pao_months'           => 12,
                'base_price'           => 20.00,
                'discount_price'       => 16.00,
                'is_featured'          => true,
                'is_bestseller'        => true,
                'is_active'            => true,
                'rating_avg'           => 4.88,
                'reviews_count'        => 980,
                'thumbnail_url'        => 'https://images.unsplash.com/photo-1556228453-efd6c1ff04f6?auto=format&fit=crop&w=800&q=80',
                'gallery_images'       => [
                    'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=800&q=80',
                ],
            ]
        );
        $cosrxSPFVariant = ProductVariant::updateOrCreate(
            ['sku' => 'CSX-SUN-65ML'],
            [
                'product_id'          => $cosrxSPF->id,
                'title'               => 'Aloe Suncream - 65ml Pump',
                'volume_or_weight'    => '65 ml / 2.2 fl. oz',
                'price'               => 16.00,
                'compare_at_price'    => 20.00,
                'stock_quantity'      => 100,
                'low_stock_threshold' => 15,
                'is_active'           => true,
                'image_url'           => $cosrxSPF->thumbnail_url,
            ]
        );
        ProductBatch::firstOrCreate(
            ['batch_number' => 'LOT-CSX-SUN-2025'],
            [
                'product_variant_id' => $cosrxSPFVariant->id,
                'supplier_name'      => 'COSRX Clean Formulation Facility Seoul',
                'manufactured_at'    => Carbon::now()->subMonths(1)->toDateString(),
                'expires_at'         => Carbon::now()->addMonths(24)->toDateString(),
                'initial_quantity'   => 100,
                'current_quantity'   => 100,
                'unit_cost'          => 4.10,
                'status'             => 'active',
                'warehouse_location' => 'Sun Care Bay S-05',
            ]
        );

        // Product 21: Charlotte Tilbury Pillow Talk Matte Revolution Lipstick (Lip Care)
        $ctLipstick = Product::updateOrCreate(
            ['slug' => 'charlotte-tilbury-matte-revolution-pillow-talk-lipstick'],
            [
                'brand_id'             => $brands['charlotte-tilbury']->id,
                'category_id'          => $categories['lip-care']->id,
                'name'                 => 'Matte Revolution Lipstick',
                'subtitle'             => 'Velvet-matte lipstick with 24-hour hydration and iconic rose parfum',
                'description'          => 'A buildable, velvety matte lipstick in cult-favourite Pillow Talk shades. Infused with Hyaluronic Acid Lip Complex for 24-hour moisturization, rose oil, and vitamin E to smooth and plump lips. Long-wearing satin-matte finish.',
                'how_to_use'           => 'Apply directly to lips, starting from the cupid bow. For a more defined look, line lips with the Lip Cheat liner in a matching shade first.',
                'full_ingredients_text'=> 'Ricinus Communis (Castor) Seed Oil, Ozokerite, Cera Microcristallina, Polyethylene, Caprylic/Capric Triglyceride, Copernicia Cerifera (Carnauba) Wax, Tocopheryl Acetate, Rosa Centifolia Flower Extract, Sodium Hyaluronate.',
                'target_skin_types'    => ['normal', 'dry', 'sensitive', 'combination', 'oily'],
                'target_concerns'      => ['dehydration', 'fine_lines'],
                'finish_type'          => 'matte',
                'coverage_level'       => 'full',
                'spf_rating'           => null,
                'pao_months'           => 24,
                'base_price'           => 40.00,
                'discount_price'       => 40.00,
                'is_featured'          => true,
                'is_bestseller'        => true,
                'is_active'            => true,
                'rating_avg'           => 4.94,
                'reviews_count'        => 1380,
                'thumbnail_url'        => 'https://images.unsplash.com/photo-1512207736890-6ffed8a84e8d?auto=format&fit=crop&w=800&q=80',
                'gallery_images'       => [
                    'https://images.unsplash.com/photo-1586495777744-4413f21062fa?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=800&q=80',
                ],
            ]
        );
        $ctLipShades = [
            ['sku' => 'CT-LIP-PTLK',  'title' => 'Pillow Talk - Original Dusty Rose Nude',  'shade_name' => 'Pillow Talk',  'shade_hex' => '#C97B88', 'stock' => 60],
            ['sku' => 'CT-LIP-PTMED', 'title' => 'Pillow Talk Medium - Berry Rose',          'shade_name' => 'Pillow Talk Medium', 'shade_hex' => '#A85B70', 'stock' => 45],
            ['sku' => 'CT-LIP-REDCA', 'title' => 'Red Carpet Red - Classic True Red',         'shade_name' => 'Red Carpet Red', 'shade_hex' => '#B01B2D', 'stock' => 40],
            ['sku' => 'CT-LIP-NUDE',  'title' => 'Nude Kate - Barely-There Nude',            'shade_name' => 'Nude Kate',   'shade_hex' => '#C2917E', 'stock' => 35],
        ];
        foreach ($ctLipShades as $lp) {
            $lpVariant = ProductVariant::updateOrCreate(
                ['sku' => $lp['sku']],
                [
                    'product_id'          => $ctLipstick->id,
                    'title'               => $lp['title'],
                    'shade_name'          => $lp['shade_name'],
                    'shade_hex'           => $lp['shade_hex'],
                    'depth_level'         => 4,
                    'undertone'           => 'neutral',
                    'volume_or_weight'    => '3.5 g / 0.12 oz',
                    'price'               => 40.00,
                    'compare_at_price'    => null,
                    'stock_quantity'      => $lp['stock'],
                    'low_stock_threshold' => 10,
                    'is_active'           => true,
                    'image_url'           => $ctLipstick->thumbnail_url,
                ]
            );
            ProductBatch::firstOrCreate(
                ['batch_number' => 'LOT-CT-LIP-' . substr($lp['sku'], -4)],
                [
                    'product_variant_id' => $lpVariant->id,
                    'supplier_name'      => 'Charlotte Tilbury Luxury Formulation Lab UK',
                    'manufactured_at'    => Carbon::now()->subMonths(2)->toDateString(),
                    'expires_at'         => Carbon::now()->addMonths(36)->toDateString(),
                    'initial_quantity'   => $lp['stock'],
                    'current_quantity'   => $lp['stock'],
                    'unit_cost'          => 10.00,
                    'status'             => 'active',
                    'warehouse_location' => 'Lip Bay L-08',
                ]
            );
        }

        // Product 22: Fenty Beauty Glossy Posse Lip Gloss (Lip Care)
        $fentyGloss = Product::updateOrCreate(
            ['slug' => 'fenty-beauty-gloss-bomb-universal-lip-luminizer'],
            [
                'brand_id'             => $brands['fenty-beauty']->id,
                'category_id'          => $categories['lip-care']->id,
                'name'                 => 'Gloss Bomb Universal Lip Luminizer',
                'subtitle'             => 'Non-sticky, high-shine clear lip gloss with shea butter and vitamin E',
                'description'          => 'A non-sticky, universally-flattering lip gloss that gives lips a megawatt shine and a cushiony feel. Formulated with shea butter and vitamin E for a smooth, glossy finish that works on its own or over any lipstick.',
                'how_to_use'           => 'Apply directly to bare lips or over any lip color for a high-shine glossy finish.',
                'full_ingredients_text'=> 'Hydrogenated Polyisobutene, Diisostearyl Malate, Polybutene, Silica Dimethyl Silylate, Butyrospermum Parkii (Shea) Butter, Tocopheryl Acetate, Vanillin, Fragrance, [+/- Mica, Titanium Dioxide, Red 7 Lake].',
                'target_skin_types'    => ['normal', 'dry', 'combination', 'oily', 'sensitive'],
                'target_concerns'      => ['dehydration', 'fine_lines'],
                'finish_type'          => 'glossy',
                'coverage_level'       => 'buildable',
                'spf_rating'           => null,
                'pao_months'           => 24,
                'base_price'           => 22.00,
                'discount_price'       => 22.00,
                'is_featured'          => true,
                'is_bestseller'        => true,
                'is_active'            => true,
                'rating_avg'           => 4.89,
                'reviews_count'        => 2100,
                'thumbnail_url'        => 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=800&q=80',
                'gallery_images'       => [
                    'https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=800&q=80',
                ],
            ]
        );
        $fentyGlossShades = [
            ['sku' => 'FNT-GB-FENTY', 'title' => "Fenty Glow - Sheer Rosy Nude",    'shade_name' => 'Fenty Glow',   'shade_hex' => '#E8C0B2', 'stock' => 65],
            ['sku' => 'FNT-GB-CLEAR', 'title' => "Glass Slipper - Clear High-Shine", 'shade_name' => 'Glass Slipper','shade_hex' => '#F5EFE8', 'stock' => 55],
            ['sku' => 'FNT-GB-HOTCH', 'title' => "Hot Chocolit - Warm Brown Mauve",  'shade_name' => 'Hot Chocolit', 'shade_hex' => '#8B4F3C', 'stock' => 40],
        ];
        foreach ($fentyGlossShades as $fg) {
            $fgVariant = ProductVariant::updateOrCreate(
                ['sku' => $fg['sku']],
                [
                    'product_id'          => $fentyGloss->id,
                    'title'               => $fg['title'],
                    'shade_name'          => $fg['shade_name'],
                    'shade_hex'           => $fg['shade_hex'],
                    'depth_level'         => 2,
                    'undertone'           => 'neutral',
                    'volume_or_weight'    => '9 ml / 0.3 fl. oz',
                    'price'               => 22.00,
                    'compare_at_price'    => null,
                    'stock_quantity'      => $fg['stock'],
                    'low_stock_threshold' => 10,
                    'is_active'           => true,
                    'image_url'           => $fentyGloss->thumbnail_url,
                ]
            );
            ProductBatch::firstOrCreate(
                ['batch_number' => 'LOT-FNT-GB-' . substr($fg['sku'], -4)],
                [
                    'product_variant_id' => $fgVariant->id,
                    'supplier_name'      => 'Kendo Fragrances & Cosmetics Lab',
                    'manufactured_at'    => Carbon::now()->subMonths(2)->toDateString(),
                    'expires_at'         => Carbon::now()->addMonths(36)->toDateString(),
                    'initial_quantity'   => $fg['stock'],
                    'current_quantity'   => $fg['stock'],
                    'unit_cost'          => 5.50,
                    'status'             => 'active',
                    'warehouse_location' => 'Lip Bay L-10',
                ]
            );
        }

        // Product 23: Rare Beauty Kind Words Matte Lipstick (Lip Care)
        $rareBeautyLip = Product::updateOrCreate(
            ['slug' => 'rare-beauty-kind-words-matte-lipstick'],
            [
                'brand_id'             => $brands['rare-beauty']->id,
                'category_id'          => $categories['lip-care']->id,
                'name'                 => 'Kind Words Matte Lipstick',
                'subtitle'             => 'Comfortable matte lipstick that stays on all day without drying out lips',
                'description'          => 'A comfortable, long-lasting matte lipstick that keeps lips feeling moisturized all day. Enriched with shea butter, jojoba oil, and vitamin E, it delivers rich color payoff without feeling dry or heavy.',
                'how_to_use'           => 'Apply directly to lips. For a more intense look, layer or line first with the matching lip liner.',
                'full_ingredients_text'=> 'Ricinus Communis (Castor) Seed Oil, Ozokerite, Polyethylene, Caprylic/Capric Triglyceride, Beeswax, Butyrospermum Parkii (Shea) Butter, Simmondsia Chinensis (Jojoba) Seed Oil, Tocopheryl Acetate, Hydrogenated Polyisobutene.',
                'target_skin_types'    => ['normal', 'dry', 'combination', 'oily', 'sensitive'],
                'target_concerns'      => ['dehydration'],
                'finish_type'          => 'matte',
                'coverage_level'       => 'full',
                'spf_rating'           => null,
                'pao_months'           => 24,
                'base_price'           => 20.00,
                'discount_price'       => 20.00,
                'is_featured'          => false,
                'is_bestseller'        => true,
                'is_active'            => true,
                'rating_avg'           => 4.82,
                'reviews_count'        => 560,
                'thumbnail_url'        => 'https://images.unsplash.com/photo-1625772452859-1c03d5bf1137?auto=format&fit=crop&w=800&q=80',
                'gallery_images'       => [
                    'https://images.unsplash.com/photo-1586495777744-4413f21062fa?auto=format&fit=crop&w=800&q=80',
                ],
            ]
        );
        $rbLipShades = [
            ['sku' => 'RB-KW-GRACE',  'title' => 'Grace - Warm Terracotta Rose',   'shade_name' => 'Grace',  'shade_hex' => '#C07560', 'stock' => 50],
            ['sku' => 'RB-KW-TRUTH',  'title' => 'Truth - Deep Berry Plum',         'shade_name' => 'Truth',  'shade_hex' => '#7A3052', 'stock' => 40],
            ['sku' => 'RB-KW-BRAVE',  'title' => 'Brave - Cool Muted Pink',         'shade_name' => 'Brave',  'shade_hex' => '#C08898', 'stock' => 35],
        ];
        foreach ($rbLipShades as $rl) {
            $rlVariant = ProductVariant::updateOrCreate(
                ['sku' => $rl['sku']],
                [
                    'product_id'          => $rareBeautyLip->id,
                    'title'               => $rl['title'],
                    'shade_name'          => $rl['shade_name'],
                    'shade_hex'           => $rl['shade_hex'],
                    'depth_level'         => 4,
                    'undertone'           => 'neutral',
                    'volume_or_weight'    => '3.9 g / 0.14 oz',
                    'price'               => 20.00,
                    'compare_at_price'    => null,
                    'stock_quantity'      => $rl['stock'],
                    'low_stock_threshold' => 10,
                    'is_active'           => true,
                    'image_url'           => $rareBeautyLip->thumbnail_url,
                ]
            );
            ProductBatch::firstOrCreate(
                ['batch_number' => 'LOT-RB-KW-' . substr($rl['sku'], -4)],
                [
                    'product_variant_id' => $rlVariant->id,
                    'supplier_name'      => 'Rare Beauty Labs California',
                    'manufactured_at'    => Carbon::now()->subMonths(1)->toDateString(),
                    'expires_at'         => Carbon::now()->addMonths(36)->toDateString(),
                    'initial_quantity'   => $rl['stock'],
                    'current_quantity'   => $rl['stock'],
                    'unit_cost'          => 5.00,
                    'status'             => 'active',
                    'warehouse_location' => 'Lip Bay L-12',
                ]
            );
        }

        // Product 24: Charlotte Tilbury Magic Cream (Skincare)
        $ctMagicCream = Product::updateOrCreate(
            ['slug' => 'charlotte-tilbury-magic-cream-moisturizer'],
            [
                'brand_id'             => $brands['charlotte-tilbury']->id,
                'category_id'          => $categories['skincare']->id,
                'name'                 => 'Charlotte\'s Magic Cream Moisturizer',
                'subtitle'             => 'Award-winning instant turnaround moisturizer with Hyaluronic Acid & Bionymph Peptides',
                'description'          => 'The secret to glowing, hydrated, red-carpet ready skin. Formulated with Charlotte\'s Magic 8 Matrix of ingredients including Hyaluronic Acid, Bionymph Peptide, Rosehip Oil, and Vitamin C to visibly reduce fine lines and flood the skin with moisture.',
                'how_to_use'           => 'Apply every morning to face and neck using the \'Tilbury Tap\' facial massage technique to boost micro-circulation and create a radiant glow.',
                'full_ingredients_text'=> 'Aqua, Homosalate, Glyceryl Stearate SE, Ethylhexyl Salicylate, Butylene Glycol, Glycerin, Butyl Methoxydibenzoylmethane, Octocrylene, Cetyl Alcohol, C12-15 Alkyl Benzoate, Cyclopentasiloxane, Dimethicone, Phenoxyethanol, Butyrospermum Parkii (Shea) Butter, Steareth-21, Avena Sativa (Oat) Kernel Extract, Carbomer, Dimethiconol, Potassium Cetyl Phosphate, Chlorphenesin, Caprylyl Glycol, Xanthan Gum, Hydrolyzed Viola Tricolor Extract, Allantoin, Aloe Barbadensis Leaf Juice, Disodium EDTA, Tocopheryl Acetate, Camellia Oleifera Seed Oil, Rosa Canina Fruit Oil, Rosa Damascena Extract, Sodium Hyaluronate, Helianthus Annuus (Sunflower) Seed Oil, Michelia Alba Leaf Oil, Sodium Hydroxide, Ceramide NP, Palmitoyl Tetrapeptide-7, Palmitoyl Tripeptide-1.',
                'target_skin_types'    => ['dry', 'normal', 'combination', 'sensitive'],
                'target_concerns'      => ['dehydration', 'dullness', 'fine_lines', 'barrier_repair'],
                'finish_type'          => 'dewy',
                'coverage_level'       => null,
                'spf_rating'           => 15,
                'pao_months'           => 12,
                'base_price'           => 65.00,
                'discount_price'       => 65.00,
                'is_featured'          => true,
                'is_bestseller'        => true,
                'is_active'            => true,
                'rating_avg'           => 4.96,
                'reviews_count'        => 890,
                'thumbnail_url'        => 'https://images.unsplash.com/photo-1571875257727-256c39da42af?auto=format&fit=crop&w=800&q=80',
                'gallery_images'       => [
                    'https://images.unsplash.com/photo-1571875257727-256c39da42af?auto=format&fit=crop&w=800&q=80',
                ],
            ]
        );
        $ctMagicCream->ingredients()->syncWithoutDetaching([
            $ingredients['hyaluronic-acid']->id => ['concentration_percentage' => 2.00, 'is_key_active' => true],
            $ingredients['peptides']->id        => ['concentration_percentage' => 1.50, 'is_key_active' => true],
        ]);
        $ctMcVariants = [
            ['sku' => 'CT-MC-30ML', 'title' => 'Travel Jar - 30ml / 1.0 fl. oz', 'price' => 65.00, 'stock' => 45],
            ['sku' => 'CT-MC-50ML', 'title' => 'Full Size Jar - 50ml / 1.7 fl. oz', 'price' => 100.00, 'stock' => 50],
        ];
        foreach ($ctMcVariants as $mv) {
            $mVar = ProductVariant::updateOrCreate(
                ['sku' => $mv['sku']],
                [
                    'product_id'          => $ctMagicCream->id,
                    'title'               => $mv['title'],
                    'volume_or_weight'    => explode(' - ', $mv['title'])[1],
                    'price'               => $mv['price'],
                    'compare_at_price'    => null,
                    'stock_quantity'      => $mv['stock'],
                    'low_stock_threshold' => 10,
                    'is_active'           => true,
                    'image_url'           => $ctMagicCream->thumbnail_url,
                ]
            );
            ProductBatch::firstOrCreate(
                ['batch_number' => 'LOT-CT-MC-' . substr($mv['sku'], -4)],
                [
                    'product_variant_id' => $mVar->id,
                    'supplier_name'      => 'Charlotte Tilbury Luxury Formulation Lab UK',
                    'manufactured_at'    => Carbon::now()->subMonths(1)->toDateString(),
                    'expires_at'         => Carbon::now()->addMonths(24)->toDateString(),
                    'initial_quantity'   => $mv['stock'],
                    'current_quantity'   => $mv['stock'],
                    'unit_cost'          => 18.00,
                    'status'             => 'active',
                    'warehouse_location' => 'Skincare Bay SK-01',
                ]
            );
        }

        // Product 25: CeraVe Daily Moisturizing Cream (Skincare)
        $ceraveMoisturizer = Product::updateOrCreate(
            ['slug' => 'cerave-daily-moisturizing-cream-ceramides'],
            [
                'brand_id'             => $brands['cerave']->id,
                'category_id'          => $categories['skincare']->id,
                'name'                 => 'Moisturizing Cream with 3 Essential Ceramides',
                'subtitle'             => 'Barrier-restoring rich cream with MVE delivery technology & Hyaluronic Acid',
                'description'          => 'Developed with dermatologists, this rich, non-greasy cream provides 24-hour hydration with 3 essential skin-identical ceramides (1, 3, 6-II) and hyaluronic acid to restore the protective skin barrier. Non-comedogenic, oil-free, and fragrance-free.',
                'how_to_use'           => 'Apply liberally as often as needed, or as directed by a physician. Suitable for use on the face, body, and hands.',
                'full_ingredients_text'=> 'Aqua / Water / Eau, Glycerin, Cetearyl Alcohol, Caprylic/Capric Triglyceride, Cetyl Alcohol, Ceteareth-20, Petrolatum, Potassium Phosphate, Ceramide NP, Ceramide AP, Ceramide EOP, Carbomer, Dimethicone, Behentrimonium Methosulfate, Sodium Lauroyl Lactylate, Sodium Hyaluronate, Cholesterol, Phenoxyethanol, Disodium EDTA, Dipotassium Phosphate, Tocopherol, Phytosphingosine, Xanthan Gum, Ethylhexylglycerin.',
                'target_skin_types'    => ['dry', 'normal', 'combination', 'sensitive'],
                'target_concerns'      => ['dehydration', 'barrier_repair', 'redness'],
                'finish_type'          => 'natural',
                'coverage_level'       => null,
                'spf_rating'           => null,
                'pao_months'           => 12,
                'base_price'           => 19.50,
                'discount_price'       => 19.50,
                'is_featured'          => true,
                'is_bestseller'        => true,
                'is_active'            => true,
                'rating_avg'           => 4.91,
                'reviews_count'        => 1420,
                'thumbnail_url'        => 'https://images.unsplash.com/photo-1611080626919-7cf5a9dbab12?auto=format&fit=crop&w=800&q=80',
                'gallery_images'       => [
                    'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=800&q=80',
                ],
            ]
        );
        $ceraveMoisturizer->ingredients()->syncWithoutDetaching([
            $ingredients['ceramides']->id       => ['concentration_percentage' => 3.00, 'is_key_active' => true],
            $ingredients['hyaluronic-acid']->id => ['concentration_percentage' => 1.00, 'is_key_active' => true],
        ]);
        $crvMVar = ProductVariant::updateOrCreate(
            ['sku' => 'CRV-MC-454G'],
            [
                'product_id'          => $ceraveMoisturizer->id,
                'title'               => 'Moisturizing Cream - 454g / 16 oz Tub with Pump',
                'volume_or_weight'    => '454 g / 16 oz',
                'price'               => 19.50,
                'compare_at_price'    => null,
                'stock_quantity'      => 90,
                'low_stock_threshold' => 15,
                'is_active'           => true,
                'image_url'           => $ceraveMoisturizer->thumbnail_url,
            ]
        );
        ProductBatch::firstOrCreate(
            ['batch_number' => 'LOT-CRV-MC-454G'],
            [
                'product_variant_id' => $crvMVar->id,
                'supplier_name'      => 'CeraVe Dermatological Supply New York',
                'manufactured_at'    => Carbon::now()->subMonths(1)->toDateString(),
                'expires_at'         => Carbon::now()->addMonths(36)->toDateString(),
                'initial_quantity'   => 90,
                'current_quantity'   => 90,
                'unit_cost'          => 4.80,
                'status'             => 'active',
                'warehouse_location' => 'Skincare Bay SK-02',
            ]
        );

        // Product 26: La Roche-Posay Hyalu B5 Serum (Skincare)
        $lrpHyalu = Product::updateOrCreate(
            ['slug' => 'la-roche-posay-hyalu-b5-pure-serum'],
            [
                'brand_id'             => $brands['la-roche-posay']->id,
                'category_id'          => $categories['skincare']->id,
                'name'                 => 'Hyalu B5 Pure Hyaluronic Acid Serum',
                'subtitle'             => 'Dermatological anti-aging serum with dual-molecular Hyaluronic Acid & Vitamin B5',
                'description'          => 'Intensively replumps and repairs the skin barrier within 1 hour. Clinically proven to reduce fine lines, replenish moisture, and restore skin bounce with prebiotic thermal water, pure hyaluronic acid, and vitamin B5 (Panthenol).',
                'how_to_use'           => 'Apply 3-4 drops morning and/or evening onto face and neck after cleansing. Use alone or under your daily moisturizer.',
                'full_ingredients_text'=> 'Aqua / Water, Glycerin, Alcohol Denat., Propylene Glycol, Panthenol, Pentylene Glycol, Dimethicone, PEG-6 Caprylic/Capric Glycerides, PPG-6-Decyltetradeceth-30, Glyceryl Isostearate, Madecassoside, Sodium Hyaluronate, Ammonium Polyacryloyldimethyl Taurate, Disodium EDTA, Hydrolyzed Hyaluronic Acid, Caprylyl Glycol, Citric Acid, Xanthan Gum, Butylene Glycol, Tocopherol, Phenoxyethanol.',
                'target_skin_types'    => ['dry', 'normal', 'combination', 'sensitive'],
                'target_concerns'      => ['dehydration', 'fine_lines', 'barrier_repair', 'redness'],
                'finish_type'          => 'dewy',
                'coverage_level'       => null,
                'spf_rating'           => null,
                'pao_months'           => 12,
                'base_price'           => 39.99,
                'discount_price'       => 39.99,
                'is_featured'          => true,
                'is_bestseller'        => true,
                'is_active'            => true,
                'rating_avg'           => 4.93,
                'reviews_count'        => 780,
                'thumbnail_url'        => 'https://images.unsplash.com/photo-1617897903246-719242758050?auto=format&fit=crop&w=800&q=80',
                'gallery_images'       => [
                    'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=800&q=80',
                ],
            ]
        );
        $lrpHyalu->ingredients()->syncWithoutDetaching([
            $ingredients['hyaluronic-acid']->id   => ['concentration_percentage' => 2.50, 'is_key_active' => true],
            $ingredients['centella-asiatica']->id => ['concentration_percentage' => 1.00, 'is_key_active' => true],
        ]);
        $lrpHyaluVar = ProductVariant::updateOrCreate(
            ['sku' => 'LRP-HB5-30ML'],
            [
                'product_id'          => $lrpHyalu->id,
                'title'               => 'Hyalu B5 Serum - 30ml Dropper',
                'volume_or_weight'    => '30 ml / 1.0 fl. oz',
                'price'               => 39.99,
                'compare_at_price'    => null,
                'stock_quantity'      => 70,
                'low_stock_threshold' => 12,
                'is_active'           => true,
                'image_url'           => $lrpHyalu->thumbnail_url,
            ]
        );
        ProductBatch::firstOrCreate(
            ['batch_number' => 'LOT-LRP-HB5-2025'],
            [
                'product_variant_id' => $lrpHyaluVar->id,
                'supplier_name'      => 'La Roche-Posay Laboratoire Dermatologique France',
                'manufactured_at'    => Carbon::now()->subMonths(1)->toDateString(),
                'expires_at'         => Carbon::now()->addMonths(24)->toDateString(),
                'initial_quantity'   => 70,
                'current_quantity'   => 70,
                'unit_cost'          => 9.50,
                'status'             => 'active',
                'warehouse_location' => 'Skincare Bay SK-03',
            ]
        );

        // Product 27: Summer Fridays Jet Lag Mask (Skincare)
        $summerFridaysMask = Product::updateOrCreate(
            ['slug' => 'summer-fridays-jet-lag-mask'],
            [
                'brand_id'             => $brands['summer-fridays']->id,
                'category_id'          => $categories['skincare']->id,
                'name'                 => 'Jet Lag Mask + Barrier Hydration',
                'subtitle'             => 'Cult-favorite leave-on mask with Niacinamide, Glycerin & Hyaluronic Acid',
                'description'          => 'An ultra-hydrating cream mask that nourishes, soothes, and restores stressed, dehydrated, or dull skin. Can be used as a 10-minute rinse-off mask, an overnight moisture treatment, or a daily barrier cream. Non-comedogenic and fragrance-free.',
                'how_to_use'           => 'Apply a visible layer on cleansed, dry skin. Leave on for 10 minutes, then blot away excess with a warm washcloth or tissue if needed. No rinsing required.',
                'full_ingredients_text'=> 'Water / Aqua / Eau, Caprylic/Capric Triglyceride, Castor Isostearate Succinate, Glycerin, Dimethicone, Niacinamide, Cetearyl Alcohol, Hydroxyethyl Acrylate/Sodium Acryloyldimethyl Taurate Copolymer, Sodium Hyaluronate, Panthenol, Bisabolol, Allantoin, Cucumis Sativus (Cucumber) Fruit Extract, Chamomilla Recutita (Matricaria) Flower Extract, Ethylhexylglycerin, Phenoxyethanol.',
                'target_skin_types'    => ['dry', 'normal', 'combination', 'sensitive'],
                'target_concerns'      => ['dehydration', 'dullness', 'redness', 'barrier_repair'],
                'finish_type'          => 'dewy',
                'coverage_level'       => null,
                'spf_rating'           => null,
                'pao_months'           => 12,
                'base_price'           => 49.00,
                'discount_price'       => 49.00,
                'is_featured'          => true,
                'is_bestseller'        => false,
                'is_active'            => true,
                'rating_avg'           => 4.89,
                'reviews_count'        => 560,
                'thumbnail_url'        => 'https://images.unsplash.com/photo-1532413992378-f169ac26fff0?auto=format&fit=crop&w=800&q=80',
                'gallery_images'       => [
                    'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=800&q=80',
                ],
            ]
        );
        $summerFridaysMask->ingredients()->syncWithoutDetaching([
            $ingredients['niacinamide']->id     => ['concentration_percentage' => 2.00, 'is_key_active' => true],
            $ingredients['hyaluronic-acid']->id => ['concentration_percentage' => 1.50, 'is_key_active' => true],
        ]);
        $sfMaskVar = ProductVariant::updateOrCreate(
            ['sku' => 'SF-JLM-64G'],
            [
                'product_id'          => $summerFridaysMask->id,
                'title'               => 'Jet Lag Mask - 64 g / 2.25 oz Tube',
                'volume_or_weight'    => '64 g / 2.25 oz',
                'price'               => 49.00,
                'compare_at_price'    => null,
                'stock_quantity'      => 60,
                'low_stock_threshold' => 10,
                'is_active'           => true,
                'image_url'           => $summerFridaysMask->thumbnail_url,
            ]
        );
        ProductBatch::firstOrCreate(
            ['batch_number' => 'LOT-SF-JLM-2025'],
            [
                'product_variant_id' => $sfMaskVar->id,
                'supplier_name'      => 'Summer Fridays Clean Lab USA',
                'manufactured_at'    => Carbon::now()->subMonths(1)->toDateString(),
                'expires_at'         => Carbon::now()->addMonths(24)->toDateString(),
                'initial_quantity'   => 60,
                'current_quantity'   => 60,
                'unit_cost'          => 11.20,
                'status'             => 'active',
                'warehouse_location' => 'Skincare Bay SK-04',
            ]
        );

        // Product 28: Fenty Skin Total Cleans'r (Skincare)
        $fentyCleanser = Product::updateOrCreate(
            ['slug' => 'fenty-skin-total-cleansr'],
            [
                'brand_id'             => $brands['fenty-beauty']->id,
                'category_id'          => $categories['skincare']->id,
                'name'                 => 'Total Cleans\'r 2-in-1 Makeup-Removing Cleanser',
                'subtitle'             => 'Deep cleansing pore-refining lather with Barbados Cherry & Green Tea',
                'description'          => 'A 2-in-1 makeup remover and gentle facial cleanser with a luscious creamy lather. Removes dirt, oil, and longwear makeup without stripping or drying skin. Infused with antioxidant-rich Barbados cherry, ginkgo biloba, and green tea.',
                'how_to_use'           => 'Wet skin, lather into hands, and gently massage across face. Rinse with warm water. Use morning and night.',
                'full_ingredients_text'=> 'Aqua / Water / Eau, Sodium Cocoyl Glycinate, Sodium Lauroamphoacetate, Cocamidopropyl Betaine, Sodium Chloride, Polyglyceryl-10 Laurate, Glycol Distearate, Citric Acid, Niacinamide, Zinc PCA, Malpighia Punicifolia (Acerola) Fruit Extract, Camellia Sinensis Leaf Extract, Ginkgo Biloba Leaf Extract, Ficus Carica (Fig) Fruit Extract, Phenoxyethanol, Ethylhexylglycerin.',
                'target_skin_types'    => ['oily', 'combination', 'normal'],
                'target_concerns'      => ['large_pores', 'excess_shine', 'texture'],
                'finish_type'          => 'natural',
                'coverage_level'       => null,
                'spf_rating'           => null,
                'pao_months'           => 12,
                'base_price'           => 29.00,
                'discount_price'       => 29.00,
                'is_featured'          => true,
                'is_bestseller'        => false,
                'is_active'            => true,
                'rating_avg'           => 4.88,
                'reviews_count'        => 640,
                'thumbnail_url'        => 'https://images.unsplash.com/photo-1599305090598-fe179d501227?auto=format&fit=crop&w=800&q=80',
                'gallery_images'       => [
                    'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=800&q=80',
                ],
            ]
        );
        $fentyCleanser->ingredients()->syncWithoutDetaching([
            $ingredients['niacinamide']->id => ['concentration_percentage' => 1.50, 'is_key_active' => true],
        ]);
        $fntClVar = ProductVariant::updateOrCreate(
            ['sku' => 'FNT-TC-145ML'],
            [
                'product_id'          => $fentyCleanser->id,
                'title'               => 'Total Cleans\'r - 145 ml / 4.9 fl. oz Tube',
                'volume_or_weight'    => '145 ml / 4.9 fl. oz',
                'price'               => 29.00,
                'compare_at_price'    => null,
                'stock_quantity'      => 80,
                'low_stock_threshold' => 15,
                'is_active'           => true,
                'image_url'           => $fentyCleanser->thumbnail_url,
            ]
        );
        ProductBatch::firstOrCreate(
            ['batch_number' => 'LOT-FNT-TC-2025'],
            [
                'product_variant_id' => $fntClVar->id,
                'supplier_name'      => 'Fenty Skin San Francisco Labs',
                'manufactured_at'    => Carbon::now()->subMonths(1)->toDateString(),
                'expires_at'         => Carbon::now()->addMonths(24)->toDateString(),
                'initial_quantity'   => 80,
                'current_quantity'   => 80,
                'unit_cost'          => 6.20,
                'status'             => 'active',
                'warehouse_location' => 'Skincare Bay SK-05',
            ]
        );

        // 6. Seed Product Bundles (Combo Offers)
        $glassSkinBundle = ProductBundle::updateOrCreate(
            ['slug' => 'glass-skin-clarifying-duo'],
            [
                'title' => 'The Pore-Refining Glass Skin Duo',
                'description' => 'Synergistic chemical exfoliation and barrier pore-tightening. Combines Paula’s Choice 2% BHA with The Ordinary Niacinamide 10% for flawless texture.',
                'bundle_price' => 41.00, // Regular total: $35 + $10.80 = $45.80 (10% discount bundle)
                'original_price' => 45.80,
                'thumbnail_url' => 'https://images.unsplash.com/photo-1583241800698-e8ab01830a66?auto=format&fit=crop&w=800&q=80',
                'is_active' => true,
            ]
        );

        BundleItem::firstOrCreate([
            'product_bundle_id' => $glassSkinBundle->id,
            'product_variant_id' => $bhaVariant->id,
            'quantity' => 1,
        ]);

        BundleItem::firstOrCreate([
            'product_bundle_id' => $glassSkinBundle->id,
            'product_variant_id' => $niacinamideVariant->id,
            'quantity' => 1,
        ]);

        // 7. Seed Coupons
        Coupon::updateOrCreate(
            ['code' => 'GLOW20'],
            [
                'description' => '20% off site-wide for new beauty members',
                'discount_type' => 'percentage',
                'discount_value' => 20.00,
                'min_order_amount' => 30.00,
                'max_discount_amount' => 50.00,
                'usage_limit_total' => 1000,
                'usage_limit_per_user' => 1,
                'usage_count' => 14,
                'starts_at' => Carbon::now()->subDays(10),
                'expires_at' => Carbon::now()->addMonths(6),
                'is_active' => true,
            ]
        );

        Coupon::updateOrCreate(
            ['code' => 'BEAUTY10'],
            [
                'description' => '$10 Flat Discount on luxury foundation and skincare',
                'discount_type' => 'fixed',
                'discount_value' => 10.00,
                'min_order_amount' => 50.00,
                'max_discount_amount' => 10.00,
                'usage_limit_total' => 500,
                'usage_limit_per_user' => 2,
                'usage_count' => 42,
                'starts_at' => Carbon::now()->subDays(5),
                'expires_at' => Carbon::now()->addMonths(3),
                'is_active' => true,
            ]
        );

        // 8. Seed Verified Customer Reviews with Skin Types & Undertones
        Review::updateOrCreate(
            ['product_id' => $fentyFoundation->id, 'user_id' => $customer->id],
            [
                'order_id' => null,
                'rating' => 5,
                'title' => 'Finally a true neutral undertone that doesn\'t turn orange!',
                'review_text' => 'I have combination skin with warm undertones and struggle with foundations oxidizing into a pumpkin shade. Shade 230 was my exact match from the undertone matrix. Lasts 12 hours through heat without caking in my T-zone.',
                'verified_purchase' => true,
                'reviewer_skin_type' => 'combination',
                'reviewer_undertone' => 'warm',
                'purchased_shade' => '230 Medium Warm',
                'photos' => ['https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=400&q=80'],
                'helpful_votes' => 38,
                'status' => 'approved',
            ]
        );

        Review::updateOrCreate(
            ['product_id' => $ordinaryNiacinamide->id, 'user_id' => $customer->id],
            [
                'order_id' => null,
                'rating' => 5,
                'title' => 'Holy grail for sebaceous filaments and hormonal redness',
                'review_text' => 'Within 3 weeks of daily morning use, my nose pores appear visibly smaller and my skin barrier feels substantially stronger. At this price point, nothing competes.',
                'verified_purchase' => true,
                'reviewer_skin_type' => 'combination',
                'reviewer_undertone' => 'warm',
                'purchased_shade' => null,
                'photos' => null,
                'helpful_votes' => 64,
                'status' => 'approved',
            ]
        );

        // 9. Seed Sample Orders with realistic order history & status tracking
        $sampleOrder = Order::updateOrCreate(
            ['order_number' => 'BV-2026-98124'],
            [
                'user_id' => $customer->id,
                'customer_name' => 'Sophia Chen',
                'customer_email' => 'customer@beautyvault.com',
                'customer_phone' => '+1 (555) 987-6543',
                'shipping_address' => [
                    'recipient_name' => 'Sophia Chen',
                    'street' => '742 Evergreen Terrace, Apt 4B',
                    'city' => 'Beverly Hills',
                    'state' => 'CA',
                    'postal_code' => '90210',
                    'country' => 'US',
                ],
                'billing_address' => [
                    'recipient_name' => 'Sophia Chen',
                    'street' => '742 Evergreen Terrace, Apt 4B',
                    'city' => 'Beverly Hills',
                    'state' => 'CA',
                    'postal_code' => '90210',
                    'country' => 'US',
                ],
                'coupon_code' => 'GLOW20',
                'subtotal' => 48.80,
                'discount_amount' => 9.76,
                'shipping_cost' => 0.00,
                'tax_amount' => 3.12,
                'total_amount' => 42.16,
                'payment_method' => 'stripe',
                'payment_status' => 'paid',
                'stripe_payment_intent_id' => 'pi_3MtwxKLkdIwHu7ix28a3tqZ1',
                'status' => 'dispatched',
                'tracking_number' => 'DHL-US-940010009823412',
                'shipping_carrier' => 'DHL Express Priority',
                'customer_notes' => 'Please leave with concierge if unavailable.',
                'paid_at' => Carbon::now()->subDays(2),
                'dispatched_at' => Carbon::now()->subHours(18),
            ]
        );

        // Order Items
        $orderItem1 = OrderItem::firstOrCreate(
            ['order_id' => $sampleOrder->id, 'product_variant_id' => $shades[2]['sku'] ? ProductVariant::where('sku', 'FNT-PF-230W')->first()->id : 1],
            [
                'product_id' => $fentyFoundation->id,
                'product_name' => $fentyFoundation->name,
                'variant_title' => '230 - Medium with Warm Golden Undertones',
                'shade_name' => '230 Medium Warm',
                'shade_hex' => '#D8A279',
                'unit_price' => 38.00,
                'quantity' => 1,
                'subtotal' => 38.00,
            ]
        );

        $orderItem2 = OrderItem::firstOrCreate(
            ['order_id' => $sampleOrder->id, 'product_variant_id' => $niacinamideVariant->id],
            [
                'product_id' => $ordinaryNiacinamide->id,
                'product_name' => $ordinaryNiacinamide->name,
                'variant_title' => 'Standard Dropper - 60ml Value Size',
                'shade_name' => null,
                'shade_hex' => null,
                'unit_price' => 10.80,
                'quantity' => 1,
                'subtotal' => 10.80,
            ]
        );

        // Status history for real-time tracking demonstration
        OrderStatusHistory::firstOrCreate(
            ['order_id' => $sampleOrder->id, 'status' => 'pending'],
            ['comment' => 'Order placed online by customer.', 'created_by_user_id' => $customer->id, 'created_at' => Carbon::now()->subDays(2)]
        );
        OrderStatusHistory::firstOrCreate(
            ['order_id' => $sampleOrder->id, 'status' => 'confirmed'],
            ['comment' => 'Payment verified via Stripe Intent pi_3MtwxKLkdIwHu7ix28a3tqZ1.', 'created_by_user_id' => $admin->id, 'created_at' => Carbon::now()->subDays(2)->addMinutes(5)]
        );
        OrderStatusHistory::firstOrCreate(
            ['order_id' => $sampleOrder->id, 'status' => 'preparing'],
            ['comment' => 'Stock allocated using FIFO lot LOT-2025-230W-A. Package inspected and boxed.', 'created_by_user_id' => $admin->id, 'created_at' => Carbon::now()->subDays(1)]
        );
        OrderStatusHistory::firstOrCreate(
            ['order_id' => $sampleOrder->id, 'status' => 'dispatched'],
            ['comment' => 'Handed over to DHL Express with tracking #DHL-US-940010009823412.', 'created_by_user_id' => $admin->id, 'created_at' => Carbon::now()->subHours(18)]
        );
    }
}
