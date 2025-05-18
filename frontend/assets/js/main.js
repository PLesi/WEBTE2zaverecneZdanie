document.addEventListener('DOMContentLoaded', () => {
    // Redirect to the PDF operations pages
    document.querySelector('[data-i18n="operations.compress"]').addEventListener('click', () => {
        window.location.href = '<?php echo $base_url; ?>/pages/compression.php';
    });

    document.querySelector('[data-i18n="operations.jpg_to_pdf"]').addEventListener('click', () => {
        window.location.href = '<?php echo $base_url; ?>/pages/jpgToPdf.php';
    });

    document.querySelector('[data-i18n="operations.merge"]').addEventListener('click', () => {
        window.location.href = '<?php echo $base_url; ?>/pages/merge.php';
    });

    document.querySelector('[data-i18n="operations.rotate"]').addEventListener('click', () => {
        window.location.href = '<?php echo $base_url; ?>/pages/rotate.php';
    });

    document.querySelector('[data-i18n="operations.number"]').addEventListener('click', () => {
        window.location.href = '<?php echo $base_url; ?>/pages/number.php';
    });

    document.querySelector('[data-i18n="operations.protect"]').addEventListener('click', () => {
        window.location.href = '<?php echo $base_url; ?>/pages/protect.php';
    });

    document.querySelector('[data-i18n="operations.edit"]').addEventListener('click', () => {
        window.location.href = '<?php echo $base_url; ?>/pages/edit.php';
    });

    document.querySelector('[data-i18n="operations.delete_page"]').addEventListener('click', () => {
        window.location.href = '<?php echo $base_url; ?>/pages/deletepage.php';
    });

    document.querySelector('[data-i18n="operations.split"]').addEventListener('click', () => {
        window.location.href = '<?php echo $base_url; ?>/pages/split.php';
    });

    document.querySelector('[data-i18n="operations.rearrange"]').addEventListener('click', () => {
        window.location.href = '<?php echo $base_url; ?>/pages/rearrange.php';
    });
});