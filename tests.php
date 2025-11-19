<?php
include __DIR__ . '/includes/session.php';
include __DIR__ . '/data/tests-data.php';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/nav.php';
?>

<style>
    :root {
        --bs-bg: #e3f3f6;
        --bs-black: #000 !important;
        --bs-white: #fff !important;
        --bs-teal: #00796b;
        --bs-lteal: #72c5bb;
        --bs-br: 0.3em !important;
        --bs-font: 'Roboto', sans-serif !important;
        --bs-hfont: "Montserrat", sans-serif;
    }

    .subheading {
        font-size: 1rem;
        margin-bottom: 40px;
        text-align: center;
        color: var(--bs-black);
        max-width: 700px;
        margin-left: auto;
        margin-right: auto;
    }

    .filter-bar {
        background: var(--bs-white);
        border-radius: 12px;
        padding: 1rem;
        margin: 20px auto 40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        flex-wrap: wrap;
    }

    .test-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 25px;
    }

    .test-card {
        background: var(--bs-white);
        border-radius: var(--bs-br);
        padding: 25px 20px;
        text-align: center;
        transition: all 0.3s ease;
        position: relative;
        display: flex;
        flex-direction: column;
    }

    .test-card:hover {
        border: 1px solid var(--bs-teal);
        transform: translateY(-8px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }

    .icon {
        font-size: 48px;
        margin-bottom: 15px;
    }

    h3 {
        font-size: 1.25rem;
        margin: 12px 0;
        font-weight: 500;
    }

    .no-results {
        text-align: center;
        color: #888;
        margin-top: 20px;
        display: none;
    }
</style>

<section class="popular-tests" id="tests">
    <div class="container">
        <div class="top">
            <h2 class="text-center">Popular Tests</h2>
            <p class="subheading">
                Explore our frequently requested medical tests to prioritise your health with confidence and convenience.
            </p>
        </div>

        <!-- Filter/Search Bar -->
        <div class="filter-bar mb-5 d-flex flex-wrap justify-content-between align-items-center">
            <div class="mb-2">
                <input type="text" id="searchInput" class="form-control" placeholder="🔍 Search tests...">
            </div>
            <div>
                <select id="sortSelect" class="form-select">
                    <option value="">Sort by</option>
                    <option value="low">Price: Low to High</option>
                    <option value="high">Price: High to Low</option>
                </select>
            </div>
        </div>

        <div class="test-cards" id="testsGrid">
            <?php foreach ($tests as $id => $test): ?>
                <?php $discount_text = isset($test['discounted_price']) ? round((1 - $test['discounted_price'] / $test['price']) * 100) . "% Off" : ""; ?>
                <div class="test-card"
                    data-name="<?= htmlspecialchars($test['name']); ?>"
                    data-desc="<?= htmlspecialchars($test['desc']); ?>"
                    data-price="<?= isset($test['discounted_price']) ? $test['discounted_price'] : $test['price']; ?>">
                    <div class="icon"><?= $test['icon']; ?></div>
                    <h3><?= htmlspecialchars($test['name']); ?></h3>
                    <p class="text-muted small"><?= htmlspecialchars($test['desc']); ?></p>
                    <div class="mt-auto">
                      <?php if (isset($test['discounted_price'])): ?>
                        <div class="badge">
                          <span class="fw-bold">₹<?= $test['discounted_price']; ?></span>
                          <span><small class="text-muted"><s>₹<?= $test['price']; ?></s></small></span>
                        </div>
                        <small class="text-success d-block mb-3"><?= $discount_text; ?></small>
                      <?php else: ?>
                        <p class="badge">₹<?= $test['price']; ?></p>
                      <?php endif; ?>
                    </div>
                    <a href="addtocart.php?type=test&key=<?= urlencode($id); ?>" class="btn custom-btn w-100 mt-auto">Add To Cart</a>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- No Results -->
        <p class="no-results" id="noResults">No tests found.</p>
    </div>
</section>

<!-- 🔍 Search + Sort JS -->
<script>
    const searchInput = document.getElementById("searchInput");
    const sortSelect = document.getElementById("sortSelect");
    const testsGrid = document.getElementById("testsGrid");
    const testCards = Array.from(document.querySelectorAll(".test-card"));
    const noResults = document.getElementById("noResults");

    // Search function
    searchInput.addEventListener("input", () => {
        filterAndSort();
    });

    // Sort function
    sortSelect.addEventListener("change", () => {
        filterAndSort();
    });

    function filterAndSort() {
        const query = searchInput.value.toLowerCase();
        let visibleCards = testCards.filter(card => {
            const name = card.getAttribute("data-name").toLowerCase();
            const desc = card.getAttribute("data-desc").toLowerCase();
            return name.includes(query) || desc.includes(query);
        });

        // Sort by price
        if (sortSelect.value === "low") {
            visibleCards.sort((a, b) => a.getAttribute("data-price") - b.getAttribute("data-price"));
        } else if (sortSelect.value === "high") {
            visibleCards.sort((a, b) => b.getAttribute("data-price") - a.getAttribute("data-price"));
        }

        // Update DOM
        testsGrid.innerHTML = "";
        visibleCards.forEach(card => testsGrid.appendChild(card));

        // Toggle No Results message
        noResults.style.display = visibleCards.length === 0 ? "block" : "none";
    }

    // Handle initial search from URL
    const urlParams = new URLSearchParams(window.location.search);
    const initialSearch = urlParams.get('search');
    if (initialSearch) {
        searchInput.value = initialSearch;
        filterAndSort();
    }
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
