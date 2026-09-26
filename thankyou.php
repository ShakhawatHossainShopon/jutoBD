<?php
  include "./config/config.php";
if(!isset($_SESSION['order_success'])){
    header("Location: index.php"); // redirect if accessed directly
    exit;
}

$order = $_SESSION['order_success'];
unset($_SESSION['order_success']); // remove session after showing
ob_start()
?>
<style>
    /* Base Styling & Responsiveness */


    .mm-ty-container { /* Renamed from .email-container */
        margin: 0 auto;
        padding: 20px 0;
    }

    /* Header (Logo/Placeholder) */
    .mm-ty-header { /* Renamed from .header */
        padding: 10px 0 30px 0;
    }

    .mm-ty-logo-placeholder { /* Renamed from .logo-placeholder */
        font-size: 1.2rem;
        font-weight: bold;
        color: #66BB6A; /* Shopify-like green */
        margin-bottom: 5px;
    }

    /* Main Thank You Block */
    .mm-ty-thank-you-title { /* Renamed from .thank-you-title */
        font-family: Georgia, serif; /* Serif font for impact */
        font-size: 44px;
        font-weight: bold;
        margin: 0;
        padding: 20px 0 10px 0;
    }

    .mm-ty-subtitle { /* Renamed from .subtitle */
        font-size: 16px;
        margin-bottom: 40px;
    }

    /* Gift Card Box */
    .mm-ty-gift-box { /* Renamed from .gift-box */
        padding: 20px 0px;
        border-radius: 4px;
        position: relative;
    }

    .mm-ty-gift-box h3 {
        font-size: 18px;
        font-weight: bold;
        margin-top: 0;
        margin-bottom: 20px;
        color: #333333;
    }

    /* Coupon Text */
    .mm-ty-coupon-text { /* Renamed from .coupon-text */
        font-size: 14px;
        color: #666666;
        line-height: 1.4;
        margin-bottom: 30px;
    }

    .mm-ty-coupon-code { /* Renamed from .coupon-code */
        font-weight: bold;
        color: #333333;
    }

    /* Green Button Style */
    .mm-ty-button { /* Renamed from .button */
        display: inline-block;
        background-color: #1E40AF
; /* Muted Green */
        color: white;
        padding: 12px 25px;
        text-decoration: none;
        border-radius: 4px;
        font-weight: bold;
        transition: background-color 0.3s;
        box-shadow: 0 4px 0 0 #1E40AF; /* Darker green shadow for depth */
    }

    .mm-ty-button:hover {
        background-color: #6c9c54; /* Darken on hover */
    }

    /* Responsive adjustments for smaller screens */
    @media only screen and (max-width: 600px) {
        .mm-ty-container {
            max-width: 100%;
            padding: 0 10px;
        }
        .mm-ty-thank-you-title {
            font-size: 36px;
        }
    }
</style>
<div class="main">
  <div class="mm-ty-container">

          <div class="mm-ty-header">
              <div class="mm-ty-logo-placeholder">Megamart Commerce</div>
          </div>

          <h1 class="thank-you-title">Thank You! <strong><?= htmlspecialchars($order['name']) ?></h1>
            <h3>Order Placed Successfully!</h3>

          <!-- Main white content box -->
          <div class="mm-ty-gift-box">
              <a href="index.php?order=processing" class="mm-ty-button">Continue Shopping</a>

          </div>

  </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
