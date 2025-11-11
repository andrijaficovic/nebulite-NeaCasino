## Purpose

This theme is a **lightweight, modular, SEO-friendly WordPress starter theme** designed for iGaming / casino landing pages.
It should support **ACF blocks**, use **Sass for styling**, have a **responsive header**, and be fully optimized for speed.

---

## 1️⃣ Theme Setup

* Base theme: `_underscores`
* Enable **Sass support** using the Sassify plugin or build system.
* Use **modern WordPress best practices**:

  * Enqueue scripts and styles via `wp_enqueue_script()` and `wp_enqueue_style()`.
  * Load minified CSS/JS for production.
  * Use `async` or `defer` on scripts where possible.
* Follow **WordPress coding standards** for PHP, JS, and CSS.

---

## 2️⃣ Theme Features

* **Responsive header**:

  * Left: Logo
  * Right: Navigation menu (mega menu support)
* **Custom Post Types (CPTs)**:

  * `casino` for listing casinos
* **ACF support**:

* **SEO-friendly**:

  * Proper HTML5 structure
  * Schema.org markup for casino listings and ratings
  * Clean headings (`h1` for page title, `h2/h3` for sections)
* **Modular template structure**:

  * Separate partials for hero, blocks, and footer
  * Each block has its own SCSS partial
* **Default preview image** for casinos
* **Fast load**:

  * Lazy-load images
  * Conditional asset loading per block/page
  * disable emojis
  * disable XML-RPC (add_filter('xmlrpc_enabled', '__return_false');)
  * hide WP version (remove_action('wp_head', 'wp_generator');)


---

## 3️⃣ Recommended Folder Structure

```
/wp-content/themes/nebulite/
├─ assets/
│  ├─ js/
│  ├─ sass/
│  └─ images/
├─ template-parts/
│  ├─ blocks/
│  │  ├─ hero.php
│  │  ├─ casino-list.php
│  │  ├─ coins.php
│  │  └─ guides.php
│  └─ header.php
├─ functions.php
├─ style.css
├─ index.php
└─ single-casino.php
```

* Sass partials go in `/assets/sass/` with main `style.scss` importing all partials.

---

## 4️⃣ ACF Blocks & Fields


* **Casino List Block**

**Instructions:**

1. **Initial Layout:**

   * Display a list of casinos in a table or card layout.
   * Columns/sections visible: `Casino`, `Bonus`, `Features`, `Rating`, `Website`.
   * Load **10 casinos initially**. Include a **“55 more to explore”** button to load more in batches of 10 without page reload.

2. **Expandable Casino Details:**

   * Each casino can expand to show additional sections:

     * `Bonus`
     * `Wagering Requirements`
     * `Games`
     * `Coins`
     * Other features: `Established`, `VPN Friendly`, `KYC`, `Telegram`, `Discord`

3. **Filters & Sorting:**

   * Filters appear in a **popup modal** when user clicks **Filters** button.
   * Filter categories:

     * **General Filters:** Low Wagering, Low Deposit, No KYC, VPN Friendly, etc.
     * **Accepted Coins:** BTC, ETH, etc.
     * **Games:** Slots, Crash, Plinko, etc.
   * Apply filters **instantly** without page reload.
   * Sorting options: `Rating` (descending) and `Newest`.
   * If two casinos have the same rating, allow **manual rearrangement in the dashboard**.

4. **Interactions:**

   * **Load more:** Clicking the “more to explore” button loads next 10 casinos dynamically.
   * **Expand/collapse:** Each casino card expands to show detailed info without affecting other cards.
   * **Filter application:** Update the displayed list instantly based on selected filters.

5. **UX Requirements:**

   * Smooth animations for expand/collapse and filter popup.
   * Responsive design for desktop and mobile.
   * Sorting and filter states must persist until changed or page refreshed.

  here is export of that casino:
add_action( 'acf/include_fields', function() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( array(
	'key' => 'group_6908b6b031925',
	'title' => 'Casinos',
	'fields' => array(
		array(
			'key' => 'field_6908b796ffdd3',
			'label' => 'Image',
			'name' => 'image',
			'aria-label' => '',
			'type' => 'image',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'return_format' => 'array',
			'library' => 'all',
			'min_width' => '',
			'min_height' => '',
			'min_size' => '',
			'max_width' => '',
			'max_height' => '',
			'max_size' => '',
			'mime_types' => '',
			'allow_in_bindings' => 0,
			'preview_size' => 'medium',
		),
		array(
			'key' => 'field_6908b77fffdd2',
			'label' => 'Website',
			'name' => 'website',
			'aria-label' => '',
			'type' => 'link',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'return_format' => 'array',
			'allow_in_bindings' => 0,
		),
		array(
			'key' => 'field_6908b7a6ffdd4',
			'label' => 'Bonus',
			'name' => 'bonus',
			'aria-label' => '',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'maxlength' => '',
			'allow_in_bindings' => 0,
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
		),
		array(
			'key' => 'field_6908b7b2ffdd5',
			'label' => 'Features',
			'name' => 'features',
			'aria-label' => '',
			'type' => 'repeater',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'layout' => 'table',
			'pagination' => 0,
			'min' => 0,
			'max' => 0,
			'collapsed' => '',
			'button_label' => 'Add Row',
			'rows_per_page' => 20,
			'sub_fields' => array(
				array(
					'key' => 'field_6908b7b9ffdd6',
					'label' => 'Feature item',
					'name' => 'feature_item',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'maxlength' => '',
					'allow_in_bindings' => 0,
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'parent_repeater' => 'field_6908b7b2ffdd5',
				),
			),
		),
		array(
			'key' => 'field_6908b7e0ffdd7',
			'label' => 'Rating',
			'name' => 'rating',
			'aria-label' => '',
			'type' => 'number',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'min' => '',
			'max' => 5,
			'allow_in_bindings' => 0,
			'placeholder' => '',
			'step' => '',
			'prepend' => '',
			'append' => '',
		),
		array(
			'key' => 'field_6908b856ffdd9',
			'label' => 'CTA',
			'name' => 'cta',
			'aria-label' => '',
			'type' => 'link',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'return_format' => 'array',
			'allow_in_bindings' => 0,
		),
		array(
			'key' => 'field_6908b870ffdda',
			'label' => 'Wagering Requirement',
			'name' => 'wagering_requirement',
			'aria-label' => '',
			'type' => 'repeater',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'layout' => 'table',
			'pagination' => 0,
			'min' => 0,
			'max' => 0,
			'collapsed' => '',
			'button_label' => 'Add Row',
			'rows_per_page' => 20,
			'sub_fields' => array(
				array(
					'key' => 'field_6908b88bffddb',
					'label' => 'Requirement',
					'name' => 'requirement',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'maxlength' => '',
					'allow_in_bindings' => 0,
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'parent_repeater' => 'field_6908b870ffdda',
				),
			),
		),
		array(
			'key' => 'field_6908bb26862d5',
			'label' => 'Games',
			'name' => 'games',
			'aria-label' => '',
			'type' => 'group',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'layout' => 'block',
			'sub_fields' => array(
				array(
					'key' => 'field_6908bb46862d6',
					'label' => 'All Games',
					'name' => 'all_games',
					'aria-label' => '',
					'type' => 'true_false',
					'instructions' => 'If this casino supports all games, leave this option checked. Otherwise, select only the specific games it offers.',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'message' => '',
					'default_value' => 0,
					'allow_in_bindings' => 0,
					'ui_on_text' => '',
					'ui_off_text' => '',
					'ui' => 1,
				),
				array(
					'key' => 'field_6908bb5b862d7',
					'label' => 'Choose Games',
					'name' => 'choose_games',
					'aria-label' => '',
					'type' => 'relationship',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => array(
						array(
							array(
								'field' => 'field_6908bb46862d6',
								'operator' => '!=',
								'value' => '1',
							),
						),
					),
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'post_type' => array(
						0 => 'game',
					),
					'post_status' => array(
						0 => 'publish',
					),
					'taxonomy' => '',
					'filters' => array(
						0 => 'search',
						1 => 'post_type',
						2 => 'taxonomy',
					),
					'return_format' => 'object',
					'min' => '',
					'max' => '',
					'allow_in_bindings' => 0,
					'elements' => '',
					'bidirectional' => 0,
					'bidirectional_target' => array(
					),
				),
			),
		),
		array(
			'key' => 'field_6908bc23cd0c5',
			'label' => 'Coins',
			'name' => 'coins',
			'aria-label' => '',
			'type' => 'group',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'layout' => 'block',
			'sub_fields' => array(
				array(
					'key' => 'field_6908bc23cd0ca',
					'label' => 'All Coins',
					'name' => 'all_coins',
					'aria-label' => '',
					'type' => 'true_false',
					'instructions' => 'If this casino supports all coins, leave this option checked. Otherwise, select only the specific coins it offers.',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'message' => '',
					'default_value' => 0,
					'allow_in_bindings' => 0,
					'ui_on_text' => '',
					'ui_off_text' => '',
					'ui' => 1,
				),
				array(
					'key' => 'field_6908bc23cd0cb',
					'label' => 'Choose Coins',
					'name' => 'choose_coins',
					'aria-label' => '',
					'type' => 'relationship',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => array(
						array(
							array(
								'field' => 'field_6908bc23cd0ca',
								'operator' => '!=',
								'value' => '1',
							),
						),
					),
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'post_type' => array(
						0 => 'coin',
					),
					'post_status' => array(
						0 => 'publish',
					),
					'taxonomy' => '',
					'filters' => array(
						0 => 'search',
						1 => 'post_type',
						2 => 'taxonomy',
					),
					'return_format' => 'object',
					'min' => '',
					'max' => '',
					'allow_in_bindings' => 0,
					'elements' => '',
					'bidirectional' => 0,
					'bidirectional_target' => array(
					),
				),
			),
		),
		array(
			'key' => 'field_6908b9c34cc42',
			'label' => 'Addittional Features',
			'name' => 'addittional_features',
			'aria-label' => '',
			'type' => 'group',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'layout' => 'block',
			'sub_fields' => array(
				array(
					'key' => 'field_6908b9d74cc43',
					'label' => 'Established',
					'name' => 'established',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'maxlength' => '',
					'allow_in_bindings' => 0,
					'placeholder' => 'e.g. 2017',
					'prepend' => '',
					'append' => '',
				),
				array(
					'key' => 'field_6908ba0e4cc44',
					'label' => 'VPN-friendly?',
					'name' => 'vpn-friendly',
					'aria-label' => '',
					'type' => 'true_false',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'message' => '',
					'default_value' => 0,
					'allow_in_bindings' => 0,
					'ui_on_text' => '',
					'ui_off_text' => '',
					'ui' => 1,
				),
				array(
					'key' => 'field_6908ba4d3e7ce',
					'label' => 'KYC',
					'name' => 'kyc',
					'aria-label' => '',
					'type' => 'true_false',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'message' => '',
					'default_value' => 0,
					'allow_in_bindings' => 0,
					'ui_on_text' => '',
					'ui_off_text' => '',
					'ui' => 1,
				),
				array(
					'key' => 'field_6908ba543e7cf',
					'label' => 'Telegram',
					'name' => 'telegram',
					'aria-label' => '',
					'type' => 'true_false',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'message' => '',
					'default_value' => 0,
					'allow_in_bindings' => 0,
					'ui_on_text' => '',
					'ui_off_text' => '',
					'ui' => 1,
				),
				array(
					'key' => 'field_6908ba5d3e7d0',
					'label' => 'Discord',
					'name' => 'discord',
					'aria-label' => '',
					'type' => 'true_false',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'message' => '',
					'default_value' => 0,
					'allow_in_bindings' => 0,
					'ui_on_text' => '',
					'ui_off_text' => '',
					'ui' => 1,
				),
			),
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'casino',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => true,
	'description' => '',
	'show_in_rest' => 0,
	'display_title' => '',
) );
} );


---

## 5️⃣ Styling

* Use **Sass partials** for each block:

  * `_hero.scss`, `_casino-list.scss`, `_coins.scss`, `_guides.scss`
* Use **BEM naming convention** for classes:

  * `.casino-list__item`, `.casino-list__rating`, etc.
* **Responsive design**:

  * Mobile-first
  * Flexbox / CSS Grid for layout
* **Optional theme skin support** for future customization

---

## 6️⃣ Performance & SEO Optimizations

* **Lazy-load all images** (including previews and logos)
* **Conditional asset loading** per block
* Use **schema.org markup** for casino list:

  * Ratings, bonuses, games
* Use proper heading hierarchy
* Output **clean HTML markup** (no unnecessary wrappers)
* Minify CSS/JS in production

---

## 7️⃣ Header

* **Logo left**, **navigation right**
* Responsive menu: collapsible on mobile
* Support mega menu or multi-level dropdowns
* Enqueue **minimal JS** only for toggle functionality

---

## 8️⃣ Theme Defaults

* **Text domain:** `nebulit`
* **Default preview image:** `/assets/images/default-casino-preview.jpg`
* **Default blocks enabled:** hero + casino list
* **ACF blocks:** dynamic, reusable across pages

---

## 9️⃣ Cursor AI Instructions

* Write concise, modular PHP/HTML
* Use WordPress hooks for extensibility
* Use OOP where appropriate
* Follow WordPress coding standards
* Include comments explaining each major section
* Generate **Sass partials** for each block
* Include **default fallback images** and text
* Include **responsive header** implementation
