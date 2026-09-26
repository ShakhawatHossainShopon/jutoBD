<style>
.category-container-wrapper {
    margin: 0 auto;
    margin-top: 2rem;
}

/* Header Styling */
.category-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    padding-bottom: 8px;
    margin-bottom: 20px;
    border-bottom: 1px solid #ccc;
}

.category-title {
    font-size: 18px;
    font-weight: normal;
    color: #333;
}

.category-title-highlight {
    font-weight: bold;
    color: #333;
    position: relative;
    display: inline-block;
}

.category-title-highlight::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: -8px;
    width: 100%;
    height: 2px;
    background-color: #007bff;
}

.category-view-all {
    color: #1D4ED8;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    display: flex;
    align-items: center;
}

.category-view-all:hover {
    text-decoration: underline;
}

/* Category List Container */
.category-list-flex {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(115px, 1fr));
    gap: 30px;
    padding-bottom: 20px;
    justify-content: flex-start;
}

/* Category Item Styling */
.category-item {
    cursor: pointer;
    height: 140px;
    position: relative;
}

/* Circular Image Container */
.category-circle-bg {
    width: 100%;
    height: 100%;
    margin: 1 auto;
    border-radius: 4%;
    background-color: #f0f0f0;
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: hidden;
    position: relative;
    transition: transform 0.2s;
}

.category-item:hover .category-circle-bg {
    transform: translateY(-2px);
}

/* Category Image */
.category-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Always Visible Label */
.category-label {
    font-size: 12px;
    font-weight: 600;
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    text-align: start;
background: linear-gradient(90deg, rgba(124,58,237,0.8), rgba(236,72,153,0.9));

    padding: 5px 0px 5px 10px;
    opacity: 1;
    color: #f5f5f5;
    pointer-events: none;
    text-transform: capitalize;
}
</style>

<div class="category-container-wrapper">

    <!-- Header Section -->
    <div class="category-header">
        <h2 class="category-title">
            <span class="category-title-highlight">
                Top Categories
            </span>
        </h2>
        <a href="category_list.php" class="category-view-all">
            View All
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 4px;">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </a>
    </div>

    <!-- Category List Container -->
    <div class="category-list-flex">
        <?php foreach ($categories as $cat): ?>
            <a href="category_page.php?title=<?= urlencode($cat['title']) ?>" class="category-item">
                <div class="category-circle-bg">
                    <img src="<?= htmlspecialchars($cat['image']) ?>"
                        alt="<?= htmlspecialchars($cat['title']) ?>"
                        class="category-image full-cover" />
                    <p class="category-label"><?= htmlspecialchars($cat['title']) ?></p>
                </div>
            </a>
        <?php endforeach; ?>
    </div>

</div>
