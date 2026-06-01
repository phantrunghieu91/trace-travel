<?php
/**
 * WC_Category_Hero_Banner
 *
 * Adds a hero banner image field to WooCommerce product category
 * add/edit forms. Saves the attachment ID as term meta.
 *
 * Usage — add to your theme's functions.php:
 *
 *   require_once get_template_directory() . '/inc/class-wc-category-hero-banner.php';
 *   WC_Category_Hero_Banner::init();
 *
 * Retrieve the image in templates:
 *
 *   $url = WC_Category_Hero_Banner::get_image_url( $term->term_id, 'full' );
 *   $id  = WC_Category_Hero_Banner::get_image_id( $term->term_id );
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'WC_Category_Hero_Banner' ) ) {
    return;
}

class WC_Category_Hero_Banner {

    /** Term-meta key used to store the attachment ID. */
    const META_KEY = '_gpw_hero_banner_id';

    /**
     * Register all hooks. Call once from functions.php.
     */
    public static function init() {
        $instance = new self();

        add_action( 'product_cat_add_form_fields',  [ $instance, 'render_add_form_field' ] );
        add_action( 'product_cat_edit_form_fields', [ $instance, 'render_edit_form_field' ], 10, 1 );
        add_action( 'created_product_cat',          [ $instance, 'save' ] );
        add_action( 'edited_product_cat',           [ $instance, 'save' ] );
        add_action( 'admin_enqueue_scripts',        [ $instance, 'enqueue_assets' ] );
        add_action( 'admin_footer',                 [ $instance, 'print_l10n' ] );
    }

    // -------------------------------------------------------------------------
    // Public helpers (use these in templates)
    // -------------------------------------------------------------------------

    /**
     * Return the hero banner attachment ID for a category, or false if unset.
     *
     * @param  int        $term_id
     * @return int|false
     */
    public static function get_image_id( $term_id ) {
        $id = get_term_meta( (int) $term_id, self::META_KEY, true );
        return $id ? (int) $id : false;
    }

    /**
     * Return the hero banner image URL for a category, or '' if unset.
     *
     * @param  int    $term_id
     * @param  string $size     Any registered image size (default 'full').
     * @return string
     */
    public static function get_image_url( $term_id, $size = 'full' ) {
      $id = self::get_image_id( $term_id );
      return $id ? (string) wp_get_attachment_image_url( $id, $size ) : '';
    }

    // -------------------------------------------------------------------------
    // Admin field rendering
    // -------------------------------------------------------------------------

    /** "Add New Category" form — no table, uses <div class="form-field">. */
    public function render_add_form_field() {
      ?>
      <div class="form-field wchb-field-wrap">
          <label for="wchb_hero_banner_id">
              <?php esc_html_e( 'Hero Banner Image', 'wc-category-hero-banner' ); ?>
          </label>
          <?php $this->the_field_html( '', '' ); ?>
          <p class="description">
              <?php esc_html_e( 'Upload or choose a hero banner image for this category.', 'wc-category-hero-banner' ); ?>
          </p>
      </div>
      <?php
    }

    /** "Edit Category" form — uses <tr> table row. */
    public function render_edit_form_field( $term ) {
      $image_id  = self::get_image_id( $term->term_id );
      $image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'medium' ) : '';
      ?>
      <tr class="form-field wchb-field-wrap">
          <th scope="row">
              <label for="wchb_hero_banner_id">
                  <?php esc_html_e( 'Hero Banner Image', 'wc-category-hero-banner' ); ?>
              </label>
          </th>
          <td>
              <?php $this->the_field_html( $image_id, $image_url ); ?>
              <p class="description">
                  <?php esc_html_e( 'Upload or choose a hero banner image for this category.', 'wc-category-hero-banner' ); ?>
              </p>
          </td>
      </tr>
      <?php
    }

    /**
     * Echo the shared field markup (hidden input + preview block + buttons).
     *
     * @param int|string $image_id   Attachment ID, or '' when empty.
     * @param string     $image_url  Preview URL (medium size), or '' when empty.
     */
    private function the_field_html( $image_id, $image_url ) {
      $has_image     = ! empty( $image_url );
      $preview_style = $has_image ? ' style="background-image:url(' . esc_url( $image_url ) . ');"' : '';
      ?>
      <div class="wchb-uploader" id="wchb-uploader-wrap">

          <!-- Stores the attachment ID, submitted with the category form -->
          <input
              type="hidden"
              name="wchb_hero_banner_id"
              id="wchb_hero_banner_id"
              value="<?php echo esc_attr( $image_id ); ?>"
          />

          <!-- Preview block -->
          <div
              class="wchb-preview<?php echo $has_image ? ' wchb-preview--has-image' : ''; ?>"
              id="wchb-preview"
              <?php echo $preview_style; ?>
          >
              <!-- Placeholder shown when no image is selected -->
              <span class="wchb-preview__placeholder"<?php echo $has_image ? ' style="display:none;"' : ''; ?>>
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                      <rect x="3" y="3" width="18" height="18" rx="2"/>
                      <circle cx="8.5" cy="8.5" r="1.5"/>
                      <polyline points="21 15 16 10 5 21"/>
                  </svg>
                  <span><?php esc_html_e( 'No image selected', 'wc-category-hero-banner' ); ?></span>
              </span>

              <!-- Remove button — top-right corner, visible only when an image is set -->
              <button
                  type="button"
                  class="wchb-btn wchb-btn--remove"
                  id="wchb-remove-btn"
                  <?php echo $has_image ? '' : 'style="display:none;"'; ?>
                  title="<?php esc_attr_e( 'Remove image', 'wc-category-hero-banner' ); ?>"
                  aria-label="<?php esc_attr_e( 'Remove hero banner image', 'wc-category-hero-banner' ); ?>"
              >
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                      <line x1="18" y1="6" x2="6" y2="18"/>
                      <line x1="6" y1="6" x2="18" y2="18"/>
                  </svg>
              </button>
          </div>

          <!-- Choose / Change image button -->
          <button
              type="button"
              class="wchb-btn wchb-btn--choose button"
              id="wchb-choose-btn"
          >
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                  <polyline points="17 8 12 3 7 8"/>
                  <line x1="12" y1="3" x2="12" y2="15"/>
              </svg>
              <span id="wchb-choose-label">
                  <?php echo $has_image
                      ? esc_html__( 'Change Image', 'wc-category-hero-banner' )
                      : esc_html__( 'Choose Image', 'wc-category-hero-banner' );
                  ?>
              </span>
          </button>

      </div>
      <?php
    }

    // -------------------------------------------------------------------------
    // Save
    // -------------------------------------------------------------------------

    /**
     * Persist (or delete) the hero banner attachment ID.
     *
     * @param int $term_id
     */
    public function save( $term_id ) {
        if ( ! isset( $_POST['wchb_hero_banner_id'] ) ) {
            return;
        }

        $image_id = absint( $_POST['wchb_hero_banner_id'] );

        if ( $image_id ) {
            update_term_meta( $term_id, self::META_KEY, $image_id );
        } else {
            delete_term_meta( $term_id, self::META_KEY );
        }
    }

    // -------------------------------------------------------------------------
    // Assets
    // -------------------------------------------------------------------------

    /**
     * Enqueue the WP media uploader and inject inline CSS/JS.
     * Fires only on product_cat taxonomy screens.
     *
     * @param string $hook  Current admin page hook.
     */
    public function enqueue_assets( $hook ) {
        if ( ! in_array( $hook, [ 'edit-tags.php', 'term.php' ], true ) ) {
            return;
        }

        $screen = get_current_screen();
        if ( ! $screen || 'product_cat' !== $screen->taxonomy ) {
            return;
        }

        wp_enqueue_media();
        wp_add_inline_style( 'wp-admin', $this->inline_css() );
        wp_add_inline_script( 'media-upload', $this->inline_js() );
    }

    /** Print JS localisation object — only on product_cat screens. */
    public function print_l10n() {
        $screen = get_current_screen();
        if ( ! $screen || 'product_cat' !== $screen->taxonomy ) {
            return;
        }
        ?>
        <script>
        var wchbL10n = {
            modalTitle:  <?php echo wp_json_encode( __( 'Select Hero Banner Image', 'wc-category-hero-banner' ) ); ?>,
            modalButton: <?php echo wp_json_encode( __( 'Use this image',           'wc-category-hero-banner' ) ); ?>,
            chooseImage: <?php echo wp_json_encode( __( 'Choose Image',             'wc-category-hero-banner' ) ); ?>,
            changeImage: <?php echo wp_json_encode( __( 'Change Image',             'wc-category-hero-banner' ) ); ?>
        };
        </script>
        <?php
    }

    // -------------------------------------------------------------------------
    // Inline CSS
    // -------------------------------------------------------------------------

    private function inline_css() {
        return '
/* ── WC_Category_Hero_Banner ─────────────────────────────── */
.wchb-uploader {
    display: flex;
    flex-direction: column;
    gap: 10px;
    max-width: 480px;
}

.wchb-preview {
    position: relative;
    width: 100%;
    height: 180px;
    border: 2px dashed #c3c4c7;
    border-radius: 6px;
    background-color: #f6f7f7;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    transition: border-color 0.2s ease;
}

.wchb-preview--has-image {
    border-style: solid;
    border-color: #2271b1;
}

.wchb-preview__placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    color: #8c8f94;
    pointer-events: none;
    user-select: none;
}
.wchb-preview__placeholder svg {
    width: 40px;
    height: 40px;
    opacity: 0.5;
}
.wchb-preview__placeholder span {
    font-size: 13px;
}

.wchb-btn--remove {
    position: absolute;
    top: 8px;
    right: 8px;
    width: 28px;
    height: 28px;
    padding: 0;
    border: none;
    border-radius: 50%;
    background: rgba(0,0,0,0.55);
    color: #fff;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.15s ease;
    line-height: 1;
    box-shadow: 0 1px 4px rgba(0,0,0,0.35);
}
.wchb-btn--remove:hover { background: #d63638; }
.wchb-btn--remove svg {
    width: 14px;
    height: 14px;
    display: block;
    pointer-events: none;
}

.wchb-btn--choose {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    align-self: flex-start;
}
.wchb-btn--choose svg {
    width: 15px;
    height: 15px;
    flex-shrink: 0;
}
/* ── end WC_Category_Hero_Banner ─────────────────────────── */
';
    }

    // -------------------------------------------------------------------------
    // Inline JavaScript
    // -------------------------------------------------------------------------

    private function inline_js() {
        return '
(function ($) {
    "use strict";

    $(function () {
        var frame;
        var $wrap    = $("#wchb-uploader-wrap");
        if (!$wrap.length) return;

        var $input   = $("#wchb_hero_banner_id");
        var $preview = $("#wchb-preview");
        var $choose  = $("#wchb-choose-btn");
        var $label   = $("#wchb-choose-label");
        var $remove  = $("#wchb-remove-btn");
        var $holder  = $preview.find(".wchb-preview__placeholder");

        function setImage(id, url) {
            $input.val(id);
            $preview.css("background-image", "url(" + url + ")");
            $preview.addClass("wchb-preview--has-image");
            $holder.hide();
            $remove.show();
            $label.text(wchbL10n.changeImage);
        }

        function clearImage() {
            $input.val("");
            $preview.css("background-image", "");
            $preview.removeClass("wchb-preview--has-image");
            $holder.show();
            $remove.hide();
            $label.text(wchbL10n.chooseImage);
        }

        $choose.on("click", function (e) {
            e.preventDefault();
            if (frame) { frame.open(); return; }

            frame = wp.media({
                title:    wchbL10n.modalTitle,
                button:   { text: wchbL10n.modalButton },
                multiple: false,
                library:  { type: "image" }
            });

            frame.on("select", function () {
                var att = frame.state().get("selection").first().toJSON();
                var url = (att.sizes && att.sizes.medium) ? att.sizes.medium.url : att.url;
                setImage(att.id, url);
            });

            frame.open();
        });

        $remove.on("click", function (e) {
            e.preventDefault();
            clearImage();
        });
    });
})(jQuery);
';
    }
}