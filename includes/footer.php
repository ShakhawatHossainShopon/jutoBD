<footer>

     <div class="mm-footer-top">
         <div class="mm-footer-content-wrapper">

             <!-- 1. Brand and Contact Column -->
             <div class="mm-brand-contact">
                 <h2 class="mm-logo">Zellomarket</h2>

                 <div class="mm-contact-title">Contact Us</div>

                 <!-- WhatsApp -->
                 <div class="mm-contact-item">
                     <div class="mm-contact-row">
                         <svg class="mm-icon" viewBox="0 0 24 24">
                             <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                             <polyline points="7 10 12 15 17 10"></polyline>
                             <line x1="12" y1="15" x2="12" y2="3"></line>
                         </svg>
                         <span>WhatsApp</span>
                     </div>
                     <div class="mm-contact-row" style="margin-left: 28px;">
                          +880 1642956206
                     </div>
                 </div>

                 <!-- Call Us -->
                 <div class="mm-contact-item">
                     <div class="mm-contact-row">
                         <svg class="mm-icon" viewBox="0 0 24 24">
                             <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-4.75-4.75 19.79 19.79 0 0 1-3.07-8.63A2 2 0 0 1 3.08 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                         </svg>
                         <span>Call Us</span>
                     </div>
                      <div class="mm-contact-row" style="margin-left: 28px;">
                         +880 1642956206
                     </div>
                 </div>


             </div>

             <!-- 2. Most Popular Categories Column -->
             <div class="mm-link-column" style="text-transform:capitalize">
                 <h3 class="mm-link-group-title">Most Popular Categories</h3>
                 <ul class="mm-link-list">
                     <?php
                     foreach ($categories as $cat): ?>
                         <li class="mm-link-list-item">
                             <a href="category_page.php?title=<?= urlencode($cat['title']) ?>">
                                 <?= htmlspecialchars($cat['title']) ?>
                             </a>
                         </li>
                     <?php endforeach; ?>
                 </ul>
             </div>


             <!-- 3. Customer Services Column -->
             <div class="mm-link-column" >
                 <h3 class="mm-link-group-title">Customer Services</h3>
                 <ul class="mm-link-list" >
                     <li class="mm-link-list-item"><a href="about-us.php">About Us</a></li>
                     <li class="mm-link-list-item"><a href="faq.php">FAQ</a></li>
                 </ul>
             </div>

         </div>
     </div>

     <!-- --- Footer Bottom Section (Copyright) --- -->
     <div class="mm-footer-bottom">
         &copy; 2025 All rights reserved. <a href="#" class="mm-copyright-link">www.zellomarket.com</a>
     </div>

 </footer>
