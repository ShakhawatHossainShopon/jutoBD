<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css"/>
<link rel="icon" href="./includes/logo/favicon.png" type="image/png">
<title><?= isset($page_title) ? $page_title : 'Zellomarket' ?></title>
<style>
:root {
  --primary-bg-color: #65b741;
  --primary-bg-color-hover: #bbc863;
}
#slider-wrapper::-webkit-scrollbar {
  display: none; /* Chrome, Safari, Opera */
}
#slider-wrapper {
  -ms-overflow-style: none;  /* IE, Edge */
  scrollbar-width: none;     /* Firefox */
}
* {
  box-sizing: border-box;
  padding: 0;
  margin: 0;
  font-family: Arial, sans-serif; /* default font */
  line-height: 1.5; /* better readability */
}

body {
  background-color: white; /* optional background */
  color: #333; /* default text color */
  padding: 0;
  margin:0;
}

img,
video {
  max-width: 100%;
  height: auto;
  display: block;
}

a {
  text-decoration: none;
  color: inherit;
}
.text-sm{
  color: #222222;
  font-size: 14px;
}
.border{
  border: 1px solid red;
}


.upper-header{
  display:flex;
  justify-content:space-between;
  padding:7px 120px;
  background-color:#EBEBEB
}
header {
  position: sticky;
  top: 0;
  background: white; /* or any color */
  z-index: 1000;     /* ensures it stays above other content */
}


.middle-nav {
  display: flex;
  width: 100%;
  align-items: center;
  justify-content: space-between;
  padding: 16px 120px;
  background-color: white;
  border-bottom: 1px solid #E5E7EB;
}


.nav-search{
flex:1;
margin:0 90px;
position:relative;
}
.cart{
  display:flex;
  align-items:center;
  gap:20px;
  font-size:14px;
  color:#008ECC;
}
.bottom-nav {
  display: none;
  justify-content: space-around;
  color: white;
  background-color: white;
  padding: 16px;
  position: fixed;
  bottom: 0;
  border: none;
  outline: none;
  width: 100%;
  border-top: 2px solid rgba(0, 142, 204, 0.1); /* 0.3 is 30% opacity */
}
.bottom-nav i{
  color: #1D4ED8;
  font-size: 20px

}

.swiper {
  width: 100%;
  height: 360px; /* full viewport height */
  margin: 0;
  border-radius: 16px; /* remove if you want full-bleed */
  margin: 1rem 0rem;
}

.swiper-slide {
  display: flex;
  align-items: center;
  justify-content: center;
  background-size: cover; /* makes images cover entire slide */
  background-position: center;
}

.swiper-slide img {
  width: 100%;
  height: 100%;
  object-fit: cover; /* keeps image ratio but fills slide */
}

/* Make buttons bigger and circular */
.swiper-button-next,
.swiper-button-prev {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  color: #D1D5DB;
  display: flex;
  align-items: center;
  justify-content: center;
  top: 60%;
  transform: translateY(-60%);
  transition: all 0.3s ease;
}

/* Hover effect */
.swiper-button-next:hover,
.swiper-button-prev:hover {
  background-color: white; /* solid blue on hover */
  scale: 1.1;
}

/* Position arrows inside the circle */
.swiper-button-next::after,
.swiper-button-prev::after {
  font-size: 16px; /* make arrow bigger */
  font-weight: bold;
}

.swiper-pagination-bullets .swiper-pagination-bullet {
  background: white;
  opacity: 0.6;
}

.swiper-pagination-bullets .swiper-pagination-bullet-active {
  opacity: 1;
}
.main{
  padding:0rem 120px
}

.category-btn {
  white-space: nowrap;       /* keep text in one line */
  padding: 7px 15px;         /* horizontal and vertical padding */
  border-radius: 20px;
  border: none;
  cursor: pointer;
  font-size: 12px;
  font-weight: 600;
  background: #F3F4F6;
  color: #4B5563;
  flex-shrink: 0;            /* prevent shrinking in scroll */
  text-transform: capitalize;
  transition: background-color 0.8s ease;
}
.category-btn:hover{
  background-color: #008ECC;
  color: white;
}
.category-btn.active {
  background: #008ECC;
  color: #fff;
}


        .deals-section-container {
            max-width: 100%;
            margin: 0 auto;
            margin-top: 2rem;
        }

        /* Header Styling */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding-bottom: 8px;
            margin-bottom: 20px;
            border-bottom: 1px solid #ccc;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }

        .title-highlight {
            color: #00000; /* Primary Blue */
            position: relative;
            display: inline-block;
            text-transform: capitalize;
        }

        .title-highlight::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -8px; /* Position below the text */
            width: 100%;
            height: 2px;
            background-color: #007bff;
        }

        .view-all-link {
            color: #1D4ED8;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
        }

        .view-all-link:hover {
            text-decoration: underline;
        }

        /* --- Grid and Card Styling --- */
        .product-carousel {
            display: grid;
            /* FIX: Default for smallest screens (mobile) is now 2 columns */
            grid-template-columns: repeat(1, 1fr);
            gap: 20px; /* Spacing between cards */
            padding-bottom: 10px;
        }

        /* Removed 550px breakpoint, now 2 columns is the standard mobile layout. */
        @media (min-width: 320px) {
            .product-carousel {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        /* 3 columns on tablet */
        @media (min-width: 768px) {
            .product-carousel {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        /* 4 columns on medium desktop */
        @media (min-width: 1024px) {
            .product-carousel {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        /* 5 columns on wide desktop (as requested) */
        @media (min-width: 1200px) {
            .product-carousel {
                grid-template-columns: repeat(5, 1fr);
            }
        }

        .product-card {
            /* Width is now controlled purely by the grid (1fr) */
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            transition: box-shadow 0.3s;
            border: 1px solid #e0e0e0;
            cursor: pointer;
            outline: none;
        }

        .product-card:hover {
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
            border: 1px solid #007bff; /* Highlighted blue border */
            padding: 0;
        }


        /* Discount Sticker */
        .discount-sticker {
            position: absolute;
            top: 0;
            right: 0;
            background-color: #007bff;
            color: white;
            font-size: 12px;
            font-weight: bold;
            padding: 4px 8px;
            border-bottom-left-radius: 8px;
            z-index: 10;
        }

        /* Image Placeholder */
        .product-image-container {
            height: 200px;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f8f8f8;
            padding: 10px;
        }

        .product-image-container img {
            max-height: 100%;
            object-fit: contain;
        }

        /* Info Section */
        .product-info {
            padding: 10px;
            padding-top: 5px;
        }

        .product-name {
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 4px;
        }

        .storage-text {
            font-size: 12px;
            color: #666;
            font-weight: normal;
        }

        .price-row {
            display: flex;
            align-items: baseline;
            gap: 8px;
            margin-top: 4px;
        }

        .current-price {
            font-size: 16px;
            font-weight: bold;
            color: #f57224;
        }

        .old-price {
            font-size: 12px;
            color: #888;
        }

        .save-text {
            font-size: 14px;
            color: #28a745; /* Green */
            font-weight: 500;
            margin-top: 4px;
        }

        /* Media Queries for Responsiveness */
        @media (min-width: 768px) {
            .title {
                font-size: 18px;
            }
        }
@media (max-width: 1024px) {
  .upper-header {
    display: none !important;
  }
  .middle-nav{
      padding:16px 4%;
  }
  .swiper{
      padding:none;
  }
  .nav-search{
    display: none !important;
  }
  .cart span{
    display: none !important;
  }
    .bottom-nav { display: flex; }
    .main{padding: 0 4%}

    .swiper{
        height: 200px;
    }
    .prev-nav{
      display: none;
    }
}

.mm-footer-top {
      background-color: #007bff; /* Bright Blue */
      color: white;
      padding: 40px 120px;
      position: relative;
      overflow: hidden; /* Contains the pseudo-element circle */
  }

  /* Large decorative circle on the right (similar to the image) */
  .mm-footer-top::after {
      content: '';
      position: absolute;
      top: 0;
      right: 0;
      width: 300px;
      height: 300px;
      background-color: rgba(255, 255, 255, 0.1);
      border-radius: 50%;
      transform: translate(50%, -50%);
      pointer-events: none;
      z-index: 1;
  }

  .mm-footer-content-wrapper {
      max-width: 1200px;
      margin: 0 auto;
      display: grid;
      gap: 40px;
      position: relative;
      z-index: 2; /* Ensures content is above the circle */
  }

  /* Desktop Layout: 1 column for Brand/Contact, 2 columns for Links */
  @media (min-width: 768px) {
      .mm-footer-content-wrapper {
          grid-template-columns: 1fr 1fr 1fr; /* 3 main columns on desktop */
      }
  }
  @media (max-width: 768px) {
      .mm-footer-top{
        padding:2rem 7% !important;
      }
  }

  /* --- Brand and Contact Column --- */
  .mm-brand-contact {
      display: flex;
      flex-direction: column;
      gap: 20px;
  }

  .mm-logo {
      font-size: 30px;
      font-weight: bold;
      margin-bottom: 20px;
  }

  .mm-contact-item {
      display: flex;
      flex-direction: column;
      gap: 4px;
  }

  .mm-contact-title {
      font-weight: bold;
      margin-bottom: 5px;
  }

  .mm-contact-row {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 14px;
  }

  /* Icon styles */
  .mm-icon {
      width: 18px;
      height: 18px;
      fill: none;
      stroke: white;
      stroke-width: 2;
      stroke-linecap: round;
      stroke-linejoin: round;
  }

  /* App Download Section */
  .mm-app-download {
      margin-top: 15px;
  }

  .mm-app-title {
      font-weight: bold;
      margin-bottom: 15px;
  }

  .mm-app-links {
      display: flex;
      gap: 10px;
  }

  .mm-app-link-img {
      width: 120px;
      border-radius: 6px;
      overflow: hidden;
      transition: opacity 0.2s;
  }

  .mm-app-link-img:hover {
      opacity: 0.8;
  }

  /* --- Link Columns Styling --- */
  .mm-link-group-title {
      font-size: 18px;
      font-weight: bold;
      padding-bottom: 5px;
      margin-bottom: 15px;
      border-bottom: 2px solid rgba(255, 255, 255, 0.5);
      display: inline-block;
  }

  .mm-link-list {
      list-style: none;
      padding: 0;
      margin: 0;
      line-height: 2.2; /* Spacing between list items */
  }

  .mm-link-list-item a {
      color: white;
      text-decoration: none;
      font-size: 14px;
      transition: color 0.2s;
  }

  .mm-link-list-item a:hover {
      color: #ccc;
  }

  /* --- Footer Bottom Section (Copyright) --- */
  .mm-footer-bottom {
      background-color: black; /* Light gray from the image */
      color: #ccc;
      text-align: center;
      padding: 15px 20px;
      font-size: 12px;
  }

  .mm-copyright-link {
      color: white;
      text-decoration: none;
  }



          /* Container and Layout */
          .pdp-container {
              max-width: 700px; /* Reduced max width for a smaller feel */
              margin: 20px auto;
              padding: 15px;
              background-color: #fff;
              border-radius: 8px;
              box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
          }

          .pdp-grid {
              display: flex;
              flex-direction: column;
              gap: 20px;
          }

          /* Header - Simple Nav */
          .pdp-header {
              background-color: #fff;
              border-bottom: 1px solid #eee;
              padding: 10px 0;
              text-align: center;
              font-size: 1.2rem;
              font-weight: bold;
          }

          /* Images */
          .pdp-main-image-area {
              width: 100%;
              aspect-ratio: 1 / 1;
              overflow: hidden;
              border-radius: 6px;
              background-color: #eee;
          }
          #main-product-image {
              width: 100%;
              height: 100%;
              object-fit: cover;
              transition: opacity 0.3s;
          }

          .pdp-thumbnail-gallery {
              display: grid;
              grid-template-columns: repeat(4, 1fr);
              gap: 5px;
              margin-top: 10px;
          }
          .pdp-thumbnail {
              width: 100%;
              height: auto;
              border-radius: 4px;
              cursor: pointer;
              border: 2px solid transparent;
              opacity: 0.8;
              transition: all 0.15s;
          }
          .pdp-thumbnail:hover {
              opacity: 1;
              border-color: #007bff;
          }
          .pdp-thumbnail-active {
              border-color: #007bff;
              opacity: 1;
              box-shadow: 0 0 0 1px #007bff;
          }

          /* Product Info */
          h1 {
              font-size: 1.5rem; /* Smaller font */
              margin-top: 0;
              margin-bottom: 5px;
          }

          .pdp-rating-price {
              display: flex;
              align-items: center;
              margin-bottom: 10px;
          }
          .pdp-star {
              color: orange;
              font-size: 1rem;
              margin-right: 2px;
          }
          .pdp-review-count {
              font-size: 0.8rem;
              color: #666;
              margin-left: 10px;
          }

          .pdp-price {
              font-size: 1.8rem; /* Smaller font */
              font-weight: bold;
              color: #333;
              margin-bottom: 15px;
          }

          /* Variants */
          .pdp-variant-group {
              margin-bottom: 15px;
          }
          .pdp-variant-group p {
              font-size: 0.9rem;
              margin-bottom: 5px;
          }

          .pdp-color-swatch, .pdp-size-swatch {
              border: 1px solid #ccc;
              border-radius: 50%;
              cursor: pointer;
              transition: all 0.2s;
              margin-right: 5px;
              padding: 0;
              display: inline-block;
              vertical-align: middle;
          }
          .pdp-color-swatch {
              width: 25px; /* Smaller swatches */
              height: 25px;
          }
          .pdp-size-swatch {
              border-radius: 4px;
              padding: 4px 10px; /* Smaller padding */
              font-size: 0.8rem;
              min-width: 30px;
              text-align: center;
          }

          .pdp-variant-selected {
              border-color: #007bff !important;
              box-shadow: 0 0 0 2px #007bff;
          }

          /* Quantity and CTA */
          .pdp-qty-cta-row {
              display: flex;
              flex-direction: column;
              gap: 10px;
              margin-bottom: 20px;
          }
          .pdp-qty-selector {
              display: flex;
              border: 1px solid #ccc;
              border-radius: 4px;
              width: 100%;
          }
          .pdp-qty-selector button {
              background: #f4f4f4;
              border: none;
              padding: 8px; /* Smaller padding */
              cursor: pointer;
              font-size: 1rem;
              color: #333;
              transition: background 0.1s;
          }
          .pdp-qty-selector button:hover {
              background: #ddd;
          }
          #qty-input {
              text-align: center;
              width: 40px;
              border: none;
              font-size: 1rem;
              padding: 5px 0;
              -moz-appearance: textfield; /* Hide arrows in Firefox */
          }
          #qty-input::-webkit-outer-spin-button,
          #qty-input::-webkit-inner-spin-button {
              -webkit-appearance: none;
              margin: 0;
          }

          #add-to-cart-btn {
              background-color: #FF8C42;
              color: #f9fafb;
              border: none;
              padding: 8px; /* Smaller padding */
              font-size: 14px;
              font-weight: bold;
              border-radius: 4px;
              cursor: pointer;
              transition: background-color 0.2s;
              text-transform: uppercase;
              flex-grow: 1;
              text-align: center;
          }
          #add-to-cart-btn:hover {
              background-color: #0056b3;
          }

          /* Trust Points */
          .pdp-trust-points {
              border-top: 1px solid #eee;
              padding-top: 15px;
          }
          .pdp-trust-item {
              display: flex;
              align-items: flex-start;
              margin-bottom: 8px;
              font-size: 0.85rem;
          }
          .pdp-trust-item strong {
              font-weight: bold;
              color: #007bff;
              margin-right: 5px;
          }

          /* Description */
          .pdp-description-section {
              border-top: 1px solid #eee;
              padding-top: 20px;
              margin-top: 20px;
          }
          .pdp-description-section h2 {
              font-size: 1.2rem; /* Smaller font */
              margin-top: 0;
              margin-bottom: 10px;
          }
          .pdp-features-list {
              list-style: none;
              padding: 0;
              font-size: 0.9rem;
          }
          .pdp-features-list li {
              margin-bottom: 5px;
              padding-left: 15px;
              position: relative;
          }
          .pdp-features-list li::before {
              content: '✓';
              color: #007bff;
              position: absolute;
              left: 0;
          }

          /* Modal Styling */
          .pdp-modal {
              position: fixed;
              top: 0;
              left: 0;
              width: 100%;
              height: 100%;
              background: rgba(0, 0, 0, 0.7);
              display: none; /* Controlled by JS */
              align-items: center;
              justify-content: center;
              z-index: 1000;
          }
          .pdp-modal-content {
              background: white;
              padding: 20px;
              border-radius: 8px;
              box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
              max-width: 300px;
              width: 90%;
              text-align: center;
              transform: scale(0.9);
              opacity: 0;
              transition: all 0.3s ease-in-out;
          }
          .pdp-modal.pdp-modal-show .pdp-modal-content {
              transform: scale(1);
              opacity: 1;
          }
          .pdp-modal-content h3 {
              color: green;
              font-size: 1.1rem;
              margin-top: 0;
          }
          #modal-close-btn {
              background-color: #007bff;
              color: white;
              border: none;
              padding: 8px 15px;
              border-radius: 4px;
              cursor: pointer;
              margin-top: 15px;
              width: 100%;
              font-size: 0.9rem;
          }
          #modal-message {
              font-size: 0.9rem;
          }

          /* Responsive adjustments for desktop */
          @media (min-width: 600px) {
              .pdp-grid {
                  flex-direction: row;
              }
              .pdp-grid > div {
                  flex: 1;
              }
              .pdp-qty-cta-row {
                  flex-direction: row;
                  align-items: center;
              }
              .pdp-qty-selector {
                  width: 120px;
              }
          }
          .pdp-container {
              margin: 20px auto;
              padding: 20px 20px 60px 20px;
              background-color: #fff;
              border-radius: 8px;
              box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
          }

          /* Grid Layout for Form and Summary */
          .order-form-grid {
              display: flex;
              flex-direction: column;
              gap: 25px;
          }

          /* Form Styling */
          .form-section h2 {
              font-size: 1.2rem;
              margin-bottom: 15px;
              border-bottom: 1px solid #eee;
              padding-bottom: 5px;
          }

          .form-group {
              margin-bottom: 15px;
          }

          .form-group label {
              display: block;
              font-size: 0.9rem;
              font-weight: bold;
              margin-bottom: 5px;
          }

          .form-group input, .form-group select {
              width: 100%;
              padding: 8px;
              border: 1px solid #ccc;
              border-radius: 4px;
              box-sizing: border-box;
              font-size: 0.9rem;
          }

          .form-row {
              display: flex;
              gap: 15px;
          }

          .form-row > .form-group {
              flex: 1;
          }

          /* Shipping Options (Radio Buttons) */
          .shipping-options {
              border: 1px solid #eee;
              padding: 10px;
              border-radius: 6px;
          }
          .shipping-option {
              display: flex;
              align-items: center;
              justify-content: space-between;
              padding: 8px 0;
              border-bottom: 1px dashed #eee;
              cursor: pointer;
          }
          .shipping-option:last-child {
              border-bottom: none;
          }
          .shipping-option input[type="radio"] {
              margin-right: 10px;
              width: auto;
          }
          .shipping-option label {
              flex-grow: 1;
              font-weight: normal;
          }

          /* Order Summary Box */
          .order-summary {
              background-color: #f4f4f4;
              padding: 15px;
              border-radius: 6px;
              font-size: 0.9rem;
              border: 1px solid #ddd;
          }
          .order-summary h2 {
              margin-top: 0;
              color: #007bff;
          }
          .summary-item {
              display: flex;
              justify-content: space-between;
              padding: 4px 0;
              border-bottom: 1px solid #eee;
          }
          .summary-item:last-of-type {
              border-bottom: none;
          }
          .summary-total {
              font-size: 1.1rem;
              font-weight: bold;
              margin-top: 10px;
              padding-top: 10px;
              border-top: 2px solid #ccc;
          }

          /* Quantity Selector (Reused style) */
          .pdp-qty-selector {
              display: flex;
              border: 1px solid #ccc;
              border-radius: 4px;
              width: 120px; /* Fixed small width */
          }
          .pdp-qty-selector button {
              background: #f4f4f4;
              border: none;
              padding: 8px;
              cursor: pointer;
              font-size: 1rem;
              color: #333;
              transition: background 0.1s;
          }
          .pdp-qty-selector button:hover {
              background: #ddd;
          }
          #qty-input {
              text-align: center;
              width: 40px;
              border: none;
              font-size: 1rem;
              padding: 5px 0;
              -moz-appearance: textfield;
          }

          /* Submit Button */
          #place-order-btn {
              background-color: #28a745; /* Green for final action */
              color: white;
              border: none;
              padding: 12px;
              font-size: 1rem;
              font-weight: bold;
              border-radius: 4px;
              cursor: pointer;
              transition: background-color 0.2s;
              text-transform: uppercase;
              width: 100%;
              margin-top: 15px;
          }
          #place-order-btn:hover {
              background-color: #1e7e34;
          }

          /* Header - Simple Nav (Reused style) */
          .pdp-header {
              background-color: #fff;
              border-bottom: 1px solid #eee;
              padding: 10px 0;
              text-align: center;
              font-size: 1.2rem;
              font-weight: bold;
          }

          /* Modal Styling (Reused style) */
          .pdp-modal {
              position: fixed;
              top: 0;
              left: 0;
              width: 100%;
              height: 100%;
              background: rgba(0, 0, 0, 0.7);
              display: none;
              align-items: center;
              justify-content: center;
              z-index: 1000;
          }
          .pdp-modal-content {
              background: white;
              padding: 20px;
              border-radius: 8px;
              box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
              max-width: 300px;
              width: 90%;
              text-align: center;
              transform: scale(0.9);
              opacity: 0;
              transition: all 0.3s ease-in-out;
          }
          .pdp-modal.pdp-modal-show .pdp-modal-content {
              transform: scale(1);
              opacity: 1;
          }
          .pdp-modal-content h3 {
              color: green;
              font-size: 1.1rem;
              margin-top: 0;
          }
          #modal-close-btn {
              background-color: #007bff;
              color: white;
              border: none;
              padding: 8px 15px;
              border-radius: 4px;
              cursor: pointer;
              margin-top: 15px;
              width: 100%;
              font-size: 0.9rem;
          }

          /* Responsive adjustments for desktop */
          @media (min-width: 600px) {
              .order-form-grid {
                  flex-direction: row;
              }
              .form-column, .summary-column {
                  flex: 1;
              }
              .form-column {
                  padding-right: 20px;
              }
          }
          .category-image.full-cover {
              width: 100%;
              height: 100%;
              object-fit: cover; /* Fill the circle fully without stretching */
              display: block;
          }

          #place-order-btn.loading {
  pointer-events: none;
  opacity: 0.7;
  position: relative;
}
#place-order-btn.loading::after {
  content: "";
  position: absolute;
  right: 15px;
  top: 50%;
  width: 16px;
  height: 16px;
  border: 2px solid #fff;
  border-top-color: transparent;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  transform: translateY(-50%);
}
@keyframes spin {
  to { transform: rotate(360deg); }
}
.category-arrow-btn {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  z-index: 10;
  background-color: none;
  color: black;
  border: none;
  border-radius: 50%;
  width: 30px;
  height: 30px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  transition: background-color 0.2s, transform 0.2s;
}

.category-arrow-btn:hover {
  background-color: white;
  transform: translateY(-50%) scale(1.1);
}

#prev-nav {
  left: 5px;
}

#next-nav {
  right: 5px;
}
@media (max-width: 768px) {
    .category-arrow-btn{
      display: none;
    }
}
.category-slider-div{
  overflow:hidden;
  padding:14px 4%;
}
.category-slider-main-div{
  border-bottom:1px solid #E5E7EB;
  position: relative;
  margin:0rem 6%
}
@media (max-width: 768px) {
  .category-slider-div{
    overflow:hidden;
    padding:14px 0%;
  }
  .category-slider-main-div{
    border-bottom:1px solid #E5E7EB;
    position: relative;
    margin:0rem 4%
  }
}
</style>
</head>
<body>
<?php include "./includes/header.php" ?>


  <main>
      <?php echo $content; // page content goes here ?>
  </main>

<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
<script type="text/javascript">
const swiper = new Swiper(".mySwiper", {
  slidesPerView: 1,
  spaceBetween: 20,
  loop: true,
  pagination: { el: ".swiper-pagination", clickable: true },
  navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev" },
  autoplay: {
  delay: 2000,           // 3 seconds per slide
  disableOnInteraction: false, // continue autoplay after user interacts
},
});

</script>
<script src="./scripts/scripts.js" charset="utf-8"></script>
<script type="text/javascript">
document.getElementById('order-form').addEventListener('submit', function() {
const btn = document.getElementById('place-order-btn');
btn.classList.add('loading');
btn.textContent = 'Placing Order...';
});
</script>
</body>

</html>
