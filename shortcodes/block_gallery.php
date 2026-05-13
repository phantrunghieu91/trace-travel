<?php
add_shortcode('gallery_short_code', function ()
{
    ob_start();
    $gallery = get_field('homepage_gallery', 'option');
?>
    <section class="block-galery">
        <div class="list-gal aos-init aos-animate" data-aos="zoom-in">
            <?php
            if (!empty($gallery)) {
                foreach ($gallery as $key => $img) {
                    if ($key <= 5) {
            ?>
                        <a class="img" data-fancybox="imggal" data-caption="IMAGE" href="<?= $img['url'] ?>">
                            <img src="<?= $img['url'] ?>" alt="IMAGE">
                        </a>
            <?php
                    }
                }
            }
            ?>
        </div>
        <?php
        foreach ($gallery as $key => $img) {
            if ($key == 6) {
        ?>
                <a href="<?= $img['url'] ?>" data-fancybox="imggal" data-caption="IMAGE" class="btn-df btn-viewall">
                    <img src="<?= $img['url'] ?>" alt="IMAGE" style="display: none;">

                    Xem thêm thư viện ảnh <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">

                        <path d="M5 12H19" stroke="#F1B71C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M14 7L19 12L14 17" stroke="#F1B71C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </a>
            <?php
            } elseif ($key >= 7) {
            ?>
                <a href="<?= $img['url'] ?>" data-fancybox="imggal" data-caption="IMAGE">
                    <img src="<?= $img['url'] ?>" alt="IMAGE" style="display: none;">

                </a>
        <?php
            }
        }
        ?>
    </section>
<?php
    return ob_get_clean();
});
