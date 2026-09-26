<style>
    @keyframes scroll-marquee {
        0% { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }
    .animate-scroll-marquee {
        display: flex;
        width: max-content;
        animation: scroll-marquee 25s linear infinite;
    }
    .animate-scroll-marquee:hover {
        animation-play-state: paused;
    }
</style>

<?php
// The 3 logical items for the marquee
$marquee_items = [
    "JUTO: TIMELESS ELEGANCE",
    "JUTO: TIMELESS ELEGANCE",
    "JUTO: TIMELESS ELEGANCE"
];
?>

<!-- Infinite Scrolling Marquee -->
<section class="w-full bg-[#eee9df] py-4 sm:py-5 overflow-hidden flex items-center border-t border-b border-[#e5dfd3] z-10 relative">
    <div class="animate-scroll-marquee">
        <!-- We duplicate the 3 items multiple times just to ensure the seamless CSS loop works on ultra-wide screens -->
        <?php for($loop=0; $loop<4; $loop++): ?>
            <?php foreach($marquee_items as $item): ?>
                <div class="flex items-center">
                    
                    <!-- Text with large margins so exactly ~3 fit on screen visually -->
                    <span class="font-serif text-[#4d3c31] text-[13px] sm:text-[15px] tracking-[0.15em] uppercase mx-12 md:mx-24 lg:mx-32 font-normal">
                        <?= $item ?>
                    </span>
                    
                    <!-- Asterisk Separator -->
                    <div class="text-[#4d3c31]">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="3" x2="12" y2="21"></line>
                            <line x1="5.64" y1="5.64" x2="18.36" y2="18.36"></line>
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="5.64" y1="18.36" x2="18.36" y2="5.64"></line>
                        </svg>
                    </div>
                    
                </div>
            <?php endforeach; ?>
        <?php endfor; ?>
    </div>
</section>
