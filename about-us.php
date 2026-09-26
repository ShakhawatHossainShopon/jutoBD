<?php
include "./config/config.php";
ob_start();
$page_title = "Zellomarket - About Us";
?>

<main class="about-container" style="font-family:Arial,sans-serif; padding:20px; max-width:1200px; margin:0 auto;">

  <!-- Hero Section -->
  <section class="about-hero" style="text-align:center; padding:40px 20px;">
    <h1 style="font-size:2.5rem; color:#008ECC; margin-bottom:15px;">About MegaMart</h1>
    <p style="font-size:1.1rem; color:#555; max-width:700px; margin:0 auto;">
      At MegaMart, we strive to bring you the best products from groceries to essentials, ensuring quality and reliability for our valued customers.
    </p>
  </section>

  <!-- Our Mission & Vision -->
  <section class="mission-vision" style="display:flex; flex-wrap:wrap; gap:20px; margin:40px 0;">
    <div style="flex:1; min-width:280px; background:#F3F9FB; padding:25px; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.05);">
      <h2 style="color:#008ECC; margin-bottom:10px;">Our Mission</h2>
      <p style="color:#555; line-height:1.6;">To provide high-quality products at competitive prices with fast delivery and excellent customer service.</p>
    </div>
    <div style="flex:1; min-width:280px; background:#F3F9FB; padding:25px; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.05);">
      <h2 style="color:#008ECC; margin-bottom:10px;">Our Vision</h2>
      <p style="color:#555; line-height:1.6;">To be the most trusted online marketplace where customers can find everything they need in one place.</p>
    </div>
  </section>


  <!-- Values Section -->
  <section class="values" style="margin:40px 0; text-align:center;">
    <h2 style="color:#008ECC; margin-bottom:30px;">Our Core Values</h2>
    <div style="display:flex; flex-wrap:wrap; justify-content:center; gap:20px;">
      <div style="flex:1 1 200px; min-width:200px; background:#F3F9FB; padding:20px; border-radius:10px;">
        <h3 style="color:#008ECC;">Quality</h3>
        <p style="color:#555; font-size:0.9rem;">We ensure all products meet strict quality standards.</p>
      </div>
      <div style="flex:1 1 200px; min-width:200px; background:#F3F9FB; padding:20px; border-radius:10px;">
        <h3 style="color:#008ECC;">Integrity</h3>
        <p style="color:#555; font-size:0.9rem;">We operate honestly and transparently in all dealings.</p>
      </div>
      <div style="flex:1 1 200px; min-width:200px; background:#F3F9FB; padding:20px; border-radius:10px;">
        <h3 style="color:#008ECC;">Customer First</h3>
        <p style="color:#555; font-size:0.9rem;">We put our customers’ needs above everything else.</p>
      </div>
    </div>
  </section>

  <!-- Contact CTA -->
  <section class="contact-cta" style="text-align:center; margin:50px 0;">
    <p style="font-size:1.1rem; color:#555; margin-bottom:15px;">Have questions or want to work with us?</p>

  </section>

</main>

<style>
  @media (max-width:768px){
    .mission-vision, .team, .values {
      flex-direction: column !important;
      text-align: center;
    }
  }
</style>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
