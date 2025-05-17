// Placeholder pre hlavnú JS logiku
document.addEventListener('DOMContentLoaded', () => {
    console.log('Main JS loaded');
    // Redirect to the compression page when the button is clicked
    document.querySelector('[data-i18n="operations.compress"]').addEventListener('click', () => {
        window.location.href = '<?php echo $base_url; ?>/pages/compression.php';
    });
});