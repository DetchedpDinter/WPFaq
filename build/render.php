<?php
/**
 * Render callback for the FAQ block.
 *
 * @param array $attributes Block attributes.
 * @return string Rendered HTML.
 */
function faq_render_callback($attributes) {
    // Block attributes
    $selected_category = $attributes['selectedCategory'] ?? null;
    $faq_count = $attributes['faqCount'] ?? 3;

    if (!$selected_category) {
        return '<p>No category selected.</p>';
    }

    // Query FAQs based on selected category
    $args = [
        'post_type'      => 'faq',
        'posts_per_page' => $faq_count,
        'tax_query'      => [
            [
                'taxonomy' => 'category',
                'field'    => 'id',
                'terms'    => $selected_category,
            ],
        ],
    ];

    $faqs = new WP_Query($args);

    if (!$faqs->have_posts()) {
        return '<p>No FAQs found for the selected category.</p>';
    }

    // Render FAQ list
    $output = '<div class="faq-list">';
    while ($faqs->have_posts()) {
        $faqs->the_post();
        $output .= '<div class="faq-item">';
        $output .= '<div class="faq-question" onclick="toggleAccordion(event)">' . get_the_title() . '<span class="faq-toggle">+</span></div>';
        $output .= '<div class="faq-answer" style="display: none;">' . apply_filters('the_content', get_the_content()) . '</div>';
        $output .= '</div>';
    }
    $output .= '</div>';

    // Add JavaScript for accordion functionality
    $output .= '
        <script>
            let lastOpenedIndex = -1;

            function toggleAccordion(event) {
                const faqItems = document.querySelectorAll(".faq-item");
                const currentIndex = Array.from(faqItems).indexOf(event.currentTarget.parentElement);

                if (lastOpenedIndex !== -1 && lastOpenedIndex !== currentIndex) {
                    faqItems[lastOpenedIndex].querySelector(".faq-answer").style.display = "none";
                    faqItems[lastOpenedIndex].querySelector(".faq-toggle").innerHTML = "+";
                }

                const currentAnswer = event.currentTarget.nextElementSibling;
                const currentToggle = event.currentTarget.querySelector(".faq-toggle");

                if (currentAnswer.style.display === "block") {
                    currentAnswer.style.display = "none";
                    currentToggle.innerHTML = "+";
                    lastOpenedIndex = -1;
                } else {
                    currentAnswer.style.display = "block";
                    currentToggle.innerHTML = "-";
                    lastOpenedIndex = currentIndex;
                }
            }
        </script>
    ';

    wp_reset_postdata();
    return $output;
}
