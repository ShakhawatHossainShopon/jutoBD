<?php
include "./config/config.php";
ob_start();
$page_title = "Zellomarket - FAQ";
?>

<main class="faq-container" style="font-family:Arial,sans-serif; padding:20px; max-width:900px; margin:0 auto;">

  <!-- Hero Section -->
  <section class="faq-hero" style="text-align:center; padding:40px 20px;">
    <h1 style="font-size:2.5rem; color:#008ECC; margin-bottom:15px;">Frequently Asked Questions</h1>
    <p style="font-size:1.1rem; color:#555; max-width:700px; margin:0 auto;">
      Have questions? Find answers here. Click on a question to view the answer.
    </p>
  </section>

  <!-- FAQ List -->
  <section class="faq-list" style="margin:40px 0;">
    <div class="faq-item" style="margin-bottom:15px; border-radius:8px; overflow:hidden; box-shadow:0 2px 6px rgba(0,0,0,0.05);">
      <button class="faq-question" style="width:100%; text-align:left; padding:15px 20px; background:#F3F9FB; border:none; cursor:pointer; font-size:1rem; color:#008ECC; font-weight:bold;">
        What is MegaMart's delivery time?
      </button>
      <div class="faq-answer" style="padding:15px 20px; display:none; background:#fff; color:#555; line-height:1.5;">
        We offer fast 3-day delivery for most products within Dhaka and 5-7 days for outside Dhaka.
      </div>
    </div>

    <div class="faq-item" style="margin-bottom:15px; border-radius:8px; overflow:hidden; box-shadow:0 2px 6px rgba(0,0,0,0.05);">
      <button class="faq-question" style="width:100%; text-align:left; padding:15px 20px; background:#F3F9FB; border:none; cursor:pointer; font-size:1rem; color:#008ECC; font-weight:bold;">
        Can I return a product?
      </button>
      <div class="faq-answer" style="padding:15px 20px; display:none; background:#fff; color:#555; line-height:1.5;">
        Yes! We have a 30-day return policy. Contact our support to initiate a return.
      </div>
    </div>

    <div class="faq-item" style="margin-bottom:15px; border-radius:8px; overflow:hidden; box-shadow:0 2px 6px rgba(0,0,0,0.05);">
      <button class="faq-question" style="width:100%; text-align:left; padding:15px 20px; background:#F3F9FB; border:none; cursor:pointer; font-size:1rem; color:#008ECC; font-weight:bold;">
        How can I track my order?
      </button>
      <div class="faq-answer" style="padding:15px 20px; display:none; background:#fff; color:#555; line-height:1.5;">
        You can track your order using the tracking number sent to your email after placing the order.
      </div>
    </div>

    <div class="faq-item" style="margin-bottom:15px; border-radius:8px; overflow:hidden; box-shadow:0 2px 6px rgba(0,0,0,0.05);">
      <button class="faq-question" style="width:100%; text-align:left; padding:15px 20px; background:#F3F9FB; border:none; cursor:pointer; font-size:1rem; color:#008ECC; font-weight:bold;">
        Do you offer cash on delivery?
      </button>
      <div class="faq-answer" style="padding:15px 20px; display:none; background:#fff; color:#555; line-height:1.5;">
        Yes, cash on delivery is available for Dhaka and selected cities across Bangladesh.
      </div>
    </div>
  </section>

</main>

<script>
  document.querySelectorAll('.faq-question').forEach(button => {
    button.addEventListener('click', () => {
      const answer = button.nextElementSibling;
      const isOpen = answer.style.display === 'block';
      document.querySelectorAll('.faq-answer').forEach(a => a.style.display = 'none'); // close others
      answer.style.display = isOpen ? 'none' : 'block';
    });
  });
</script>

<style>
  @media (max-width:768px){
    .faq-hero h1 { font-size:2rem; }
    .faq-question { font-size:0.95rem; padding:12px 15px; }
    .faq-answer { padding:12px 15px; }
  }
</style>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
