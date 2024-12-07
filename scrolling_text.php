<?php
?>
<div class="scrolling-text-container">
    <p class="scrolling-text">
        <strong>Important:</strong> For accurate alerts, use the product web URLs (from browser) instead of app
        URLs when tracking Amazon products.
    </p>
</div>

<style>
    .scrolling-text-container {
        width: 100%;
        padding: 5px 0;
        overflow: hidden;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 9999;
    }

    .scrolling-text {
        white-space: nowrap;
        font-size: 1rem;
        animation: scrollText 25s linear infinite;
    }

    @keyframes scrollText {
        0% {
            transform: translateX(100%);
        }

        100% {
            transform: translateX(-100%);
        }
    }

    @media (max-width: 1200px) {
        .scrolling-text {
            font-size: 0.95rem;
            animation: scrollText 22s linear infinite;
        }
    }

    @media (max-width: 768px) {
        .scrolling-text {
            font-size: 0.75rem;
            animation: scrollText 18s linear infinite;
        }
    }

    @media (max-width: 576px) {
        .scrolling-text {
            font-size: 0.7rem;
            animation: scrollText 12s linear infinite;
            width: calc(100% + 100px);
        }
    }
</style>